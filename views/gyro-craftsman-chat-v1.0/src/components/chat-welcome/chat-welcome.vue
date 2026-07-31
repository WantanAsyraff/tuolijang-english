<template>
  <div class="chat-main-layout">
    <h3 class="welcome-title">
      <img :src="defaultAppLogo" class="w-46px h-46px" />
      {{ welcomeText }}
    </h3>
    <div>
      <ChatMessageInput :mini="false" @send="handleSend" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from "vue-router";
import { ROUTE_KEY } from "@/constants/route-key";
import { useUserStore } from "@/pinia/stores/useUserStore";
import { storeToRefs } from "pinia";
import { INJECT_KEY } from "@/constants/inject-key";
import { useProvideLoading } from "@/composables/ui/useChatCreateLoading";
import { useLoginDialogStore } from "@/pinia/stores/ui/useLoginDialogStore";
import { defaultLogo } from "@/config";
import { useAppListStore } from "@/pinia/stores/useAppListStore";
import { useChatStore } from "@/pinia/stores/useChatStore";
import { Message } from "@/utils/message";
import { handleError } from "@/utils/error-handler";
import { useI18n } from "vue-i18n";

const props = defineProps<{
  appId?: string;
}>();

const { t } = useI18n();
const { appId } = toRefs(props);

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();
const appListStore = useAppListStore();
const chatStore = useChatStore();
const loginDialogStore = useLoginDialogStore();

const { isLoading: isCreateChatLoading, setLoading } = useProvideLoading(INJECT_KEY.CHAT_READY_LOADING);

const { userInfo, isLogin } = storeToRefs(userStore);
const { appList } = storeToRefs(appListStore);

const defaultAppLogo = computed(() => {
  if (appList.value.length) {
    if (appId.value) {
      const app = appList.value.find(item => item.id === Number(appId.value));
      return app?.pic || defaultLogo;
    }
    return appList.value[0].pic || defaultLogo;
  }
  return defaultLogo;
});

const welcomeText = computed(() => {
  if (isLogin.value && userInfo.value?.name) {
    return `${t("chat.welcome")} ${userInfo.value.name}`;
  }
  return t("chat.welcome");
});

const handleSend = async (msg: string) => {
  if (!isLogin.value) {
    Message.error(t("error.loginRequired"));
    loginDialogStore.handleSetLoginDialogOpen();
    return;
  }
  if (isCreateChatLoading.value) return;
  if (!appList.value.length) {
    Message.error(t("chat.noApps"));
    return;
  }
  setLoading(true);
  const routePath = route.path;
  try {
    const nowAppId = appId.value ? Number(appId.value) : appList.value[0].id;
    const chatId = await chatStore.createChat(msg.trim(), nowAppId);
    if (routePath !== route.path) return;
    router.push({
      name: ROUTE_KEY.CHAT_MAIN,
      params: {
        id: chatId,
      },
    });
  } catch (error: any) {
    handleError(error);
  } finally {
    setLoading(false);
  }
};

</script>

<style lang="scss" scoped>
.welcome-title {
  @apply flex items-center gap-16px justify-center font-bold text-30px leading-42px mb-32px;
  padding-top: calc(var(--vh) * 0.3);
  transition: padding-top 0.3s ease;

  @media screen and (max-height: 700px) {
    padding-top: calc(var(--vh) * 0.4);
  }

  @media screen and (max-height: 600px) {
    padding-top: calc(var(--vh) * 0.5);
  }
}
</style>
