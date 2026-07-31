const fs = require('fs')
const path = require('path')

const file = path.join(__dirname, 'index.js')
let source = fs.readFileSync(file, 'utf8')

if (!source.includes("./mobile-en-system-text.js")) {
  source = source.replace(
    "import { MANUAL_SYSTEM_TEXT_EN } from './manual-en-system-text.js'",
    "import { MANUAL_SYSTEM_TEXT_EN } from './manual-en-system-text.js'\nimport { MOBILE_SYSTEM_TEXT_EN } from './mobile-en-system-text.js'"
  )
  source = source.replace(
    '  ...MANUAL_SYSTEM_TEXT_EN\n})',
    '  ...MANUAL_SYSTEM_TEXT_EN,\n  ...MOBILE_SYSTEM_TEXT_EN\n})'
  fs.writeFileSync(file, source)
}
