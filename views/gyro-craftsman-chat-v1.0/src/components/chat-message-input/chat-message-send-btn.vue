<template>
  <button @click="handleBtnClick" class="w-34px h-34px send-btn relative" :class="{ 'is-focus': props.isFocus }"
    :data-status="props.chatStatus" v-loading="isLoading" :data-event="STOP_GENERATE_CHAT_EVENT">
    <div
      class="flex absolute top-50% left-50% -translate-x-50% -translate-y-50% gap-2px bg-white p-1px pointer-events-none"
      v-if="props.chatStatus === CHAT_STATUS.PENDING">
      <div class="ani-dot" v-for="i in 3" :key="i" :data-index="i"></div>
    </div>
  </button>
</template>

<script setup lang="ts">
import { CHAT_STATUS } from "@/constants/chat";
import { INJECT_KEY } from "@/constants/inject-key";
import { useInjectLoading } from "@/composables/ui/useChatCreateLoading";
import { STOP_GENERATE_CHAT_EVENT } from "@/constants/dataset-key";

interface Props {
  isFocus: boolean;
  chatStatus: CHAT_STATUS;
}

const emit = defineEmits<{
  (e: "send"): void;
}>();

const props = defineProps<Props>();

const isLoading = useInjectLoading(INJECT_KEY.CHAT_READY_LOADING);

const handleBtnClick = (e: MouseEvent) => {
  if (isLoading.value) return;
  if (props.chatStatus === CHAT_STATUS.PENDING) return;
  e.stopPropagation();
  emit("send");
};

</script>

<style scoped lang="scss">
@keyframes send-btn-pending {
  0% {
    opacity: 0;
  }

  100% {
    opacity: 1;
  }
}

.ani-dot {
  @apply w-5px h-5px rounded-full;
  background-image: linear-gradient(to bottom, #A496FF 0%, #A496FF 20%, #1890ff 100%);
  animation: send-btn-pending .6s infinite;

  @for $i from 1 through 3 {
    &[data-index="#{$i}"] {
      animation-delay: #{$i * 0.2}s;
    }
  }
}

.send-btn {
  background: url("@/assets/images/msg-send-btn-ready.png") no-repeat center / contain;
  transition: background-image 0.2s ease-in-out;

  &.is-focus {
    background-image: url("@/assets/images/msg-send-btn-ready-focus.png");
  }

  &[data-status="pending"] {
    background-image: url("@/assets/images/msg-send-btn-pending.png");
  }

  :deep(.el-loading-spinner) {
    margin-left: -2px;
    --el-loading-spinner-size: 38px;
  }
}
</style>
