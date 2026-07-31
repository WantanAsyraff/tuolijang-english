<template>
  <div class="text-15px leading-21px pb-40px chat-messages">
    <template v-for="(message, index) in props.messages" :key="index">
      <ChatMessageUser :message="message" v-if="message.problemText" />
      <ChatMessageAi :message="message" :is-last="index === messages.length - 1" :is-first="index === 0" />
      <slot v-if="index === 0" />
    </template>
  </div>
</template>

<script setup lang="ts">
import type { ChatMessage } from "@/types/chat";
const props = defineProps<{
  messages: ChatMessage[] | Readonly<ChatMessage[]>;
}>();
</script>

<style scoped lang="scss">
@media screen and (max-width: 990px) {
  .chat-messages {
    margin-left: 46px;
  }
}
</style>
