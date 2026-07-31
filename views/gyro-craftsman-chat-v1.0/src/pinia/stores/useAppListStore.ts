import { STORE_KEY } from "@/constants/store-key";
import { defineStore } from "pinia";
import type { App } from "@/types/app";
import { appService } from "@/services/app";

type AppMap = {
  [key: number]: App;
};

export const useAppListStore = defineStore(STORE_KEY.APP_LIST_STORE, () => {
  const appList = ref<App[]>([]);

  /**
 * 应用 id -> 应用 info
 */
  const appMap = computed(() => {
    return appList.value.reduce((acc, app) => {
      acc[app.id] = app;
      return acc;
    }, {} as AppMap);
  });

  /**
   * 初始化应用列表
   */
  const initializeAppList = async () => {
    try {
      const appListResp = await appService.getAppList();
      appList.value = appListResp;
    } catch (error: any) {
      throw error;
    }
  };

  /**
   * 初始化应用信息，使用单个 App 的 id 初始化应用列表
   */
  const initializeBySingleApp = async (appId: number) => {
    try {
      const appInfoResp = await appService.getAppInfo(appId);
      appList.value = [appInfoResp];
    } catch (error: any) {
      throw error;
    }
  };

  /**
   * 重置应用列表
   */
  const resetAppList = () => {
    appList.value = [];
  };

  return {
    appList: readonly(appList),
    appMap: readonly(appMap),

    initializeAppList,
    initializeBySingleApp,
    resetAppList,
  } as const;
});
