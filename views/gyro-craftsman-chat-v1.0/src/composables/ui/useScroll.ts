import type { EventCallback } from "@/provider/EventProvider";
import { ScrollProvider } from "@/provider/ScrollProvider";

/**
 * 监听滚动
 * @param callback 回调函数
 */
export const useScroll = (elRef: Ref<HTMLElement | undefined>, callback: EventCallback) => {
  let instance: ScrollProvider | null = null;

  onMounted(() => {
    if (!elRef.value) return;
    instance = new ScrollProvider(elRef.value);
    instance.addCallback(callback);
  });

  onUnmounted(() => {
    instance?.removeCallback(callback);
  });
};
