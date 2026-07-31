<template>
  <ChatMainContainer>
    <template v-if="messageList?.[0]?.answerText">
      <div class="chat-main-layout">
        <ChatMessages :messages="messageList">
          <ChatMessageSuggestion v-if="suggestionConfig.length" @suggestion-click="handleCreateChat"
            :config="suggestionConfig" />
        </ChatMessages>
      </div>
      <ChatMessageIndex @send="handleCreateChat" :chat-status="chatStatus" />
    </template>
    <ChatWelcome :app-id="appId" v-else />
  </ChatMainContainer>
</template>

<script setup lang="ts">
import { CHAT_STATUS } from "@/constants/chat";
import { useAppSuggestion } from "@/composables/app/useAppSuggestion";
import { useRouter } from "vue-router";
import { ROUTE_KEY } from "@/constants/route-key";
import { INJECT_KEY } from "@/constants/inject-key";
import { useProvideLoading } from "@/composables/ui/useChatCreateLoading";
import { useChatStore } from "@/pinia/stores/useChatStore";
import { handleError } from "@/utils/error-handler";

const props = defineProps<{
  appId: string;
}>();

const { appId } = toRefs(props);

const router = useRouter();
const chatStore = useChatStore();

const currentAppId = computed(() => Number(appId.value));

const chatStatus = ref<CHAT_STATUS>(CHAT_STATUS.READY);
const { messageList, suggestionConfig } = useAppSuggestion();

const { isLoading: isCreateChatLoading, setLoading } = useProvideLoading(INJECT_KEY.CHAT_READY_LOADING);

const handleCreateChat = async (msg: string) => {
  if (isCreateChatLoading.value || !msg.trim().length) return;
  setLoading(true);
  const nowAppId = currentAppId.value;

  // 判断创建会话成功时的页面是否还是会话对应的 App 页面
  // 如果在创建会话的过程中用户切换到了其他应用页面，则不进行后续处理
  const isEqualAppId = () => nowAppId === currentAppId.value;
  try {
    const chatId = await chatStore.createChat(msg, nowAppId);
    if (isEqualAppId()) {
      router.push({
        name: ROUTE_KEY.CHAT_MAIN,
        params: {
          id: chatId,
        },
        query: {
          suggestion: 1
        }
      });
    }
  } catch (error: any) {
    if (isEqualAppId()) {
      handleError(error);
    }
  } finally {
    if (isEqualAppId()) {
      setLoading(false);
    }
  }
};

watch(currentAppId, () => {
  if (isCreateChatLoading.value) {
    setLoading(false);
  }
});

</script>
