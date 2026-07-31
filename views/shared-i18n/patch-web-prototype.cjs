const fs = require('fs')
const path = require('path')

const file = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src/bootstrap/prototypes.js')
let source = fs.readFileSync(file, 'utf8')
if (!source.includes('translateRuntimeText')) {
  source = source.replace(
    'import { getLanguage } from "@/lang";',
    'import { getLanguage } from "@/lang";\nimport { translateRuntimeText } from "@/utils/i18ns";'
  )
  source = source.replace(
    '  Vue.prototype.$modalForm = modalForm;',
    '  Vue.prototype.$ts = function translateSourceText(text) {\n    return translateRuntimeText(text, this);\n  };\n  Vue.prototype.$modalForm = modalForm;'
  )
  fs.writeFileSync(file, source)
}
