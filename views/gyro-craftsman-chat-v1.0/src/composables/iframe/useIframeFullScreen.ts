import { isInIframe, isMobile } from "@/config";
import { IFRAME_SCREEN_STATE } from "@/constants/iframe";
import { useIframeStore } from "@/pinia/stores/ui/useIframeStore";
import { storeToRefs } from "pinia";

/**
 * 判断是否在 iframe 中，并且不是全屏状态
 */
export const useIframeFullScreen = () => {
  const iframeStore = useIframeStore();
  const { iframeScreenState } = storeToRefs(iframeStore);

  const isIframeAndNotFullScreen = computed(() => {
    return isInIframe && iframeScreenState.value !== IFRAME_SCREEN_STATE.FULL_SCREEN && !isMobile;
  });

  return isIframeAndNotFullScreen;
};
