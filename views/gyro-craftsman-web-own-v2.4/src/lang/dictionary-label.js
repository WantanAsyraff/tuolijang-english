function rawDictionaryLabel(entry, key = 'name') {
  if (entry === undefined || entry === null) return entry
  if (typeof entry !== 'object') return String(entry)

  const value = entry[key] ?? entry.label ?? entry.name ?? ''
  return value === undefined || value === null ? value : String(value)
}

function hasDictionaryOwnership(entry) {
  return Boolean(
    entry &&
      typeof entry === 'object' &&
      (Object.prototype.hasOwnProperty.call(entry, 'is_system_owned') ||
        Object.prototype.hasOwnProperty.call(entry, 'is_default') ||
        Object.prototype.hasOwnProperty.call(entry, 'system_field'))
  )
}

function isSystemOwnedDictionaryEntry(entry) {
  if (!hasDictionaryOwnership(entry)) return false
  return Number(entry.is_system_owned ?? entry.is_default ?? entry.system_field) === 1
}

function dictionaryDisplayLabel(entry, translate, key = 'name') {
  const raw = rawDictionaryLabel(entry, key)
  if (!isSystemOwnedDictionaryEntry(entry) || typeof translate !== 'function') return raw
  return translate(raw)
}

module.exports = {
  dictionaryDisplayLabel,
  hasDictionaryOwnership,
  isSystemOwnedDictionaryEntry,
  rawDictionaryLabel
}
