import { defineStore } from "pinia";
import { STORE_KEY } from "@/constants/store-key";
import { IFRAME_SCREEN_STATE } from "@/constants/iframe";

const getInitialScreenState = () => {
  return IFRAME_SCREEN_STATE.MINI_SCREEN;
};

/**
 * 提供嵌入模式下的屏幕状态
 */
export const useIframeStore = defineStore(STORE_KEY.IFRAME_STORE, () => {
  const iframeScreenState = ref(getInitialScreenState());

  const setIframeScreenState = (state: IFRAME_SCREEN_STATE) => {
    iframeScreenState.value = state;
  };

  return {
    iframeScreenState,
    setIframeScreenState,
  };
});
