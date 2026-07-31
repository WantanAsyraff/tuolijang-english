<template>
  <view />
</template>

<script setup lang="ts">
import { WxWork, isWxWorkEnv } from "@/libs/wxwork";
import { useStore } from "vuex";
import message from "@/utils/message";
import { computed, ref, watch } from "vue";
import {
  clientEditInfoApi,
  leadEditFormApi
} from "@/api/customer";

const store = useStore();
const hasRedirected = ref(false);
const isLogin = computed(() => store.state.app.isLogin);

const logLaunch = (message: string, data?: any) => {
  console.info(`[wxwork-launch] ${message}`, data || "");
};

const toWwDefaultPage = () => {
  logLaunch("redirect default page");
  uni.redirectTo({
    url: "/pages/common/ww-default"
  });
};

const handleRedirectToClientPage = async (userId: string) => {
  logLaunch("external contact userId", userId);
  const where = { userid: userId };
  const task = Promise.allSettled([
    clientEditInfoApi(0, where),
    leadEditFormApi(String(0), where)
  ]);
  try {
    const [clientInfo, leadInfo] = await task;
    logLaunch("customer matched", clientInfo);
    logLaunch("lead matched", leadInfo);

    if (clientInfo.status === "fulfilled" && clientInfo.value?.data?.data?.id) {
      // 如果企微 userid 对应的 OA 客户存在，则跳转到客户详情页
      uni.redirectTo({
        url: `/pages/customer/list/details?userid=${userId}&types=customer`
      });
    } else if (leadInfo.status === "fulfilled" && leadInfo.value?.data?.data?.id) {
      // 如果企微 userid 对应的 OA 线索存在，则跳转到线索详情页
      uni.redirectTo({
        url: `/pages/customer/lead/detail?userid=${userId}&types=customer`
      });
    } else {
      // 如果都不存在，则跳转到企微默认页面
      toWwDefaultPage();
    }
  } catch (error) {
    error.message && message.error(error.message);
    toWwDefaultPage();
  }
};

const redirectByWxWorkContext = async () => {
  if (hasRedirected.value) return;

  const toIndexPage = () => uni.reLaunch({
    url: "/pages/index/index",
  });
  if (!isWxWorkEnv) return toIndexPage();

  if (!isLogin.value) return;

  uni.showLoading({
    mask: true
  });
  try {
    const wxWork = await WxWork.getInstance();
    const { entry: entryScene, errMsg, errCode } = await wxWork.ww.getContext();
    if (errCode !== 0) throw new Error(errMsg);
    logLaunch("entry scene", entryScene);

    hasRedirected.value = true;
    try {
      // 从企微客户联系侧边栏/聊天工具栏进入时，可获取当前外部联系人。
      const contactRes = await wxWork.ww.getCurExternalContact();
      logLaunch("getCurExternalContact result", contactRes);
      const { errMsg, errCode } = contactRes;
      if (errCode !== 0) throw new Error(errMsg);
      const userId = contactRes.userId || contactRes.externalUserId || contactRes.external_userid;
      if (!userId) throw new Error("未获取到企微外部联系人ID");
      await handleRedirectToClientPage(userId);
    } catch (error: any) {
      logLaunch("get external contact failed", error?.message || error);
      if (entryScene === "normal") {
        // 常规方式进入
        toIndexPage();
      } else {
        // 群聊、其他方式进入
        toWwDefaultPage();
      }
    }
    uni.hideLoading();
  } catch (error: any) {
    uni.hideLoading();
    if (error.message) {
      message.error(error.message);
    }
    hasRedirected.value = true;
    toIndexPage();
  }
};

onLoad(() => {
  redirectByWxWorkContext();
});

watch(isLogin, (value) => {
  if (value) {
    redirectByWxWorkContext();
  }
});
</script>
