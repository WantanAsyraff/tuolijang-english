import { STORE_KEY } from "@/constants/store-key";
import { defineStore, storeToRefs } from "pinia";
import { useChatStore } from "./useChatStore";
import { useRoute } from "vue-router";
import { ROUTE_KEY } from "@/constants/route-key";
import { useUserStore } from "./useUserStore";
import { useAppListStore } from "./useAppListStore";
import { useCommonStore } from "./common/useCommonStore";
import { CHAT_STATUS } from "@/constants/chat";
import { isAppPreview, isAppPreviewUse } from "@/config";

/**
 * 根 store，管理公用状态
 */
export const useRootStore = defineStore(STORE_KEY.ROOT_STORE, () => {
  const route = useRoute();
  const userStore = useUserStore();
  const appListStore = useAppListStore();
  const chatStore = useChatStore();
  const commonStore = useCommonStore();

  const { appList } = storeToRefs(appListStore);
  const { chatById } = storeToRefs(chatStore);

  // 当前对话 ID
  const currentChatId = computed(() => {
    if (route.name === ROUTE_KEY.CHAT_MAIN) {
      return Number(route.params.id);
    }
    return;
  });

  // 当前应用 ID
  const currentAppId = computed(() => {
    if (route.name === ROUTE_KEY.CHAT_APP) {
      return Number(route.params.appId);
    } else if (route.name === ROUTE_KEY.CHAT_MAIN && currentChatId.value) {
      return chatById.value[currentChatId.value]?.appId;
    }
    return;
  });

  // 当前应用信息
  const currentAppInfo = computed(() => {
    if (currentAppId.value) {
      return appList.value.find(app => app.id === currentAppId.value);
    }
    return;
  });

  // 当前对话信息
  const currentChatInfo = computed(() => {
    if (currentChatId.value) {
      return chatById.value[currentChatId.value];
    }
    return;
  });

  /**
   * 初始化根 store
   */
  const initialize = async () => {
    userStore.initializeUserToken();
    if (userStore.isLogin) {
      return Promise.all([
        appListStore.initializeAppList(),
        chatStore.initializeChatList(),
        userStore.initializeUserInfo(),
        commonStore.initializeSiteConfig(),
      ]);
    }
  };

  /**
   * 重置根 store
   */
  const reset = () => {
    chatStore.resetChatList();
    appListStore.resetAppList();
    userStore.logout();
  };

  /**
   * 监听当前对话 ID 和当前对话信息，当 chatId 发生变化，且对话消息为空时，则要获取对话相关消息
   */
  watch([
    currentChatId,
    currentChatInfo
  ], ([chatId, chatInfo]) => {
    if (chatId && chatInfo) {
      const { status, loadOptions } = chatInfo.msgInfo;
      if (status === CHAT_STATUS.PENDING || loadOptions.loading || loadOptions.loaded) return;
      chatStore.getChatMessage(chatId);
    }
  });

  /**
   * 预览调试状态下，监听当前应用 ID 和当前应用信息，当 appId 发生变化，且应用信息不存在时，则要获取应用相关信息
   */
  if (isAppPreview || isAppPreviewUse) {
    watch([
      currentAppId,
      currentAppInfo
    ], ([appId, appInfo]) => {
      if (appId && !appInfo) {
        appListStore.initializeBySingleApp(appId);
      }
    });
  }

  return {
    currentChatId: readonly(currentChatId),
    currentAppId: readonly(currentAppId),
    currentAppInfo: shallowReadonly(currentAppInfo),
    currentChatInfo: shallowReadonly(currentChatInfo),

    initialize,
    reset,
  } as const;
});
