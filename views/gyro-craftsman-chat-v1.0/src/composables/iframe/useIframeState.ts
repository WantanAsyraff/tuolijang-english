import { useIframeStore } from "@/pinia/stores/ui/useIframeStore";
import { IFRAME_ACTION, IFRAME_SCREEN_STATE } from "@/constants/iframe";
import { postMessage } from "@/utils/iframe";

export const useIframeState = () => {
  const iframeStore = useIframeStore();

  // 发送消息给 iframe 的父窗口，调整 iframe 的窗口大小
  const handleSetIframeScreenState = (state: IFRAME_SCREEN_STATE) => {
    iframeStore.setIframeScreenState(state);
    postMessage(IFRAME_ACTION.SET_IFRAME_SCREEN_STATE, {
      state,
    });
  };

  return {
    handleSetIframeScreenState,
  };
};
