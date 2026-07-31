const fs = require('fs')
const path = require('path')

const transformFile = path.join(__dirname, 'transform-web-templates.cjs')
let transformSource = fs.readFileSync(transformFile, 'utf8')
transformSource = transformSource.replace(
  "const { NodeTypes, parse } = require('../view-uni-src/node_modules/@vue/compiler-dom')",
  "const { parse } = require('../view-uni-src/node_modules/@vue/compiler-dom')\nconst NodeTypes = { ELEMENT: 1, TEXT: 2, ATTRIBUTE: 6 }"
)
fs.writeFileSync(transformFile, transformSource)

const pluginFile = path.resolve(__dirname, '../view-uni-src/config/i18nTemplatePlugin.ts')
let pluginSource = fs.readFileSync(pluginFile, 'utf8')
pluginSource = pluginSource.replace(
  'import { NodeTypes, parse, type RootNode, type TemplateChildNode } from "@vue/compiler-dom";',
  'import { parse, type RootNode, type TemplateChildNode } from "@vue/compiler-dom";\n\nconst NodeTypes = { ELEMENT: 1, TEXT: 2, ATTRIBUTE: 6 } as const;'
)
fs.writeFileSync(pluginFile, pluginSource)
