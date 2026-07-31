const fs = require('fs')
const path = require('path')

function update(relativePath, transform) {
  const file = path.resolve(__dirname, '..', relativePath)
  const original = fs.readFileSync(file, 'utf8')
  const next = transform(original)
  if (next === original) return
  fs.writeFileSync(file, next)
}

update('gyro-craftsman-web-own-v2.4/src/lang/index.js', (source) => {
  source = source.replace(
    "  const chooseLanguage = normalizeLanguage(Cookies.get('language'));\n  if (supportedLocales.includes(chooseLanguage)) return chooseLanguage;\n\n  const browserLanguage = normalizeLanguage(navigator.language || navigator.browserLanguage);\n  if (supportedLocales.includes(browserLanguage)) return browserLanguage;\n\n  return 'zh-cn';",
    "  const cookieLanguage = normalizeLanguage(Cookies.get('language'));\n  if (supportedLocales.includes(cookieLanguage)) return cookieLanguage;\n\n  const storedLanguage = normalizeLanguage(localStorage.getItem('language'));\n  if (supportedLocales.includes(storedLanguage)) return storedLanguage;\n\n  return 'zh-cn';"
  )
  if (!source.includes('export function setLanguage(')) {
    source = source.replace(
      "const i18n = new VueI18n({",
      "export function setLanguage(language) {\n  const normalized = normalizeLanguage(language) || 'zh-cn';\n  Cookies.set('language', normalized);\n  localStorage.setItem('language', normalized);\n  i18n.locale = normalized;\n  return normalized;\n}\n\nconst i18n = new VueI18n({"
    )
  }
  return source
})

update('gyro-craftsman-chat-v1.0/src/locale/index.ts', (source) => source.replace(
  "\n  const browser = normalizeLanguage(navigator.language);\n  if (browser && supportedLocales.includes(browser)) return browser;\n\n  return \"zh-cn\";",
  "\n  return \"zh-cn\";"
))

update('view-uni-src/locale/index.ts', (source) => {
  source = source.replace(
    "\n  const systemInfo = uni.getSystemInfoSync();\n  const systemLocale = normalizeLanguage(systemInfo.language);\n  if (systemLocale && supportedLocales.includes(systemLocale)) return systemLocale;\n\n  return \"zh-cn\";",
    "\n  return \"zh-cn\";"
  )
  source = source.replace(
    "  i18n.global.locale.value = locale;\n  return locale;",
    "  i18n.global.locale.value = locale;\n  uni.$emit(\"language:changed\", locale);\n  return locale;"
  )
  return source
})

update('view-uni-src/locale/navigation.ts', (source) => source.replace(
  "export function installLocalizedNavigation(app: App): void {\n  app.mixin({",
  "export function installLocalizedNavigation(app: App): void {\n  uni.$on(\"language:changed\", () => {\n    applyLocalizedNavigationTitle();\n    applyLocalizedTabBar();\n  });\n  app.mixin({"
))
