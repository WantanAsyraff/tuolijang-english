<template>
  <Transition name="mask-fade">
    <div class="fixed inset-0 bg-black/50 z-10" v-if="isLoginDialogOpen" />
  </Transition>
  <Transition name="main-fade">
    <ChatLoginBase v-if="isLoginDialogOpen" />
  </Transition>
</template>

<script setup lang="ts">
import { useLoginDialogStore } from "@/pinia/stores/ui/useLoginDialogStore";
import { storeToRefs } from "pinia";

const loginDialogStore = useLoginDialogStore();
const { isLoginDialogOpen } = storeToRefs(loginDialogStore);
</script>

<style lang="scss" scoped>
.mask-fade-enter-active,
.mask-fade-leave-active {
  transition: opacity .3s ease;
}

.mask-fade-enter-from,
.mask-fade-leave-to {
  opacity: 0;
}

.main-fade-enter-active,
.main-fade-leave-active {
  transition: transform .2s cubic-bezier(0.25, 0.8, 0.5, 1);
}

.main-fade-enter-from,
.main-fade-leave-to {
  transform: scale(0);
}
</style>
