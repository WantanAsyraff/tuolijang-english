const fs = require('fs')
const path = require('path')
const root = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src')
const confirmations = [
  '您确定要领取此线索吗',
  '你确定要撤销申请吗',
  '您确定要将此客户取消流失吗',
  '您确定要将此客户标为流失吗',
  '您确定要领取此客户吗'
]

function walk(directory) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    const file = path.join(directory, entry.name)
    if (entry.isDirectory()) walk(file)
    else if (/\.(vue|js)$/.test(entry.name)) {
      const source = fs.readFileSync(file, 'utf8')
      let next = source
      for (const text of confirmations) next = next.replaceAll(`$t('${text}')`, `$ts('${text}')`)
      if (file.endsWith(path.join('user', 'calendar', 'composables', 'useContract.js'))) {
        next = next.replace("proxy.$t('contract.editcustomer')", "proxy.$t('customer.editcustomer')")
      }
      if (next !== source) fs.writeFileSync(file, next)
    }
  }
}

walk(root)
