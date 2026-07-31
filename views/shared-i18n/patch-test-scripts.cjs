const fs = require('fs')
const path = require('path')

for (const directory of ['gyro-craftsman-web-own-v2.4', 'gyro-craftsman-chat-v1.0', 'view-uni-src']) {
  const file = path.resolve(__dirname, '..', directory, 'package.json')
  const data = JSON.parse(fs.readFileSync(file, 'utf8'))
  data.scripts['i18n:test'] = 'node --test ../shared-i18n/localization.test.mjs'
  fs.writeFileSync(file, `${JSON.stringify(data, null, 2)}\n`)
}
