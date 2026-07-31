import { storeToRefs } from "pinia";
import { useIframeStore } from "@/pinia/stores/ui/useIframeStore";
import { isInIframe, isMobile } from "@/config";
import type { EventData } from "@/types/iframe-event";
import { IframeEventProvider } from "@/provider/IframeEventProvider";
import { IFRAME_ACTION, IFRAME_EVENT_TYPE, IFRAME_SCREEN_STATE } from "@/constants/iframe";
import { useRouter } from "vue-router";
import { ROUTE_KEY } from "@/constants/route-key";
import { useIframeState } from "./useIframeState";
import { useAppListStore } from "@/pinia/stores/useAppListStore";
import { postMessage } from "@/utils/iframe";

export const useIframeMsgHandler = () => {
  if (!isInIframe) return;

  const iframeStore = useIframeStore();
  const { iframeScreenState } = storeToRefs(iframeStore);
  const appListStore = useAppListStore();

  const { handleSetIframeScreenState } = useIframeState();
  const router = useRouter();

  // 显示 iframe 窗口
  const handleShowIframe = () => {
    const state = isMobile ? IFRAME_SCREEN_STATE.FULL_SCREEN : IFRAME_SCREEN_STATE.MEDIUM_SCREEN;
    handleSetIframeScreenState(state);
  };

  // 打开应用
  const handleOpenApp = (appId: number) => {
    router.push({
      name: ROUTE_KEY.CHAT_APP,
      params: {
        appId,
      },
    });

    if (iframeScreenState.value === IFRAME_SCREEN_STATE.MINI_SCREEN) {
      handleShowIframe();
    }
  };

  // 监听 iframe 的父窗口发送的消息
  const iframeEventProvier = new IframeEventProvider();
  iframeEventProvier.addCallback((event: EventData) => {
    switch (event.action) {
      case IFRAME_EVENT_TYPE.SHOW_IFRAME:
        handleShowIframe();
        break;
      case IFRAME_EVENT_TYPE.SET_MINIMIZE:
        handleSetIframeScreenState(IFRAME_SCREEN_STATE.MINI_SCREEN);
        break;
      case IFRAME_EVENT_TYPE.OPEN_APP:
        handleOpenApp(event.data.appId);
        break;
      case IFRAME_EVENT_TYPE.REFRESH_APP_LIST:
        appListStore.initializeAppList();
        break;
    }
  });

  // 监听已挂载，通知父窗口 iframe 已就绪，可补发懒加载期间积压的消息（show-iframe / open-app 等）
  postMessage(IFRAME_ACTION.IFRAME_READY);
};
