const fs = require('fs')
const path = require('path')

const root = path.resolve(__dirname, '../view-uni-src')

function patch(relativePath, replacements) {
  const file = path.join(root, relativePath)
  let source = fs.readFileSync(file, 'utf8').replace(/\r\n/g, '\n')
  replacements.forEach(([from, to, all = false]) => {
    if (!source.includes(from)) throw new Error(`${relativePath}: expected anchor not found: ${from}`)
    source = all ? source.split(from).join(to) : source.replace(from, to)
  })
  fs.writeFileSync(file, source, 'utf8')
  console.log(`Updated ${relativePath}`)
}

patch('locale/index.ts', [
  ['import zhCn from "./zh-cn";', 'import zhCn from "./zh-cn";\nimport { translateSystemTextValue } from "../../shared-i18n/index.js";'],
  ['export default i18n;', 'export const translateSystemText = (value: unknown, englishValue?: string): unknown =>\n  translateSystemTextValue(value, { locale: i18n.global.locale.value, englishValue });\n\nexport default i18n;']
])

patch('main.ts', [
  ['import i18n from "./locale";', 'import i18n from "./locale";\nimport { installLocalization } from "./locale/install";'],
  ['  app.use(i18n);', '  app.use(i18n);\n  installLocalization(app);']
])

patch('vite.config.ts', [
  ['import AutoImport from "unplugin-auto-import/vite";', 'import AutoImport from "unplugin-auto-import/vite";\nimport { i18nTemplatePlugin } from "./config/i18nTemplatePlugin";'],
  ['  plugins: [\n    uni(),', '  plugins: [\n    i18nTemplatePlugin(),\n    uni(),']
])

patch('App.vue', [
  ["import { resumeApp, getNum } from '@/app/bootstrap/resume'", "import { resumeApp, getNum } from '@/app/bootstrap/resume'\nimport { applyLocalizedNavigationTitle, applyLocalizedTabBar } from '@/locale/navigation'"],
  ['  bootstrapOnLaunch(options)', '  bootstrapOnLaunch(options)\n  applyLocalizedTabBar()'],
  ['  resumeApp()\n  getNum()', '  resumeApp()\n  getNum()\n  applyLocalizedNavigationTitle()\n  applyLocalizedTabBar()']
])

patch('utils/message.ts', [
  ['type IconType =', 'import { translateSystemText } from "@/locale";\n\ntype IconType ='],
  ['      title: success,', '      title: String(translateSystemText(success)),'],
  ['      title: error,', '      title: String(translateSystemText(error)),']
])

patch('utils/helper.ts', [
  ['// 手机号码正则验证', 'import { translateSystemText } from "@/locale";\n\n// 手机号码正则验证'],
  ['      title: title,\n      content: content + "?",', '      title: String(translateSystemText(title)),\n      content: String(translateSystemText(content)) + "?",']
])

patch('components/defaultNavBar/index.vue', [
  ['{{ title }}', '{{ $ts(title) }}', true],
  ['{{ item.name }}', '{{ $ts(item.name) }}', true],
  [':title="item.text"', ':title="String($ts(item.text))"'],
  ['{{ rightText }}', '{{ $ts(rightText) }}']
])

patch('components/moduleForm/index.vue', [
  ['{{ val.field_name }}', '{{ $ts(val.field_name) }}', true],
  ['{{ val.options.placeholder }}', '{{ $ts(val.options.placeholder) }}', true],
  [":placeholder=\"'请输入' + val.field_name\"", ':placeholder="String($ts(\'请输入\' + val.field_name))"', true]
])

patch('components/oaForm/index.vue', [
  ['{{ val.key_name }}', '{{ $ts(val.key_name) }}', true],
  [":placeholder=\"val.placeholder || '请填写' + val.key_name\"", ":placeholder=\"String($ts(val.placeholder || '请填写' + val.key_name))\"", true],
  [":popup-title=\"val.placeholder || '请选择'\"", ":popup-title=\"String($ts(val.placeholder || '请选择'))\"", true]
])

patch('pages/users/center/index.vue', [
  ['    data.title = "昵称";', '    data.title = String(t("user.nickname"));'],
  ['      placeholder: "请输入昵称",', '      placeholder: t("user.enterNickname"),'],
  ['    data.title = "邮箱";', '    data.title = String(t("user.email"));'],
  ['      placeholder: "请输入邮箱",', '      placeholder: t("user.enterEmail"),'],
  ['      message.success("ID已复制成功");', '      message.success(t("common.copied"));'],
  ['  showModal("确认退出登录")', '  showModal(t("common.confirmLogout"))']
])
