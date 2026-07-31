import store from "@/store";
import { socketService } from "@/app/services/socket";
import { splashService } from "@/app/services/splash";
import { ensureValidAccessToken } from "@/utils/request";
import { messageCateApi } from "@/api/user";
import { toLogin } from "@/libs/login";

/**
 * onShow 唤醒逻辑
 * 应用从后台恢复时，确保 WebSocket 连接 + 启动页兜底关闭
 */
export const resumeApp = (): void => {
  if (store.state.app.isLogin) {
    ensureValidAccessToken()
      .then(() => {
        const token = store.state.app.token;
        if (token) {
          socketService.ensureConnected(token);
        }
      })
      .catch(() => {
        toLogin({ forceLogout: true });
      });
  }

  splashService.startFallback();
};

export const getNum = () => {
  messageCateApi().then((res: any) => {
    const arr = res.data;
    let num = 0;

    arr.map((item: any) => {
      num += Number(item.count);
    });

    if (num === 0) {
      uni.removeTabBarBadge({
        index: 1,
      });
    } else {
      uni.setTabBarBadge({
        index: 1, // 人脉页面在底部菜单栏的索引
        text: num > 99 ? "99+" : num + "", // 要显示的文本（必须是字符串类型）
      });
    }
  });
};
