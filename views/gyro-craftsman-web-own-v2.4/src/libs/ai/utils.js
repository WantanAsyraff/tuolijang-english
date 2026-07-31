/**
 * AI 模块工具函数
 */

import { getAiBaseUrl } from './plugin-loader'
import { getLanguage } from '@/lang'

/**
 * 创建聊天页面 URL
 * @param {number|string|null} appId - 应用 ID，null 表示通用聊天页
 * @returns {URL} 聊天页面的 URL 对象
 */
export function createChatPageUrl(appId) {
  const url = new URL(appId !== null ? `/chat/app/${appId}` : '/chat', getAiBaseUrl())
  url.searchParams.set('language', getLanguage())
  return url
}
