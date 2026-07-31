import store from '@/store';
import vm from '@/main';
import { userMenusApi } from '@/api/public';
import { CUSTOMER_MODULE_COMPONENTS } from '@/constants/customerModules';
import {
  applyMenuState,
  clearMenuCache,
  redirectIfCurrentRouteForbidden,
  restoreMenuFromCache,
  saveMenuCache
} from '@/utils/menu-cache';

export function setToken(token) {
  return store.commit('SET_TOKEN', token);
}
export function getToken() {
  return store.getters.token;
}
export function removeToken() {
  vm && vm.closeNotice();
  return store.commit('SET_TOKEN', '');
}

/**
 * 根据组件路径获取对应的模块 key
 * @param {string} component 组件路径
 * @returns {string|null} 模块 key 或 null
 */
const getModuleKeyByComponent = (component) => {
  if (!component) return null;
  for (const [moduleKey, components] of Object.entries(CUSTOMER_MODULE_COMPONENTS)) {
    if (components.includes(component)) {
      return moduleKey;
    }
  }
  return null;
};

/**
 * 过滤菜单，移除未开启模块的菜单项
 * 采用后序遍历：先递归过滤 children，再判断当前节点
 * @param {Array} menus 菜单列表
 * @returns {Array} 过滤后的菜单列表
 */
const filterMenusByModule = (menus) => {
  const isCustomerModuleEnabled = store.getters['appConfig/isCustomerModuleEnabled'];

  const filterMenu = (menuList) => {
    return menuList
      .map((menu) => {
        // 先递归过滤 children
        if (menu.children && menu.children.length > 0) {
          const filteredChildren = filterMenu(menu.children);
          return { ...menu, children: filteredChildren };
        }
        return { ...menu, children: [] };
      })
      .filter((menu) => {
        // 判断条件一：若 menu.component 非空，检查是否属于某个模块且该模块未开启
        if (menu.component) {
          const moduleKey = getModuleKeyByComponent(menu.component);
          if (moduleKey && !isCustomerModuleEnabled(moduleKey)) {
            return false;
          }
        }
        // 判断条件二：若 menu.component 为空（纯菜单节点）且过滤后 children.length === 0，则移除
        if (!menu.component && (!menu.children || menu.children.length === 0)) {
          return false;
        }
        return true;
      });
  };

  return filterMenu(JSON.parse(JSON.stringify(menus)));
};

function fetchAppConfig() {
  return store.dispatch('appConfig/fetchConfig')
    .catch(() => {
      console.warn('[appConfig] 获取应用配置失败，使用默认配置');
      return null;
    });
}

// 获取菜单权限
export const getMenus = ({ force = false, checkCurrentRoute = false } = {}) => {
  return new Promise((resolve, reject) => {
    if (!force && restoreMenuFromCache()) {
      fetchAppConfig().then(() => {
        resolve();
      });
      return;
    }

    // 先获取应用配置（通过 Vuex 集中管理，自带缓存和去重）
    fetchAppConfig()
      .then(() => {
        // 配置已写入 store，isCustomerModuleEnabled getter 会实时派生
        return userMenusApi();
      })
      .then((response) => {
        const { menu: rawMenu, roles } = response.data;
        // 根据模块配置过滤菜单
        const menu = filterMenusByModule(rawMenu);
        applyMenuState(menu, roles); // roles 为按钮权限
        saveMenuCache(menu, roles);
        if (checkCurrentRoute) {
          redirectIfCurrentRouteForbidden();
        }

        resolve();
      })
      .catch((error) => {
        clearMenuCache();
        reject();
      });
  });
};
