import Vue from 'vue'
import Cookies from 'js-cookie'
import elementLocale from 'element-ui/lib/locale'
import elementEnLocale from 'element-ui/lib/locale/lang/en'
import elementZhLocale from 'element-ui/lib/locale/lang/zh-CN'
import { messages, translateSystemTextValue } from './generated-locale'

const supportedLocales = ['en', 'zh-cn']
const localeState = Vue.observable({ locale: 'zh-cn' })

function getNested(source, dottedKey) {
  return String(dottedKey || '').split('.').reduce((value, key) => {
    return value && Object.prototype.hasOwnProperty.call(value, key) ? value[key] : undefined
  }, source)
}

function interpolate(value, params) {
  if (!params || Object.prototype.toString.call(params) !== '[object Object]') return value
  return String(value).replace(/\{\{?\s*([\w.]+)\s*\}?\}/g, (match, key) => {
    const replacement = getNested(params, key)
    return replacement === undefined || replacement === null ? match : String(replacement)
  })
}

export function normalizeLanguage(language) {
  const lang = String(language || '').toLowerCase()
  if (['zh', 'zh_cn', 'zh-cn', 'zh-hans'].includes(lang)) return 'zh-cn'
  if (['en', 'en_us', 'en-us', 'en-gb'].includes(lang)) return 'en'
  return ''
}

export function getLanguage() {
  const cookieLanguage = normalizeLanguage(Cookies.get('language'))
  if (supportedLocales.includes(cookieLanguage)) return cookieLanguage

  const storedLanguage = normalizeLanguage(localStorage.getItem('language'))
  if (supportedLocales.includes(storedLanguage)) return storedLanguage

  return 'zh-cn'
}

function syncElementLocale(language) {
  elementLocale.use(language === 'en' ? elementEnLocale : elementZhLocale)
}

export function setLanguage(language) {
  const normalized = normalizeLanguage(language) || 'zh-cn'
  Cookies.set('language', normalized)
  localStorage.setItem('language', normalized)
  localeState.locale = normalized
  syncElementLocale(normalized)
  return normalized
}

export function getLocaleState() {
  return localeState
}

export function $(input, paramsOrEnglishValue) {
  if (input === undefined || input === null) return input
  const value = String(input)
  const keyed = getNested(messages[localeState.locale], value)
  if (typeof keyed === 'string') {
    return interpolate(keyed, paramsOrEnglishValue)
  }

  return translateSystemTextValue(value, {
    locale: localeState.locale,
    englishValue: typeof paramsOrEnglishValue === 'string' ? paramsOrEnglishValue : undefined,
  })
}

localeState.locale = getLanguage()
syncElementLocale(localeState.locale)
Vue.prototype.$ = $
Object.defineProperty(Vue.prototype, '$language', {
  configurable: true,
  get: () => localeState.locale,
})

export default Object.freeze({ $, getLanguage, setLanguage, normalizeLanguage, localeState })
