import { http } from "@/utils/http";

/**
 * 获取应用列表
 */
export const getAppListApi = () => {
  return http.get("/chat/history/app");
};

/**
 * 获取应用信息
 * @param appId 应用 id
 */
export const getAppInfoApi = (appId: number) => {
  return http.get(`/chat/history/app_info/${appId}`);
};
