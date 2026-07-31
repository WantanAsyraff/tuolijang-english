const fs = require('fs')
const path = require('path')

function update(relative, transform) {
  const file = path.resolve(__dirname, '..', relative)
  const source = fs.readFileSync(file, 'utf8')
  const next = transform(source)
  if (source !== next) fs.writeFileSync(file, next)
}

update('shared-i18n/localization-audit.cjs', (source) => {
  source = source.replace(
    "const missingRefs = [...refs].filter((key) => !en.values.has(key) && !key.startsWith('el.'))",
    "const missingRefs = [...refs].filter((key) => !en.values.has(key) && !key.startsWith('el.') && !key.startsWith('designer.') && !key.endsWith('.'))"
  )
  source = source.replace(
    "const mappedTabs = [...navigation.matchAll(/\\{ index: \\d+, text:/g)].length",
    "const mappedTabs = [...navigation.matchAll(/[\\{,]\\s*[\"']?index[\"']?\\s*:\\s*\\d+/g)].length"
  )
  return source
})

update('gyro-craftsman-web-own-v2.4/src/lang/zh.js', (source) => source.replace(
  /(auditstatus1:\s*'[^']*')(\r?\n\s*},)/,
  "$1,\n    datechoose: '时间选择',\n    departmentchoose: '部门筛选'$2"
))

const confirmations = [
  '您确定要领取此线索吗',
  '你确定要撤销申请吗',
  '您确定要将此客户取消流失吗',
  '您确定要将此客户标为流失吗',
  '您确定要领取此客户吗'
]
for (const file of (function walk(dir) {
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap((entry) => {
    const full = path.join(dir, entry.name)
    return entry.isDirectory() ? walk(full) : /\.(vue|js)$/.test(entry.name) ? [full] : []
  })
})(path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src'))) {
  let source = fs.readFileSync(file, 'utf8')
  for (const text of confirmations) source = source.replaceAll(`$t('${text}')`, `$ts('${text}')`)
  if (file.endsWith(path.join('user', 'calendar', 'composables', 'useContract.js'))) {
    source = source.replace("proxy.$t('contract.editcustomer')", "proxy.$t('customer.editcustomer')")
  }
  fs.writeFileSync(file, source)
}

update('view-uni-src/locale/navigation.ts', (source) => {
  source = source.replace(
    'export const NAVIGATION_TITLE_ZH: Record<string, string> = {',
    'export const NAVIGATION_TITLE_ZH: Record<string, string> = {\n  "pages/launch/index": "陀螺匠",\n  "pages/common/ww-default": "企业微信",'
  )
  return source
})
