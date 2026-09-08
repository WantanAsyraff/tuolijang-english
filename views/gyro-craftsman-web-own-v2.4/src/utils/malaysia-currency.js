/**
 * Malaysia-facing number and currency display helpers. They never mutate form
 * state, API payloads, or stored decimal values.
 */
export const MALAYSIA_CURRENCY_CODE = 'MYR'
export const MALAYSIA_CURRENCY_SYMBOL = 'RM'

function toFiniteNumber(value) {
  if (value === null || value === undefined || value === '') return null
  const number = typeof value === 'number' ? value : Number(String(value).trim())
  return Number.isFinite(number) ? number : null
}

export function formatMalaysiaNumber(value, options = {}) {
  const number = toFiniteNumber(value)
  if (number === null) return ''
  const {
    minimumFractionDigits = 0,
    maximumFractionDigits = 20,
  } = options
  return new Intl.NumberFormat('en-MY', { minimumFractionDigits, maximumFractionDigits }).format(number)
}

/**
 * Format a monetary value for display only. MYR is the Malaysia default; an
 * explicitly supplied non-MYR currency remains visible as that currency code.
 */
export function formatMalaysiaCurrency(value, options = {}) {
  const number = toFiniteNumber(value)
  if (number === null) return ''
  const currency = typeof options === 'string' ? options : options.currency || MALAYSIA_CURRENCY_CODE
  const formattedNumber = formatMalaysiaNumber(Math.abs(number), {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })

  if (currency === MALAYSIA_CURRENCY_CODE) {
    return `${number < 0 ? '-' : ''}${MALAYSIA_CURRENCY_SYMBOL} ${formattedNumber}`
  }

  return new Intl.NumberFormat('en-MY', {
    style: 'currency',
    currency,
    currencyDisplay: 'code',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(number)
}