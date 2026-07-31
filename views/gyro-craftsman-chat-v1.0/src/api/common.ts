import { http } from "@/utils/http";

/**
 * 获取通用配置
 * @returns
 */
export const getCommonConfigApi = () => {
  return http.get("/common/site_address");
};
