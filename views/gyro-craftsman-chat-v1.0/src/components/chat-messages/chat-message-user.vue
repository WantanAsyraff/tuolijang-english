<template>
  <div class="flex flex-col items-end user-msg-container">
    <div class="rounded-12px py-13px px-16px w-fit user-msg">
      {{ props.message.problemText }}
    </div>
    <ChatMessageTools :tools="tools" class="user-msg-tools invisible" />
  </div>
</template>

<script setup lang="ts">
import { copyText } from "@/utils/helper";
import type { ChatMessage, ChatMessageTool } from "@/types/chat";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const props = defineProps<{
  message: ChatMessage;
}>();

const tools = computed<ChatMessageTool[]>(() => [
  {
    type: "copy",
    icon: "ai-icon-fuzhi1",
    text: t("common.copy"),
    handler: () => {
      copyText(props.message.problemText || "");
    }
  }
]);

</script>

<style scoped lang="scss">
.user-msg {
  background: linear-gradient(329deg, #F4F1FF 1%, #E4F1FD 94%);
}

.user-msg-container {
  &:hover {
    .user-msg-tools {
      @apply visible;
    }
  }
}
</style>
