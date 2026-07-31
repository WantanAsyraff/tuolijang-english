<template>
  <ChatMainContainer @click="handleMainClick" ref="containerRef" v-loading="fetchMessageLoading">
    <div class="chat-main-layout" v-if="chat">
      <ChatMessages :messages="combinedMessageList">
        <ChatMessageSuggestion v-if="showSuggestion && suggestionConfig.length"
          @suggestion-click="handleSuggestionClick" :config="suggestionConfig" />
      </ChatMessages>
    </div>
    <ChatMessageIndex @send="handleMsgSend" :chat-status="chatStatus" />
    <ChatMessageTable ref="chatMessageTableRef" />
  </ChatMainContainer>
</template>

<script setup lang="ts">
import { useRoute } from "vue-router";
import { storeToRefs } from "pinia";
import { Message } from "@/utils/message";

import { useScroll } from "@/composables/ui/useScroll";
import { useAppSuggestion } from "@/composables/app/useAppSuggestion";
import { useAppPreviewRedirect } from "@/composables/iframe/useAppPreview";

import { STOP_GENERATE_CHAT_EVENT, TABLE_EXPAND_EVENT, REGENERATE_CHAT_EVENT } from "@/constants/dataset-key";
import { CHAT_STATUS } from "@/constants/chat";

import { useRootStore } from "@/pinia/stores/useRootStore";
import { useLoginDialogStore } from "@/pinia/stores/ui/useLoginDialogStore";
import { useUserStore } from "@/pinia/stores/useUserStore";

import { getDataSet } from "@/utils/helper";

import ChatMessageTable from "@/components/chat-messages/chat-message-table.vue";
import ChatMainContainer from "@/components/chat-main-container/chat-main-container.vue";
import { useI18n } from "vue-i18n";

import { useChatStore } from "@/pinia/stores/useChatStore";

const { t } = useI18n();
const props = defineProps<{
  id: string;
}>();

const rootStore = useRootStore();
const { currentChatInfo: chat } = storeToRefs(rootStore);

const { id: chatId } = toRefs(props);
const chatIdNum = computed(() => Number(chatId.value));

// 滚动区域容器 ref
const containerRef = ref<InstanceType<typeof ChatMainContainer>>();
// 表格 ref
const chatMessageTableRef = ref<InstanceType<typeof ChatMessageTable>>();
// 是否允许自动滚动
const userStore = useUserStore();
const loginDialogStore = useLoginDialogStore();
useAppPreviewRedirect();
const chatStore = useChatStore();
const route = useRoute();
const { messageList: suggestionMessageList, suggestionConfig } = useAppSuggestion();

const { isLogin } = storeToRefs(userStore);

const allowAutoScroll = ref(true);
const chatStatus = computed(() => chat.value?.msgInfo.status);

const showSuggestion = computed(() => {
  return route.query.suggestion === "1";
});

const combinedMessageList = computed(() => {
  if (chat.value) {
    const messageList = chat.value.msgInfo.messageList;
    return showSuggestion.value ? suggestionMessageList.value.concat(messageList) : messageList;
  }
  return [];
});

const fetchMessageLoading = computed(() => {
  if (!isLogin.value || !chat.value) return false;
  const { loadOptions, messageList } = chat.value.msgInfo;
  return loadOptions.loading && messageList.length === 0;
});

let lastScrollTop = 0;

// 滚动区域容器 ref
const scrollElRef = computed(() => containerRef.value?.mainRef);

/**
 * 监听滚动事件，当用户手动往上滚动之后，则禁止自动滚动
 * 直到用户手动滚到最底部后，则允许自动滚动
 */
useScroll(scrollElRef, () => {
  if (!scrollElRef.value) return;

  /**
   * 处理并保存上次滚动信息，来判断是否要在流式报文输出时自动滚动
   */
  const { scrollTop, scrollHeight, clientHeight } = scrollElRef.value;
  if (scrollTop < lastScrollTop) {
    allowAutoScroll.value = false;
  }
  if (scrollTop + clientHeight >= scrollHeight) {
    allowAutoScroll.value = true;
  }
  const _lastScrollTop = lastScrollTop;
  lastScrollTop = scrollTop;

  /**
   * 判断是否快滚动到顶部，加载上一页的消息
   */
  if (scrollTop < _lastScrollTop && scrollTop < 40) {
    chatStore.getChatMessage(Number(chatId.value))
      .then(() => {
        if (!scrollElRef.value) return;
        const { scrollHeight: newScrollHeight } = scrollElRef.value;
        const heightDiff = newScrollHeight - scrollHeight;
        if (heightDiff > 0) {
          scrollElRef.value.scrollTo(0, scrollTop + heightDiff);
        }
      });
  }
});

const scrollToBottom = () => {
  if (!scrollElRef.value) return;
  scrollElRef.value.scrollTo(0, scrollElRef.value.scrollHeight);
};

const handleMsgSend = (message: string) => {
  if (!isLogin.value) {
    Message.error(t("error.loginRequired"));
    loginDialogStore.handleSetLoginDialogOpen();
    return;
  }
  if (!chat || chat.value?.msgInfo.status === CHAT_STATUS.PENDING || !message.trim().length) return;

  chatStore.createMessage(chatIdNum.value, message);

  // 发送消息后，滚动到底部
  nextTick(() => {
    scrollToBottom();
  });
};

const handleSuggestionClick = (suggestion: string) => {
  handleMsgSend(suggestion);
};

// 全局统一处理，对话流中的特殊点击事件
const handleMainClick = (e: MouseEvent) => {
  if (!(e.target instanceof HTMLElement)) return;
  const event = getDataSet(e.target, "[data-event]", "event", false);

  // 如果点击的是停止生成按钮，则停止生成
  if (event === STOP_GENERATE_CHAT_EVENT) {
    chatStore.stopChatMessage(chatIdNum.value);
  } else if (event === TABLE_EXPAND_EVENT) {
    // 如果点击的是表格的展开按钮，则展开表格
    const uuid = getDataSet(e.target, ".ai-msg-container", "chatUuid", false);
    if (!uuid) return;
    chatMessageTableRef.value?.openTablePopup(uuid);
  } else if (event === REGENERATE_CHAT_EVENT) {
    // 如果点击的是重新生成按钮，则重新生成
    if (!chat || chat.value?.msgInfo.status === CHAT_STATUS.PENDING) return;
    const lastMsg = chat.value?.msgInfo.messageList[chat.value.msgInfo.messageList.length - 1];
    if (!lastMsg || !lastMsg.chatRecordUuid) return;

    chatStore.createMessage(chatIdNum.value, lastMsg.problemText, lastMsg.chatRecordUuid);
  }
};

/**
 * 监听对话id，当对话id发生变化时，允许自动滚动
 * 因为用户在上一个会话中可能触发过手动滚动，导致切换对话后无法自动滚动到底部，此时需要重置允许自动滚动的状态
 */
watch(chatIdNum, () => {
  allowAutoScroll.value = true;
});

/**
 * 监听对话消息列表，当消息列表发生变化时，如果允许自动滚动，则滚动到底部
 */
watch(combinedMessageList, async (list) => {
  if (list.length && allowAutoScroll.value) {
    await nextTick();
    scrollToBottom();
  }
}, { deep: true });

</script>
