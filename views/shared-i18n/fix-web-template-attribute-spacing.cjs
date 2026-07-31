const fs = require('fs')
const path = require('path')
const root = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src')

function walk(directory) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    const file = path.join(directory, entry.name)
    if (entry.isDirectory()) walk(file)
    else if (entry.name.endsWith('.vue')) {
      const source = fs.readFileSync(file, 'utf8')
      const next = source
        .replace(/(:[\w-]+='\$ts\("[^"]*"\)')"(?=\s|\/?>)/g, '$1')
        .replace(/([^\s<])(:[\w-]+='\$ts\()/g, '$1 $2')
      if (next !== source) fs.writeFileSync(file, next)
    }
  }
}

walk(root)
