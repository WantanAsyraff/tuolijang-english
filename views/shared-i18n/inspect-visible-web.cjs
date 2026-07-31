const fs = require('fs')
const path = require('path')
const root = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src')
const output = []
function walk(directory) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    const file = path.join(directory, entry.name)
    if (entry.isDirectory()) walk(file)
    else if (entry.name.endsWith('.vue')) {
      const match = fs.readFileSync(file, 'utf8').match(/<template(?:\s[^>]*)?>([\s\S]*?)<\/template>/i)
      if (!match) continue
      const visible = match[1].replace(/<!--[\s\S]*?-->/g, '').replace(/\{\{[\s\S]*?\}\}/g, '').replace(/<[^>]*>/g, '')
      for (const line of visible.split(/\r?\n/).map((item) => item.trim()).filter((item) => /[\u3400-\u9fff]/.test(item))) {
        output.push(`${path.relative(root, file)}: ${line}`)
      }
    }
  }
}
walk(root)
console.log(output.slice(0, 120).join('\n'))
