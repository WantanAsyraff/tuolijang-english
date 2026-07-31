const fs = require("fs");
const path = require("path");
const parser = require("../gyro-craftsman-web-own-v2.4/node_modules/@babel/parser");

const viewsRoot = path.resolve(__dirname, "..");
const webRoot = path.join(viewsRoot, "gyro-craftsman-web-own-v2.4");
const hasHan = (value) => /[\u3400-\u9fff]/.test(String(value || ""));

function parseModule(file, plugins = []) {
    return parser.parse(fs.readFileSync(file, "utf8"), {
        sourceType: "module",
        plugins,
    });
}

function flattenLocale(file) {
    const ast = parseModule(file);
    const exportDefault = ast.program.body.find(
        (node) => node.type === "ExportDefaultDeclaration"
    );
    const messages = {};

    function visit(node, prefix = "") {
        if (!node || node.type !== "ObjectExpression") return;
        node.properties.forEach((property) => {
            if (property.type !== "ObjectProperty") return;
            const key = property.key.name ?? property.key.value;
            const fullKey = prefix ? `${prefix}.${key}` : String(key);
            if (property.value.type === "ObjectExpression") {
                visit(property.value, fullKey);
            } else if (property.value.type === "StringLiteral") {
                messages[fullKey] = property.value.value;
            }
        });
    }

    visit(exportDefault && exportDefault.declaration);
    return messages;
}

function extractDirectPairs(file) {
    const ast = parseModule(file);
    const pairs = new Map();

    function visit(node) {
        if (!node || typeof node !== "object") return;
        if (node.type === "ObjectProperty") {
            const key =
                node.key.type === "StringLiteral" ? node.key.value : null;
            const value =
                node.value.type === "StringLiteral" ? node.value.value : null;
            if (key && value && hasHan(key) && !hasHan(value)) {
                pairs.set(key.trim().replace(/[:：]$/, ""), value);
            }
        }
        Object.entries(node).forEach(([key, value]) => {
            if (
                [
                    "loc",
                    "start",
                    "end",
                    "comments",
                    "leadingComments",
                    "trailingComments",
                ].includes(key)
            )
                return;
            if (Array.isArray(value)) value.forEach(visit);
            else if (value && typeof value === "object") visit(value);
        });
    }

    visit(ast);
    return pairs;
}

function objectPropertyName(property) {
    if (!property || property.computed) return "";
    if (property.key.type === "Identifier") return property.key.name;
    if (property.key.type === "StringLiteral") return property.key.value;
    return "";
}

function findObjectDeclaration(ast, name) {
    for (const statement of ast.program.body) {
        if (
            statement.type !== "VariableDeclaration" &&
            statement.type !== "ExportNamedDeclaration"
        )
            continue;
        const declaration =
            statement.type === "ExportNamedDeclaration"
                ? statement.declaration
                : statement;
        if (!declaration || declaration.type !== "VariableDeclaration")
            continue;
        for (const item of declaration.declarations) {
            if (
                item.id.type === "Identifier" &&
                item.id.name === name &&
                item.init?.type === "ObjectExpression"
            ) {
                return item.init;
            }
        }
    }
    return null;
}

function stringObjectEntries(object) {
    const entries = new Map();
    if (!object || object.type !== "ObjectExpression") return entries;
    object.properties.forEach((property) => {
        if (
            property.type !== "ObjectProperty" ||
            property.value.type !== "StringLiteral"
        )
            return;
        const key = objectPropertyName(property);
        if (key) entries.set(key, property.value.value);
    });
    return entries;
}

function nestedObject(object, name) {
    if (!object || object.type !== "ObjectExpression") return null;
    const property = object.properties.find(
        (item) =>
            item.type === "ObjectProperty" && objectPropertyName(item) === name
    );
    return property?.value?.type === "ObjectExpression" ? property.value : null;
}

const zh = flattenLocale(path.join(webRoot, "src/lang/zh.js"));
const en = flattenLocale(path.join(webRoot, "src/lang/en.js"));
const pairs = new Map();

Object.keys(zh).forEach((key) => {
    if (en[key] && hasHan(zh[key]) && !hasHan(en[key])) {
        pairs.set(zh[key].trim().replace(/[:：]$/, ""), en[key]);
    }
});

extractDirectPairs(path.join(webRoot, "src/utils/i18ns.js")).forEach(
    (value, key) => {
        pairs.set(key, value);
    }
);

// Resolve legacy Chinese menu/runtime labels through their real English
// messages. Otherwise internal keys such as "customerManagement" leak into
// the UI as camelCase text.
const legacyFile = path.join(webRoot, "src/utils/i18ns.js");
const legacyAst = parseModule(legacyFile);
const legacyKeyMap = stringObjectEntries(
    findObjectDeclaration(legacyAst, "systemTextKeyMap")
);
const legacyTranslations = stringObjectEntries(
    nestedObject(
        findObjectDeclaration(legacyAst, "systemTextTranslations"),
        "en"
    )
);

legacyKeyMap.forEach((englishKey, chineseText) => {
    const translated =
        en[`systemText.${englishKey}`] || legacyTranslations.get(englishKey);
    if (translated && !hasHan(translated)) pairs.set(chineseText, translated);
});

const sorted = Object.fromEntries(
    [...pairs.entries()].sort(([a], [b]) => a.localeCompare(b, "zh-CN"))
);
const output = `// Generated by extract-web-system-text.cjs. Do not edit by hand.\nexport const GENERATED_SYSTEM_TEXT_EN = ${JSON.stringify(
    sorted,
    null,
    2
)}\n`;
fs.writeFileSync(
    path.join(__dirname, "generated-en-system-text.js"),
    output,
    "utf8"
);
console.log(`Generated ${pairs.size} shared English system-text entries.`);
