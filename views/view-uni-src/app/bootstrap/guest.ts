import { splashService } from "@/app/services/splash";
// #ifdef WEB
import { wxworkAuth } from "@/libs/wxwork-auth";
import { isWxWorkEnv } from "@/libs/wxwork";
// #endif

/**
 * 未登录用户（游客）的启动流程
 */
export const bootstrapGuestLaunch = (): void => {
  // #ifdef APP-PLUS
  uni.reLaunch({
    url: "/pages/users/login/index",
    success: () => {
      splashService.closeNow();
    },
  });
  // #endif

  // #ifdef WEB
  // 企微环境未登录时进行静默登录
  // 如果进入的路径不是默认的落地页 pages/launch/index
  // 那么在对应的页面中获取数据时需要判断是否登录
  isWxWorkEnv && wxworkAuth();
  // #endif
};
