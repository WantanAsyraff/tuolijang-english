/**
 * AI 悬浮球显隐规则配置
 * 定义哪些路由下应该隐藏全局悬浮球（因为页面自身已承载 AI 功能）
 */

import { roterPre } from '@/settings'

/**
 * 悬浮球隐藏规则列表
 * 当页面自身已承载 AI 功能时，应隐藏全局悬浮球
 * @type {Array<{type: string, path?: string, pattern?: RegExp}>}
 */
export const FLOAT_HIDE_RULES = [
  // 精确匹配：完整路径
  {
    type: 'exact',
    path: `${roterPre}/chat/`
  },
  {
    type: 'exact',
    path: `${roterPre}/setting/uploadPicture`
  },
  {
    type: 'exact',
    path: `${roterPre}/setting/icons`
  },
  {
    type: 'exact',
    path: `${roterPre}/setting/auth`
  }
  // 支持扩展：正则匹配
  // {
  //   type: 'regex',
  //   pattern: /\/chat\/.+/
  // }
]

/**
 * 判断指定路径是否应该隐藏悬浮球
 * @param {string} path - 路由路径
 * @returns {boolean} 是否应该隐藏
 */
export function shouldHideFloatEntry(path = '') {
  return FLOAT_HIDE_RULES.some(rule => {
    if (rule.type === 'exact') {
      return path.startsWith(rule.path)
    }
    if (rule.type === 'regex') {
      return rule.pattern.test(path)
    }
    return false
  })
}
