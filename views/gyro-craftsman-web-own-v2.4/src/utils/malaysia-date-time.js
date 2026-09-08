/**
 * Display dates in the application's operating timezone without changing the
 * storage format sent to Laravel. Database datetimes are intentionally treated
 * as Malaysia wall-clock values when they do not carry an explicit offset.
 */
export const MALAYSIA_TIME_ZONE = 'Asia/Kuala_Lumpur'

const MALAYSIA_OFFSET_MINUTES = 8 * 60
const ENGLISH_SHORT_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

function pad(value) {
  return String(value).padStart(2, '0')
}

function localeFor(language) {
  return language === 'en' ? 'en-MY' : 'zh-CN'
}

function hasExplicitTimeZone(value) {
  return /(?:Z|[+-]\d{2}:?\d{2})$/i.test(value)
}

/**
 * Convert a Laravel-style datetime to an instant for display. A plain
 * `YYYY-MM-DD HH:mm:ss` value is already in application wall-clock time, so
 * parsing it in the browser's local timezone would be incorrect for users
 * outside Malaysia.
 */
export function toMalaysiaDate(value) {
  if (value === null || value === undefined || value === '') return null
  if (value instanceof Date) return Number.isNaN(value.getTime()) ? null : value
  if (typeof value === 'number') {
    const milliseconds = String(Math.abs(value)).length === 10 ? value * 1000 : value
    const date = new Date(milliseconds)
    return Number.isNaN(date.getTime()) ? null : date
  }

  const text = String(value).trim()
  const match = text.match(/^(\d{4})[-/](\d{1,2})[-/](\d{1,2})(?:[ T](\d{1,2}):(\d{2})(?::(\d{2}))?)?$/)
  if (match && !hasExplicitTimeZone(text)) {
    const [, year, month, day, hour = '12', minute = '00', second = '00'] = match
    const utcMilliseconds = Date.UTC(
      Number(year),
      Number(month) - 1,
      Number(day),
      Number(hour) - Math.floor(MALAYSIA_OFFSET_MINUTES / 60),
      Number(minute),
      Number(second),
    )
    const date = new Date(utcMilliseconds)
    return Number.isNaN(date.getTime()) ? null : date
  }

  const date = new Date(text)
  return Number.isNaN(date.getTime()) ? null : date
}

function getParts(value, language, options) {
  const date = toMalaysiaDate(value)
  if (!date) return null
  const formatter = new Intl.DateTimeFormat(localeFor(language), {
    timeZone: MALAYSIA_TIME_ZONE,
    ...options,
  })
  return formatter.formatToParts(date).reduce((parts, part) => {
    if (part.type !== 'literal') parts[part.type] = part.value
    return parts
  }, {})
}

function numericParts(value) {
  return getParts(value, 'en', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    hourCycle: 'h23',
    minute: '2-digit',
    second: '2-digit',
  })
}

/** Canonical display format: 8 Sep 2026. */
export function formatMalaysiaDate(value, language = 'en') {
  const parts = getParts(value, language, { year: 'numeric', month: 'short', day: 'numeric' })
  if (!parts) return ''
  if (language === 'en') {
    const numeric = numericParts(value)
    return `${Number(numeric.day)} ${ENGLISH_SHORT_MONTHS[Number(numeric.month) - 1]} ${numeric.year}`
  }
  return `${parts.year}年${parts.month}月${parts.day}日`
}

/** Canonical display format: 8 Sep 2026, 3:30 PM. */
export function formatMalaysiaDateTime(value, language = 'en') {
  const date = formatMalaysiaDate(value, language)
  const parts = getParts(value, language, {
    hour: 'numeric',
    minute: '2-digit',
    hour12: language === 'en',
  })
  if (!date || !parts) return ''
  const time = language === 'en' ? `${parts.hour}:${parts.minute} ${parts.dayPeriod.toUpperCase()}` : `${parts.hour}:${parts.minute}`
  return `${date}, ${time}`
}

export function formatMalaysiaTime(value, language = 'en', includeSeconds = false) {
  const parts = getParts(value, language, {
    hour: 'numeric',
    minute: '2-digit',
    ...(includeSeconds ? { second: '2-digit' } : {}),
    hour12: language === 'en',
  })
  if (!parts) return ''
  const clock = `${parts.hour}:${parts.minute}${includeSeconds ? `:${parts.second}` : ''}`
  return language === 'en' ? `${clock} ${parts.dayPeriod.toUpperCase()}` : clock
}

/**
 * Format an existing value into the legacy storage-compatible token formats.
 * This is for display/filter compatibility only; it never writes to storage.
 */
export function formatMalaysiaPattern(value, pattern = 'YYYY-MM-DD') {
  const parts = numericParts(value)
  if (!parts) return ''
  const hour12 = Number(parts.hour) % 12 || 12
  const replacements = {
    YYYY: parts.year,
    YY: parts.year.slice(-2),
    MM: parts.month,
    M: String(Number(parts.month)),
    DD: parts.day,
    D: String(Number(parts.day)),
    HH: parts.hour,
    H: String(Number(parts.hour)),
    hh: pad(hour12),
    h: String(hour12),
    mm: parts.minute,
    m: String(Number(parts.minute)),
    ss: parts.second,
    s: String(Number(parts.second)),
    A: Number(parts.hour) < 12 ? 'AM' : 'PM',
  }
  return String(pattern).replace(/YYYY|YY|MM|DD|HH|hh|mm|ss|M|D|H|h|m|s|A/g, (token) => replacements[token])
}

/** Preserve the API's existing YYYY-MM-DD[ HH:mm:ss] value contract. */
export function formatMalaysiaStorageDateTime(value, includeTime = true) {
  return formatMalaysiaPattern(value, includeTime ? 'YYYY-MM-DD HH:mm:ss' : 'YYYY-MM-DD')
}