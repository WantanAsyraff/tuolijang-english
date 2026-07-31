const fs = require('fs')
const path = require('path')
const compiler = require('../gyro-craftsman-web-own-v2.4/node_modules/vue-template-compiler')

const root = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src')
const HAS_HAN = /[\u3400-\u9fff]/
const SAFE_ATTRIBUTES = new Set([
  'alt', 'title', 'placeholder', 'content', 'text', 'empty-text', 'loading-text',
  'confirm-button-text', 'cancel-button-text', 'confirm-text', 'cancel-text',
  'description', 'tip', 'tips', 'btntext', 'defaulttitle', 'poptitle', 'buttontitle'
])
const SAFE_LABEL_TAGS = new Set([
  'el-table-column', 'el-form-item', 'el-tab-pane', 'el-descriptions-item',
  'el-divider', 'el-page-header'
])

function vueFiles(directory) {
  return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const full = path.join(directory, entry.name)
    return entry.isDirectory() ? vueFiles(full) : entry.name.endsWith('.vue') ? [full] : []
  })
}

function wrapText(source) {
  const leading = source.match(/^\s*/)?.[0] || ''
  const trailing = source.match(/\s*$/)?.[0] || ''
  const text = source.slice(leading.length, source.length - trailing.length)
  return text && HAS_HAN.test(text) ? `${leading}{{ $ts(${JSON.stringify(text)}) }}${trailing}` : source
}

function staticExpression(value) {
  const match = String(value).trim().match(/^(['"`])([\s\S]*)\1$/)
  return match && HAS_HAN.test(match[2]) && !match[2].includes('${') ? match[2] : ''
}

function collect(node, replacements, visited) {
  if (!node || visited.has(node)) return
  visited.add(node)

  if (node.type === 3 && !node.isComment && HAS_HAN.test(node.text || '')) {
    replacements.push({ start: node.start, end: node.end, value: wrapText(node.text) })
  }

  if (node.type === 1) {
    for (const raw of Object.values(node.rawAttrsMap || {})) {
      const rawName = raw.name || ''
      const bound = rawName.startsWith(':') || rawName.startsWith('v-bind:')
      const name = rawName.replace(/^:|^v-bind:/, '')
      const lowerName = name.toLowerCase()
      const safe = SAFE_ATTRIBUTES.has(lowerName) || (lowerName === 'label' && SAFE_LABEL_TAGS.has(node.tag))
      if (!safe) continue

      const text = bound ? staticExpression(raw.value) : raw.value
      if (!text || !HAS_HAN.test(text)) continue
      replacements.push({
        start: raw.start,
        end: raw.end,
        value: `:${name}='$ts(${JSON.stringify(text)})'`
      })
    }
    for (const child of node.children || []) collect(child, replacements, visited)
    for (const condition of node.ifConditions || []) collect(condition.block, replacements, visited)
  }
}

let changedFiles = 0
let changedStrings = 0
for (const file of vueFiles(root)) {
  const source = fs.readFileSync(file, 'utf8')
  const descriptor = compiler.parseComponent(source)
  if (!descriptor.template) continue
  const template = descriptor.template.content
  const compiled = compiler.compile(template, { outputSourceRange: true, comments: true })
  if (!compiled.ast) continue

  const replacements = []
  collect(compiled.ast, replacements, new Set())
  if (!replacements.length) continue

  const translated = replacements.sort((a, b) => b.start - a.start).reduce((result, item) => {
    return result.slice(0, item.start) + item.value + result.slice(item.end)
  }, template)
  const open = source.slice(0, descriptor.template.start).lastIndexOf('<template')
  const openEnd = source.indexOf('>', open) + 1
  const next = source.slice(0, openEnd) + translated + source.slice(descriptor.template.end)
  fs.writeFileSync(file, next)
  changedFiles += 1
  changedStrings += replacements.length
}

console.log(`Localized ${changedStrings} residual Vue 2 template strings across ${changedFiles} files.`)
