import store from "@/store";
import { syncQuestionnaireContext } from "@/app/bootstrap/context";
import { bootstrapAuthenticatedLaunch } from "@/app/bootstrap/authenticated";
import { bootstrapGuestLaunch } from "@/app/bootstrap/guest";
import { checkVersion } from "@/utils/version";

/**
 * onLaunch 总入口 — 启动编排器
 * 负责初始化 session、处理启动参数、按登录状态分流
 */
export const bootstrapOnLaunch = (options: App.LaunchShowOption): void => {
  // #ifdef WEB
  syncQuestionnaireContext(options);
  // #endif

  store.commit("init");

  // #ifdef APP-PLUS
  checkVersion();
  // #endif

  if (store.state.app.isLogin) {
    bootstrapAuthenticatedLaunch();
  } else {
    bootstrapGuestLaunch();
  }
};
