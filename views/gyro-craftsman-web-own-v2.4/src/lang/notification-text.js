import { translateSystemTextValue } from './system-text.js'

const HAS_HAN = /[\u3400-\u9fff]/
const HTML_TAG = /(<[^>]+>)/g
const KNOWN_NOTIFICATION_TEXT = {
  '制定考核目标提醒': 'Set assessment goals reminder',
  '开启考核任务提醒': 'Start assessment task reminder',
  '考核自我评价提醒': 'Self-assessment reminder',
  '考核上级评价提醒': 'Manager assessment reminder',
  '考核异常提醒': 'Assessment exception reminder',
  '【业务类型】审批提醒': 'Business approval reminder',
  '【业务类型】撤回提醒': 'Business withdrawal reminder',
  '申请人【业务类型】审批通过提醒': 'Applicant approval reminder',
  '抄送人【业务类型】审批通过提醒': 'CC recipient approval reminder',
  '订单急需续费提醒': 'Urgent order renewal reminder',
  '订单续费今日到期提醒': 'Order renewal due today reminder',
  '订单续费过期提醒': 'Overdue order renewal reminder',
  '合同订单续费提醒': 'Contract and order renewal reminder',
  '合同订单支出提醒': 'Contract and order expense reminder',
  '合同订单即将到期提醒': 'Contract and order expiring soon reminder',
  '合同订单今日到期提醒': 'Contract and order due today reminder',
  '财务审核已通过提醒': 'Finance approval reminder',
  '财务审核未通过提醒': 'Finance rejection reminder',
  '财务发票审核已通过提醒': 'Invoice finance approval reminder',
  '财务发票审核未通过提醒': 'Invoice finance rejection reminder',
  '发票已开具提醒': 'Invoice issued reminder',
  '发票未开具提醒': 'Invoice not issued reminder',
  '平台审核通过提醒': 'Platform approval reminder',
  '平台审核未通过提醒': 'Platform rejection reminder',
  '人员申请加入提醒': 'Employee join request reminder',
  '人员加入提醒': 'Employee joined reminder',
  '人员拒绝加入提醒': 'Employee join request rejected reminder',
  '文件删除提醒': 'File deletion reminder',
  '文件创建提醒': 'File creation reminder',
  '文件浏览提醒': 'File view reminder',
  '上班打卡提醒': 'Clock-in reminder',
  '下班打卡提醒': 'Clock-out reminder',
  '上班缺卡提醒': 'Missing clock-in reminder',
  '下班缺卡提醒': 'Missing clock-out reminder',
  '团队出勤日报提醒': 'Team attendance daily report reminder',
  '团队出勤周报提醒': 'Team attendance weekly report reminder',
  '团队出勤月报提醒': 'Team attendance monthly report reminder',
  '个人周统计提醒': 'Personal weekly statistics reminder',
  '个人月统计提醒': 'Personal monthly statistics reminder'
}

const TRAILING_PUNCTUATION = /[\s\u3002\uff01!]+$/

const COMPOUND_TERMS = [
  ['\u65e5\u62a5', 'daily report'],
  ['\u5468\u62a5', 'weekly report'],
  ['\u6708\u62a5', 'monthly report'],
  ['\u5de5\u4f5c\u6c47\u62a5', 'work report'],
  ['\u7ee9\u6548\u8003\u6838', 'performance assessment'],
  ['\u5ba1\u6279', 'approval'],
  ['\u7533\u8bf7', 'application'],
  ['\u5408\u540c', 'contract'],
  ['\u8ba2\u5355', 'order'],
  ['\u53d1\u7968', 'invoice'],
  ['\u56de\u6b3e', 'payment'],
  ['\u8003\u52e4', 'attendance'],
  ['\u6587\u4ef6', 'file'],
  ['\u4f01\u4e1a\u52a8\u6001', 'company news'],
  ['\u67e5\u770b', 'review'],
  ['\u586b\u5199', 'submission'],
  ['\u4fee\u6539', 'update'],
  ['\u7ed3\u679c', 'result'],
  ['\u5f02\u5e38', 'exception'],
  ['\u901a\u77e5', 'notification'],
  ['\u6d88\u606f', 'message']
]

const ACTIONS = {
  '\u63d0\u4ea4': 'submitted',
  '\u4fee\u6539': 'updated',
  '\u64a4\u56de': 'withdrawn',
  '\u901a\u8fc7': 'approved',
  '\u62d2\u7edd': 'rejected',
  '\u5220\u9664': 'deleted',
  '\u4f5c\u5e9f': 'invalidated',
  '\u5904\u7406': 'processed'
}

const PROMPTS = {
  '\u67e5\u770b': 'review it',
  '\u5904\u7406': 'handle it',
  '\u586b\u5199': 'complete it',
  '\u63d0\u4ea4': 'submit it',
  '\u5ba1\u6279': 'review it',
  '\u786e\u8ba4': 'confirm it'
}

function isEnglish(locale) {
  return String(locale || '').toLowerCase().startsWith('en')
}

function sentenceCase(value) {
  return value ? value.charAt(0).toUpperCase() + value.slice(1) : value
}

function lowerSentence(value) {
  return value ? value.charAt(0).toLowerCase() + value.slice(1) : value
}

function possessive(value) {
  const owner = String(value || '').trim()
  return /s$/i.test(owner) ? `${owner}'` : `${owner}'s`
}

function translateCompound(value, locale) {
  const source = String(value || '').trim()
  if (!source) return source

  let translated = source
  for (const [chinese, english] of COMPOUND_TERMS) {
    translated = translated.split(chinese).join(` ${english} `)
  }
  translated = translated.replace(/\s+/g, ' ').trim()
  if (!HAS_HAN.test(translated)) return translated

  const exact = translateSystemTextValue(source, { locale })
  return exact !== source ? exact : source
}

function translatePattern(value, locale) {
  const source = String(value || '').trim().replace(TRAILING_PUNCTUATION, '')
  let match = source.match(/^(.+?)\u7684(.+?)\u5df2(\u63d0\u4ea4|\u4fee\u6539|\u64a4\u56de|\u901a\u8fc7|\u62d2\u7edd|\u5220\u9664|\u4f5c\u5e9f|\u5904\u7406)[\uff0c,]\s*\u8bf7\u53ca\u65f6(\u67e5\u770b|\u5904\u7406|\u586b\u5199|\u63d0\u4ea4|\u5ba1\u6279|\u786e\u8ba4)$/)
  if (match) {
    const subject = translateCompound(match[2], locale)
    const action = ACTIONS[match[3]]
    const prompt = PROMPTS[match[4]]
    return `${possessive(match[1])} ${lowerSentence(subject)} has been ${action}. Please ${prompt} promptly.`
  }

  match = source.match(/^(.+?)\u5df2(\u63d0\u4ea4|\u4fee\u6539|\u64a4\u56de|\u901a\u8fc7|\u62d2\u7edd|\u5220\u9664|\u4f5c\u5e9f|\u5904\u7406)[\uff0c,]\s*\u8bf7\u53ca\u65f6(\u67e5\u770b|\u5904\u7406|\u586b\u5199|\u63d0\u4ea4|\u5ba1\u6279|\u786e\u8ba4)$/)
  if (match) {
    const subject = translateCompound(match[1], locale)
    return `${sentenceCase(subject)} has been ${ACTIONS[match[2]]}. Please ${PROMPTS[match[3]]} promptly.`
  }

  match = source.match(/^\u8bf7\u53ca\u65f6(\u67e5\u770b|\u5904\u7406|\u586b\u5199|\u63d0\u4ea4|\u5ba1\u6279|\u786e\u8ba4)(.+)$/)
  if (match) {
    const subject = translateCompound(match[2], locale)
    return `Please ${PROMPTS[match[1]]} ${lowerSentence(subject)} promptly.`
  }

  match = source.match(/^(.+?)\u63d0\u9192$/)
  if (match) {
    const subject = translateCompound(match[1], locale)
    if (!HAS_HAN.test(subject)) return `${sentenceCase(subject)} reminder`
  }

  match = source.match(/^\u60a8\u7684\u6388\u6743\u8bc1\u4e66\u8fd8\u6709(\d+)\u5929\u8fc7\u671f[\uff0c,]\u8bf7\u53ca\u65f6\u524d\u5f80\u9640\u87ba\u5320\u5b98\u65b9\u8fdb\u884c\u6388\u6743\u8ba4\u8bc1$/)
  if (match) {
    return `Your license expires in ${match[1]} days. Please renew it through the official Tuoluojiang service.`
  }

  return value
}

function translatePlainText(value, locale) {
  const direct = translateSystemTextValue(value, { locale })
  const knownNotification = KNOWN_NOTIFICATION_TEXT[value.trim()]
  if (knownNotification) return value.replace(value.trim(), knownNotification)

  if (direct !== value) return direct

  const leading = value.match(/^\s*/)[0]
  const trailing = value.match(/\s*$/)[0]
  const core = value.slice(leading.length, value.length - trailing.length)
  const patterned = translatePattern(core, locale)
  if (patterned !== core) return `${leading}${patterned}${trailing}`

  const parts = core.split(/([\uff0c,\u3002\uff01!\uff1f?\uff1b;\r\n]+)/)
  let changed = false
  const translated = parts.map((part) => {
    if (!part || /^[\uff0c,\u3002\uff01!\uff1f?\uff1b;\r\n]+$/.test(part)) {
      return part
        .replace(/\uff0c/g, ',')
        .replace(/\u3002/g, '.')
        .replace(/\uff01/g, '!')
        .replace(/\uff1f/g, '?')
        .replace(/\uff1b/g, ';')
    }
    const next = translateSystemTextValue(part.trim(), { locale })
    if (next !== part.trim()) changed = true
    return part.replace(part.trim(), next)
  }).join('')

  return changed ? `${leading}${translated}${trailing}` : value
}

export function translateNotificationText(value, locale = 'zh-cn') {
  if (typeof value !== 'string' || !value || !isEnglish(locale) || !HAS_HAN.test(value)) return value

  if (/<[^>]+>/.test(value)) {
    return value
      .split(HTML_TAG)
      .map((part) => (part.startsWith('<') && part.endsWith('>') ? part : translatePlainText(part, locale)))
      .join('')
  }

  return translatePlainText(value, locale)
}

function errorMessage(value) {
  if (!value || typeof value !== 'object') return ''
  if (value instanceof Error || Object.prototype.toString.call(value) === '[object Error]') {
    return value.message || ''
  }
  return ''
}

export function normalizeNotificationInput(input, translate = (value) => value) {
  if (typeof input === 'string') return translate(input)

  const directError = errorMessage(input)
  if (directError) return translate(directError)

  if (!input || typeof input !== 'object' || Array.isArray(input)) return input

  const normalized = { ...input }
  const fields = ['message', 'title', 'description', 'content']
  fields.forEach((field) => {
    if (typeof normalized[field] === 'string') {
      normalized[field] = translate(normalized[field])
    } else {
      const nestedError = errorMessage(normalized[field])
      if (nestedError) normalized[field] = translate(nestedError)
    }
  })

  if (normalized.message === undefined) {
    const fallback = [normalized.msg, normalized.detail, normalized.error]
      .map((value) => (typeof value === 'string' ? value : errorMessage(value)))
      .find(Boolean)
    if (fallback) normalized.message = translate(fallback)
  }

  if (Array.isArray(normalized.buttons)) {
    normalized.buttons = normalized.buttons.map((button) => {
      if (typeof button === 'string') return translate(button)
      if (!button || typeof button !== 'object') return button
      const next = { ...button }
      ;['title', 'label', 'text'].forEach((field) => {
        if (typeof next[field] === 'string') next[field] = translate(next[field])
      })
      return next
    })
  }

  return normalized
}

