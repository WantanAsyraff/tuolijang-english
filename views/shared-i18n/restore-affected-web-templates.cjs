const fs = require('fs')
const path = require('path')
const compiler = require('../gyro-craftsman-web-own-v2.4/node_modules/vue-template-compiler')

const currentRoot = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src')
const originalRoot = path.resolve(__dirname, '../.restore-web-original/gyro-craftsman-web-own-v2.4/src')
const cutoff = new Date('2026-07-21T16:56:00').getTime()
const restored = []

function walk(directory) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    const file = path.join(directory, entry.name)
    if (entry.isDirectory()) {
      walk(file)
      continue
    }
    if (!entry.name.endsWith('.vue') || fs.statSync(file).mtimeMs < cutoff) continue

    const relative = path.relative(currentRoot, file)
    const originalFile = path.join(originalRoot, relative)
    if (!fs.existsSync(originalFile)) throw new Error(`Archive file missing: ${relative}`)

    const currentSource = fs.readFileSync(file, 'utf8')
    const originalSource = fs.readFileSync(originalFile, 'utf8')
    const currentBlock = compiler.parseComponent(currentSource).template
    const originalBlock = compiler.parseComponent(originalSource).template
    if (!currentBlock || !originalBlock) throw new Error(`Template block missing: ${relative}`)

    const next = currentSource.slice(0, currentBlock.start) + originalBlock.content + currentSource.slice(currentBlock.end)
    fs.writeFileSync(file, next)
    restored.push(relative.replace(/\\/g, '/'))
  }
}

walk(currentRoot)
fs.writeFileSync(path.join(__dirname, 'restored-web-template-files.json'), `${JSON.stringify(restored, null, 2)}\n`)
console.log(`Restored ${restored.length} affected web template blocks from views/view-pc.zip.`)
