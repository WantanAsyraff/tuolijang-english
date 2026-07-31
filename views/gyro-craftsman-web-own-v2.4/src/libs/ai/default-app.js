/**
 * AI 悬浮球默认应用解析
 * 负责拉取 AI 应用列表，挑选首个「已发布（status === 1）」的应用作为悬浮球默认打开的应用
 */

import { getApplicationsListApi } from '@/api/chatAi'

// 缓存解析结果。沿用 runtime-config 的双缓存策略：
// 1. 已解析完成时直接复用 defaultAppId（用 resolved 区分「未解析」与「解析为 undefined」）
// 2. 首次请求进行中时复用 defaultAppIdTask，避免并发重复请求
let defaultAppId
let resolved = false
let defaultAppIdTask = null

/**
 * 从应用列表中挑选首个符合条件的应用 ID
 * @param {Array} list - 应用列表
 * @returns {number|string|undefined} 首个 status === 1 的应用 ID，没有则 undefined
 * @private
 */
function pickDefaultAppId(list = []) {
  const app = list.find((item) => Number(item.status) === 1)
  return app ? app.id : undefined
}

/**
 * 拉取应用列表并解析默认应用 ID
 * @returns {Promise<number|string|undefined>}
 * @private
 */
async function fetchDefaultAppId() {
  try {
    const res = await getApplicationsListApi({ page: 1, limit: 20 })
    return pickDefaultAppId(res?.data?.list)
  } catch (error) {
    // 列表请求失败时降级为 undefined（打开通用聊天页），不阻塞悬浮球创建
    console.warn('[AI] 获取应用列表失败，悬浮球将打开通用聊天页', error)
    return undefined
  }
}

/**
 * 使默认应用缓存失效
 * 后台修改配置、用户登出或强制刷新时调用
 */
export function invalidateDefaultAppId() {
  defaultAppId = undefined
  resolved = false
  defaultAppIdTask = null
}

/**
 * 获取悬浮球默认应用 ID（带缓存）
 * @param {boolean} force - 是否强制刷新缓存
 * @returns {Promise<number|string|undefined>} 默认应用 ID，无符合条件应用时为 undefined
 */
export function getDefaultAppId(force = false) {
  if (force) {
    invalidateDefaultAppId()
  }

  if (resolved) {
    return Promise.resolve(defaultAppId)
  }

  if (!defaultAppIdTask) {
    // 只让第一个调用真正发请求，后续并发调用全部等待同一个 Promise
    defaultAppIdTask = fetchDefaultAppId()
      .then((appId) => {
        defaultAppId = appId
        resolved = true
        return appId
      })
      .catch((error) => {
        // 请求失败后释放任务缓存，避免运行期无法再次解析
        defaultAppIdTask = null
        throw error
      })
  }

  return defaultAppIdTask
}
