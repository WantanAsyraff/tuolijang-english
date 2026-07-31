import { getAppListApi, getAppInfoApi } from "@/api/app";
import type { App } from "@/types/app";
import { translate } from "@/locale";

class AppService {
  private appInfoCache: Map<number, App> = new Map();

  /**
   * 获取应用列表
   */
  async getAppList(): Promise<App[]> {
    const res = await getAppListApi();
    return res.data;
  }

  /**
   * 获取应用信息
   */
  async getAppInfo(appId: number): Promise<App> {
    if (this.appInfoCache.has(appId)) {
      return this.appInfoCache.get(appId) as App;
    }
    const res = await getAppInfoApi(appId);
    if (!res.data.appInfo) throw new Error(translate("error.appNotFound"));
    this.appInfoCache.set(appId, res.data.appInfo);
    return res.data.appInfo;
  }
}

export const appService = new AppService();
