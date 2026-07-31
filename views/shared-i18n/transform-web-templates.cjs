const fs = require('fs')
const path = require('path')
const { parse } = require('../view-uni-src/node_modules/@vue/compiler-dom')
const NodeTypes = { ELEMENT: 1, TEXT: 2, ATTRIBUTE: 6 }

const root = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src')
const HAS_HAN = /[\u3400-\u9fff]/
const SAFE_ATTRIBUTES = new Set([
  'alt', 'title', 'placeholder', 'content', 'text', 'empty-text', 'loading-text',
  'confirm-button-text', 'cancel-button-text', 'confirm-text', 'cancel-text',
  'description', 'tip', 'tips'
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

function translatedText(source) {
  const leading = source.match(/^\s*/)?.[0] || ''
  const trailing = source.match(/\s*$/)?.[0] || ''
  const text = source.slice(leading.length, source.length - trailing.length)
  if (!text || !HAS_HAN.test(text)) return source
  return `${leading}{{ $ts(${JSON.stringify(text)}) }}${trailing}`
}

function collect(node, replacements) {
  if (node.type === NodeTypes.TEXT && HAS_HAN.test(node.content)) {
    replacements.push({ start: node.loc.start.offset, end: node.loc.end.offset, value: translatedText(node.loc.source) })
  }

  if (node.type === NodeTypes.ELEMENT) {
    node.props.forEach((property) => {
      if (property.type !== NodeTypes.ATTRIBUTE || !property.value || !HAS_HAN.test(property.value.content)) return
      const isSafeLabel = property.name === 'label' && SAFE_LABEL_TAGS.has(node.tag)
      if (!SAFE_ATTRIBUTES.has(property.name) && !isSafeLabel) return
      replacements.push({
        start: property.loc.start.offset,
        end: property.loc.end.offset,
        value: `:${property.name}='$ts(${JSON.stringify(property.value.content)})'`
      })
    })
  }

  if (Array.isArray(node.children)) node.children.forEach((child) => collect(child, replacements))
}

let changedFiles = 0
let changedStrings = 0
for (const file of vueFiles(root)) {
  const source = fs.readFileSync(file, 'utf8')
  const match = source.match(/<template(?:\s[^>]*)?>([\s\S]*?)<\/template>/i)
  if (!match || match.index === undefined) continue

  let ast
  try {
    ast = parse(match[1], { comments: true })
  } catch (error) {
    console.warn(`Skipped ${path.relative(root, file)}: ${error.message}`)
    continue
  }

  const replacements = []
  collect(ast, replacements)
  if (!replacements.length) continue

  const translated = replacements.sort((a, b) => b.start - a.start).reduce((result, item) => {
    return result.slice(0, item.start) + item.value + result.slice(item.end)
  }, match[1])
  const start = match.index + match[0].indexOf(match[1])
  const next = source.slice(0, start) + translated + source.slice(start + match[1].length)
  fs.writeFileSync(file, next)
  changedFiles += 1
  changedStrings += replacements.length
}

console.log(`Localized ${changedStrings} web template strings across ${changedFiles} files.`)
