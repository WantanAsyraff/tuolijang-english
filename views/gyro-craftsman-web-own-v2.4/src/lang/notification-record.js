function rawNotificationText(record, key) {
  if (!record || typeof record !== 'object') return ''
  const value = record[key]
  return value === undefined || value === null ? value : String(value)
}

// Older notification-list responses do not always include message_id,
// template_type, or is_system_owned. Only recognise the installed system
// templates here: arbitrary authored notification titles and bodies must stay
// exactly as stored.
function hasKnownSystemNotificationTemplate(record) {
  const title = String(rawNotificationText(record, 'title') ?? '').trim()
  const message = String(rawNotificationText(record, 'message') ?? '').trim()
  const knownTitles = new Set([
    '待办任务提醒',
    '合同待办提醒',
    '日报查看提醒',
    'To-do task reminder',
    'Contract task reminder',
    'Daily report reminder',
  ])
  const knownMessages = [
    /^.+的.+已(提交|更新)，请及时查看！$/,
    /^您有一条个人待办任务，请记得处理哦！待办内容【.*】$/,
    /^您有一条客户跟进任务，请记得处理哦！提醒内容【.*】$/,
    /^您有一条回款任务，请记得处理哦！提醒内容【.*】$/,
    /^您有一条.+任务，请记得处理哦！提醒内容【.*】$/,
  ]

  return knownTitles.has(title) && knownMessages.some((pattern) => pattern.test(message))
}

function isSystemOwnedNotificationRecord(record) {
  if (!record || typeof record !== 'object') return false
  const hasKnownTemplate = hasKnownSystemNotificationTemplate(record)
  if (Object.prototype.hasOwnProperty.call(record, 'is_system_owned')) {
    // A complete reserved title/message pair identifies an installed template,
    // even when a legacy API serializes its ownership flag incorrectly.
    return Number(record.is_system_owned) === 1 || hasKnownTemplate
  }
  return Number(record.message_id) > 0 || Boolean(String(record.template_type || '').trim()) || hasKnownTemplate
}

function notificationRecordText(record, translate, key) {
  const raw = rawNotificationText(record, key)
  if (!isSystemOwnedNotificationRecord(record) || typeof translate !== 'function') return raw
  return translate(raw)
}

module.exports = {
  isSystemOwnedNotificationRecord,
  hasKnownSystemNotificationTemplate,
  notificationRecordText,
  rawNotificationText
}