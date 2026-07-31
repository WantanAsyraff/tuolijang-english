const fs = require('fs')
const path = require('path')

const webRoot = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4')

function patch(relativePath, replacements) {
  const file = path.join(webRoot, relativePath)
  let source = fs.readFileSync(file, 'utf8')
  replacements.forEach(([from, to]) => {
    if (!source.includes(from)) throw new Error(`${relativePath}: expected anchor not found: ${from}`)
    source = source.replace(from, to)
  })
  fs.writeFileSync(file, source, 'utf8')
  console.log(`Updated ${relativePath}`)
}

patch('src/utils/i18ns.js', [
  ["import enMessages from '@/lang/en'", "import enMessages from '@/lang/en'\nimport { translateSystemTextValue } from '../../../shared-i18n/index.js'"],
  ["  const locale = getLocale(ctx)\n  const withoutColon = trimmed.replace(/[:：]$/, '')", "  const locale = getLocale(ctx)\n  const sharedTranslation = translateSystemTextValue(rawText, { locale })\n  if (sharedTranslation !== rawText) return sharedTranslation\n  const withoutColon = trimmed.replace(/[:：]$/, '')"]
])

patch('src/store/modules/app.js', [
  ["import { getStorageJson } from '@/utils/storage'", "import { getStorageJson } from '@/utils/storage'\nimport moment from 'moment'"],
  ["    Cookies.set('language', language)\n  },", "    Cookies.set('language', language)\n    localStorage.setItem('language', language)\n    localStorage.setItem('form_cache', language === 'en' ? 'en-US' : 'zh-CN')\n    moment.locale(language === 'en' ? 'en' : 'zh-cn')\n  },"]
])

patch('src/bootstrap/prototypes.js', [
  ['import { processResourceUrl } from "@/utils/resourceUtil";', 'import { processResourceUrl } from "@/utils/resourceUtil";\nimport { getLanguage } from "@/lang";'],
  ['moment.locale("zh-cn");', 'moment.locale(getLanguage() === "en" ? "en" : "zh-cn");']
])

patch('src/utils/i18n.js', [
  ['import zhLocale_extension from "@/lang/zh-CN_extension";', 'import zhLocale_extension from "@/lang/zh-CN_extension";\nimport { getLanguage } from "@/lang";'],
  ["  lang: localStorage.getItem('form_cache') || 'zh-CN',", "  lang: localStorage.getItem('form_cache') || (getLanguage() === 'en' ? 'en-US' : 'zh-CN'),"],
  ["  localStorage.setItem('form_cache', langName)\n  localStorage.setItem('form_cache', langName)", "  localStorage.setItem('form_cache', langName)"]
])

patch('src/libs/ai/utils.js', [
  ["import { getAiBaseUrl } from './plugin-loader'", "import { getAiBaseUrl } from './plugin-loader'\nimport { getLanguage } from '@/lang'"],
  ["  return new URL(appId !== null ? `/chat/app/${appId}` : '/chat', getAiBaseUrl())", "  const url = new URL(appId !== null ? `/chat/app/${appId}` : '/chat', getAiBaseUrl())\n  url.searchParams.set('language', getLanguage())\n  return url"]
])
