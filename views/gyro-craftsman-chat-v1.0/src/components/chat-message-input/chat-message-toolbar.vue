<template>
  <div class="h-34px my-10px flex gap-x-10px" @click="handleAppListClick">
    <button class="app-button" :data-event="TOOL_EVENT.CLEAR">
      <img src="@/assets/icons/clear-icon.png" class="w-16px h-16px normal-icon" />
      <img src="@/assets/icons/clear-icon-active.png" class="w-16px h-16px active-icon hidden" />
      <span class="single-line max-w-70px">{{ t("chat.clearConversation") }}</span>
    </button>

    <div class="flex-1 relative" v-if="!isAppPreview && !isAppPreviewUse">
      <div class="absolute inset-0 overflow-auto whitespace-nowrap flex gap-x-10px">
        <button v-for="app in appList" :key="app.id" class="app-button" :data-event="TOOL_EVENT.SET_APP"
          :data-app-id="app.id" :class="{ active: app.id === currentAppId }">
          <img :src="app.pic" class="w-20px h-20px rounded-50%" />
          <span class="single-line max-w-70px">{{ app.name }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { getDataSet } from "@/utils/helper";
import { useAppListStore } from "@/pinia/stores/useAppListStore";
import { storeToRefs } from "pinia";
import { useRouter, useRoute } from "vue-router";
import { ROUTE_KEY } from "@/constants/route-key";
import { isAppPreviewUse, isAppPreview } from "@/config";
import { useChatStore } from "@/pinia/stores/useChatStore";
import { useRootStore } from "@/pinia/stores/useRootStore";
import { Message } from "@/utils/message";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const route = useRoute();
const router = useRouter();
const chatStore = useChatStore();
const appListStore = useAppListStore();
const rootStore = useRootStore();

const { appList } = storeToRefs(appListStore);
const { currentAppId, currentChatId } = storeToRefs(rootStore);

const enum TOOL_EVENT {
  CLEAR = "clear",
  SET_APP = "set-app",
}

const handleClearData = () => {
  if (route.name !== ROUTE_KEY.CHAT_MAIN || !currentChatId.value) {
    Message.info(t("chat.conversationCleared"));
    return;
  };
  chatStore.clearChatMessage(currentChatId.value);
};

const handleAppListClick = (e: Event) => {
  if (!(e.target instanceof HTMLElement)) return;
  const dataset = getDataSet(e.target, ".app-button", "event", true);
  if (!dataset) return;
  if (dataset.event === TOOL_EVENT.CLEAR) {
    handleClearData();
  } else if (dataset.event === TOOL_EVENT.SET_APP) {
    router.push({
      name: ROUTE_KEY.CHAT_APP,
      params: {
        appId: dataset.appId,
      },
    });
  }
};
</script>

<style scoped lang="scss">
.app-button {
  @apply inline-flex items-center h-32px gap-5px px-8px border-1px border-solid border-#E6E8EF rounded-10px text-14px bg-white min-w-fit;
  transition: all 0.3s ease;

  &:hover {
    &[data-event="clear"] {
      .normal-icon {
        @apply hidden;
      }

      .active-icon {
        @apply display-initial;
      }
    }
  }

  &.active,
  &:hover {
    @apply primary-color;
    border-color: var(--primary-color);
  }
}
</style>
