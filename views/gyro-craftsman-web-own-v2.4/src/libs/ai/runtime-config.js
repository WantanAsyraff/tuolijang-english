/**
 * AI 运行时配置管理
 * 负责获取和缓存 AI 相关的后端配置（开关状态、图标等）
 */

import store from '@/store'
import defaultAIIcon from '@/assets/images/ai-assistant-icon.jpg'

// 缓存 AI 运行时配置。这里区分”最终结果缓存”和”进行中的请求缓存”：
// 1. 已拿到配置时直接复用 runtimeConfig
// 2. 首次请求进行中时复用 runtimeConfigTask，避免并发重复请求
let runtimeConfig = null
let runtimeConfigTask = null

/**
 * 标准化 enabled 字段值
 * @param {string|number|boolean} value - 原始值
 * @returns {boolean} 标准化后的布尔值
 * @private
 */
function normalizeEnabled(value) {
  return value === '1' || value === 1 || value === true
}

/**
 * 从后端获取 AI 运行时配置
 * appConfig 部分从 Vuex store 读取（已在路由守卫中预加载）
 * @returns {Promise<Object>} 运行时配置对象 { enabled, floatIcon }
 * @private
 */
async function fetchAiRuntimeConfig() {
  // appConfig 从 Vuex store 读取，无需再发请求
  const appConfig = store.getters['appConfig/configData'] || {}

  return {
    enabled: normalizeEnabled(appConfig.ai_status),
    floatIcon: appConfig.ai_image || defaultAIIcon
  }
}

/**
 * 使配置缓存失效
 * 配置被后台修改、用户登出、或调用方要求强制刷新时调用
 */
export function invalidateAiRuntimeConfig() {
  runtimeConfig = null
  runtimeConfigTask = null
}

/**
 * 获取 AI 运行时配置（带缓存）
 * @param {boolean} force - 是否强制刷新缓存
 * @returns {Promise<Object>} 运行时配置对象 { enabled, floatIcon }
 */
export function getAiRuntimeConfig(force = false) {
  if (force) {
    invalidateAiRuntimeConfig()
  }

  if (runtimeConfig) {
    return Promise.resolve(runtimeConfig)
  }

  if (!runtimeConfigTask) {
    // 只让第一个调用真正发请求，后续并发调用全部等待同一个 Promise
    runtimeConfigTask = fetchAiRuntimeConfig()
      .then((config) => {
        runtimeConfig = config
        return config
      })
      .catch((error) => {
        // 请求失败后不能保留失败任务，否则运行期将无法再次获取配置
        runtimeConfigTask = null
        throw error
      })
  }

  return runtimeConfigTask
}
