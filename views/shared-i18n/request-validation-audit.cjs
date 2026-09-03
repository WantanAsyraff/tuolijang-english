#!/usr/bin/env node

const fs = require("node:fs");
const path = require("node:path");

const repoRoot = path.resolve(__dirname, "../..");
const requestsRoot = path.join(repoRoot, "app/Http/Requests");
const han = /[\u3400-\u9fff]/u;
const validationBlock = /(?:function\s+(?:message|messages)\s*\([^)]*\)|\$message\s*=)[\s\S]*?(?=\n\s*(?:public|protected|private)\s+(?:function|\$)|\n\s*})/g;
const phpString = /(["'])((?:\\.|(?!\1)[^\r\n])*?)\1/g;
const composedFragments = new Set([
  "的密码组合",
  "的组合",
  "个字符",
  "密码格式不正确,请输入",
  "密码长度不正确,最少",
  "确认密码不符合规则,请输入",
  "确认密码格式不正确,请输入",
  "输入的密码不符合规则,请输入",
]);

function walk(directory, output = []) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    const full = path.join(directory, entry.name);
    if (entry.isDirectory()) {
      if (entry.name !== "Chat") walk(full, output);
    } else if (entry.name.endsWith(".php")) {
      output.push(full);
    }
  }
  return output;
}

function extractValidationMessages() {
  const candidates = new Map();
  for (const filename of walk(requestsRoot)) {
    const source = fs.readFileSync(filename, "utf8");
    for (const blockMatch of source.matchAll(validationBlock)) {
      const block = blockMatch[0];
      for (const stringMatch of block.matchAll(phpString)) {
        const text = stringMatch[2].replace(/\\(["'\\])/g, "$1");
        if (!han.test(text)) continue;
        const absoluteIndex = (blockMatch.index || 0) + (stringMatch.index || 0);
        const line = source.slice(0, absoluteIndex).split(/\r?\n/).length;
        const record = candidates.get(text) || { text, occurrences: [] };
        record.occurrences.push({
          file: path.relative(repoRoot, filename).replaceAll("\\", "/"),
          line,
        });
        candidates.set(text, record);
      }
    }
  }
  return [...candidates.values()].sort((left, right) => left.text.localeCompare(right.text, "zh-CN"));
}

function auditRequestValidation({ translate } = {}) {
  const runtime = translate || require("./generated-catalog.js").translateSystemTextValue;
  const candidates = extractValidationMessages();
  const rows = candidates.map((candidate) => {
    const english = runtime(candidate.text, { locale: "en" });
    const composed = composedFragments.has(candidate.text);
    return {
      ...candidate,
      english,
      composed,
      covered: composed || (typeof english === "string" && !han.test(english)),
    };
  });
  return {
    total: rows.length,
    covered: rows.filter((row) => row.covered).length,
    unresolved: rows.filter((row) => !row.covered),
    rows,
  };
}

if (require.main === module) {
  const result = auditRequestValidation();
  if (process.argv.includes("--json")) {
    process.stdout.write(`${JSON.stringify(result, null, 2)}\n`);
  } else {
    console.log(`Management validation messages: ${result.total}`);
    console.log(`English runtime results: ${result.covered}`);
    console.log(`Unresolved validation messages: ${result.unresolved.length}`);
    result.unresolved.forEach((row) => {
      const first = row.occurrences[0];
      console.log(`${first.file}:${first.line} ${row.text}`);
    });
  }
  if (result.unresolved.length) process.exitCode = 1;
}

module.exports = { auditRequestValidation, extractValidationMessages };
