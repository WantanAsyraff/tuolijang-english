const fs = require("node:fs");
const path = require("node:path");
const { spawnSync } = require("node:child_process");
const babelParser = require("@babel/parser");
const { auditSql, formatSqlAuditSummary } = require("./sql-audit.cjs");

const root = __dirname;
const viewsRoot = path.resolve(root, "..");
const appNames = ["web", "chat", "mobile"];
const outputs = {
  web: path.join(viewsRoot, "gyro-craftsman-web-own-v2.4/src/lang/generated-locale.js"),
  chat: path.join(viewsRoot, "gyro-craftsman-chat-v1.0/src/locale/generated-locale.ts"),
  mobile: path.join(viewsRoot, "view-uni-src/locale/generated-locale.ts"),
};
const sharedOutput = path.join(root, "generated-catalog.js");
const hasHan = /[\u3400-\u9fff]/;

function argument(name, fallback = "") {
  const index = process.argv.indexOf(name);
  return index >= 0 ? process.argv[index + 1] || fallback : fallback;
}

function readCatalog(name) {
  return JSON.parse(fs.readFileSync(path.join(root, "catalogs", `${name}.json`), "utf8"));
}

function placeholders(value) {
  return [...String(value).matchAll(/\{\{?\s*([\w.]+)\s*\}?\}|%[sd]/g)]
    .map((match) => match[1] || match[0])
    .sort();
}

function loadAndValidate() {
  const catalogs = Object.fromEntries(["common", ...appNames].map((name) => [name, readCatalog(name)]));
  const ids = new Set();
  const runtimeValues = new Map();
  const runtimeValuesByApp = Object.fromEntries(appNames.map((app) => [app, new Map()]));
  const errors = [];
  for (const [catalogName, entries] of Object.entries(catalogs)) {
    for (const [id, entry] of Object.entries(entries)) {
      if (ids.has(id)) errors.push(`duplicate canonical key: ${id}`);
      ids.add(id);
      if (!entry || typeof entry["zh-cn"] !== "string" || typeof entry.en !== "string") {
        errors.push(`${catalogName}:${id} must contain string zh-cn and en values`);
        continue;
      }
      if (hasHan.test(entry.en) && entry.en !== "中文") errors.push(`Chinese text in English value: ${id}`);
      if (placeholders(entry["zh-cn"]).join("|") !== placeholders(entry.en).join("|")) {
        errors.push(`placeholder mismatch: ${id}`);
      }
      if (entry.runtime) {
        const previous = runtimeValues.get(entry["zh-cn"]);
        if (previous && previous !== entry.en) errors.push(`conflicting runtime translation: ${entry["zh-cn"]}`);
        runtimeValues.set(entry["zh-cn"], entry.en);
        const runtimeApps = entry.apps || appNames;
        if (!Array.isArray(runtimeApps) || runtimeApps.some((app) => !appNames.includes(app))) {
          errors.push(`${catalogName}:${id} has invalid runtime apps`);
        } else {
          runtimeApps.forEach((app) => runtimeValuesByApp[app].set(entry["zh-cn"], entry.en));
        }
      }
    }
  }
  if (errors.length) throw new Error(`Catalog validation failed:\n${errors.slice(0, 100).join("\n")}`);
  return { catalogs, runtimeValues, runtimeValuesByApp };
}

function setNested(target, dottedKey, value) {
  const parts = dottedKey.split(".");
  let node = target;
  parts.forEach((part, index) => {
    if (index === parts.length - 1) {
      if (Object.prototype.hasOwnProperty.call(node, part)) throw new Error(`Duplicate output key: ${dottedKey}`);
      node[part] = value;
      return;
    }
    if (typeof node[part] === "string") throw new Error(`Output key conflicts with namespace: ${dottedKey}`);
    node[part] ||= {};
    node = node[part];
  });
}

function runtimeSource(typescript = false) {
  let source = fs
    .readFileSync(path.join(root, "runtime.js"), "utf8")
    .replace("export function createLocalizationRuntime", "function createLocalizationRuntime")
    .trim();
  if (typescript) {
    source = source
      .replace("function createLocalizationRuntime(systemTextEn)", "function createLocalizationRuntime(systemTextEn: Readonly<Record<string, string>>)")
      .replace("function normalizeLocale(language)", "function normalizeLocale(language: unknown)")
      .replace("function containsHan(value)", "function containsHan(value: unknown)")
      .replace("function translateExact(value)", "function translateExact(value: unknown)")
      .replace("function translateParameterized(value)", "function translateParameterized(value: unknown)")
      .replace(
        "function translateSystemTextValue(value, options = {})",
        "function translateSystemTextValue(value: unknown, options: { locale?: unknown; englishValue?: unknown } = {})",
      );
  }
  return source;
}

function renderModule(app, catalog, runtimeValues) {
  const messages = { en: {}, "zh-cn": {} };
  const navigation = {};
  const tabs = [];
  const prefix = `${app}.`;
  for (const [id, entry] of Object.entries(catalog).sort(([left], [right]) => left.localeCompare(right))) {
    const outputKey = id.slice(prefix.length);
    if (app === "mobile" && outputKey.startsWith("navigation.")) {
      navigation[outputKey.slice("navigation.".length)] = { "zh-cn": entry["zh-cn"], en: entry.en };
      continue;
    }
    if (app === "mobile" && outputKey.startsWith("tab.")) {
      tabs.push({ index: Number(outputKey.slice("tab.".length)), "zh-cn": entry["zh-cn"], en: entry.en });
      continue;
    }
    setNested(messages.en, outputKey, entry.en);
    setNested(messages["zh-cn"], outputKey, entry["zh-cn"]);
  }
  tabs.sort((left, right) => left.index - right.index);
  const map = Object.fromEntries([...runtimeValues].sort(([left], [right]) => left.localeCompare(right, "zh-CN")));
  const mobileExports = app === "mobile"
    ? `\nexport const MOBILE_NAVIGATION = Object.freeze(${JSON.stringify(navigation, null, 2)});\nexport const MOBILE_TABS = Object.freeze(${JSON.stringify(tabs, null, 2)});\n`
    : "";
  return `// Generated by views/shared-i18n/cli.cjs. Do not edit by hand.\n` +
    `export const messages = Object.freeze(${JSON.stringify(messages, null, 2)});\n` +
    `export const SYSTEM_TEXT_EN = Object.freeze(${JSON.stringify(map, null, 2)});\n\n` +
    `${runtimeSource(app !== "web")}\n\n` +
    `const localizationRuntime = createLocalizationRuntime(SYSTEM_TEXT_EN);\n` +
    `export const normalizeLocale = localizationRuntime.normalizeLocale;\n` +
    `export const normalizeSystemLocale = localizationRuntime.normalizeLocale;\n` +
    `export const translateSystemTextValue = localizationRuntime.translateSystemTextValue;\n` +
    `export const containsHan = localizationRuntime.containsHan;\n` + mobileExports +
    `export default messages;\n`;
}

function renderShared(runtimeValues) {
  const map = Object.fromEntries([...runtimeValues].sort(([left], [right]) => left.localeCompare(right, "zh-CN")));
  return `// Generated by views/shared-i18n/cli.cjs. Do not edit by hand.\n` +
    `export const SYSTEM_TEXT_EN = Object.freeze(${JSON.stringify(map, null, 2)});\n\n` +
    `${runtimeSource()}\n\n` +
    `const localizationRuntime = createLocalizationRuntime(SYSTEM_TEXT_EN);\n` +
    `export const normalizeLocale = localizationRuntime.normalizeLocale;\n` +
    `export const translateSystemTextValue = localizationRuntime.translateSystemTextValue;\n` +
    `export const containsHan = localizationRuntime.containsHan;\n`;
}

function expectedOutputs(selectedApp) {
  const { catalogs, runtimeValues, runtimeValuesByApp } = loadAndValidate();
  const selected = selectedApp && selectedApp !== "all" ? [selectedApp] : appNames;
  if (selected.some((app) => !appNames.includes(app))) throw new Error(`Unknown app: ${selectedApp}`);
  const expected = new Map([[sharedOutput, renderShared(runtimeValues)]]);
  selected.forEach((app) => expected.set(outputs[app], renderModule(app, catalogs[app], runtimeValuesByApp[app])));
  return expected;
}

function generate(selectedApp) {
  for (const [file, source] of expectedOutputs(selectedApp)) {
    fs.mkdirSync(path.dirname(file), { recursive: true });
    fs.writeFileSync(file, source, "utf8");
    console.log(`generated ${path.relative(viewsRoot, file)}`);
  }
}

function check(selectedApp) {
  const stale = [];
  for (const [file, source] of expectedOutputs(selectedApp)) {
    if (!fs.existsSync(file) || fs.readFileSync(file, "utf8") !== source) stale.push(path.relative(viewsRoot, file));
  }
  if (stale.length) throw new Error(`Generated localization files are stale:\n${stale.join("\n")}\nRun i18n:generate.`);
  console.log(`localization output is current (${selectedApp || "all"})`);
}

function maskPreservingLines(value) {
  return value.replace(/[^\r\n]/g, " ");
}

function rootTemplate(source) {
  const opening = source.match(/<template(?:\s[^>]*)?>/i);
  if (!opening || opening.index === undefined) return { source: "", offset: 0 };
  const offset = opening.index + opening[0].length;
  const followingBlock = source.slice(offset).search(/<(?:script|style)(?:\s[^>]*)?>/i);
  const searchEnd = followingBlock >= 0 ? offset + followingBlock : source.length;
  const closing = source.lastIndexOf("</template>", searchEnd);
  const end = closing >= offset ? closing : searchEnd;
  return { source: source.slice(offset, end), offset };
}

function maskTemplateTags(value) {
  const chars = [...value];
  let quote = "";
  let inTag = false;
  for (let index = 0; index < chars.length; index += 1) {
    const char = chars[index];
    if (!inTag && char === "<") inTag = true;
    if (!inTag) continue;
    if (quote) {
      if (char === quote && chars[index - 1] !== "\\") quote = "";
    } else if (char === '"' || char === "'") quote = char;
    else if (char === ">") inTag = false;
    if (char !== "\r" && char !== "\n") chars[index] = " ";
  }
  return chars.join("");
}

const displayProperties = new Set([
  "alt", "ariaLabel", "buttonText", "cancelText", "confirmText", "content", "description",
  "emptyText", "innerHTML", "innerText", "label", "loadingText", "message", "placeholder", "popupTitle", "rightText",
  "text", "textContent", "tip", "tips", "title", "unit",
]);
const displayCalls = new Set([
  "$alert", "$confirm", "$message", "$notify", "$prompt", "alert", "confirm", "error", "info",
  "open", "prompt", "setNavigationBarTitle", "showLoading", "showModal", "showToast", "success", "warning",
]);
const translators = new Set(["$", "$localize", "$t", "$ts", "t", "translate", "translateSystemText"]);

function propertyName(node) {
  if (!node || node.computed) return "";
  if (node.type === "Identifier") return node.name;
  if (node.type === "StringLiteral") return node.value;
  return "";
}

function calleeName(node) {
  if (!node) return "";
  if (node.type === "Identifier") return node.name;
  if (["MemberExpression", "OptionalMemberExpression"].includes(node.type)) {
    return node.computed ? propertyName(node.property) : node.property?.name || "";
  }
  return "";
}

function isConsoleCall(call) {
  return call?.callee?.type === "MemberExpression" && call.callee.object?.type === "Identifier" && call.callee.object.name === "console";
}

function scriptBlocks(file, source) {
  if (!file.endsWith(".vue")) return [{ source, lineOffset: 0 }];
  return [...source.matchAll(/<script(?:\s[^>]*)?>([\s\S]*?)<\/script>/gi)].map((match) => ({
    source: match[1],
    lineOffset: source.slice(0, match.index).split(/\r?\n/).length - 1,
  }));
}

function isComparison(parent) {
  return (parent?.type === "BinaryExpression" && ["==", "===", "!=", "!==", "in"].includes(parent.operator)) || parent?.type === "SwitchCase";
}

function auditScriptBlock(block, relative, issues) {
  let ast;
  try {
    ast = babelParser.parse(block.source, {
      sourceType: "module",
      plugins: ["typescript", "jsx", "decorators-legacy", "optionalChaining", "nullishCoalescingOperator"],
    });
  } catch {
    return;
  }
  function visit(node, parent, ancestors) {
    if (!node || typeof node !== "object") return;
    const text = node.type === "StringLiteral" ? node.value : node.type === "TemplateElement" ? node.value.cooked || node.value.raw : "";
    if (text && hasHan.test(text)) {
      const call = [...ancestors].reverse().find((entry) => entry.type === "CallExpression");
      const translated = call && translators.has(calleeName(call.callee));
      const displayProperty = (parent?.type === "ObjectProperty" && displayProperties.has(propertyName(parent.key))) || [...ancestors].reverse().some((entry) =>
        ["ObjectProperty", "ObjectMethod"].includes(entry.type) && displayProperties.has(propertyName(entry.key))
      );
      const assignmentProperty = (parent?.type === "AssignmentExpression" && parent.right === node && displayProperties.has(calleeName(parent.left))) || [...ancestors].reverse().some((entry) =>
        entry.type === "AssignmentExpression" && displayProperties.has(calleeName(entry.left))
      );
      const sinkCall = (parent?.type === "CallExpression" && parent.arguments.includes(node) && displayCalls.has(calleeName(parent.callee)) && !isConsoleCall(parent)) || [...ancestors].reverse().some((entry) =>
        entry.type === "CallExpression" && displayCalls.has(calleeName(entry.callee)) && !isConsoleCall(entry)
      );
      const formatterReturn = parent?.type === "ReturnStatement" && ancestors.some((entry) =>
        ["ObjectMethod", "ObjectProperty"].includes(entry.type) && propertyName(entry.key) === "formatter"
      );
      if (!translated && !isComparison(parent) && (displayProperty || assignmentProperty || sinkCall || formatterReturn)) {
        issues.push(`${relative}:${(node.loc?.start.line || 1) + block.lineOffset} (${text.replace(/\s+/g, " ").trim()})`);
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

function audit(selectedApp) {
  check(selectedApp);
  const selected = selectedApp && selectedApp !== "all" ? [selectedApp] : appNames;
  let webRuntimeValues = new Map();
  let webMessageKeys = new Set();
  if (selected.includes("web")) {
    const { catalogs, runtimeValues, runtimeValuesByApp } = loadAndValidate();
    webRuntimeValues = runtimeValuesByApp.web;
    webMessageKeys = new Set(Object.keys(catalogs.web).map((id) => id.replace(/^web\./, "")));
    const sqlResult = auditSql({ repoRoot: path.resolve(viewsRoot, ".."), runtimeValues });
    console.log(formatSqlAuditSummary(sqlResult));
  }
  const roots = {
    web: path.join(viewsRoot, "gyro-craftsman-web-own-v2.4/src"),
    chat: path.join(viewsRoot, "gyro-craftsman-chat-v1.0/src"),
    mobile: path.join(viewsRoot, "view-uni-src"),
  };
  const literalIssues = [];
  const missingRuntimeIssues = [];
  const missingKeyIssues = [];
  const staticTemplateIssues = [];
  const staticScriptIssues = [];
  const legacyIssues = [];
  if (selected.includes("web")) {
    const packageJson = JSON.parse(fs.readFileSync(path.join(viewsRoot, "gyro-craftsman-web-own-v2.4/package.json"), "utf8"));
    if (packageJson.dependencies?.["vue-i18n"] || packageJson.dependencies?.["smart-vue-i18n"]) {
      legacyIssues.push("dashboard package still depends on a legacy i18n engine");
    }
    const forbiddenFiles = [
      "src/utils/i18n.js", "src/utils/i18ns.js", "src/lang/en-US.js", "src/lang/zh-CN.js",
      "src/lang/en-US_render.js", "src/lang/zh-CN_render.js", "src/lang/en-US_extension.js", "src/lang/zh-CN_extension.js",
      "src/lang/es.js", "src/lang/ja.js"
    ];
    forbiddenFiles.forEach((file) => {
      if (fs.existsSync(path.join(roots.web, "..", file))) legacyIssues.push(`obsolete dashboard locale file: ${file}`);
    });
  }
  function walk(directory) {
    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
      if (entry.isDirectory() && ["node_modules", "dist", "unpackage", "uni_modules", "public", "static"].includes(entry.name)) continue;
      const file = path.join(directory, entry.name);
      if (entry.isDirectory()) walk(file);
      else if (/\.(?:vue|js|ts|jsx|tsx)$/.test(entry.name) && !/generated-locale\.(?:js|ts)$/.test(entry.name)) {
        const source = fs.readFileSync(file, "utf8");
        const relative = path.relative(viewsRoot, file);
        if (selected.includes("web") && relative.startsWith("gyro-craftsman-web-own-v2.4")) {
          const forbidden = [
            [/\$(?:t|ts|te|st|st2)\s*\(/, "legacy translation method"],
            [/\bi18n(?:t|2t)\s*\(/, "legacy designer translation method"],
            [/\b(?:translateText|translateChartText|i18n)\s*\(/, "alternate translation wrapper"],
            [/(?:@\/|utils\/)i18ns?(?:\.js)?/, "legacy translation utility"],
            [/\bform_cache\b/, "legacy designer locale cache"],
            [/\bVueI18n\b|from\s+["']vue-i18n["']|smart-vue-i18n/, "legacy i18n engine"]
          ];
          forbidden.forEach(([pattern, label]) => {
            if (pattern.test(source)) legacyIssues.push(`${relative}: ${label}`);
          });
        }
        for (const match of source.matchAll(/\$ts\(\s*(["'])([^"']*[\u3400-\u9fff][^"']*)\1/g)) {
          const line = source.slice(0, match.index).split(/\r?\n/).length;
          literalIssues.push(`${path.relative(viewsRoot, file)}:${line}`);
        }
        if (selected.includes("web")) {
          for (const match of source.matchAll(/\$\(\s*(["'])([^"']*[\u3400-\u9fff][^"']*)\1/g)) {
            if (webRuntimeValues.has(match[2])) continue;
            const line = source.slice(0, match.index).split(/\r?\n/).length;
            missingRuntimeIssues.push(`${path.relative(viewsRoot, file)}:${line} (${match[2]})`);
          }
          for (const match of source.matchAll(/\$\(\s*(["'])([A-Za-z][\w.-]*\.[\w.-]*[\w-])\1/g)) {
            if (webMessageKeys.has(match[2])) continue;
            const line = source.slice(0, match.index).split(/\r?\n/).length;
            missingKeyIssues.push(`${path.relative(viewsRoot, file)}:${line} (${match[2]})`);
          }
        }
        if (entry.name.endsWith(".vue")) {
          const templateBlock = rootTemplate(source);
          const visible = templateBlock.source
            .replace(/<!--[\s\S]*?-->/g, maskPreservingLines)
            .replace(/\{\{[\s\S]*?\}\}/g, maskPreservingLines);
          const textOnly = maskTemplateTags(visible);
          for (const match of textOnly.matchAll(/[\u3400-\u9fff]+/g)) {
            const line = source.slice(0, templateBlock.offset + (match.index || 0)).split(/\r?\n/).length;
            staticTemplateIssues.push(`${path.relative(viewsRoot, file)}:${line} (${match[0]})`);
          }
          const attributePattern = /\s(?::)?(?:alt|aria-label|btn-text|btnText|button-text|cancel-text|confirm-text|content|default-title|defaultTitle|empty-text|end-placeholder|label|loading-text|no-data-text|no-filtered-data-text|no-match-text|placeholder|range-separator|start-placeholder|text|title|active-text|inactive-text|close-text|element-loading-text|no-filtered-userFrom-text|no-userFrom-text)\s*=\s*(["'])([\s\S]*?)\1/gi;
          for (const match of visible.matchAll(attributePattern)) {
            const displayExpression = match[2].replace(/(?:===?|!==?)\s*(["'`])[^"'`]*[\u3400-\u9fff][^"'`]*\1/g, "");
            if (!/[\u3400-\u9fff]/.test(displayExpression) || /\$(?:t|ts)?\s*\(/.test(displayExpression)) continue;
            const line = source.slice(0, templateBlock.offset + (match.index || 0)).split(/\r?\n/).length;
            staticTemplateIssues.push(`${path.relative(viewsRoot, file)}:${line} (${match[2].trim()})`);
          }
        }
        const vendorOwnedLocale = /system[\\/]dashboard-design[\\/]charts[\\/]configData\.js$/.test(relative);
        if (relative.startsWith("gyro-craftsman-web-own-v2.4") && !vendorOwnedLocale && !/(?:^|[\\/])(?:lang|locale)(?:[\\/])/.test(relative)) {
          scriptBlocks(file, source).forEach((block) => auditScriptBlock(block, relative, staticScriptIssues));
        }
      }
    }
  }
  selected.forEach((app) => walk(roots[app]));
  if (legacyIssues.length) throw new Error(`Legacy dashboard localization is not allowed:\n${[...new Set(legacyIssues)].slice(0, 100).join("\n")}`);
  if (literalIssues.length) throw new Error(`Literal dynamic translations must use canonical keys:\n${literalIssues.slice(0, 100).join("\n")}`);
  if (missingRuntimeIssues.length) throw new Error(`Literal $() system text is missing from the canonical runtime index:\n${[...new Set(missingRuntimeIssues)].slice(0, 100).join("\n")}`);
  if (missingKeyIssues.length) throw new Error(`Literal $() key does not exist in the generated dashboard catalog:\n${[...new Set(missingKeyIssues)].slice(0, 100).join("\n")}`);
  if (staticTemplateIssues.length) throw new Error(`Static Chinese template UI must use canonical $() keys:\n${[...new Set(staticTemplateIssues)].slice(0, 100).join("\n")}`);
  if (staticScriptIssues.length) throw new Error(`Static Chinese script UI must use canonical $() keys:\n${[...new Set(staticScriptIssues)].slice(0, 100).join("\n")}`);
  console.log(`localization audit passed (${selected.join(", ")})`);
}

function runTests(selectedApp) {
  const result = spawnSync(process.execPath, ["--test", path.join(root, "localization.test.mjs")], {
    cwd: viewsRoot,
    stdio: "inherit",
    env: { ...process.env, I18N_TEST_APP: selectedApp },
  });
  if (result.status !== 0) process.exitCode = result.status || 1;
}

const command = process.argv[2];
const selectedApp = argument("--app", "all");
try {
  if (command === "generate") generate(selectedApp);
  else if (command === "check") check(selectedApp);
  else if (command === "audit") audit(selectedApp);
  else if (command === "test") runTests(selectedApp);
  else throw new Error("Usage: node cli.cjs <generate|check|audit|test> [--app web|chat|mobile|all]");
} catch (error) {
  console.error(error.message || error);
  process.exitCode = 1;
}
