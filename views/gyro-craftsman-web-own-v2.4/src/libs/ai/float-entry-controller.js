/**
 * AI 悬浮入口控制器
 * 负责路由级别的悬浮球显隐控制和状态同步
 */

import { AiEmbeddedManager } from './runtime'
import { shouldHideFloatEntry } from './float-config'

class AiFloatEntryController {
  /**
   * 强制刷新悬浮入口
   * 配置被后台修改时调用，强制刷新配置缓存并重建悬浮入口
   * @param {string} token - 用户令牌
   * @param {string} path - 当前路由路径
   * @returns {Promise<AiEmbeddedClient|null>}
   */
  async refresh(token, path = '') {
    return this.ensure(token, path, true)
  }

  /**
   * 确保悬浮入口存在并同步显隐状态
   * @param {string} token - 用户令牌
   * @param {string} path - 当前路由路径
   * @param {boolean} force - 是否强制刷新
   * @returns {Promise<AiEmbeddedClient|null>}
   */
  async ensure(token, path = '', force = false) {
    try {
      const client = await AiEmbeddedManager.ensureFloatEntry(token, {
        defaultShow: !shouldHideFloatEntry(path),
        force
      })

      this.updateVisibility(path)
      return client
    } catch (error) {
      // controller 不维护本地副本状态；失败后记录日志，后续路由进入时可再次重试
      console.error('AI 悬浮球状态同步失败:', error)
      return null
    }
  }

  /**
   * 根据路由更新悬浮球显示状态
   * @param {string} path - 当前路由路径
   */
  updateVisibility(path) {
    // 显隐判断统一以 AI runtime 里的全局悬浮入口是否存在为准
    if (!AiEmbeddedManager.hasFloatEntry()) return

    if (shouldHideFloatEntry(path)) {
      AiEmbeddedManager.hideFloatEntry()
    } else {
      AiEmbeddedManager.showFloatEntry()
    }
  }
}

export default new AiFloatEntryController()
