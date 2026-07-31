const fs = require('fs')
const path = require('path')
const compiler = require('../gyro-craftsman-web-own-v2.4/node_modules/vue-template-compiler')

const currentRoot = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src')
const originalRoot = path.resolve(__dirname, '../.restore-web-original/gyro-craftsman-web-own-v2.4/src')
const manifest = JSON.parse(fs.readFileSync(path.join(__dirname, 'restored-web-template-files.json'), 'utf8'))

for (const relative of manifest) {
  const currentFile = path.join(currentRoot, relative)
  const originalFile = path.join(originalRoot, relative)
  const currentSource = fs.readFileSync(currentFile, 'utf8')
  const originalSource = fs.readFileSync(originalFile, 'utf8')
  const originalBlock = compiler.parseComponent(originalSource).template
  if (!originalBlock) throw new Error(`Archive template missing: ${relative}`)

  const closeStart = originalSource.indexOf('</template>', originalBlock.end)
  if (closeStart < 0) throw new Error(`Archive template close tag missing: ${relative}`)
  const templateEnd = closeStart + '</template>'.length

  const scriptMatch = currentSource.match(/^<script(?:\s[^>]*)?>/m)
  const styleMatch = currentSource.match(/^<style(?:\s[^>]*)?>/m)
  const suffixStart = scriptMatch?.index ?? styleMatch?.index
  if (suffixStart === undefined) throw new Error(`Current script/style suffix missing: ${relative}`)

  const next = `${originalSource.slice(0, templateEnd)}\n${currentSource.slice(suffixStart)}`
  fs.writeFileSync(currentFile, next)
}

console.log(`Robustly restored ${manifest.length} complete template sections while preserving current scripts and styles.`)
