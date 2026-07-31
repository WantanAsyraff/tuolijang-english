const fs = require('fs')
const path = require('path')

const apps = [
  ['gyro-craftsman-web-own-v2.4', 'web'],
  ['gyro-craftsman-chat-v1.0', 'chat'],
  ['view-uni-src', 'mobile']
]

for (const [directory, name] of apps) {
  const file = path.resolve(__dirname, '..', directory, 'package.json')
  const data = JSON.parse(fs.readFileSync(file, 'utf8'))
  data.scripts = data.scripts || {}
  data.scripts['i18n:audit'] = `node ../shared-i18n/localization-audit.cjs ${name}`
  fs.writeFileSync(file, `${JSON.stringify(data, null, 2)}\n`)
}
