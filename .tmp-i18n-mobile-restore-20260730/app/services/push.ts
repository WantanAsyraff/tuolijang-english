import { clickNavigateTo } from "@/utils/helper";

/**
 * APP-PLUS 推送监听服务
 * register() 内部防重复注册，多次调用安全
 */
export const pushService = (() => {
  let registered = false;

  const createLocalMessage = (title: string, body: string): void => {
    try {
      const options: PlusPushMessageOptions = {
        cover: false,
        sound: "system",
        title,
      };
      plus.push.createMessage(body, "LocalMSG", options);
    } catch (e) {
      console.error("创建本地推送失败", e);
    }
  };

  const handleClick = (msg: any): void => {
    try {
      const payload = JSON.parse(JSON.parse(msg.payload));
      clickNavigateTo(payload.url);
    } catch (e) {
      console.error("解析推送点击消息失败", e);
    }
  };

  const handleReceive = (msg: any): void => {
    try {
      if (msg.aps) {
        createLocalMessage(msg.payload.title, msg.payload.body);
      } else if (msg.payload === "LocalMSG") {
        // 本地消息，忽略
      } else if (msg.payload?.type === "url") {
        clickNavigateTo(msg.payload.url);
      } else {
        createLocalMessage(msg.payload.title, msg.payload.body);
      }
    } catch (e) {
      console.error("处理推送接收消息失败", e);
    }
  };

  /**
   * 注册推送监听器（仅首次调用生效）
   */
  const register = (): void => {
    if (registered) return;
    registered = true;

    plus.push.addEventListener("click", handleClick, false);
    plus.push.addEventListener("receive", handleReceive, false);
  };

  return { register };
})();
