<!-- 聊天主体区域公用容器 -->
<template>
  <div class="h-full chat-main-container">

    <!-- iframe 嵌入非全屏状态下的彩色遮罩图 -->
    <div class="chat-iframe-mask absolute top-0 left-0 w-full z--1" v-if="isIframeAndNotFullScreen && !isAppPreview" />

    <!-- 非应用预览状态下的顶部工具栏 -->
    <ChatMainTopbar v-if="!isAppPreview" />

    <!-- 聊天主体区域 -->
    <main class="h-full overflow-y-auto scroll-container" ref="mainRef" @click="handleMainClick">
      <div class="pt-60px h-full flex flex-col scroll-body"
        :class="{ 'iframe-not-full-screen': isIframeAndNotFullScreen, 'app-preview': isAppPreview }">
        <!-- 聊天主体区域内容 -->
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useIframeFullScreen } from "@/composables/iframe/useIframeFullScreen";
import { isAppPreview } from "@/config";

const mainRef = ref<HTMLElement>();

const emit = defineEmits<{
  (e: "click", event: MouseEvent): void;
}>();

const isIframeAndNotFullScreen = useIframeFullScreen();

const handleMainClick = (e: MouseEvent) => {
  emit("click", e);
};

defineExpose({
  mainRef,
});
</script>

<style scoped lang="scss">
.chat-iframe-mask {
  // 按照 mask 遮罩图来计算高度
  padding-top: calc(225 / 785 * 100%);
  background: url(@/assets/images/chat-main-iframe-mask.png) no-repeat center / cover;
}

// 非应用预览状态下的聊天主体区域，需要错开工具栏
.scroll-body {

  &.iframe-not-full-screen {
    @apply pt-90px;
  }

  &.app-preview {
    @apply pt-20px;
  }
}

.scroll-container {
  scrollbar-width: thin;
}
</style>
