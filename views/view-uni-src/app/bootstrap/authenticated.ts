import store from "@/store";
import { splashService } from "@/app/services/splash";
import { socketService } from "@/app/services/socket";
// #ifdef APP-PLUS
import { pushService } from "@/app/services/push";
// #endif
import { autoLoad } from "@/utils/autoload";

/**
 * 已登录用户的启动流程（onLaunch 阶段）
 * WebSocket 不在此处初始化，而是在 onShow（resumeApp）中初始化，与原始时序一致
 */
export const bootstrapAuthenticatedLaunch = (): void => {
  // #ifdef APP-PLUS
  splashService.closeNow();
  pushService.register();
  // #endif

  autoLoad();
};

/**
 * 登录成功后的会话初始化
 * 与 bootstrapAuthenticatedLaunch 不同：此函数在 loginSuccess() 中调用，
 * 由于不会触发 App.vue 的 onShow，需要主动建立 WebSocket 连接
 */
export const bootstrapAuthenticatedSession = (): void => {
  bootstrapAuthenticatedLaunch();

  const token = store.state.app.token;
  if (token) {
    socketService.ensureConnected(token);
  }
};
