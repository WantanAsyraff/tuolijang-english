import { translateRuntimeText } from '@/utils/i18ns'

const TRANSLATABLE_ATTRS = ['placeholder', 'title', 'aria-label']
const SKIP_TAGS = new Set(['SCRIPT', 'STYLE', 'CODE', 'PRE'])

function isChinese(text) {
  return /[\u4e00-\u9fff]/.test(text)
}

function shouldSkipElement(el) {
  if (!el || !el.tagName) return true
  if (SKIP_TAGS.has(el.tagName)) return true
  if (el.closest && el.closest('[data-skip-dom-i18n], .w-e-text-container, .tox, .CodeMirror')) return true
  return false
}

function translateTextNode(node, i18n) {
  const current = node.nodeValue
  if (!current || !current.trim()) return

  if (!node.__domI18nOriginal || isChinese(current)) {
    node.__domI18nOriginal = current
  }

  const original = node.__domI18nOriginal
  const next = i18n.locale === 'en' ? translateRuntimeText(original, i18n) : original
  if (next !== current) node.nodeValue = next
}

function translateAttributes(el, i18n) {
  if (!el || !el.getAttribute) return
  TRANSLATABLE_ATTRS.forEach((attr) => {
    const current = el.getAttribute(attr)
    if (!current || !current.trim()) return
    const key = `__domI18nOriginal_${attr}`
    if (!el[key] || isChinese(current)) el[key] = current
    const original = el[key]
    const next = i18n.locale === 'en' ? translateRuntimeText(original, i18n) : original
    if (next !== current) el.setAttribute(attr, next)
  })
}

function translateControlValue(el, i18n) {
  if (!el || el.tagName !== 'INPUT') return
  const displayOnly = el.readOnly || el.disabled || Boolean(el.closest && el.closest('.el-pagination__sizes'))
  if (!displayOnly) return

  const current = el.value
  if (!current || !current.trim()) return
  if (!el.__domI18nOriginalValue || isChinese(current)) el.__domI18nOriginalValue = current

  const original = el.__domI18nOriginalValue
  let next = i18n.locale === 'en' ? translateRuntimeText(original, i18n) : original
  if (i18n.locale === 'en' && next === original && /^[a-z]+(?:[A-Z][A-Za-z0-9]*)+$/.test(original)) {
    next = original.replace(/([a-z0-9])([A-Z])/g, '$1 $2')
    next = next.charAt(0).toUpperCase() + next.slice(1).toLowerCase()
  }
  if (next !== current) el.value = next
}

function walk(root, i18n) {
  if (!root) return
  if (root.nodeType === Node.TEXT_NODE) {
    if (!shouldSkipElement(root.parentElement)) translateTextNode(root, i18n)
    return
  }
  if (root.nodeType !== Node.ELEMENT_NODE || shouldSkipElement(root)) return

  translateAttributes(root, i18n)
  const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
    acceptNode(node) {
      return shouldSkipElement(node.parentElement) ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT
    }
  })
  let node = walker.nextNode()
  while (node) {
    translateTextNode(node, i18n)
    node = walker.nextNode()
  }

  root.querySelectorAll &&
    root.querySelectorAll('input, textarea, [placeholder], [title], [aria-label]').forEach((el) => {
      if (shouldSkipElement(el)) return
      translateAttributes(el, i18n)
      translateControlValue(el, i18n)
    })
}

export function installDomI18nTranslator({ i18n, router, store }) {
  if (typeof window === 'undefined' || !window.MutationObserver) return

  let applying = false
  let scheduled = false
  const schedule = () => {
    if (scheduled) return
    scheduled = true
    window.setTimeout(() => {
      scheduled = false
      if (i18n.locale !== 'en') return
      applying = true
      walk(document.body, i18n)
      applying = false
    }, 80)
  }

  const observer = new MutationObserver(() => {
    if (!applying) schedule()
  })

  window.requestAnimationFrame(() => {
    if (!document.body) return
    observer.observe(document.body, {
      childList: true,
      subtree: true,
      characterData: true,
      attributes: true,
      attributeFilter: TRANSLATABLE_ATTRS
    })
    schedule()
  })

  if (router && router.afterEach) router.afterEach(() => schedule())
  if (store && store.watch) store.watch((state) => state.app.language, schedule)
}
