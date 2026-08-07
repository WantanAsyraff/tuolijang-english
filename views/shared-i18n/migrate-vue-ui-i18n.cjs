const fs = require("fs");
const path = require("path");
const { pathToFileURL } = require("url");

const viewsRoot = path.resolve(__dirname, "..");
const babelParser = require("../gyro-craftsman-web-own-v2.4/node_modules/@babel/parser");
const vue2Compiler = require("../gyro-craftsman-web-own-v2.4/node_modules/vue-template-compiler");
const vue3Compiler = require("../view-uni-src/node_modules/@vue/compiler-dom");

const HAS_HAN = /[\u3400-\u9fff]/;
const MANUAL_EN = { ...require("./manual-ui-en.cjs"), ...require("./manual-ui-en-extra.cjs"), ...require("./manual-ui-en-mobile.cjs"), ...require("./manual-ui-en-web.cjs"), ...require("./manual-ui-en-final.cjs") };
const VISIBLE_ATTRIBUTES = new Set([
  "active-text",
  "alt",
  "aria-label",
  "button-text",
  "button-title",
  "btntext",
  "cancel-button-text",
  "cancel-text",
  "close-text",
  "confirm-button-text",
  "confirm-text",
  "content",
  "default-title",
  "description",
  "element-loading-text",
  "empty-text",
  "empty-title",
  "end-placeholder",
  "inactive-text",
  "label",
  "left-text",
  "lefttext",
  "loading-text",
  "no-data-text",
  "no-filtered-data-text",
  "no-filtered-userfrom-text",
  "no-match-text",
  "no-userfrom-text",
  "placeholder",
  "popup-title",
  "range-separator",
  "right-text",
  "righttext",
  "start-placeholder",
  "text",
  "tip",
  "tips",
  "title",
  "topbreadcrumbtext",
  "unit",
]);
const SKIP_DIRS = new Set(["node_modules", "dist", "unpackage", "public", "static", "__MACOSX"]);
const SCRIPT_DISPLAY_PROPERTIES = new Set([
  "activeText", "alt", "ariaLabel", "btnText", "buttonText", "cancelText", "confirmText", "content",
  "description", "emptyText", "emptyTitle", "endPlaceholder", "inactiveText", "label", "loadingText",
  "message", "placeholder", "popupTitle", "rangeSeparator", "rightText", "startPlaceholder", "text", "tip",
  "tips", "title", "unit"
]);
const SCRIPT_SINK_CALLS = new Set([
  "$alert", "$confirm", "$message", "$notify", "$prompt", "alert", "confirm", "error", "info", "open",
  "prompt", "setNavigationBarTitle", "showLoading", "showModal", "showToast", "success", "warning"
]);
const SCRIPT_TRANSLATORS = new Set(["$localize", "$t", "$ts", "t", "translate", "translateSystemText"]);

const APPS = {
  web: {
    root: path.join(viewsRoot, "gyro-craftsman-web-own-v2.4"),
    source: "src",
    compiler: "vue2",
    migrateScripts: true,
    i18nImport: "@/lang",
    i18nAccessor: "t",
    generatedEn: "src/lang/generated-ui-en.js",
    generatedZh: "src/lang/generated-ui-zh.js",
  },
  chat: {
    root: path.join(viewsRoot, "gyro-craftsman-chat-v1.0"),
    source: "src",
    compiler: "vue3",
    generatedEn: "src/locale/generated-ui-en.ts",
    generatedZh: "src/locale/generated-ui-zh.ts",
  },
  mobile: {
    root: path.join(viewsRoot, "view-uni-src"),
    source: ".",
    compiler: "vue3",
    migrateScripts: true,
    i18nImport: "@/locale",
    i18nAccessor: "global.t",
    recoverScriptParse: true,
    generatedEn: "locale/generated-ui-en.ts",
    generatedZh: "locale/generated-ui-zh.ts",
    skipDirs: new Set(["uni_modules"]),
  },
};

function vueFiles(directory, extraSkipped = new Set()) {
  if (!fs.existsSync(directory)) return [];
  return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    if (entry.isDirectory() && (SKIP_DIRS.has(entry.name) || extraSkipped.has(entry.name))) return [];
    const full = path.join(directory, entry.name);
    if (entry.isDirectory()) return vueFiles(full, extraSkipped);
    return entry.name.endsWith(".vue") ? [full] : [];
  });
}

function readGenerated(file) {
  if (!fs.existsSync(file)) return {};
  const source = fs.readFileSync(file, "utf8");
  const match = source.match(/export default\s+(\{[\s\S]*\})\s*;?\s*$/);
  if (!match) throw new Error(`Unable to parse generated locale file: ${file}`);
  return JSON.parse(match[1]);
}

function words(value) {
  return String(value || "")
    .replace(/([a-z0-9])([A-Z])/g, "$1 $2")
    .replace(/[^A-Za-z0-9]+/g, " ")
    .trim()
    .split(/\s+/)
    .filter(Boolean);
}

function camel(value) {
  const parts = words(value);
  if (!parts.length) return "text";
  return parts
    .map((part, index) => {
      const lower = part.toLowerCase();
      return index === 0 ? lower : lower.charAt(0).toUpperCase() + lower.slice(1);
    })
    .join("");
}

function cleanText(value) {
  return String(value || "").replace(/\s+/g, " ").trim();
}

function templateLiteralValue(node, source) {
  if (node.type !== "TemplateLiteral" || node.expressions.length) return "";
  return source.slice(node.start + 1, node.end - 1);
}

function parseExpression(source) {
  return babelParser.parseExpression(source, {
    sourceType: "module",
    plugins: ["typescript", "optionalChaining", "nullishCoalescingOperator"],
  });
}

function walkExpression(node, parent, source, register, replacements) {
  if (!node || typeof node !== "object") return;
  const comparisonOperand =
    parent &&
    ((parent.type === "BinaryExpression" && ["==", "===", "!=", "!==", "in"].includes(parent.operator)) ||
      parent.type === "SwitchCase");
  const translationArgument =
    parent &&
    parent.type === "CallExpression" &&
    ["$t", "$ts", "t", "translate", "translateSystemText"].includes(
      parent.callee?.name || parent.callee?.property?.name
    );
  const propertyKey = parent && parent.type === "ObjectProperty" && parent.key === node && !parent.computed;

  if (!comparisonOperand && !translationArgument && !propertyKey && node.type === "StringLiteral" && HAS_HAN.test(node.value)) {
    const key = register(node.value);
    replacements.push({ start: node.start, end: node.end, value: `$t('${key}')` });
    return;
  }

  const templateValue = templateLiteralValue(node, source);
  if (!comparisonOperand && !translationArgument && templateValue && HAS_HAN.test(templateValue)) {
    const key = register(templateValue);
    replacements.push({ start: node.start, end: node.end, value: `$t('${key}')` });
    return;
  }

  for (const [field, value] of Object.entries(node)) {
    if (["loc", "start", "end", "leadingComments", "trailingComments", "innerComments"].includes(field)) continue;
    if (Array.isArray(value)) value.forEach((child) => walkExpression(child, node, source, register, replacements));
    else if (value && typeof value === "object") walkExpression(value, node, source, register, replacements);
  }
}

function transformExpression(source, register) {
  if (!HAS_HAN.test(source)) return source;
  let ast;
  try {
    ast = parseExpression(source);
  } catch {
    return source;
  }
  const replacements = [];
  walkExpression(ast, null, source, register, replacements);
  return replacements
    .sort((a, b) => b.start - a.start)
    .reduce((result, item) => result.slice(0, item.start) + item.value + result.slice(item.end), source);
}

function translatedText(raw, register) {
  const match = raw.match(/^((?:\s|&nbsp;|&emsp;|&#160;)*)([\s\S]*?)((?:\s|&nbsp;|&emsp;|&#160;)*)$/);
  const leading = match?.[1] || "";
  const value = match?.[2] || raw;
  const trailing = match?.[3] || "";
  if (!value || !HAS_HAN.test(value)) return raw;
  return `${leading}{{ $t(${JSON.stringify(register(value))}) }}${trailing}`;
}

function addReplacement(replacements, replacement) {
  if (replacement.start == null || replacement.end == null || replacement.start >= replacement.end) return;
  const overlaps = replacements.some(
    (item) => replacement.start < item.end && replacement.end > item.start
  );
  if (!overlaps) replacements.push(replacement);
}

function collectMustacheExpressions(template, register, replacements) {
  for (const match of template.matchAll(/\{\{([\s\S]*?)\}\}/g)) {
    const expression = match[1];
    const next = transformExpression(expression, register);
    if (next === expression) continue;
    const start = match.index + 2;
    addReplacement(replacements, { start, end: start + expression.length, value: next });
  }
}

function transformVue2Template(template, register) {
  const newline = template.includes("\r\n") ? "\r\n" : "\n";
  const normalizedTemplate = template.replace(/\r\n?/g, "\n");
  const compiled = vue2Compiler.compile(normalizedTemplate, { outputSourceRange: true, comments: true });
  if (!compiled.ast) return template;
  // vue-template-compiler trims leading whitespace before calculating source ranges.
  // Account for the real prefix (LF, CRLF, or indentation) instead of assuming one byte.
  const sourceOffset = Math.max(0, normalizedTemplate.search(/\S/));
  const replacements = [];
  const visited = new Set();

  function visit(node) {
    if (!node || visited.has(node)) return;
    visited.add(node);
    if (node.type === 3 && !node.isComment && !node.text.includes("<!--") && HAS_HAN.test(node.text || "")) {
      addReplacement(replacements, {
        start: node.start + sourceOffset,
        end: node.end + sourceOffset,
        value: translatedText(node.text, register),
      });
    }
    if (node.type !== 1) return;

    for (const raw of Object.values(node.rawAttrsMap || {})) {
      const rawName = raw.name || "";
      const bound = rawName.startsWith(":") || rawName.startsWith("v-bind:");
      const sourceName = rawName.replace(/^:|^v-bind:/, "");
      const name = sourceName.replace(/([a-z0-9])([A-Z])/g, "$1-$2").toLowerCase();
      const lookupName = name.replace(/-/g, "") === "btntext" ? "btntext" : name;
      if (!VISIBLE_ATTRIBUTES.has(lookupName) || !HAS_HAN.test(raw.value || "")) continue;
      if (bound) {
        const next = transformExpression(raw.value, register);
        if (next !== raw.value) {
          addReplacement(replacements, {
            start: raw.start + sourceOffset,
            end: raw.end + sourceOffset,
            value: `:${name}="${next.replace(/"/g, "&quot;")}"`,
          });
        }
      } else {
        addReplacement(replacements, {
          start: raw.start + sourceOffset,
          end: raw.end + sourceOffset,
          value: `:${name}="$t('${register(raw.value)}')"`,
        });
      }
    }
    for (const child of node.children || []) visit(child);
    for (const slot of Object.values(node.scopedSlots || {})) visit(slot);
    for (const condition of node.ifConditions || []) visit(condition.block);
  }

  visit(compiled.ast);
  collectMustacheExpressions(normalizedTemplate, register, replacements);
  const transformed = replacements
    .sort((a, b) => b.start - a.start)
    .reduce(
      (result, item) => result.slice(0, item.start) + item.value + result.slice(item.end),
      normalizedTemplate
    );
  return newline === "\r\n" ? transformed.replace(/\n/g, "\r\n") : transformed;
}

function scriptSourceFiles(root, extraSkipped = new Set()) {
  const result = [];
  function visit(directory) {
    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
      if (entry.isDirectory() && (SKIP_DIRS.has(entry.name) || extraSkipped.has(entry.name))) continue;
      const target = path.join(directory, entry.name);
      if (entry.isDirectory()) visit(target);
      else if (/\.(?:vue|[cm]?[jt]s)$/i.test(entry.name) && !/\.d\.ts$/i.test(entry.name)) result.push(target);
    }
  }
  visit(root);
  return result;
}

function scriptPropertyName(node) {
  if (!node || node.computed) return "";
  if (node.type === "Identifier") return node.name;
  if (node.type === "StringLiteral") return node.value;
  return "";
}

function scriptCalleeName(node) {
  if (!node) return "";
  if (node.type === "Identifier") return node.name;
  if (node.type === "MemberExpression" || node.type === "OptionalMemberExpression") {
    return node.computed ? scriptPropertyName(node.property) : node.property?.name || "";
  }
  return "";
}

function isScriptComparison(parent) {
  return (
    (parent?.type === "BinaryExpression" && ["==", "===", "!=", "!==", "in"].includes(parent.operator)) ||
    parent?.type === "SwitchCase"
  );
}

function transformVue3Template(template, register) {
  let ast;
  try {
    ast = vue3Compiler.baseParse(template, { comments: true });
  } catch {
    return template;
  }
  const replacements = [];

  function visit(node) {
    if (!node) return;
    if (node.type === 2 && !node.content.includes("<!--") && HAS_HAN.test(node.content || "")) {
      addReplacement(replacements, {
        start: node.loc.start.offset,
        end: node.loc.end.offset,
        value: translatedText(node.loc.source, register),
      });
    }
    if (node.type === 5 && HAS_HAN.test(node.content?.loc?.source || "")) {
      const expression = node.content.loc.source;
      const next = transformExpression(expression, register);
      if (next !== expression) {
        addReplacement(replacements, {
          start: node.content.loc.start.offset,
          end: node.content.loc.end.offset,
          value: next,
        });
      }
    }
    if (node.type === 1) {
      for (const prop of node.props || []) {
        if (prop.type === 6 && prop.value && VISIBLE_ATTRIBUTES.has(prop.name) && HAS_HAN.test(prop.value.content)) {
          addReplacement(replacements, {
            start: prop.loc.start.offset,
            end: prop.loc.end.offset,
            value: `:${prop.name}="$t('${register(prop.value.content)}')"`,
          });
        }
        if (
          prop.type === 7 &&
          prop.name === "bind" &&
          prop.arg?.type === 4 &&
          VISIBLE_ATTRIBUTES.has(prop.arg.content) &&
          prop.exp?.type === 4 &&
          HAS_HAN.test(prop.exp.content)
        ) {
          const next = transformExpression(prop.exp.content, register);
          if (next !== prop.exp.content) {
            addReplacement(replacements, {
              start: prop.exp.loc.start.offset,
              end: prop.exp.loc.end.offset,
              value: next,
            });
          }
        }
      }
    }
    for (const child of node.children || []) visit(child);
  }

  visit(ast);
  collectMustacheExpressions(template, register, replacements);
  return replacements
    .sort((a, b) => b.start - a.start)
    .reduce((result, item) => result.slice(0, item.start) + item.value + result.slice(item.end), template);
}

function transformScriptUi(source, register, i18nImport, i18nAccessor, lang = "js", errorRecovery = false) {
  let ast;
  try {
    ast = babelParser.parse(source, {
      sourceType: "module",
      plugins: [
        "typescript",
        ...(lang === "ts" ? [] : ["jsx"]),
        "decorators-legacy",
        "optionalChaining"
      ],
      errorRecovery
    });
  } catch {
    return source;
  }

  let i18nName = "appI18n";
  for (const statement of ast.program.body) {
    if (statement.type !== "ImportDeclaration" || statement.source.value !== i18nImport) continue;
    const defaultImport = statement.specifiers.find((item) => item.type === "ImportDefaultSpecifier");
    if (defaultImport) i18nName = defaultImport.local.name;
  }
  const hasI18nImport = ast.program.body.some(
    (statement) =>
      statement.type === "ImportDeclaration" &&
      statement.source.value === i18nImport &&
      statement.specifiers.some((item) => item.type === "ImportDefaultSpecifier")
  );
  const replacements = [];

  function visit(node, parent, ancestors) {
    if (!node || typeof node !== "object") return;
    if (node.type === "StringLiteral" && HAS_HAN.test(node.value || "")) {
      const call = [...ancestors].reverse().find((entry) => entry.type === "CallExpression");
      const translated = call && SCRIPT_TRANSLATORS.has(scriptCalleeName(call.callee));
      const displayProperty =
        parent?.type === "ObjectProperty" && parent.value === node && SCRIPT_DISPLAY_PROPERTIES.has(scriptPropertyName(parent.key));
      const assignmentProperty =
        parent?.type === "AssignmentExpression" &&
        parent.right === node &&
        SCRIPT_DISPLAY_PROPERTIES.has(scriptCalleeName(parent.left));
      const sinkCall =
        parent?.type === "CallExpression" &&
        parent.arguments.includes(node) &&
        SCRIPT_SINK_CALLS.has(scriptCalleeName(parent.callee)) &&
        !(parent.callee?.object?.type === "Identifier" && parent.callee.object.name === "console");
      const formatterReturn =
        parent?.type === "ReturnStatement" &&
        ancestors.some(
          (entry) =>
            (entry.type === "ObjectMethod" && scriptPropertyName(entry.key) === "formatter") ||
            (entry.type === "ObjectProperty" && scriptPropertyName(entry.key) === "formatter")
        );
      if (!translated && !isScriptComparison(parent) && (displayProperty || assignmentProperty || sinkCall || formatterReturn)) {
        addReplacement(replacements, {
          start: node.start,
          end: node.end,
          value: `${i18nName}.${i18nAccessor}('${register(node.value)}')`
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
  if (!replacements.length) return source;
  let transformed = replacements
    .sort((a, b) => b.start - a.start)
    .reduce((result, item) => result.slice(0, item.start) + item.value + result.slice(item.end), source);
  if (!hasI18nImport) transformed = `import ${i18nName} from '${i18nImport}';\n${transformed}`;
  return transformed;
}

function transformSourceScripts(file, source, register, i18nImport, i18nAccessor, errorRecovery) {
  if (!file.endsWith(".vue")) {
    const lang = path.extname(file).slice(1).toLowerCase();
    return transformScriptUi(source, register, i18nImport, i18nAccessor, lang, errorRecovery);
  }
  const replacements = [];
  for (const match of source.matchAll(/<script(?![^>]*\bsrc=)(\s[^>]*)?>([\s\S]*?)<\/script>/gi)) {
    const attributes = match[1] || "";
    const body = match[2];
    const lang = attributes.match(/\blang\s*=\s*["']([^"']+)["']/i)?.[1]?.toLowerCase() || "js";
    const next = transformScriptUi(body, register, i18nImport, i18nAccessor, lang, errorRecovery);
    if (next === body) continue;
    const start = match.index + match[0].indexOf(body);
    replacements.push({ start, end: start + body.length, value: next });
  }
  return replacements
    .sort((a, b) => b.start - a.start)
    .reduce((result, item) => result.slice(0, item.start) + item.value + result.slice(item.end), source);
}

function generatedSource(catalog) {
  const sorted = Object.fromEntries(Object.entries(catalog).sort(([a], [b]) => a.localeCompare(b)));
  return `// Generated by migrate-vue-ui-i18n.cjs. Do not edit by hand.\nexport default ${JSON.stringify(sorted, null, 2)};\n`;
}

function rootTemplateContentRange(source, file) {
  const templateStart = source.indexOf("<template");
  if (templateStart < 0) return null;
  const contentStart = source.indexOf(">", templateStart) + 1;
  const contentEnd = source.lastIndexOf("</template>");
  if (contentStart <= templateStart || contentEnd < contentStart) {
    throw new Error(`Unable to resolve root template boundaries: ${file}`);
  }
  return { start: contentStart, end: contentEnd };
}
async function run(appName) {
  const config = APPS[appName];
  if (!config) throw new Error(`Use one of: ${Object.keys(APPS).join(", ")}`);
  const shared = await import(pathToFileURL(path.join(__dirname, "index.js")).href);
  const enFile = path.join(config.root, config.generatedEn);
  const zhFile = path.join(config.root, config.generatedZh);
  const enCatalog = readGenerated(enFile);
  const zhCatalog = readGenerated(zhFile);
  const usedKeys = new Set([...Object.keys(enCatalog), ...Object.keys(zhCatalog)]);
  const pairKeys = new Map();
  const unresolved = new Map();
  Object.keys(zhCatalog).forEach((key) => pairKeys.set(`${zhCatalog[key]}\u0000${enCatalog[key] || ""}`, key));

  let currentRelative = "";
  function register(rawValue) {
    const zhValue = cleanText(rawValue);
    const enValue = cleanText(MANUAL_EN[zhValue] || shared.translateSystemTextValue(zhValue, { locale: "en" }));
    if (!zhValue || !HAS_HAN.test(zhValue)) throw new Error(`Invalid Chinese UI text in ${currentRelative}: ${rawValue}`);
    if (!enValue || HAS_HAN.test(enValue)) {
      if (!unresolved.has(zhValue)) unresolved.set(zhValue, currentRelative);
      return "ui.missingTranslation";
    }
    const pair = `${zhValue}\u0000${enValue}`;
    if (pairKeys.has(pair)) return `ui.${pairKeys.get(pair)}`;

    const relativeStem = currentRelative
      .replace(/\\/g, "/")
      .replace(/\.vue$/i, "")
      .split("/")
      .filter((part) => !["src", "views", "pages", "components"].includes(part))
      .slice(-5)
      .join(" ");
    const phrase = words(enValue).slice(0, 9).join(" ");
    let key = camel(`${relativeStem} ${phrase}`).slice(0, 120);
    if (!key) key = "text";
    let suffix = 2;
    const base = key;
    while (usedKeys.has(key)) key = `${base}${suffix++}`;
    usedKeys.add(key);
    pairKeys.set(pair, key);
    enCatalog[key] = enValue;
    zhCatalog[key] = zhValue;
    return `ui.${key}`;
  }

  let changedFiles = 0;
  let changedTemplates = 0;
  const pendingWrites = new Map();
  const sourceRoot = path.join(config.root, config.source);
  for (const file of vueFiles(sourceRoot, config.skipDirs || new Set())) {
    const source = fs.readFileSync(file, "utf8");
    currentRelative = path.relative(sourceRoot, file);
    const range = rootTemplateContentRange(source, currentRelative);
    if (!range) continue;
    const template = source.slice(range.start, range.end);
    const nextTemplate =
      config.compiler === "vue2"
        ? transformVue2Template(template, register)
        : transformVue3Template(template, register);
    if (nextTemplate === template) continue;
    const next = source.slice(0, range.start) + nextTemplate + source.slice(range.end);
    pendingWrites.set(file, next);
    changedFiles += 1;
    changedTemplates += 1;
  }

  if (config.migrateScripts) {
    for (const file of scriptSourceFiles(sourceRoot, config.skipDirs || new Set())) {
      const relative = path.relative(sourceRoot, file).replace(/\\/g, "/");
      if (/(?:^|\/)(?:lang|locale)\//.test(relative) || /system-text\.(?:js|ts)$/.test(relative)) continue;
      currentRelative = relative;
      const alreadyPending = pendingWrites.has(file);
      const source = alreadyPending ? pendingWrites.get(file) : fs.readFileSync(file, "utf8");
      const next = transformSourceScripts(
        file,
        source,
        register,
        config.i18nImport,
        config.i18nAccessor,
        config.recoverScriptParse || false
      );
      if (next === source) continue;
      pendingWrites.set(file, next);
      if (!alreadyPending) changedFiles += 1;
    }
  }

  if (unresolved.size) {
    const details = [...unresolved].map(([text, file]) => `${file}: ${text}`).join("\n");
    throw new Error(`Missing ${unresolved.size} English UI translations:\n${details}`);
  }
  pendingWrites.forEach((source, file) => fs.writeFileSync(file, source));
  fs.writeFileSync(enFile, generatedSource(enCatalog));
  fs.writeFileSync(zhFile, generatedSource(zhCatalog));
  console.log(
    `[${appName}] localized ${changedTemplates} templates in ${changedFiles} files; generated ${Object.keys(enCatalog).length} explicit UI keys`
  );
}

run(process.argv[2]).catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
