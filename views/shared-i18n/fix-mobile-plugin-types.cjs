const fs = require('fs')
const path = require('path')

const file = path.resolve(__dirname, '../view-uni-src/config/i18nTemplatePlugin.ts')
let source = fs.readFileSync(file, 'utf8')
source = source.replace(
  '    node.children.forEach((child) => collectReplacements(child, replacements));',
  '    node.children.forEach((child) => {\n      if (typeof child === "object" && child && "type" in child) {\n        collectReplacements(child as TemplateChildNode, replacements);\n      }\n    });'
)
fs.writeFileSync(file, source)
