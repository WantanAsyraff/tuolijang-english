<template>
  <div class="ai-msg-container relative" :data-chat-uuid="message.chatRecordUuid">
    <div class="absolute left--46px top--4px">
      <img :src="currentAppInfo?.pic" v-if="currentAppInfo?.pic" class="w-30px h-30px rounded-full" />
      <img src="@/assets/images/logo.png" v-else class="w-30px h-30px rounded-full" />
    </div>
    <div class="leading-25px break-all md-render-container" :class="{ 'has-page-table': allowShowTableExpand }"
      :data-chat-uuid="message.chatRecordUuid">
      <div class="flex items-center" v-if="message.status === CHAT_MESSAGE_STATUS.PENDING">
        {{ t("chat.thinking") }}
        <div class="ml-8px inline-flex items-center is-loading">
          <i-ep-loading class="text-15px" />
        </div>
      </div>
      <div v-else-if="message.status === CHAT_MESSAGE_STATUS.ERROR" class="text-red-500">
        {{ t("chat.errorLabel") }} {{ message.errorText }}
      </div>
      <div v-if="message.thinkingList?.length" class="thinking-box">
        <div class="thinking-title">{{ t("chat.reasoning") }}</div>
        <div
          v-for="(item, index) in message.thinkingList"
          :key="index"
          class="thinking-item"
          :class="{ 'is-error': item.stage === 'tool_error' }"
        >
          <span v-if="item.toolName" class="thinking-tool">{{ item.toolName }}</span>
          <span class="thinking-content">{{ item.content }}</span>
        </div>
      </div>
      <div class="md-answer-container" :data-chat-uuid="message.chatRecordUuid">
        <ChatMarkdown v-if="message.answerText" :content="message.answerText" />
      </div>
    </div>
    <button v-if="allowShowStopBtn" class="text-14px leading-20px primary-color my-12px"
      :data-event="STOP_GENERATE_CHAT_EVENT">{{ t("chat.stopGenerating") }}</button>
    <ChatMessageTools v-if="allowShowToolbar" :tools="filterTools" class="ai-msg-tools"
      :class="{ 'invisible': !isLast }">
      <button v-if="allowShowTableExpand" class="inline-flex items-center cursor-pointer h-24px px-6px primary-color"
        :data-event="TABLE_EXPAND_EVENT">
        {{ t("chat.moreData") }}
        <i class="ai-icon ai-icon-jinru1 text-10px ml-6px" />
      </button>
    </ChatMessageTools>
  </div>
</template>

<script setup lang="ts">
import { copyText } from "@/utils/helper";
import { CHAT_MESSAGE_STATUS } from "@/constants/chat";
import { REGENERATE_CHAT_EVENT, STOP_GENERATE_CHAT_EVENT } from "@/constants/dataset-key";
import type { ChatMessage, ChatMessageTool } from "@/types/chat";
import { TABLE_EXPAND_EVENT } from "@/constants/dataset-key";
import { storeToRefs } from "pinia";
import { useRootStore } from "@/pinia/stores/useRootStore";
import { useI18n } from "vue-i18n";

type Props = {
  message: ChatMessage;
  isLast: boolean;
  isFirst: boolean;
};

type Tool = ChatMessageTool & {
  filter?: () => boolean;
};

const { message, isLast } = defineProps<Props>();
const { t } = useI18n();

const rootStore = useRootStore();
const { currentChatId, currentAppInfo } = storeToRefs(rootStore);

const allowShowStopBtn = computed(() => {
  return [
    CHAT_MESSAGE_STATUS.PENDING,
    CHAT_MESSAGE_STATUS.LOADING
  ].includes(message.status) && isLast;
});

const allowShowToolbar = computed(() => {
  if (message.isSuggest) return false;
  return [CHAT_MESSAGE_STATUS.SUCCESS, CHAT_MESSAGE_STATUS.ERROR].includes(message.status) && currentChatId.value;
});

const allowShowTableExpand = computed(() => {
  return message.hasPageTable && [CHAT_MESSAGE_STATUS.SUCCESS, CHAT_MESSAGE_STATUS.ERROR].includes(message.status);
});

const tools = computed<Tool[]>(() => [
  {
    type: "re-generate",
    event: REGENERATE_CHAT_EVENT,
    icon: "ai-icon-luntan-huanyipi",
    text: t("chat.regenerate"),
    filter: () => {
      const statusList = [CHAT_MESSAGE_STATUS.SUCCESS, CHAT_MESSAGE_STATUS.ERROR];

      // 如果消息是成功或错误状态（不在接收响应的状态下），最后一个消息，并且有 chatRecordUuid，则显示重新生成按钮
      return statusList.includes(message.status) && isLast && !!message.chatRecordUuid;
    }
  },
  {
    type: "copy",
    icon: "ai-icon-fuzhi1",
    text: t("common.copy"),
    handler: () => {
      const el = document.querySelector<HTMLDivElement>(`.md-answer-container[data-chat-uuid="${message.chatRecordUuid}"]`);
      if (!el) return;
      copyText(el.innerText || "");
    },
    filter: () => {
      const statusList = [CHAT_MESSAGE_STATUS.SUCCESS, CHAT_MESSAGE_STATUS.ERROR];
      return statusList.includes(message.status);
    }
  }
]);

const filterTools = computed(() => {
  return tools.value.filter((tool) => {
    if (!tool.filter) return true;
    return tool.filter();
  });
});

</script>

<style lang="scss">
@use "@/styles/md-result.scss";
</style>

<style lang="scss" scoped>
@keyframes rotating {
  0% {
    transform: rotate(0)
  }

  to {
    transform: rotate(360deg)
  }
}

.ai-msg-container {
  &:hover {
    .ai-msg-tools {
      @apply visible;
    }
  }
}

.is-loading {
  animation: rotating 2s linear infinite;
}

.thinking-box {
  margin-bottom: 8px;
  padding: 8px 10px;
  border-left: 3px solid #d0d7de;
  border-radius: 4px;
  color: #667085;
  background: #f8fafc;
  font-size: 13px;
  line-height: 20px;
}

.thinking-title {
  margin-bottom: 4px;
  font-weight: 500;
  color: #475467;
}

.thinking-item {
  overflow-wrap: break-word;
  word-break: break-word;

  & + & {
    margin-top: 4px;
  }

  &.is-error {
    color: #b42318;
  }
}

.thinking-content {
  white-space: pre-wrap;
}

.thinking-tool {
  display: inline-flex;
  align-items: center;
  height: 18px;
  margin-right: 6px;
  padding: 0 5px;
  border-radius: 3px;
  background: #eef2f6;
  color: #475467;
  font-size: 12px;
  line-height: 18px;
  vertical-align: 1px;
}
</style>
