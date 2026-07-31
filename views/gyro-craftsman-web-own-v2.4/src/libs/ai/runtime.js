/**
 * AI 运行时管理器
 * 管理全局 AI 客户端实例的生命周期，提供统一的创建、销毁和状态管理接口
 */

import { getAiRuntimeConfig, invalidateAiRuntimeConfig } from './runtime-config'
import { getDefaultAppId, invalidateDefaultAppId } from './default-app'
import { getAiBaseUrl } from './plugin-loader'
import { AiEmbeddedClient } from './client'
import { createChatPageUrl } from './utils'

// AI runtime 的全局状态只保留一份：
// 1. clients：当前页面生命周期内创建过的所有 AI client
// 2. floatEntry：全局悬浮入口单例
const runtimeState = {
  clients: new Set(),
  floatEntry: null
}

/**
 * 判断是否需要强制刷新配置
 * @param {Object} options - 选项对象
 * @returns {boolean}
 * @private
 */
function shouldForceRefresh(options = {}) {
  return Boolean(options.force || options.forceConfig)
}

/**
 * 释放客户端实例
 * 从全局状态中移除，如果是悬浮入口则清空引用
 * @param {AiEmbeddedClient} client - 要释放的客户端
 * @private
 */
function releaseClient(client) {
  runtimeState.clients.delete(client)

  if (runtimeState.floatEntry === client) {
    runtimeState.floatEntry = null
  }
}

/**
 * 创建受管理的客户端实例
 * @param {string} kind - 客户端类型
 * @returns {AiEmbeddedClient}
 * @private
 */
function createManagedClient(kind) {
  const client = new AiEmbeddedClient({
    kind,
    onDestroy: releaseClient
  })

  runtimeState.clients.add(client)
  return client
}

/**
 * 销毁全局悬浮入口客户端
 * @private
 */
function destroyFloatEntryClient() {
  runtimeState.floatEntry?.destroy()
  runtimeState.floatEntry = null
}

/**
 * 获取或创建全局悬浮入口客户端
 * @param {Object} options - 选项
 * @param {boolean} options.force - 是否强制重建
 * @returns {AiEmbeddedClient}
 * @private
 */
function getOrCreateFloatEntryClient({ force = false } = {}) {
  if (force) {
    // force 用于重建全局悬浮入口，确保旧实例和旧 token 被彻底替换
    destroyFloatEntryClient()
  }

  if (!runtimeState.floatEntry || runtimeState.floatEntry.isDestroyed) {
    runtimeState.floatEntry = createManagedClient('float-entry')
  }

  return runtimeState.floatEntry
}

/**
 * 创建预览 iframe URL
 * @param {number|string} appId - 应用 ID
 * @param {string} token - 用户令牌
 * @returns {string} 完整的预览 URL
 * @private
 */
function createPreviewIframeUrl(appId, token) {
  const url = createChatPageUrl(appId)
  url.searchParams.set('app-preview', '1')
  url.searchParams.set('not-save-chat', '1')

  if (token) {
    url.searchParams.set('token', token)
  }

  return url.href
}

/**
 * 创建预览消息载荷
 * @param {Object} data - 消息数据
 * @returns {Object} postMessage 载荷
 * @private
 */
function createPreviewMessagePayload(data) {
  return {
    source: 'ai-chat-parent',
    action: 'update-app-preview-state',
    data
  }
}

/**
 * AI 嵌入式管理器
 * 提供全局 AI 客户端管理的静态方法
 */
export class AiEmbeddedManager {
  /**
   * 获取 AI 运行时配置
   * @param {boolean} force - 是否强制刷新缓存
   * @returns {Promise<Object>} 运行时配置对象
   */
  static getRuntimeConfig(force = false) {
    // 统一走配置服务，业务层不再自己拼 appConfig/site_address 两套接口
    return getAiRuntimeConfig(force)
  }

  /**
   * 确保全局悬浮入口就绪
   * 唯一允许暴露给业务层的悬浮入口初始化方法
   * @param {string} token - 用户令牌
   * @param {Object} options - 初始化选项
   * @param {boolean} options.defaultShow - 默认是否显示
   * @param {boolean} options.force - 是否强制重建
   * @returns {Promise<AiEmbeddedClient|null>} 客户端实例或 null（AI 未启用时）
   */
  static async ensureFloatEntry(token, options = {}) {
    // 只做三件事：判断 AI 开关、获取/重建单例、确保该单例 ready
    const forceRefresh = shouldForceRefresh(options)
    const runtimeConfig = await AiEmbeddedManager.getRuntimeConfig(forceRefresh)

    if (!runtimeConfig.enabled) {
      destroyFloatEntryClient()
      return null
    }

    // AI 开启后再解析默认应用，避免未启用的租户白请求应用列表；
    // 调用方未显式指定 appId 时才回退到列表里首个已发布的应用
    const appId =
      options.appId ?? (await getDefaultAppId(forceRefresh))

    const client = getOrCreateFloatEntryClient({ force: forceRefresh })
    await client.ensureReady(token, { ...options, appId }, runtimeConfig)
    return client
  }

  /**
   * 创建预览客户端实例
   * 预览实例永远不复用全局悬浮入口，避免聊天预览销毁时误伤浮球
   * @returns {AiEmbeddedClient}
   */
  static createPreviewClient() {
    return createManagedClient('preview')
  }

  /**
   * 销毁全局悬浮入口
   */
  static destroyFloatEntry() {
    destroyFloatEntryClient()
  }

  /**
   * 显示全局悬浮入口
   */
  static showFloatEntry() {
    runtimeState.floatEntry?.show()
  }

  /**
   * 隐藏全局悬浮入口
   */
  static hideFloatEntry() {
    runtimeState.floatEntry?.hide()
  }

  /**
   * 刷新全局悬浮入口的应用列表
   */
  static refreshFloatEntryAppList() {
    runtimeState.floatEntry?.refreshAppList()
  }

  /**
   * 判断全局悬浮入口是否存在
   * @returns {boolean}
   */
  static hasFloatEntry() {
    return Boolean(runtimeState.floatEntry && !runtimeState.floatEntry.isDestroyed)
  }

  /**
   * 销毁所有客户端实例并清空配置缓存
   * 退出登录等场景需要调用，防止下一次登录仍复用上一个租户/用户的配置结果
   */
  static destroyAll() {
    Array.from(runtimeState.clients).forEach((client) => {
      client.destroy()
    })
    runtimeState.floatEntry = null
    invalidateAiRuntimeConfig()
    invalidateDefaultAppId()
  }

  /**
   * 更新 AI 应用预览状态
   * 通过 postMessage 向预览 iframe 发送状态更新
   * @param {HTMLIFrameElement} iframeRef - iframe 元素引用
   * @param {Object} data - 要更新的状态数据
   * @returns {boolean} 是否发送成功
   */
  static updateAiAppPreviewState(iframeRef, data) {
    // 预览 iframe 可能尚未挂载，先做空值保护，避免保存配置时抛异常
    if (!iframeRef?.contentWindow) {
      return false
    }

    iframeRef.contentWindow.postMessage(
      createPreviewMessagePayload(data),
      getAiBaseUrl().origin
    )

    return true
  }

  /**
   * 获取预览 iframe URL
   * @param {number|string} appId - 应用 ID
   * @param {Object} options - 选项
   * @param {string} options.token - 用户令牌
   * @returns {string} 预览页 URL
   */
  static getPreviewIframeUrl(appId, { token } = {}) {
    // 预览页仍沿用现有 query 约定，这里集中拼装，避免 token/preview 参数散落在业务层
    return createPreviewIframeUrl(appId, token)
  }
}
