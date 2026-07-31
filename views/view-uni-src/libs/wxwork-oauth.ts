import { wxworkSlientAuthApi } from "@/api/wxwork";
import { loadWxWorkAuthConfig, type WxWorkAuthConfig } from "@/libs/wxwork-config";

/**
 * 企业微信 OAuth 工具层
 * 只负责 OAuth 相关的接口请求、URL 处理和授权地址构造，不参与登录状态编排，也不初始化 JS-SDK。
 */

/**
 * 根据企业微信应用标识和目标页面地址，生成 OAuth 静默授权地址。
 */
function buildWxWorkAuthUrl({ corpid, agentid }: WxWorkAuthConfig, targetUrl: string) {
  const urlSearchParams = new URLSearchParams({
    appid: corpid,
    redirect_uri: targetUrl,
    response_type: "code",
    scope: "snsapi_base",
    agentid: String(agentid)
  });

  return `https://open.weixin.qq.com/connect/oauth2/authorize?${urlSearchParams.toString()}#wechat_redirect`;
}

/**
 * 将业务页面地址规整为获取 OAuth 基础配置时使用的签名前地址。
 * 这里会移除 query/hash，避免未登录阶段因为业务参数变化引入额外干扰。
 */
function getAuthConfigRequestUrl(targetUrl: string) {
  const url = new URL(targetUrl, location.origin);
  url.hash = "";
  url.search = "";
  return url.toString();
}

/**
 * 清理当前地址中的企微授权参数，保留业务路径和原始查询参数。
 */
export function getCleanWxWorkRedirectUrl(currentUrl: string = location.href) {
  const redirectUrl = new URL(currentUrl);
  redirectUrl.hash = "";
  redirectUrl.searchParams.delete("code");
  redirectUrl.searchParams.delete("state");
  return redirectUrl;
}

/**
 * 调用后端接口获取 corpId/agentId，并拼出最终的企业微信 OAuth 跳转地址。
 */
export async function getWxWorkOAuthRedirectUrl(targetUrl: string = window.location.href.split("#")[0]) {
  const authConfig = await loadWxWorkAuthConfig(getAuthConfigRequestUrl(targetUrl));
  return buildWxWorkAuthUrl(authConfig, targetUrl);
}

/**
 * 使用企业微信回调 code 向后端换取业务系统登录态。
 */
export async function wxworkSilentAuth(code: string) {
  const res = await wxworkSlientAuthApi(code);
  if (res.status === 200) {
    return res.data;
  }

  throw new Error(res.message);
}
