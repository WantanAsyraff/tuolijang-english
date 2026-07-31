<template>
  <el-config-provider :locale="elementLocale">
    <div class="flex h-full">
      <ChatSidebarIndex />
      <div class="flex-1 relative overflow-hidden">
        <router-view />
      </div>
    </div>
    <ChatLogin />
  </el-config-provider>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import zhCn from "element-plus/es/locale/lang/zh-cn";
import en from "element-plus/es/locale/lang/en";
import { useWindowAttr } from "@/composables/effects/useWindowAttr";
import { useLoginDialogStore } from "@/pinia/stores/ui/useLoginDialogStore";
import { useUserStore } from "@/pinia/stores/useUserStore";
import { storeToRefs } from "pinia";
import { useRootStore } from "@/pinia/stores/useRootStore";
import { useWebTitle } from "@/composables/effects/useWebTitle";
import { useIframeMsgHandler } from "@/composables/iframe/useIframeMsgHandler";
import { handleError } from "@/utils/error-handler";

const { locale } = useI18n();
const elementLocale = computed(() => (locale.value === "en" ? en : zhCn));

useWindowAttr();
useIframeMsgHandler();

const rootStore = useRootStore();
const { initialize } = rootStore;

initialize().catch(handleError);
const userStore = useUserStore();
const { isLogin } = storeToRefs(userStore);
const loginDialogStore = useLoginDialogStore();
useWebTitle();

if (!isLogin.value) {
  loginDialogStore.handleSetLoginDialogOpen();
}
</script>

<style lang="scss">
@use "@/styles/base.scss";
@use "@/styles/chat-layout.scss";
@use "@/styles/iconfont.scss";
</style>
