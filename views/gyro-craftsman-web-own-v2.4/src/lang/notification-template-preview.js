function humanizePlaceholder(token) {
  return String(token)
    .replace(/([a-z0-9])([A-Z])/g, '$1 $2')
    .replace(/([A-Za-z])(\d+)/g, '$1 $2')
    .replace(/[_-]+/g, ' ')
    .trim()
    .toLowerCase()
}

function formatNotificationTemplatePreview(value, locale) {
  const text = value === undefined || value === null ? '' : String(value)
  if (locale !== 'en') return text

  return text.replace(/\{#([^{}]+)\}/g, (match, token) => humanizePlaceholder(token) || match)
}

module.exports = {
  formatNotificationTemplatePreview,
  humanizePlaceholder
}