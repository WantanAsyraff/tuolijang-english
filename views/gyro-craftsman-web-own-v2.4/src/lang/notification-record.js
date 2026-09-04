function rawNotificationText(record, key) {
  if (!record || typeof record !== 'object') return ''
  const value = record[key]
  return value === undefined || value === null ? value : String(value)
}

function isSystemOwnedNotificationRecord(record) {
  if (!record || typeof record !== 'object') return false
  if (Object.prototype.hasOwnProperty.call(record, 'is_system_owned')) {
    return Number(record.is_system_owned) === 1
  }
  return Number(record.message_id) > 0 || Boolean(String(record.template_type || '').trim())
}

function notificationRecordText(record, translate, key) {
  const raw = rawNotificationText(record, key)
  if (!isSystemOwnedNotificationRecord(record) || typeof translate !== 'function') return raw
  return translate(raw)
}

module.exports = {
  isSystemOwnedNotificationRecord,
  notificationRecordText,
  rawNotificationText
}