#!/usr/bin/env node

const fs = require("node:fs");
const path = require("node:path");

const root = path.resolve(__dirname, "../..");
const han = /[\u3400-\u9fff]/;
const responseCall = /(?:->|::)(?:fail|success|exception|message|msg)\s*\(\s*(["'])((?:\\.|(?!\1)[\s\S])*?)\1/g;
const responseLine = /(?:->|::)(?:fail|success|exception|message|msg)\s*\(|throw\s+new\s+[A-Za-z_\\][\w\\]*Exception\s*\(/;
const phpString = /(["'])((?:\\.|(?!\1).)*)\1/g;
const excludedDirectories = new Set(["vendor", "database", "storage", "public", "views", "node_modules", ".git", "app\\Console", "crmeb\\command"]);
const internalExclusions = new Map([
  [" 不是有效的目录!", "internal filesystem verification"],
  [" 不是有效的文件或目录!", "internal filesystem verification"],
  ["表不存在", "internal split-table infrastructure"],
  ["方法不存在", "internal page-path dispatch"],
  ["请设置任务主键", "developer-only task configuration"],
]);
const composedFragments = new Set([
  "\"实体中的一对一关联字段", "”输入的公式错误,错误原因:", "]实体的主字段展示",
  "】存在负责人, 不能进行取消流失操作", "】状态异常, 不能进行取消流失操作",
  "%s不能晚于%s", "%s不能早于%s", "%s最多输入%d个%s", "%s最多选择数量%d",
  "%s最少输入%d个%s", "%s最少输入字数%d", "%s最少选择数量%d", "表中删除数据",
  "不能为空", "创建电子签约订单失败:", "创建电子签约流程失败:", "导入结果，成功:%s条,失败:%s条.",
  "导入失败：{$e->getMessage()}", "的记录已存在，请勿重复添加！", "的考核记录已存在，无法重复添加！",
  "的值不能重复", "分类数量到达上限", "个文件", "获取会话存档失败:",
  "获取企业微信应用配置失败：", "获取token失败:", "客户ID", "模块中分配该数据！",
  "模块中分享该数据！", "模块中更新该数据！", "模块中删除该数据！", "模块中新增数据！",
  "平台错误：", "七牛云：", "权限有误", "失败原因:", "时间格式无法解析", "条,失败：",
  "条.", "条数据", "未通过原因", "信息", "行数据", "已存在", "远程文件下载失败: ",
  "直属", "中删除数据", "中修改数据", "转移人ID", "最少输入字数%d", "Redis 连接失败: ",
]);

function walk(directory, output = []) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    const full = path.join(directory, entry.name);
    const relative = path.relative(root, full);
    if (entry.isDirectory()) {
      if (![...excludedDirectories].some((item) => relative === item || relative.startsWith(`${item}${path.sep}`))) walk(full, output);
    } else if (entry.name.endsWith(".php")) output.push(full);
  }
  return output;
}

function addCandidate(map, value, filename, line, code) {
  if (!han.test(value)) return;
  const record = map.get(value) || { text: value, occurrences: [] };
  record.occurrences.push({ file: path.relative(root, filename).replaceAll("\\", "/"), line, code: code.trim() });
  map.set(value, record);
}

const direct = new Map();
const broad = new Map();
for (const filename of [...walk(path.join(root, "app")), ...walk(path.join(root, "crmeb"))]) {
  const source = fs.readFileSync(filename, "utf8");
  const lines = source.split(/\r?\n/);
  for (const match of source.matchAll(responseCall)) {
    const value = match[2].replace(/\\(["'\\])/g, "$1");
    const line = source.slice(0, match.index).split("\n").length;
    addCandidate(direct, value, filename, line, lines[line - 1]);
  }
  lines.forEach((code, index) => {
    if (!responseLine.test(code)) return;
    phpString.lastIndex = 0;
    for (const match of code.matchAll(phpString)) addCandidate(broad, match[2].replace(/\\(["'\\])/g, "$1"), filename, index + 1, code);
  });
}

const catalogs = ["common", "web", "chat", "mobile"].map((name) =>
  JSON.parse(fs.readFileSync(path.join(__dirname, `catalogs/${name}.json`), "utf8"))
);
const mapped = new Set(catalogs.flatMap((catalog) =>
  Object.values(catalog).filter((entry) => entry.runtime).map((entry) => entry["zh-cn"])
));
const directRecords = [...direct.values()].sort((a, b) => a.text.localeCompare(b.text, "zh-CN"));
const broadResidual = [...broad.values()].filter((item) => !mapped.has(item.text));
const result = {
  totals: {
    directCandidates: directRecords.length,
    directRuntimeMapped: directRecords.filter((item) => mapped.has(item.text)).length,
    directUnmapped: directRecords.filter((item) => !mapped.has(item.text)).length,
    broadCandidates: broad.size,
    composedFragments: broadResidual.filter((item) => composedFragments.has(item.text)).length,
    intentionallyExcluded: broadResidual.filter((item) => internalExclusions.has(item.text)).length,
    unclassified: broadResidual.filter((item) => !composedFragments.has(item.text) && !internalExclusions.has(item.text)).length,
  },
  directUnmapped: directRecords.filter((item) => !mapped.has(item.text)),
  intentionallyExcluded: broadResidual.filter((item) => internalExclusions.has(item.text)).map((item) => ({ ...item, reason: internalExclusions.get(item.text) })),
  unclassified: broadResidual.filter((item) => !composedFragments.has(item.text) && !internalExclusions.has(item.text)),
};

if (process.argv.includes("--json")) process.stdout.write(`${JSON.stringify(result, null, 2)}\n`);
else {
  console.log(`Direct backend candidates: ${result.totals.directCandidates}`);
  console.log(`Direct runtime mapped: ${result.totals.directRuntimeMapped}`);
  console.log(`Direct unmapped: ${result.totals.directUnmapped}`);
  console.log(`Reviewed composed fragments: ${result.totals.composedFragments}`);
  console.log(`Intentionally excluded internal literals: ${result.totals.intentionallyExcluded}`);
  console.log(`Unclassified residuals: ${result.totals.unclassified}`);
}
if (result.totals.directUnmapped || result.totals.unclassified) process.exitCode = 1;