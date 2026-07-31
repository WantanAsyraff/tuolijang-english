<template>
  <div
    class="msg-input-container relative pt-14px pb-12px pl-12px pr-14px rounded-22px bg-white bg-clip-padding cursor-text"
    :class="{ 'is-foucs': isFocus, mini: mini }" @click="handleInputContainerClick">
    <el-input v-model="inputMsg" resize="none" type="textarea" :placeholder="t('chat.inputPlaceholder')" :autosize="autoSizeConfig"
      @focus="handleFoucs" @blur="handleBlur" @keydown.enter="handleEnter" class="msg-input" ref="textareaRef" />
    <div class="flex justify-between items-end pt-10px tool-wrapper cursor-initial" :class="{ mini: mini }">
      <!-- Reserved for file upload tools. -->
      <!-- <ChatMessageUpload /> -->
      <!-- <div class="h-21px w-1px bg-#D8D8D8 mx-12px" v-if="mini" /> -->
      <ChatMessageSendBtn class="ml-auto" :isFocus="isFocus" :chat-status="chatStatus" @send="handleSend" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFocus } from "@/composables/ui/useFocus";
import { useIframeStore } from "@/pinia/stores/ui/useIframeStore";
import { CHAT_STATUS } from "@/constants/chat";
import type { ElInput } from "element-plus";
import { isInIframe } from "@/config";
import { storeToRefs } from "pinia";
import { IFRAME_SCREEN_STATE } from "@/constants/iframe";
import { useI18n } from "vue-i18n";

interface Props {
  mini?: boolean;
  chatStatus?: CHAT_STATUS;
}

const props = withDefaults(defineProps<Props>(), {
  mini: true,
  chatStatus: CHAT_STATUS.READY,
});

const emit = defineEmits<{
  (e: "send", msg: string): void
}>();

const { t } = useI18n();
const inputMsg = ref("");
const textareaRef = ref<InstanceType<typeof ElInput>>();
const iframeStore = useIframeStore();
const { iframeScreenState } = storeToRefs(iframeStore);

const handleFoucsInput = () => {
  if (!isInIframe || iframeScreenState.value !== IFRAME_SCREEN_STATE.MINI_SCREEN) {
    nextTick(() => {
      textareaRef.value?.focus();
    });
  }
};

watch(iframeScreenState, () => {
  handleFoucsInput();
});

onMounted(handleFoucsInput);

const { isFocus, handleFoucs, handleBlur } = useFocus();

const autoSizeConfig = computed(() => {
  if (props.mini) {
    return {
      minRows: 1,
      maxRows: 7,
    };
  }
  return {
    minRows: 4,
    maxRows: 4,
  };
});

const handleInputContainerClick = (e: MouseEvent) => {
  if (!(e.target instanceof HTMLElement)) return;

  if (e.target.closest(".msg-input-container") && !e.target.closest(".tool-wrapper")) {
    textareaRef.value?.focus();
  }
};

const handleSend = () => {
  if (inputMsg.value.trim() === "" || props.chatStatus === CHAT_STATUS.PENDING) return;
  emit("send", inputMsg.value);
  inputMsg.value = "";
};

const handleEnter = (e: Event | KeyboardEvent) => {
  e.preventDefault();

  if (e instanceof KeyboardEvent && e.isComposing) return;
  handleSend();
};
</script>
<style scoped lang="scss">
.msg-input-container {
  :deep(.el-textarea__inner) {
    --el-input-border-color: transparent;
    --el-input-focus-border-color: transparent;
    --el-input-hover-border-color: transparent;
    padding: 0;
  }

  &.mini {
    &::before {
      content: '';
      box-shadow: 0px 4px 18px 0px rgba(222, 223, 225, 0.5);
      @apply absolute inset-0 z--1 rounded-inherit;
    }
  }

  &::after {
    content: '';
    @apply absolute inset-0 rounded-inherit z--1 m--1px border-1px border-solid border-#E6E8EF;
  }

  &.is-foucs {
    &::after {
      @apply border-none;
      background-image: linear-gradient(140deg, #1890ff 0%, #a496ff 100%);
    }
  }

  .tool-wrapper {
    &.mini {
      @apply items-center justify-end w-fit ml-auto;
    }
  }
}

.msg-input {
  :deep(.el-textarea__inner) {
    scrollbar-width: thin;
  }
}
</style>
