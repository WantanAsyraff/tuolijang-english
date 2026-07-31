<template>
  <div class="chat-main-msg-layout bg-white">
    <div class="chat-main-layout">
      <ChatMessageToolbar />
      <ChatMessageInput v-bind="$attrs" />
    </div>
  </div>
</template>

<style scoped lang="scss">
[data-app-preview] {
  .chat-main-msg-layout {
    @apply bg-#f9fbfc;
  }
}
</style>
