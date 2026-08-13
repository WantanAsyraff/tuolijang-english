import assert from "node:assert/strict";
import { spawnSync } from "node:child_process";
import fs from "node:fs";
import path from "node:path";
import test from "node:test";
import { fileURLToPath } from "node:url";

import { createLocalizationRuntime } from "./runtime.js";

const root = path.dirname(fileURLToPath(import.meta.url));
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
  assert.equal(runtime.translateSystemTextValue("保存", { locale: "zh-cn", englishValue: "Persist" }), "保存");
});

test("runtime formatting is allowlisted and unknown business content is preserved", () => {
  assert.equal(runtime.translateSystemTextValue("共 12 条", { locale: "en" }), "Total 12");
  assert.equal(runtime.translateSystemTextValue("2个实体", { locale: "en" }), "2 entities");
  assert.equal(runtime.translateSystemTextValue("操作日志(8)", { locale: "en" }), "Operation logs (8)");
  assert.equal(runtime.translateSystemTextValue("12月", { locale: "en" }), "December");
  assert.equal(runtime.translateSystemTextValue("客户手写备注", { locale: "en" }), "客户手写备注");
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
});
test("all clients expose the same localization maintenance commands", () => {
  const expected = ["i18n:generate", "i18n:check", "i18n:audit", "i18n:test"];
  for (const name of selectedApps) {
    const directory = appRoots[name];
    const pkg = JSON.parse(fs.readFileSync(path.join(directory, "package.json"), "utf8"));
    for (const script of expected) assert.equal(typeof pkg.scripts[script], "string", `${name} missing ${script}`);
  }
});
