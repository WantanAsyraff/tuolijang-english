import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import getPageTitle from '@/utils/get-page-title'
import { roterPre } from '@/settings'
import { getMenus } from '@/utils/auth'
import { aiFloatEntryController } from '@/libs/ai'

NProgress.configure({ showSpinner: false }) // NProgress Configuration

const whiteList = [`login`, `share`, '/auth-redirect', '404'] // no redirect whitelist
let isPermissionGuardRegistered = false

export function setupPermissionGuard(router, store) {
  if (isPermissionGuardRegistered) {
    return
  }
  isPermissionGuardRegistered = true

  router.beforeEach(async (to, from, next) => {
    NProgress.start()
    document.title = getPageTitle(to.meta.title)
    const hasToken = store.getters.token

    if (hasToken) {
      if (to.path === `${roterPre}/login`) {
        next({ path: '/' })
        NProgress.done()
        return
      }

      if (store.getters.menuList.length <= 0) {
        await getMenus()
        next({ ...to, replace: true })
        return
      }

      // AI 悬浮入口不阻塞路由导航，失败时由控制器内部降级处理。
      aiFloatEntryController.ensure(store.getters.token, to.path)
      next()
      NProgress.done()
    } else if (whiteList.includes(to.name)) {
      next()
    } else {
      await store.dispatch('user/resetToken')
      next(`${roterPre}/login?redirect=${to.fullPath}`)
      NProgress.done()
    }
  })

  router.afterEach(() => {
    NProgress.done()
  })
}
