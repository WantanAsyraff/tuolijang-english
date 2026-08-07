const fs = require('fs')
const path = require('path')
const { pathToFileURL } = require('url')

const viewsRoot = path.resolve(__dirname, '..')
const babelParser = require('../gyro-craftsman-web-own-v2.4/node_modules/@babel/parser')
const vue2Compiler = require('../gyro-craftsman-web-own-v2.4/node_modules/vue-template-compiler')
const vue3Compiler = require('../view-uni-src/node_modules/@vue/compiler-dom')
const { auditScriptUi } = require('./script-ui-audit.cjs')
const APP_CONFIG = {
  web: {
    root: path.join(viewsRoot, 'gyro-craftsman-web-own-v2.4'),
    locales: ['src/lang/zh.js', 'src/lang/en.js'],
    source: 'src',
    templateCompiler: 'vue2',
    runtimeGuard: 'src/utils/dom-i18n.js',
    templateGuard: 'vue.config.js'
  },
  chat: {
    root: path.join(viewsRoot, 'gyro-craftsman-chat-v1.0'),
    locales: ['src/locale/zh-cn.ts', 'src/locale/en.ts'],
    source: 'src',
    templateCompiler: 'vue3'
  },
  mobile: {
    root: path.join(viewsRoot, 'view-uni-src'),
    locales: ['locale/zh-cn.ts', 'locale/en.ts'],
    source: '.',
    templateCompiler: 'vue3',
    templateGuard: 'config/i18nTemplatePlugin.ts'
  }
}
const HAS_HAN = /[\u3400-\u9fff]/
const IGNORE_DIRS = new Set(['node_modules', 'dist', 'unpackage', 'public', 'static', 'uni_modules', '__MACOSX'])
const IGNORE_FILES = new Set(['system-text.ts', 'system-text.js'])

function parse(file) {
  return babelParser.parse(fs.readFileSync(file, 'utf8'), {
    sourceType: 'module',
    plugins: ['typescript', 'jsx', 'decorators-legacy', 'optionalChaining']
  })
}

function localeObject(ast) {
  for (const statement of ast.program.body) {
    if (statement.type === 'ExportDefaultDeclaration' && statement.declaration.type === 'ObjectExpression') return statement.declaration
    if (statement.type === 'ExportDefaultDeclaration' && statement.declaration.type === 'Identifier') {
      const declaration = ast.program.body.find((item) => item.type === 'VariableDeclaration' && item.declarations.some((decl) => decl.id.name === statement.declaration.name))
      const declarator = declaration?.declarations.find((decl) => decl.id.name === statement.declaration.name)
      if (declarator?.init?.type === 'ObjectExpression') return declarator.init
    }
  }
  throw new Error('Locale file must export an object')
}

function propertyName(property) {
  if (property.computed) return ''
  return property.key.type === 'Identifier' ? property.key.name : String(property.key.value)
}

function extractCatalog(object, prefix = '', result = { values: new Map(), duplicates: [] }) {
  const siblings = new Set()
  for (const property of object.properties) {
    if (property.type !== 'ObjectProperty') continue
    const name = propertyName(property)
    if (!name) continue
    if (siblings.has(name)) result.duplicates.push(`${prefix}${name}`)
    siblings.add(name)
    const key = prefix ? `${prefix}.${name}` : name
    if (property.value.type === 'ObjectExpression') extractCatalog(property.value, key, result)
    else if (property.value.type === 'StringLiteral' || property.value.type === 'TemplateLiteral') {
      const value = property.value.type === 'StringLiteral'
        ? property.value.value
        : property.value.quasis.map((item) => item.value.cooked).join('${}')
      result.values.set(key, value)
    }
  }
  return result
}

function extractCatalogFile(file) {
  const ast = parse(file)
  const object = localeObject(ast)
  const result = extractCatalog(object)
  const imports = new Map()
  for (const statement of ast.program.body) {
    if (statement.type !== 'ImportDeclaration' || statement.specifiers.length !== 1) continue
    imports.set(statement.specifiers[0].local.name, statement.source.value)
  }
  for (const property of object.properties) {
    if (property.type !== 'ObjectProperty' || property.value.type !== 'Identifier') continue
    const imported = imports.get(property.value.name)
    if (!imported) continue
    const base = path.resolve(path.dirname(file), imported)
    const importedFile = [base, `${base}.js`, `${base}.ts`].find((candidate) => fs.existsSync(candidate))
    if (!importedFile) throw new Error(`Unable to resolve locale import ${imported} from ${file}`)
    extractCatalog(localeObject(parse(importedFile)), propertyName(property), result)
  }
  return result
}
function placeholders(value) {
  return [...String(value).matchAll(/\{\{?\s*([\w.]+)\s*\}?\}|%[sd]/g)].map((match) => match[1] || match[0]).sort()
}

function files(directory) {
  if (!fs.existsSync(directory)) return []
  return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    if (entry.isDirectory() && IGNORE_DIRS.has(entry.name)) return []
    const full = path.join(directory, entry.name)
    if (entry.isDirectory()) return files(full)
    return /\.(vue|js|ts|jsx|tsx)$/.test(entry.name) && !IGNORE_FILES.has(entry.name) ? [full] : []
  })
}

function literalReferences(source) {
  return [...source.matchAll(/(?:\$t|\bi18n\.t|\btranslate)\(\s*['"]([^'"]+)['"]/g)].map((match) => match[1])
}

const VISIBLE_ATTRIBUTES = new Set([
  'active-text', 'alt', 'aria-label', 'button-text', 'btntext', 'cancel-text',
  'close-text', 'confirm-text', 'content', 'element-loading-text', 'empty-text',
  'empty-title', 'end-placeholder', 'inactive-text', 'label', 'left-text', 'lefttext',
  'loading-text', 'no-data-text', 'no-filtered-data-text', 'no-filtered-userfrom-text',
  'no-match-text', 'no-userfrom-text', 'placeholder', 'range-separator', 'right-text',
  'righttext', 'start-placeholder', 'text', 'title', 'topbreadcrumbtext'
])

function templateHanFragments(file, compiler) {
  const source = fs.readFileSync(file, 'utf8')
  const templateStart = source.indexOf('<template')
  const contentStart = templateStart < 0 ? -1 : source.indexOf('>', templateStart) + 1
  const contentEnd = source.lastIndexOf('</template>')
  const template = contentStart > templateStart && contentEnd >= contentStart ? source.slice(contentStart, contentEnd) : ''
  if (!template) return []
  const fragments = []
  const add = (value) => {
    const text = String(value || '').trim()
    if (HAS_HAN.test(text) && !text.includes('<!--')) fragments.push(text)
  }

  if (compiler === 'vue2') {
    const ast = vue2Compiler.compile(template, { comments: false }).ast
    const visit = (node) => {
      if (!node) return
      if (node.type === 3 && !node.isComment) add(node.text)
      if (node.type === 1) {
        for (const attr of node.attrsList || []) {
          if (!attr.name.startsWith(':') && !attr.name.startsWith('v-') && VISIBLE_ATTRIBUTES.has(attr.name)) add(attr.value)
        }
        for (const child of node.children || []) visit(child)
      }
    }
    visit(ast)
  } else {
    const ast = vue3Compiler.baseParse(template, { comments: false, onError: () => {} })
    const visit = (node) => {
      if (!node) return
      if (node.type === 2) add(node.content)
      if (node.type === 1) {
        for (const prop of node.props || []) {
          if (prop.type === 6 && VISIBLE_ATTRIBUTES.has(prop.name)) add(prop.value?.content)
        }
      }
      for (const child of node.children || []) visit(child)
    }
    visit(ast)
  }
  return [...new Set(fragments)]
}

function checkCatalogs(config, errors) {
  const [zhFile, enFile] = config.locales.map((relative) => path.join(config.root, relative))
  const zh = extractCatalogFile(zhFile)
  const en = extractCatalogFile(enFile)
  if (zh.duplicates.length) errors.push(`duplicate Chinese keys: ${zh.duplicates.join(', ')}`)
  if (en.duplicates.length) errors.push(`duplicate English keys: ${en.duplicates.join(', ')}`)

  const missingEn = [...zh.values.keys()].filter((key) => !en.values.has(key))
  const missingZh = [...en.values.keys()].filter((key) => !zh.values.has(key))
  if (missingEn.length) errors.push(`missing English keys: ${missingEn.join(', ')}`)
  if (missingZh.length) errors.push(`missing Chinese keys: ${missingZh.join(', ')}`)

  for (const [key, zhValue] of zh.values) {
    if (!en.values.has(key)) continue
    const left = placeholders(zhValue).join('|')
    const right = placeholders(en.values.get(key)).join('|')
    if (left !== right) errors.push(`placeholder mismatch at ${key}: [${left}] vs [${right}]`)
  }
  const hanEnglish = [...en.values].filter(([key, value]) => HAS_HAN.test(value) && value !== '中文')
  if (hanEnglish.length) errors.push(`Chinese text in English values: ${hanEnglish.map(([key]) => key).join(', ')}`)

  const sourceFiles = files(path.join(config.root, config.source))
  const refs = new Set(sourceFiles.flatMap((file) => literalReferences(fs.readFileSync(file, 'utf8'))))
  const missingRefs = [...refs].filter((key) => !en.values.has(key) && !key.startsWith('el.') && !key.startsWith('designer.') && !key.endsWith('.'))
  if (missingRefs.length) errors.push(`missing literal translation references: ${missingRefs.join(', ')}`)
  return { zh: zh.values.size, en: en.values.size, sourceFiles }
}

function checkMobileNavigation(config, errors) {
  const pagesSource = fs.readFileSync(path.join(config.root, 'pages.json'), 'utf8')
    .replace(/\/\*[\s\S]*?\*\//g, '')
    .replace(/^\s*\/\/.*$/gm, '')
  const pages = JSON.parse(pagesSource)
  const routes = [
    ...(pages.pages || []).map((page) => page.path),
    ...(pages.subPackages || []).flatMap((group) => (group.pages || []).map((page) => `${group.root}/${page.path}`))
  ]
  const navigation = fs.readFileSync(path.join(config.root, 'locale/navigation.ts'), 'utf8')
  const mappedRoutes = new Set([...navigation.matchAll(/^\s*"([^"]+)":\s*"/gm)].map((match) => match[1]))
  const missing = routes.filter((route) => !mappedRoutes.has(route))
  if (missing.length) errors.push(`missing mobile navigation routes: ${missing.join(', ')}`)
  const tabCount = pages.tabBar?.list?.length || 0
  const mappedTabs = [...navigation.matchAll(/[\{,]\s*["']?index["']?\s*:\s*\d+/g)].length
  if (mappedTabs !== tabCount) errors.push(`mobile tab mapping mismatch: expected ${tabCount}, found ${mappedTabs}`)
}

function checkMobileDynamicLocalization(config, report, errors) {
  const requiredBindings = [
    ['components/BottomActionSheet/index.vue', '$ts(item.label)'],
    ['components/DropDown/index.vue', '$ts(item.name)'],
    ['components/morePopup/index.vue', '$ts(item.name)'],
    ['components/moduleForm/index.vue', '$localize(val.data_dict)'],
    ['components/examineForm/index.vue', '$localize(item.props.options)'],
    ['pages/customer/components/common-form.vue', '$localize((item as FormItemWithOptions).options)'],
    ['pages/module/dashboard.vue', '$ts(item.label)'],
    ['pages/module/components/item.vue', '$ts(item[val.field_name_en].name)']
  ]
  for (const [relative, binding] of requiredBindings) {
    const source = fs.readFileSync(path.join(config.root, relative), 'utf8')
    if (!source.includes(binding)) errors.push(`missing mobile runtime localization binding: ${relative} -> ${binding}`)
  }

  const statusExpression = /\{\{[^}\n]*(?:status(?:\?|_status)?\.name|statusList\[[^\]]+\]\.name|source\.name)[^}\n]*\}\}/g
  const unlocalized = []
  for (const file of report.sourceFiles.filter((entry) => entry.endsWith('.vue'))) {
    const source = fs.readFileSync(file, 'utf8')
    for (const match of source.matchAll(statusExpression)) {
      if (!match[0].includes('$ts(')) unlocalized.push(`${path.relative(config.root, file)}: ${match[0]}`)
    }
  }
  if (unlocalized.length) errors.push(`unlocalized mobile status/dictionary values:\n${unlocalized.join('\n')}`)
}
function checkChartLocalization(appName, report, errors) {
  if (appName === 'web') {
    const directCharts = report.sourceFiles.filter((file) => fs.readFileSync(file, 'utf8').includes('.setOption('))
    const missing = directCharts.filter((file) => !fs.readFileSync(file, 'utf8').includes('localizeChartOption'))
    if (missing.length) errors.push(`unlocalized web chart options: ${missing.map((file) => path.relative(APP_CONFIG.web.root, file)).join(', ')}`)
  }
  if (appName === 'mobile') {
    const charts = report.sourceFiles.filter((file) => fs.readFileSync(file, 'utf8').includes('<qiun-data-charts'))
    const missing = charts.filter((file) => !fs.readFileSync(file, 'utf8').includes('$localize('))
    if (missing.length) errors.push(`unlocalized mobile chart options: ${missing.map((file) => path.relative(APP_CONFIG.mobile.root, file)).join(', ')}`)
  }
}
function checkBladeFrontend(errors) {
  const repoRoot = path.resolve(viewsRoot, '..')
  const bladeRoot = path.join(repoRoot, 'resources/views')
  const bladeFiles = []
  const visit = (directory) => {
    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
      const target = path.join(directory, entry.name)
      if (entry.isDirectory()) visit(target)
      else if (entry.name.endsWith('.blade.php')) bladeFiles.push(target)
    }
  }
  visit(bladeRoot)
  const findings = []
  for (const file of bladeFiles) {
    const source = fs.readFileSync(file, 'utf8')
      .replace(/\{\{--[\s\S]*?--\}\}/g, '')
      .replace(/<!--[\s\S]*?-->/g, '')
      .replace(/\/\*[\s\S]*?\*\//g, '')
      .replace(/^\s*\/\/.*$/gm, '')
    source.split(/\r?\n/).forEach((line, index) => {
      if (HAS_HAN.test(line)) findings.push(`${path.relative(repoRoot, file)}:${index + 1} ${line.trim()}`)
    })
  }
  if (findings.length) errors.push(`untranslated first-party Blade display text:\n${findings.slice(0, 100).join('\n')}`)

  const installerScript = fs.readFileSync(path.join(repoRoot, 'public/install/js/install-i18n.js'), 'utf8')
  if (/textMap|MutationObserver/.test(installerScript)) errors.push('installer DOM text replacement must not be used')
  if (HAS_HAN.test(installerScript)) errors.push('installer locale selector contains hardcoded Han display text')
}
async function run(appName) {
  const config = APP_CONFIG[appName]
  if (!config) throw new Error(`Use one of: ${Object.keys(APP_CONFIG).join(', ')}`)
  const errors = []
  const report = checkCatalogs(config, errors)
  const scriptUiIssues = auditScriptUi({
    root: config.root,
    sourceFiles: report.sourceFiles,
    parse: (source, context) => babelParser.parse(source, {
      sourceType: 'module',
      plugins: [
        'typescript',
        ...(context.lang === 'ts' ? [] : ['jsx']),
        'decorators-legacy',
        'optionalChaining'
      ],
      errorRecovery: appName === 'mobile'
    })
  })
  if (scriptUiIssues.length) {
    errors.push(`untranslated script/CSS display text:\n${scriptUiIssues.slice(0, 300).map((issue) => `${issue.file}:${issue.line} [${issue.sink}] ${issue.text}`).join('\n')}`)
  }

  if (appName === 'mobile') {
    checkMobileNavigation(config, errors)
    checkMobileDynamicLocalization(config, report, errors)
  }
  checkChartLocalization(appName, report, errors)
  if (config.templateGuard && !fs.existsSync(path.join(config.root, config.templateGuard))) errors.push('mobile template localization guard is missing')
  if (config.runtimeGuard && !fs.existsSync(path.join(config.root, config.runtimeGuard))) errors.push('web legacy DOM localization guard is missing')

  const { translateSystemTextValue } = await import(pathToFileURL(path.join(viewsRoot, 'shared-i18n/index.js')).href)
  const visibleStatic = report.sourceFiles.flatMap((file) =>
    templateHanFragments(file, config.templateCompiler).map((text) => ({ file: path.relative(config.root, file), text }))
  )
  const unresolvedStatic = visibleStatic
  const guardMode = config.templateGuard ? 'compile-time localized' : config.runtimeGuard ? 'legacy runtime allowlist' : 'none'
  if (unresolvedStatic.length) {
    errors.push(`untranslated visible template text:\n${unresolvedStatic.slice(0, 200).map(({ file, text }) => `${file}: ${text}`).join('\n')}`)
  }
  if (appName === 'web') checkBladeFrontend(errors)

  console.log(`[${appName}] zh/en keys: ${report.zh}/${report.en}`)
  console.log(`[${appName}] checked ${report.sourceFiles.length} first-party source files`)
  console.log(`[${appName}] checked ${visibleStatic.length} static Han fragments; ${unresolvedStatic.length} unresolved; guard: ${guardMode}`)
  if (errors.length) {
    console.error(errors.map((error) => `- ${error}`).join('\n'))
    process.exitCode = 1
  } else {
    console.log(`[${appName}] localization audit passed`)
  }
}

run(process.argv[2]).catch((error) => {
  console.error(error)
  process.exitCode = 1
})
