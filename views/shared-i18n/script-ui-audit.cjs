const fs = require("fs");
const path = require("path");

const HAS_HAN = /[\u3400-\u9fff]/;
const DISPLAY_PROPERTIES = new Set([
  "alt",
  "ariaLabel",
  "buttonText",
  "cancelText",
  "confirmText",
  "content",
  "description",
  "emptyText",
  "label",
  "loadingText",
  "message",
  "placeholder",
  "popupTitle",
  "rightText",
  "text",
  "tip",
  "tips",
  "title",
  "unit",
]);
const SINK_CALLS = new Set([
  "$alert",
  "$confirm",
  "$message",
  "$notify",
  "$prompt",
  "alert",
  "confirm",
  "error",
  "info",
  "open",
  "prompt",
  "setNavigationBarTitle",
  "showLoading",
  "showModal",
  "showToast",
  "success",
  "warning",
]);
const TRANSLATORS = new Set(["$localize", "$t", "$ts", "t", "translate", "translateSystemText"]);

function propertyName(node) {
  if (!node || node.computed) return "";
  if (node.type === "Identifier") return node.name;
  if (node.type === "StringLiteral") return node.value;
  return "";
}

function calleeName(node) {
  if (!node) return "";
  if (node.type === "Identifier") return node.name;
  if (node.type === "MemberExpression" || node.type === "OptionalMemberExpression") {
    return node.computed ? propertyName(node.property) : node.property?.name || "";
  }
  return "";
}

function scriptBlocks(file, source) {
  if (!file.endsWith(".vue")) return [{ source, offset: 0 }];
  const blocks = [];
  for (const match of source.matchAll(/<script(?:\s[^>]*)?>([\s\S]*?)<\/script>/gi)) {
    const body = match[1];
    blocks.push({ source: body, offset: source.slice(0, match.index).split(/\r?\n/).length - 1 });
  }
  return blocks;
}

function isComparison(parent) {
  return (
    (parent?.type === "BinaryExpression" && ["==", "===", "!=", "!==", "in"].includes(parent.operator)) ||
    parent?.type === "SwitchCase"
  );
}

function literalText(node) {
  if (node.type === "StringLiteral") return node.value;
  if (node.type === "TemplateElement") return node.value.cooked || node.value.raw;
  return "";
}

function inspectAst(ast, relative, lineOffset, issues) {
  function visit(node, parent, ancestors) {
    if (!node || typeof node !== "object") return;
    const text = literalText(node);
    if (text && HAS_HAN.test(text)) {
      const call = [...ancestors].reverse().find((entry) => entry.type === "CallExpression");
      const translated = call && TRANSLATORS.has(calleeName(call.callee));
      const displayProperty =
        parent?.type === "ObjectProperty" && DISPLAY_PROPERTIES.has(propertyName(parent.key));
      const assignmentProperty =
        parent?.type === "AssignmentExpression" &&
        parent.right === node &&
        DISPLAY_PROPERTIES.has(calleeName(parent.left));
      const sinkCall =
        parent?.type === "CallExpression" &&
        parent.arguments.includes(node) &&
        SINK_CALLS.has(calleeName(parent.callee));
      const formatterReturn =
        parent?.type === "ReturnStatement" &&
        ancestors.some(
          (entry) =>
            (entry.type === "ObjectMethod" && propertyName(entry.key) === "formatter") ||
            (entry.type === "ObjectProperty" && propertyName(entry.key) === "formatter")
        );

      if (!translated && !isComparison(parent) && (displayProperty || assignmentProperty || sinkCall || formatterReturn)) {
        issues.push({
          file: relative,
          line: (node.loc?.start.line || 1) + lineOffset,
          sink: displayProperty
            ? `property:${propertyName(parent.key)}`
            : assignmentProperty
              ? `assignment:${calleeName(parent.left)}`
              : sinkCall
                ? `call:${calleeName(parent.callee)}`
                : "formatter:return",
          text: text.replace(/\s+/g, " ").trim(),
        });
      }
    }

    const nextAncestors = ancestors.concat(node);
    for (const [key, value] of Object.entries(node)) {
      if (["loc", "start", "end", "leadingComments", "trailingComments", "innerComments"].includes(key)) continue;
      if (Array.isArray(value)) value.forEach((child) => visit(child, node, nextAncestors));
      else if (value && typeof value === "object") visit(value, node, nextAncestors);
    }
  }
  visit(ast.program, null, []);
}

function auditScriptUi({ root, sourceFiles, parse }) {
  const issues = [];
  for (const file of sourceFiles) {
    const relative = path.relative(root, file).replace(/\\/g, "/");
    if (
      /(?:^|\/)(?:lang|locale)\//.test(relative) ||
      /generated-ui-(?:en|zh)\.(?:js|ts)$/.test(relative) ||
      /system-text\.(?:js|ts)$/.test(relative)
    ) {
      continue;
    }
    const source = fs.readFileSync(file, "utf8");
    for (const block of scriptBlocks(file, source)) {
      if (!HAS_HAN.test(block.source)) continue;
      try {
        const ast = parse(block.source);
        inspectAst(ast, relative, block.offset, issues);
      } catch {
        // The owning client compiler remains authoritative for non-standard syntax.
      }
    }

    if (file.endsWith(".vue")) {
      for (const match of source.matchAll(/content\s*:\s*(['"])([^'"]*[\u3400-\u9fff][^'"]*)\1/g)) {
        issues.push({
          file: relative,
          line: source.slice(0, match.index).split(/\r?\n/).length,
          sink: "css:content",
          text: match[2],
        });
      }
    }
  }
  return issues;
}

module.exports = { auditScriptUi };
