import { defineStore } from "pinia";
import { STORE_KEY } from "@/constants/store-key";
import type { AppPreviewCache, UpdateAppPreviewData } from "@/types/app";
import type { EventData } from "@/types/iframe-event";
import { isAppPreview } from "@/config";
import { IFRAME_EVENT_TYPE } from "@/constants/iframe";
import { IframeEventProvider } from "@/provider/IframeEventProvider";

type ChatId = number;

/**
 * 应用预览缓存
 */
export const useAppPreviewCacheStore = defineStore(STORE_KEY.APP_PREVIEW_CACHE_STORE, () => {
  const appPreviewCache = ref(new Map<ChatId, AppPreviewCache>());

  // App 预览模式下监听来自 parent 窗口的事件
  // 事件内包含应用开场白等数据
  if (isAppPreview) {
    const handleIframeEvent = (event: EventData) => {
      // 缓存应用预览状态
      if (event.action === IFRAME_EVENT_TYPE.UPDATE_APP_PREVIEW_STATE && event.data) {
        const { appId, prologueText, prologueList } = event.data as UpdateAppPreviewData;
        appPreviewCache.value.set(appId, {
          appId: Number(appId),
          prologueText,
          prologueList
        });
      }
    };

    const iframeEventProvider = new IframeEventProvider();
    iframeEventProvider.addCallback(handleIframeEvent);
  }

  return {
    appPreviewCache
  };
});
