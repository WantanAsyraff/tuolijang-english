import { $ } from '@/lang'
/**
 * AI 插件脚本加载器
 * 负责动态加载 ChatIframe 脚本并缓存加载任务
 */

import SettingMer from '@/libs/settingMer'

const scriptSrc = '/chat/entry/index.js'
// 全局缓存脚本加载任务，避免多个入口同时初始化时重复插入 script 标签
let loadTask = null

function getConfiguredAiBaseUrl() {
  return process.env.VUE_APP_AI_BASE_URL || SettingMer.httpUrl || location.origin
}

/**
 * 获取 AI 服务基础 URL
 * @returns {URL} AI 服务的 URL 对象
 */
export function getAiBaseUrl() {
  return new URL(getConfiguredAiBaseUrl(), location.origin)
}

/**
 * 创建脚本加载任务
 * @returns {Promise<Function>} ChatIframe 构造函数
 * @private
 */
function createLoadTask() {
  return new Promise((resolve, reject) => {
    // 脚本已经加载过时直接复用全局构造函数，避免再次触发网络请求
    if (window.ChatIframe) {
      resolve(window.ChatIframe)
      return
    }

    const script = document.createElement('script')
    script.src = new URL(scriptSrc, getAiBaseUrl()).href
    script.async = true
    script.onload = () => {
      // 有些异常场景下脚本 onload 了，但全局对象没有正确挂载
      // 这里把它当成加载失败处理，避免调用方拿到 undefined 后在更深层报错
      if (window.ChatIframe) {
        resolve(window.ChatIframe)
      } else {
        script.remove()
        reject(new Error($('AI 插件加载成功但未暴露 ChatIframe')))
      }
    }
    script.onerror = () => {
      script.remove()
      reject(new Error($('AI 插件脚本加载失败')))
    }

    document.head.appendChild(script)
  })
}

/**
 * 确保 AI 插件脚本已加载
 * 多次调用会复用同一个加载任务，避免重复加载
 * @returns {Promise<Function>} ChatIframe 构造函数
 */
export function ensureAiPluginLoaded() {
  // 优先走同步命中，让已经完成加载的场景不必再经过 Promise 链
  if (window.ChatIframe) {
    return Promise.resolve(window.ChatIframe)
  }

  if (!loadTask) {
    loadTask = createLoadTask().catch((error) => {
      // 失败后必须清空任务缓存，否则后续重试会永远复用失败的 Promise
      loadTask = null
      throw error
    })
  }

  return loadTask
}
