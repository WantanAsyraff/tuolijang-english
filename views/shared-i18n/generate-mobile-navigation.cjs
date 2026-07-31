const fs = require('fs')
const path = require('path')
const JSON5 = require('../view-uni-src/node_modules/json5')

const mobileRoot = path.resolve(__dirname, '../view-uni-src')
const source = fs.readFileSync(path.join(mobileRoot, 'pages.json'), 'utf8')
const config = JSON5.parse(source.replace(/^\s*\/\/\s*#(?:ifn?def|endif).*$/gm, ''))
const navigation = {}

for (const page of config.pages || []) {
  const title = page.style?.navigationBarTitleText
  if (title) navigation[page.path] = title
}

for (const group of config.subPackages || []) {
  for (const page of group.pages || []) {
    const title = page.style?.navigationBarTitleText
    if (title) navigation[`${group.root}/${page.path}`] = title
  }
}

const tabs = (config.tabBar?.list || []).map((item, index) => ({
  index,
  pagePath: item.pagePath,
  text: item.text
}))

const output = `import type { App } from "vue";\nimport { translateSystemText } from "@/locale";\n\nexport const NAVIGATION_TITLE_ZH: Record<string, string> = ${JSON.stringify(navigation, null, 2)};\n\nexport const TAB_BAR_ZH = ${JSON.stringify(tabs, null, 2)} as const;\n\nfunction currentRoute(): string {\n  const pages = getCurrentPages();\n  const page = pages[pages.length - 1] as any;\n  return String(page?.route || page?.$page?.fullPath || page?.$page?.path || "").replace(/^\\//, "").split("?")[0];\n}\n\nexport function applyLocalizedNavigationTitle(route = currentRoute()): void {\n  const title = NAVIGATION_TITLE_ZH[route];\n  if (!title) return;\n  uni.setNavigationBarTitle({ title: String(translateSystemText(title)) });\n}\n\nexport function applyLocalizedTabBar(): void {\n  TAB_BAR_ZH.forEach((item) => {\n    uni.setTabBarItem({ index: item.index, text: String(translateSystemText(item.text)) });\n  });\n}\n\nexport function installLocalizedNavigation(app: App): void {\n  app.mixin({\n    onShow() {\n      applyLocalizedNavigationTitle();\n      applyLocalizedTabBar();\n    }\n  });\n}\n`

fs.writeFileSync(path.join(mobileRoot, 'locale/navigation.ts'), output, 'utf8')
console.log(`Generated ${Object.keys(navigation).length} navigation titles and ${tabs.length} tab labels.`)
