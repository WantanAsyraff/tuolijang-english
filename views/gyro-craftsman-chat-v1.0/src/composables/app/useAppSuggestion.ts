import { isAppPreview } from "@/config";
import { CHAT_MESSAGE_STATUS } from "@/constants/chat";
import type { ChatMessage } from "@/types/chat";
import { storeToRefs } from "pinia";
import { useAppPreviewCacheStore } from "@/pinia/stores/useAppPreviewCacheStore";
import { getRandomNumbers } from "@/utils/helper";
import { useRootStore } from "@/pinia/stores/useRootStore";

/**
 * 获取应用的提示词
 * @returns 提示词列表
 */
export const useAppSuggestion = () => {
  const rootStore = useRootStore();

  const { currentAppInfo, currentAppId } = storeToRefs(rootStore);
  const appPreviewCacheStore = useAppPreviewCacheStore();
  const { appPreviewCache } = storeToRefs(appPreviewCacheStore);

  // 根据应用提示词生成一条临时对话记录
  const messageList = computed<ChatMessage[]>(() => {
    let answerText = "";
    const appCacheInfo = isAppPreview && currentAppId.value ? appPreviewCache.value.get(currentAppId.value) : null;

    if (appCacheInfo) {
      answerText = appCacheInfo.prologueText;
    } else if (currentAppInfo.value) {
      answerText = currentAppInfo.value.prologue_text;
    } else {
      return [];
    }

    return [
      {
        status: CHAT_MESSAGE_STATUS.SUCCESS,
        answerText,
        problemText: "",
        chatRecordUuid: "",
        isSuggest: true
      }
    ];
  });

  /**
   * 获取应用的提示词
   */
  const suggestionConfig = computed<readonly string[]>(() => {
    if (isAppPreview && currentAppId.value) {
      const appCacheInfo = appPreviewCache.value.get(currentAppId.value);
      if (appCacheInfo) {
        return appCacheInfo.prologueList;
      }
    }
    return currentAppInfo.value?.prologue_list || [];
  });

  /**
   * 从提示词列表中随机取3条
   */
  const randomSuggestionConfig = computed<readonly string[]>(() => {
    const suggestionTargetCount = 3;
    if (suggestionConfig.value.length <= suggestionTargetCount) return suggestionConfig.value;
    const randomIndex = getRandomNumbers(suggestionConfig.value.length, suggestionTargetCount);
    return randomIndex.map(index => suggestionConfig.value[index]);
  });

  return {
    messageList,
    suggestionConfig: randomSuggestionConfig
  };
};
