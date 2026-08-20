import assert from "node:assert/strict";
import { spawnSync } from "node:child_process";
import fs from "node:fs";
import path from "node:path";
import test from "node:test";
import { fileURLToPath } from "node:url";
import { createRequire } from "node:module";

import { createLocalizationRuntime } from "./runtime.js";

const root = path.dirname(fileURLToPath(import.meta.url));
const require = createRequire(import.meta.url);
const views = path.resolve(root, "..");
const catalogDirectory = path.join(root, "catalogs");
const appRoots = {
  web: path.join(views, "gyro-craftsman-web-own-v2.4"),
  chat: path.join(views, "gyro-craftsman-chat-v1.0"),
  mobile: path.join(views, "view-uni-src"),
};
const selectedApp = process.env.I18N_TEST_APP || "all";
const selectedApps = selectedApp === "all" ? Object.keys(appRoots) : [selectedApp];
const includesApp = (name) => selectedApps.includes(name);

function read(relative) {
  return fs.readFileSync(path.join(views, relative), "utf8");
}

function catalog(name) {
  return JSON.parse(fs.readFileSync(path.join(catalogDirectory, `${name}.json`), "utf8"));
}

function runCli(command) {
  const result = spawnSync(process.execPath, [path.join(root, "cli.cjs"), command, "--app", selectedApp], {
    cwd: views,
    encoding: "utf8",
  });
  assert.equal(result.status, 0, `${result.stdout}\n${result.stderr}`);
}

const common = catalog("common");
const runtimeIndex = Object.fromEntries(
  Object.values(common).filter((entry) => entry.runtime).map((entry) => [entry["zh-cn"], entry.en])
);
const runtime = createLocalizationRuntime(runtimeIndex);
const repoRoot = path.resolve(views, '..');
const { auditSql, auditSqlSource, decodeSqlString } = require(path.join(root, 'sql-audit.cjs'));
const sqlPolicy = JSON.parse(fs.readFileSync(path.join(root, 'sql-audit-policy.json'), 'utf8'));
const { formatNotificationTemplatePreview } = require(
  path.join(views, "gyro-craftsman-web-own-v2.4/src/lang/notification-template-preview.js")
);

test("canonical catalogs contain complete adjacent locale pairs", () => {
  const identifiers = new Set();
  for (const name of ["common", "web", "chat", "mobile"]) {
    for (const [identifier, entry] of Object.entries(catalog(name))) {
      assert.equal(identifiers.has(identifier), false, `duplicate catalog key: ${identifier}`);
      identifiers.add(identifier);
      assert.equal(typeof entry["zh-cn"], "string", `${identifier} missing zh-cn`);
      assert.equal(typeof entry.en, "string", `${identifier} missing en`);
      assert.equal(/[\u3400-\u9fff]/.test(entry.en) && entry.en !== "中文", false, `${identifier} has Chinese English text`);
    }
  }
  assert.ok(Object.keys(common).length >= 4226);
  assert.ok(Object.keys(catalog("web")).length >= 5000);
  assert.ok(Object.keys(catalog("mobile")).length >= 900);
});

test("locale aliases normalize while unknown aliases remain unset", () => {
  assert.equal(runtime.normalizeLocale("zh_CN"), "zh-cn");
  assert.equal(runtime.normalizeLocale("zh-Hans"), "zh-cn");
  assert.equal(runtime.normalizeLocale("en-US"), "en");
  assert.equal(runtime.normalizeLocale("fr"), "");
});

test("runtime translation honors backend English and exact canonical text", () => {
  assert.equal(runtime.translateSystemTextValue("保存", { locale: "en" }), "Save");
  assert.equal(runtime.translateSystemTextValue("保存", { locale: "en", englishValue: "Persist" }), "Persist");
  assert.equal(runtime.translateSystemTextValue("账号或密码不正确", { locale: "en" }), "Incorrect account or password.");
  assert.equal(runtime.translateSystemTextValue("缺少审批流程", { locale: "en" }), "The approval process is missing.");
  assert.equal(runtime.translateSystemTextValue("保存", { locale: "zh-cn", englishValue: "Persist" }), "保存");
});

test("dynamic backend responses preserve interpolated values in both locales", () => {
  const cases = [
    ["员工导入结果，成功：12条,失败：3条.", "Employee import result — successful: 12, failed: 3."],
    ["第4行账目金额必须大于0", "In row 4, the amount must be greater than 0"],
    ["维度总分必须为100分", "The dimension total score must be 100"],
    ["目标字段“total”输入的公式错误,错误原因:division by zero", "The formula for target field “total” is invalid. Reason: division by zero"],
    ["暂无权限在Orders模块中删除该数据！", "You do not have permission to delete this data in the Orders module"],
    ["客户【Acme】存在负责人, 不能进行取消流失操作", "Customer Acme has an owner, so the lost status cannot be cancelled"],
    ["读取 JSON 文件失败：C:/data/cities.json", "Failed to read JSON file: C:/data/cities.json"],
    ["版本信息不匹配，请更新App版本至: v3.2.1版本", "Version mismatch. Update the app to version v3.2.1"],
    ["字段sku的值不能重复", "The value of field sku must be unique"],
    ["导入失败：invalid workbook", "Import failed: invalid workbook"],
    ["清除角标失败: connection refused", "Failed to clear the badge: connection refused"],
    ["Wan的日报已提交，请及时查看！", "Wan's Daily has been submitted. Please review it promptly."],
  ];

  for (const [source, expected] of cases) {
    const translated = runtime.translateSystemTextValue(source, { locale: "en" });
    assert.equal(translated, expected);
    assert.equal(/[\u3400-\u9fff]/.test(translated), false);
    assert.equal(runtime.translateSystemTextValue(source, { locale: "zh-cn" }), source);
  }
});
test("notification templates translate placeholder labels without changing Chinese mode", () => {
  const assessment = "您还未创建{#负责部门}的{#时间2}考核目标，请您尽快制定考核任务！！！";
  const fileDeletion = "{#创建人}创建的{#文件名称}文件已被{#删除人}删除，请悉知！";
  const translatedAssessment = runtime.translateSystemTextValue(assessment, { locale: "en" });
  const translatedFileDeletion = runtime.translateSystemTextValue(fileDeletion, { locale: "en" });

  assert.equal(/[\u3400-\u9fff]/.test(translatedAssessment), false);
  assert.match(translatedAssessment, /\{#responsibleDepartment\}/);
  assert.match(translatedAssessment, /\{#time2\}/);
  assert.equal(/[\u3400-\u9fff]/.test(translatedFileDeletion), false);
  assert.match(translatedFileDeletion, /\{#fileName\}/);
  assert.equal(runtime.translateSystemTextValue(assessment, { locale: "zh-cn" }), assessment);
});

test("notification subscription previews hide raw placeholder syntax in English mode", () => {
  const translated = "You have not created the {#time2} objectives for {#responsibleDepartment}.";
  const preview = formatNotificationTemplatePreview(translated, "en");

  assert.equal(preview, "You have not created the time 2 objectives for responsible department.");
  assert.doesNotMatch(preview, /\{#[^}]+\}/);
  assert.equal(formatNotificationTemplatePreview(translated, "zh-cn"), translated);
});

test("runtime formatting is allowlisted and unknown business content is preserved", () => {
  assert.equal(runtime.translateSystemTextValue("共 12 条", { locale: "en" }), "Total 12");
  assert.equal(runtime.translateSystemTextValue("2个实体", { locale: "en" }), "2 entities");
  assert.equal(runtime.translateSystemTextValue("操作日志(8)", { locale: "en" }), "Operation logs (8)");
  assert.equal(runtime.translateSystemTextValue("12月", { locale: "en" }), "December");
  assert.equal(runtime.translateSystemTextValue("客户手写备注", { locale: "en" }), "客户手写备注");
});

test("backend user-facing response audit has no unmapped or unclassified candidates", () => {
  const result = spawnSync(process.execPath, [path.join(root, "backend-audit.cjs")], {
    cwd: views,
    encoding: "utf8",
  });
  assert.equal(result.status, 0, `${result.stdout}\n${result.stderr}`);
  assert.match(result.stdout, /Direct unmapped: 0/);
  assert.match(result.stdout, /Unclassified residuals: 0/);
});
test("SQL parser classifies multiline statements, escaping, JSON, comments, identifiers, and comparison values", { skip: !includesApp("web") }, () => {
  const source = `
-- 审计注释
CREATE TABLE sample (name varchar(20) COMMENT '字段说明') COMMENT='测试表';
INSERT INTO eb_system_config (key_name, desc) VALUES
  ('引号配置', '包含\\'引号');
INSERT INTO eb_approve_form (title, content) VALUES
  ('审批表单', '{"label":"审批标题","value":"raw_key"}');
INSERT INTO eb_system_menus (menu_name, unique_auth) VALUES
  ('系统菜单', '内部标识');
INSERT INTO eb_chat_app_mcp_services (name, info)
  SELECT '客户MCP服务', '客户信息' WHERE NOT EXISTS (SELECT 1);
UPDATE eb_form_data a JOIN eb_form_cate b ON a.cate_id = b.id
  SET a.key_name = replace(key_name, '合同', '订单')
  WHERE a.key_name LIKE '%合同%';
INSERT INTO eb_message (title, content) VALUES ('测试消息', '用户内容');
`;
  const rows = auditSqlSource({ source, relative: "fixtures/sql-audit.sql", policy: sqlPolicy });
  const find = (value, classification) => rows.some((row) => row.value === value && row.classification === classification);

  assert.equal(decodeSqlString("'包含\\'引号'"), "包含'引号");
  assert.equal(rows.some((row) => row.classification === "COMMENT" && row.value.includes("审计注释")), true);
  assert.equal(find("字段说明", "DATABASE_METADATA"), true);
  assert.equal(find("包含'引号", "USER_VISIBLE"), true);
  assert.equal(find("审批标题", "USER_VISIBLE"), true);
  assert.equal(find("内部标识", "IDENTIFIER"), true);
  assert.equal(find("客户MCP服务", "USER_VISIBLE"), true);
  assert.equal(find("合同", "INTERNAL_VALUE"), true);
  assert.equal(find("测试消息", "TEST_DATA"), true);
  assert.equal(rows.some((row) => row.classification === "UNKNOWN"), false);
});

test("all tracked SQL display values are classified, mapped, and hierarchy-safe", { skip: !includesApp("web") }, () => {
  const sql = auditSql({ repoRoot, runtimeValues: new Map(Object.entries(runtimeIndex)) });

  assert.equal(sql.files, 19);
  assert.equal(sql.unknown.length, 0);
  assert.equal(sql.missing.length, 0);
  assert.equal(sql.classifications.UNKNOWN, 0);
  assert.equal(sql.userVisible, sql.mapped);
  assert.equal(sql.uncoveredFrontend.length, 0);
  assert.equal(sql.frontendCoverage.length, 20);
  assert.ok(sql.areaValues.length > 3500);
  assert.ok(Object.keys(sql.byFile).length === 19);
  assert.ok(Object.keys(sql.byTable).length > 30);
  assert.ok(Object.keys(sql.byColumn).length > 50);

  for (const row of sql.areaValues) {
    assert.notEqual(row.areaCode, null, `${row.value} missing raw area code`);
    assert.notEqual(row.parentCode, null, `${row.value} missing raw parent relationship`);
  }
  assert.equal(runtimeIndex["北京市"], "Beijing");
  assert.equal(runtimeIndex["重庆市"], "Chongqing");
  assert.equal(runtimeIndex["内蒙古自治区"], "Inner Mongolia Autonomous Region");
  assert.equal(runtimeIndex["六安市"], "Lu'an City");
  assert.equal(runtimeIndex["漯河市"], "Luohe City");
  assert.equal(/[\u3400-\u9fff]/.test(runtimeIndex["渝中区"]), false);
});
test("web localization coverage inventory and bootstrap title are current", { skip: !includesApp("web") }, () => {
  const coverage = spawnSync(process.execPath, [path.join(root, "web-coverage.cjs"), "--check"], {
    cwd: views,
    encoding: "utf8",
  });
  assert.equal(coverage.status, 0, `${coverage.stdout}\n${coverage.stderr}`);
  assert.match(coverage.stdout, /211 route modules, 336 shared components/);

  const report = fs.readFileSync(path.join(root, "reports", "web-coverage.md"), "utf8");
  assert.match(report, /211 routable Vue view modules/);
  assert.match(report, /336 shared Vue components/);
  assert.match(report, /## Shared component inventory/);

  const config = fs.readFileSync(path.join(appRoots.web, "vue.config.js"), "utf8");
  assert.match(config, /const name = "Tuoluojiang OA"/);
  assert.doesNotMatch(config, /const name = defaultSettings\.title/);
});
test("generation is deterministic and the source audit passes", () => {
  runCli("check");
  runCli("audit");
});

test("each client imports one committed generated locale module", () => {
  const adapterFiles = {
    web: "gyro-craftsman-web-own-v2.4/src/lang/index.js",
    chat: "gyro-craftsman-chat-v1.0/src/locale/index.ts",
    mobile: "view-uni-src/locale/index.ts",
  };
  const generatedFiles = {
    web: "gyro-craftsman-web-own-v2.4/src/lang/generated-locale.js",
    chat: "gyro-craftsman-chat-v1.0/src/locale/generated-locale.ts",
    mobile: "view-uni-src/locale/generated-locale.ts",
  };
  for (const name of selectedApps) {
    const source = read(adapterFiles[name]);
    assert.match(source, /generated-locale/);
    assert.doesNotMatch(source, /from\s+["']\.\/(?:en|zh-cn|zh)["']/);
    assert.equal(fs.existsSync(path.join(views, generatedFiles[name])), true, generatedFiles[name]);
  }
});

test("client adapters preserve language persistence and request contracts", () => {
  if (includesApp("web")) {
    assert.match(read("gyro-craftsman-web-own-v2.4/src/lang/index.js"), /language/);
    assert.match(read("gyro-craftsman-web-own-v2.4/src/api/request.js"), /laravel_lang/);
  }
  if (includesApp("chat")) {
    const chat = read("gyro-craftsman-chat-v1.0/src/locale/index.ts");
    assert.match(chat, /language/);
    assert.match(chat, /searchParams\.get\(LANGUAGE_KEY\)/);
    assert.match(read("gyro-craftsman-chat-v1.0/src/utils/http.ts"), /laravel_lang/);
  }
  if (includesApp("mobile")) {
    const mobile = read("view-uni-src/locale/index.ts");
    assert.match(mobile, /language/);
    assert.match(mobile, /uni\.setStorageSync|setStorageSync/);
    assert.match(read("view-uni-src/utils/request.ts"), /laravel_lang/);
    assert.match(read("view-uni-src/locale/navigation.ts"), /language:changed/);
    assert.match(read("view-uni-src/locale/navigation.ts"), /setNavigationBarTitle/);
  }
});

test("mobile route and tab metadata are generated from the canonical catalog", { skip: !includesApp("mobile") }, () => {
  const generated = read("view-uni-src/locale/generated-locale.ts");
  assert.match(generated, /export const MOBILE_NAVIGATION/);
  assert.match(generated, /export const MOBILE_TABS/);
  const mobile = catalog("mobile");
  assert.ok(Object.keys(mobile).filter((key) => key.startsWith("mobile.navigation.")).length > 100);
  assert.ok(Object.keys(mobile).filter((key) => key.startsWith("mobile.tab.")).length > 0);
});

test("dashboard exposes $() as its only application translation interface", () => {
  const adapter = read("gyro-craftsman-web-own-v2.4/src/lang/index.js");
  assert.match(adapter, /export function \$\(/);
  assert.match(adapter, /Vue\.prototype\.\$\s*=\s*\$/);
  assert.match(adapter, /interpolate\(keyed, paramsOrEnglishValue\)/);
  assert.doesNotMatch(adapter, /\$(?:t|ts|te|st|st2)\s*\(/);

  const packageJson = JSON.parse(read("gyro-craftsman-web-own-v2.4/package.json"));
  assert.equal(packageJson.dependencies?.["vue-i18n"], undefined);
  assert.equal(packageJson.dependencies?.["smart-vue-i18n"], undefined);

  for (const file of [
    "gyro-craftsman-web-own-v2.4/src/utils/i18n.js",
    "gyro-craftsman-web-own-v2.4/src/utils/i18ns.js",
    "gyro-craftsman-web-own-v2.4/src/lang/en-US.js",
    "gyro-craftsman-web-own-v2.4/src/lang/zh-CN.js",
    "gyro-craftsman-web-own-v2.4/src/lang/es.js",
    "gyro-craftsman-web-own-v2.4/src/lang/ja.js",
  ]) assert.equal(fs.existsSync(path.join(views, file)), false, file);

  const notification = read("gyro-craftsman-web-own-v2.4/src/lang/notification-text.js");
  assert.match(notification, /normalizeNotificationInput/);
  assert.doesNotMatch(notification, /generated-locale|KNOWN_NOTIFICATION_TEXT|translateSystemText/);
  assert.doesNotMatch(notification, /(?<![\w.])\$\s*\(/);
  assert.match(notification, /translate\(input\)/);
});

test("dashboard script translation calls are lexically scoped", () => {
  const sourceRoot = path.join(appRoots.web, "src");
  const pending = [sourceRoot];
  const issues = [];

  while (pending.length) {
    const directory = pending.pop();
    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
      const filename = path.join(directory, entry.name);
      if (entry.isDirectory()) {
        pending.push(filename);
        continue;
      }
      if (!entry.name.endsWith(".vue")) continue;

      const source = fs.readFileSync(filename, "utf8");
      const scriptOpen = source.indexOf("<script");
      const scriptMatch = source.match(/<script(?:\s[^>]*)?>([\s\S]*?)<\/script>/);
      if (!scriptMatch) continue;

      const relative = path.relative(sourceRoot, filename);
      const script = scriptMatch[1];
      if (source.slice(0, scriptOpen).includes("import { $ } from '@/lang'")) {
        issues.push(`${relative}: translator import is outside <script>`);
      }
      if (/(?<![\w.])\$\s*\(/.test(script) && !/import\s*\{[^}]*\$[^}]*\}\s*from\s*["']@\/lang["']/.test(script)) {
        issues.push(`${relative}: bare $() call has no translator import`);
      }
    }
  }

  assert.deepEqual(issues, []);
});
test("all clients expose the same localization maintenance commands", () => {
  const expected = ["i18n:generate", "i18n:check", "i18n:audit", "i18n:test"];
  for (const name of selectedApps) {
    const directory = appRoots[name];
    const pkg = JSON.parse(fs.readFileSync(path.join(directory, "package.json"), "utf8"));
    for (const script of expected) assert.equal(typeof pkg.scripts[script], "string", `${name} missing ${script}`);
  }
});
