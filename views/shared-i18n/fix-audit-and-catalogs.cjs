const fs = require('fs')
const path = require('path')

function update(relative, transform) {
  const file = path.resolve(__dirname, '..', relative)
  const source = fs.readFileSync(file, 'utf8')
  const next = transform(source)
  if (source !== next) fs.writeFileSync(file, next)
}

update('shared-i18n/localization-audit.cjs', (source) => {
  source = source.replace("extractCatalog(property.value, `${key}.`, result)", "extractCatalog(property.value, key, result)")
  source = source.replace("!(key.endsWith('.chinese') && value === '中文')", "value !== '中文'")
  source = source.replace(
    "const pages = JSON.parse(fs.readFileSync(path.join(config.root, 'pages.json'), 'utf8'))",
    "const pagesSource = fs.readFileSync(path.join(config.root, 'pages.json'), 'utf8')\n    .replace(/\\/\\*[\\s\\S]*?\\*\\//g, '')\n    .replace(/^\\s*\\/\\/.*$/gm, '')\n  const pages = JSON.parse(pagesSource)"
  )
  return source
})

update('gyro-craftsman-web-own-v2.4/src/lang/zh.js', (source) => {
  source = source.replace("    edit: '编辑',", "    edit: '编辑',\n    menuedit: '权限设置',")
  source = source.replace("    auditstatus1: '审核中'\n  },", "    auditstatus1: '审核中',\n    datechoose: '时间选择',\n    departmentchoose: '部门筛选'\n  },")
  return source
})

update('gyro-craftsman-web-own-v2.4/src/lang/en.js', (source) => source.replace(
  "translateToChinese: '中文'",
  "translateToChinese: 'Switch to Chinese'"
))

for (const locale of ['en.ts', 'zh-cn.ts']) {
  update(`gyro-craftsman-chat-v1.0/src/locale/${locale}`, (source) => {
    const value = locale === 'en.ts' ? 'Data table' : '数据表格'
    if (source.includes('dataTable:')) return source
    return source.replace('    regenerate:', `    dataTable: "${value}",\n    regenerate:`)
  })
}

update('gyro-craftsman-chat-v1.0/src/components/chat-messages/chat-message-table.vue', (source) => source.replace(
  '>数据表格</h4>',
  '>{{ $t("chat.dataTable") }}</h4>'
))
