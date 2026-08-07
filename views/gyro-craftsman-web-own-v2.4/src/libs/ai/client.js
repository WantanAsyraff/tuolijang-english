import i18n from '@/lang'
import { getAiRuntimeConfig } from './runtime-config'
import { ensureAiPluginLoaded } from './plugin-loader'
import { createChatPageUrl } from './utils'

/**
 * AI 客户端状态枚举
 */
const CLIENT_STATUS = Object.freeze({
  IDLE: 'idle',           // 空闲：尚未初始化
  LOADING: 'loading',     // 加载中：正在初始化
  READY: 'ready',         // 就绪：初始化完成，可用
  ERROR: 'error',         // 错误：初始化失败
  DESTROYED: 'destroyed'  // 已销毁：生命周期结束
})

/**
 * 标准化初始化选项
 * @param {Object} options - 原始选项
 * @returns {Object} 标准化后的选项
 */
function normalizeInitOptions(options = {}) {
  return {
    appId: options.appId ?? null,
    defaultShow: options.defaultShow ?? true,
    scene: options.scene
  }
}

/**
 * 创建 ChatIframe 配置对象
 * @param {string} token - 用户令牌
 * @param {Object} options - 初始化选项
 * @param {Object} runtimeConfig - 运行时配置
 * @returns {Object} ChatIframe 构造函数所需的配置
 */
function createChatIframeConfig(token, options, runtimeConfig) {
  const url = createChatPageUrl(options.appId)

  return {
    url: url.href,
    query: {
      scene: options.scene,
      token
    },
    floatIcon: runtimeConfig.floatIcon,
    zIndex: 1000,
    defaultShow: options.defaultShow
  }
}

/**
 * AI 嵌入式客户端
 * 封装 ChatIframe 实例的生命周期管理，提供统一的初始化、显隐控制和销毁接口
 */
export class AiEmbeddedClient {
  /**
   * @param {Object} options - 构造选项
   * @param {string} options.kind - 客户端类型（'preview' | 'float-entry'）
   * @param {Function} options.onDestroy - 销毁时的回调函数
   */
  constructor({ kind = 'preview', onDestroy } = {}) {
    /** @type {string} 客户端类型 */
    this.kind = kind
    /** @type {Function} 销毁回调 */
    this.onDestroy = onDestroy
    /** @type {Object|null} 底层 ChatIframe 实例，页面层只通过 client 的 facade 方法与其交互 */
    this.instance = null
    /** @type {string} 统一描述 client 生命周期，避免多个布尔字段互相打架 */
    this.status = CLIENT_STATUS.IDLE
    /** @type {Promise|null} 负责把”初始化中的 client”收敛成一个 Promise，解决并发竞态 */
    this.initTask = null
  }

  /**
   * 判断客户端是否已销毁
   * @returns {boolean}
   */
  get isDestroyed() {
    return this.status === CLIENT_STATUS.DESTROYED
  }

  /**
   * 确保客户端就绪（幂等操作）
   * 已初始化则直接返回实例，未初始化则启动初始化流程，初始化中则等待
   * @param {string} token - 用户令牌
   * @param {Object} options - 初始化选项
   * @param {Object} runtimeConfig - 运行时配置（可选，未提供则自动获取）
   * @returns {Promise<Object|null>} ChatIframe 实例或 null
   */
  async ensureReady(token, options = {}, runtimeConfig) {
    if (this.isDestroyed) {
      return null
    }

    if (this.instance) {
      return this.instance
    }

    if (this.initTask) {
      return this.initTask
    }

    this.status = CLIENT_STATUS.LOADING
    this.initTask = this.createChatIframeInstance(
      token,
      normalizeInitOptions(options),
      runtimeConfig
    )

    return this.initTask
  }

  /**
   * 创建 ChatIframe 实例
   * 并行加载脚本和配置，创建实例后更新状态
   * @param {string} token - 用户令牌
   * @param {Object} options - 标准化后的选项
   * @param {Object} runtimeConfig - 运行时配置
   * @returns {Promise<Object|null>} ChatIframe 实例或 null
   * @private
   */
  async createChatIframeInstance(token, options, runtimeConfig) {
    try {
      // 脚本加载和运行时配置可以并行完成，缩短初始化链路
      const [ChatIframeCtor, resolvedRuntimeConfig] = await Promise.all([
        ensureAiPluginLoaded(),
        runtimeConfig ? Promise.resolve(runtimeConfig) : getAiRuntimeConfig()
      ])

      this.instance = new ChatIframeCtor(
        createChatIframeConfig(token, options, resolvedRuntimeConfig)
      )
      this.status = CLIENT_STATUS.READY
      return this.instance
    } catch (error) {
      // 失败后标记 error，但不销毁 client，方便后续重试
      this.status = CLIENT_STATUS.ERROR
      console.error(i18n.t('legacyScript.failedToLoadTheAIPlugin'), error)
      return null
    } finally {
      // 无论成功还是失败，都释放 initTask，避免失败 Promise 被永久复用
      this.initTask = null
    }
  }

  /**
   * 显示 AI 聊天窗口
   */
  show() {
    this.instance?.show?.()
  }

  /**
   * 隐藏 AI 聊天窗口
   */
  hide() {
    this.instance?.hide?.()
  }

  /**
   * 打开指定的 AI 应用
   * @param {number|string} appId - 应用 ID
   */
  openApp(appId) {
    this.instance?.openApp?.(appId)
  }

  /**
   * 刷新 AI 应用列表
   */
  refreshAppList() {
    this.instance?.refreshAppList?.()
  }

  /**
   * 销毁客户端实例
   * 清理所有资源并触发销毁回调
   */
  destroy() {
    this.status = CLIENT_STATUS.DESTROYED
    this.initTask = null

    if (this.instance) {
      this.instance.destroy?.()
      this.instance = null
    }

    this.onDestroy?.(this)
  }
}
