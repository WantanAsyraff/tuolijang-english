<template>
  <div class="w-250px border-r border-#D8D8D8 relative flex flex-col bg-white z-10 overflow-hidden sidebar-wrapper">
    <div class="w-234px z--1 h-151px bg-#CAE7FC filter-blur-89px absolute top--87px left--48px"></div>
    <div class="w-167px z--1 h-126px bg-#EBE7FD filter-blur-89px absolute top--63px left-98px"></div>

    <div class="pt-20px pl-14px flex items-center">
      <img :src="commonConfig.logo" class="w-34px h-34px mr-10px" />
      <span class="font-bold text-17px leading-24px single-line">{{ commonConfig.siteName }}</span>
    </div>

    <div class="mx-14px mt-16px">
      <router-link :to="{ name: ROUTE_KEY.CHAT_INDEX }"
        class="h-42px w-full flex items-center justify-center rounded-10px primary-color new-chat-btn text-14px leading-20px font-bold"
        @click="handleCloseSidebar">
        <i class="ai-icon ai-icon-xinjian text-14px mr-7px" />
        {{ t("sidebar.newChat") }}
      </router-link>
    </div>

    <ChatSidebarApps />
    <hr class="border-#D8D8D8" />
    <ChatSidebarRecentChat v-if="!isAppPreviewUse" />
    <ChatSidebarUserinfo />
  </div>
</template>

<script setup lang="ts">
import ChatSidebarRecentChat from "./chat-sidebar-recent-chat.vue";
import ChatSidebarApps from "./chat-sidebar-apps.vue";
import ChatSidebarUserinfo from "./chat-sidebar-userinfo.vue";
import { useSidebarStore } from "@/pinia/stores/ui/useSidebarStore";
import { ROUTE_KEY } from "@/constants/route-key";
import { useCommonStore } from "@/pinia/stores/common/useCommonStore";
import { storeToRefs } from "pinia";
import { isAppPreviewUse } from "@/config";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const { handleCloseSidebar } = useSidebarStore();

const commonStore = useCommonStore();
const { commonConfig } = storeToRefs(commonStore);
</script>

<style scoped lang="scss">
.sidebar-wrapper {
  padding-top: var(--status-bar-height);
}

.new-chat-btn {
  background: linear-gradient(329deg, #D4EAFF 1%, #ECE8FC 95%);
}
</style>
