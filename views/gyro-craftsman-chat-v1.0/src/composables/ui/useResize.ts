import type { EventCallback } from "@/provider/EventProvider";
import { ResizeProvider } from "@/provider/ResizeProvider";

/**
 * 监听窗口大小变化
 * @param callback 回调函数
 */
export const useResize = (callback: EventCallback) => {
  const instance = new ResizeProvider();
  instance.addCallback(callback);

  onUnmounted(() => {
    instance?.removeCallback(callback);
  });
};
