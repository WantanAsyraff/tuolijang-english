<template>
  <div class="px-16px pb-30px flex items-center gap-8px mt-auto">
    <template v-if="isLogin && userInfo">
      <img :src="userInfo.avatar" class="w-32px h-32px rounded-full" />
      <span class="flex-1 single-line">{{ userInfo.name }}</span>
      <ChatLanguageSelect />
      <i class="ai-icon ai-icon-tuichudenglu text-20px cursor-pointer text-#606266" @click="handleLogout" />
    </template>
    <template v-else>
      <img src="@/assets/icons/user-icon.png" class="w-32px h-32px rounded-full" />
      <button @click="handleOpenLogin" class="hover:primary-color flex-1 text-left">{{ t("sidebar.login") }}</button>
      <ChatLanguageSelect />
    </template>
  </div>
</template>

<script setup lang="ts">
import { useUserStore } from "@/pinia/stores/useUserStore";
import { useRootStore } from "@/pinia/stores/useRootStore";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { ROUTE_KEY } from "@/constants/route-key";
import { useLoginDialogStore } from "@/pinia/stores/ui/useLoginDialogStore";
import { useI18n } from "vue-i18n";
import ChatLanguageSelect from "./chat-language-select.vue";

const userStore = useUserStore();
const router = useRouter();
const rootStore = useRootStore();
const { userInfo, isLogin } = storeToRefs(userStore);

const loginDialogStore = useLoginDialogStore();
const { t } = useI18n();

const handleOpenLogin = () => {
  loginDialogStore.handleSetLoginDialogOpen();
};

const handleLogout = async () => {
  try {
    await ElMessageBox.confirm(t("sidebar.logoutConfirm"), t("common.tips"), {
      confirmButtonText: t("common.confirm"),
      cancelButtonText: t("common.cancel"),
      type: "warning",
    });
    rootStore.reset();
    router.push({ name: ROUTE_KEY.CHAT_INDEX });
  } catch { }
};
</script>

<style lang="scss">
</style>