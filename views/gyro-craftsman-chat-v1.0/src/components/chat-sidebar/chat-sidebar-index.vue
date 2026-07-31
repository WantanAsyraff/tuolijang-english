<template>
  <!-- 根据是否移动端切换组件，内部组件均为 ChatSidebar 组件，移动端增加遮罩和切换动画 -->
  <ChatSidebarMobile v-if="isMediumScreen" />
  <ChatSidebar v-else />
</template>

<script setup lang="ts">
import ChatSidebar from "./chat-sidebar.vue";
import ChatSidebarMobile from "./chat-sidebar-mobile.vue";
import { useMediumScreen } from "@/composables/ui/useMediumScreen";

const { isMediumScreen } = useMediumScreen();
</script>
