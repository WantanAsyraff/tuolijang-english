function errorMessage(value) {
  if (!value || typeof value !== 'object') return ''
  if (value instanceof Error || Object.prototype.toString.call(value) === '[object Error]') {
    return value.message || ''
  }
  return ''
}

// Element accepts strings, errors, and several option shapes. This adapter only
// normalizes those shapes; every display string is resolved by the single translate().
export function normalizeNotificationInput(input, translate) {
  if (typeof translate !== 'function') return input
  if (typeof input === 'string') return translate(input)

  const directError = errorMessage(input)
  if (directError) return translate(directError)
  if (!input || typeof input !== 'object' || Array.isArray(input)) return input

  const normalized = { ...input }
  ;['message', 'title', 'description', 'content'].forEach((field) => {
    if (typeof normalized[field] === 'string') {
      normalized[field] = translate(normalized[field])
    } else {
      const nestedError = errorMessage(normalized[field])
      if (nestedError) normalized[field] = translate(nestedError)
    }
  })

  if (normalized.message === undefined) {
    const fallback = [normalized.msg, normalized.detail, normalized.error]
      .map((value) => (typeof value === 'string' ? value : errorMessage(value)))
      .find(Boolean)
    if (fallback) normalized.message = translate(fallback)
  }

  if (Array.isArray(normalized.buttons)) {
    normalized.buttons = normalized.buttons.map((button) => {
      if (typeof button === 'string') return translate(button)
      if (!button || typeof button !== 'object') return button
      const next = { ...button }
      ;['title', 'label', 'text'].forEach((field) => {
        if (typeof next[field] === 'string') next[field] = translate(next[field])
      })
      return next
    })
  }

  return normalized
}
