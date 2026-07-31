import request from "../utils/request";

/**
 * 获取企业配置
 * @return {*}
 */
export function getWxworkConfigApi(url: string) {
  return request.get("work/config", { url });
};

/**
 * 获取企业应用配置
 * @return {*}
 */
export function getWxworkAgentConfigApi(url: string) {
  console.info("[wxwork] request work/agent_config", { url });
  return request.get(`work/agent_config`, { url });
};

/**
 * 企业微信静默授权登录
 * @param code
 * @returns
 */
export function wxworkSlientAuthApi(code: string) {
  return request.post(`work/work_auth_login`, {
    code,
  });
};
