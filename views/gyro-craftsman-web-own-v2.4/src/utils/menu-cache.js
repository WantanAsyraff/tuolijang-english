import store from '@/store'
import router, { getRouterMenus } from '@/router'
import { roterPre } from '@/settings'
import { getStorageJson, removeStorageJson, setStorageJson } from '@/utils/storage'

const MENU_CACHE_KEY = 'permissionMenuCache'
const MENU_SYNC_KEY = 'permissionMenuCacheSyncAt'
const MENU_CACHE_MAX_AGE = 12 * 60 * 60 * 1000
let refreshTask = null
let syncRegistered = false
let channel = null
let lastSyncAt = 0

function clone(data) {
  return JSON.parse(JSON.stringify(data || null))
}

function getCurrentUserInfo() {
  return store.getters.userInfo || getStorageJson('userInfo', {})
}

function getCurrentEnterprise() {
  return store.getters.enterprise || getStorageJson('enterprise', {})
}

function getCacheOwner() {
  const userInfo = getCurrentUserInfo() || {}
  const enterprise = getCurrentEnterprise() || {}
  return {
    userId: userInfo.id || userInfo.uid || '',
    entId: enterprise.entid || enterprise.id || 1
  }
}

function isSameOwner(cache) {
  const owner = getCacheOwner()
  if (!cache || !cache.userId) {
    return false
  }
  return String(cache.userId) === String(owner.userId) && String(cache.entId || 1) === String(owner.entId || 1)
}

export function saveMenuCache(menu, permissions) {
  const owner = getCacheOwner()
  if (!owner.userId || !Array.isArray(menu)) {
    return
  }

  setStorageJson(MENU_CACHE_KEY, {
    ...owner,
    menu,
    permissions,
    cachedAt: Date.now()
  })
}

export function clearMenuCache() {
  removeStorageJson(MENU_CACHE_KEY)
}

export function applyMenuState(menu, permissions = []) {
  const menuForStore = clone(menu) || []
  const menuForRouter = clone(menu) || []

  store.commit('user/SET_MENU_LIST', menuForStore)
  store.commit('user/SET_PERMISSIONS', permissions || [])
  getRouterMenus(menuForRouter)
}

export function restoreMenuFromCache() {
  const cache = getStorageJson(MENU_CACHE_KEY)
  if (!isSameOwner(cache)) {
    return false
  }
  if (!cache.cachedAt || Date.now() - cache.cachedAt > MENU_CACHE_MAX_AGE) {
    clearMenuCache()
    return false
  }
  if (!Array.isArray(cache.menu) || cache.menu.length <= 0) {
    return false
  }

  applyMenuState(cache.menu, cache.permissions || [])
  return true
}

export function isCurrentRouteAccessible(path = router.currentRoute.path) {
  const whiteList = [`${roterPre}/login`, `${roterPre}/404`, `${roterPre}/user/work`]
  if (whiteList.includes(path) || !path.startsWith(roterPre)) {
    return true
  }
  const resolved = router.resolve(path).route
  return resolved.matched.some((record) => record.path !== '*')
}

export function redirectIfCurrentRouteForbidden() {
  if (!isCurrentRouteAccessible()) {
    router.replace(`${roterPre}/user/work`)
  }
}

export function broadcastMenuInvalidated() {
  const payload = { type: 'permission_changed', time: Date.now() }
  lastSyncAt = payload.time
  if (channel) {
    channel.postMessage(payload)
  }
  localStorage.setItem(MENU_SYNC_KEY, String(payload.time))
}

export function setupMenuCacheSync(refreshMenus) {
  if (syncRegistered) {
    return
  }
  syncRegistered = true

  const refresh = () => {
    if (refreshTask) {
      return refreshTask
    }
    clearMenuCache()
    refreshTask = Promise.resolve(refreshMenus({ force: true, checkCurrentRoute: true }))
      .finally(() => {
        refreshTask = null
      })
    return refreshTask
  }

  if (typeof BroadcastChannel !== 'undefined') {
    channel = new BroadcastChannel('oa-permission-menu')
    channel.onmessage = (event) => {
      if (event.data && event.data.type === 'permission_changed') {
        if (event.data.time && event.data.time === lastSyncAt) {
          return
        }
        refresh()
      }
    }
  }

  window.addEventListener('storage', (event) => {
    if (event.key === MENU_SYNC_KEY && event.newValue) {
      if (Number(event.newValue) === lastSyncAt) {
        return
      }
      refresh()
    }
  })
}
