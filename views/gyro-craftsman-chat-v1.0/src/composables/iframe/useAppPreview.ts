import { isAppPreview } from "@/config";
import { IFRAME_EVENT_TYPE } from "@/constants/iframe";
import { ROUTE_KEY } from "@/constants/route-key";
import { IframeEventProvider } from "@/provider/IframeEventProvider";
import type { EventData } from "@/types/iframe-event";
import { useRouter } from "vue-router";
import type { UpdateAppPreviewData } from "@/types/app";

/**
 * 应用预览模式下，当开场白发生变化时，重定向到应用详情页
 */
export const useAppPreviewRedirect = () => {
  if (!isAppPreview) return;
  const router = useRouter();

  const handleIframeEvent = (event: EventData) => {
    if (event.action === IFRAME_EVENT_TYPE.UPDATE_APP_PREVIEW_STATE && event.data) {
      const { appId } = event.data as UpdateAppPreviewData;
      router.replace({
        name: ROUTE_KEY.CHAT_APP,
        params: {
          appId
        }
      });
    }
  };
  const iframeEventProvider = new IframeEventProvider();
  iframeEventProvider.addCallback(handleIframeEvent);

  onUnmounted(() => {
    iframeEventProvider.removeCallback(handleIframeEvent);
  });
};
