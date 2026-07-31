<template>
  <Transition name="sidebar-mask">
    <div class="fixed inset-0 bg-#000/50 z-2" v-if="isSidebarVisible" @click.self="handleCloseSidebar"></div>
  </Transition>
  <Transition name="sidebar">
    <ChatSidebar v-show="isSidebarVisible" class="mobile-sidebar" />
  </Transition>
</template>

<script setup lang="ts">
import { storeToRefs } from "pinia";
import ChatSidebar from "./chat-sidebar.vue";
import { useSidebarStore } from "@/pinia/stores/ui/useSidebarStore";

const sidebarStore = useSidebarStore();
const { isSidebarVisible } = storeToRefs(sidebarStore);
const { handleCloseSidebar } = sidebarStore;
</script>

<style scoped lang="scss">
.sidebar-mask-enter-active,
.sidebar-mask-leave-active {
  transition: opacity 0.3s ease;
}

.sidebar-mask-enter-from,
.sidebar-mask-leave-to {
  opacity: 0;
}

.sidebar-enter-active,
.sidebar-leave-active {
  transition: transform 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateX(-100%);
}

.mobile-sidebar {
  @apply fixed top-0 left-0 bottom-0;
}
</style>
