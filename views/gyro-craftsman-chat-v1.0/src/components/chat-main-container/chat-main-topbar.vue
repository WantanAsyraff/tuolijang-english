<template>
  <div
    class="chat-main-topbar absolute top-0 left-0 w-full flex items-center pl-24px pr-20px z-1 chat-main-topbar"
    :class="{ 'iframe-not-full-screen': isIframeAndNotFullScreen }">
    <!-- logo区域 -->
    <div class="flex items-center overflow-hidden" v-if="isIframeAndNotFullScreen">
      <img src="@/assets/images/logo.png" class="w-28px h-28px mr-10px" />
      <span class="font-bold text-17px leading-24px single-line">{{ commonConfig.siteName }}</span>
    </div>

    <!-- 非嵌入模式下，移动端显示侧边栏按钮 -->
    <button class="flex items-center" v-if="isSideBarBtnVisible" @click="handleOpenSidebar">
      <i-ep-operation class="text-20px" />
    </button>

    <!-- 嵌入模式下，iframe 缩放按钮 -->
    <div class="flex ml-auto gap-18px text-#606266" v-if="isInIframe">
      <button class="toolbar-btn" @click="handleSetIframeLevel" v-if="!isMobile">
        <i class="text-16px ai-icon ai-icon-shouqi1" v-if="iframeScreenState === IFRAME_SCREEN_STATE.FULL_SCREEN" />
        <i class="text-16px ai-icon ai-icon-zhankai2" v-else />
      </button>
      <button class="toolbar-btn" @click="handleSetIframeMiniScreen">
        <i class="text-14px ai-icon ai-icon-cha1" />
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { storeToRefs } from "pinia";
import { isInIframe, isMobile } from "@/config";
import { useMediumScreen } from "@/composables/ui/useMediumScreen";
import { IFRAME_SCREEN_STATE } from "@/constants/iframe";
import { useSidebarStore } from "@/pinia/stores/ui/useSidebarStore";
import { useIframeStore } from "@/pinia/stores/ui/useIframeStore";
import { useIframeFullScreen } from "@/composables/iframe/useIframeFullScreen";
import { useCommonStore } from "@/pinia/stores/common/useCommonStore";
import { useIframeState } from "@/composables/iframe/useIframeState";
const { isMediumScreen } = useMediumScreen();
const { handleOpenSidebar } = useSidebarStore();
const commonStore = useCommonStore();
const { commonConfig } = storeToRefs(commonStore);

const iframeStore = useIframeStore();
const { iframeScreenState } = storeToRefs(iframeStore);
const { handleSetIframeScreenState } = useIframeState();
const isIframeAndNotFullScreen = useIframeFullScreen();

const isSideBarBtnVisible = computed(() => {
  return (!isInIframe && isMediumScreen.value) || isMobile;
});

const handleSetIframeLevel = () => {
  if (iframeScreenState.value === IFRAME_SCREEN_STATE.FULL_SCREEN) {
    handleSetIframeScreenState(IFRAME_SCREEN_STATE.MEDIUM_SCREEN);
  } else if (iframeScreenState.value === IFRAME_SCREEN_STATE.MEDIUM_SCREEN) {
    handleSetIframeScreenState(IFRAME_SCREEN_STATE.FULL_SCREEN);
  }
};

const handleSetIframeMiniScreen = () => {
  handleSetIframeScreenState(IFRAME_SCREEN_STATE.MINI_SCREEN);
};

</script>

<style scoped lang="scss">
.chat-main-topbar {
  padding-top: var(--status-bar-height);
  height: calc(var(--status-bar-height) + 60px);

  &.iframe-not-full-screen {
    @apply border-b border-#DCDFE6 border-solid;
    background: url(@/assets/images/chat-main-iframe-mask.png) no-repeat left top / 100% auto;
  }
}

.toolbar-btn {
  @apply flex items-center justify-center w-30px h-30px rounded-4px hover:bg-#F3F4F5;
}
</style>
