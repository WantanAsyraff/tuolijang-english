import { getCleanWxWorkRedirectUrl, getWxWorkOAuthRedirectUrl, wxworkSilentAuth } from "@/libs/wxwork-oauth";
import store from "@/store";
import { loginSuccess, toLogin } from "./login";
import message from "@/utils/message";

/**
 * 企业微信 OAuth 流程编排层
 * 只负责读取 URL 参数、触发 code 换 token、更新登录态并回跳到业务页面。
 */

let wxworkAuthPromise: Promise<void> | null = null;

/**
 * 执行企业微信静默登录流程：
 * 1. 有 code 时完成业务登录并回到原页面；
 * 2. 无 code 时发起企业微信 OAuth 跳转。
 */
export async function wxworkAuth() {
  if (wxworkAuthPromise) return wxworkAuthPromise;

  wxworkAuthPromise = runWxworkAuth().finally(() => {
    wxworkAuthPromise = null;
  });

  return wxworkAuthPromise;
};

async function runWxworkAuth() {
  const redirectUrl = getCleanWxWorkRedirectUrl();
  try {
    const urlParams = new URLSearchParams(location.search);
    const code = urlParams.get("code");
    if (code) {
      const tokenInfo = await wxworkSilentAuth(code);
      store.commit("login", tokenInfo);
      await loginSuccess(false);

      // 回到原业务地址，仅移除企微授权注入的参数
      location.replace(`${redirectUrl.pathname}${redirectUrl.search}`);
    } else {
      location.replace(await getWxWorkOAuthRedirectUrl(redirectUrl.toString()));
    }
  } catch (error: any) {
    message.error(error.message);
    toLogin({ forceManualLogin: true, forceLogout: true });
  }
}
