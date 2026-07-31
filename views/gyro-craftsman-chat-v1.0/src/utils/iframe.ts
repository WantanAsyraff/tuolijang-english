import { iframePrefix, isInWebview } from "@/config";
import type { IFRAME_ACTION } from "@/constants/iframe";

/**
 * 发送消息给 iframe 的父窗口
 * @param action 动作
 * @param data 数据
 */
export const postMessage = (action: IFRAME_ACTION, data?: any) => {
  const messageData = {
    channel: iframePrefix,
    source: "ai-chat",
    action,
    data,
  };
  if (isInWebview && "plus" in window) {
    const title = document.title;
    document.title = JSON.stringify(messageData);
    window.requestAnimationFrame(() => {
      document.title = title;
    });
  } else {
    window.parent.postMessage(messageData, {
      targetOrigin: "*"
    });
  }
};
