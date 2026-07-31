<template>
  <div class="absolute inset-0 z-10 flex justify-center items-center">
    <div class="login-container" :data-login-method="loginMethod">
      <button class="absolute top--35px right--35px" @click="closeLoginModal">
        <img src="@/assets/images/login-close.png" class="w-24px h-24px" />
      </button>

      <el-tooltip effect="dark" :content="loginMethodTips" placement="left">
        <img src="@/assets/images/login-scan.png" v-if="loginMethod === LoginMethod.FORM" class="login-method-icon"
          @click="handleToggleLoginMethod" />
        <img src="@/assets/images/login-pwd.png" v-else class="login-method-icon" @click="handleToggleLoginMethod" />
      </el-tooltip>

      <h2 class="text-20px leading-28px font-bold flex items-center mb-42px">
        <img src="@/assets/images/logo.png" class="w-34px h-34px mr-10px" />
        {{ t("login.welcome") }}
      </h2>

      <div class="qrcode-login-container" v-show="loginMethod === LoginMethod.QRCODE">
        <p class="text-center text-18px leading-25px font-bold mb-30px">{{ t("login.scanLogin") }}</p>
        <div class="w-162px h-162px relative mx-auto" v-loading="qrcodeLoading">
          <img :src="qrcodeImg" v-if="qrcodeImg" class="w-full h-full" />
          <img src="@/assets/images/logo.png" v-if="qrcodeImg"
            class="absolute top-50% left-50% -translate-x-1/2 -translate-y-1/2 bg-white p-2px rounded-4px overflow-hidden w-40px h-40px" />
          <div class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center" v-if="qrcodeIsExpired">
            <p class="text-15px mb-10px text-white">{{ t("login.qrExpired") }}</p>
            <el-button size="small" type="primary" class="rounded-8px text-13px leading-13px"
              @click="refreshQrcode">{{ t("login.refreshQr") }}</el-button>
          </div>
        </div>
        <p class="text-center text-16px leading-20px mt-30px">{{ t("login.scanPrompt") }}</p>
      </div>

      <div class="form-login-container" v-show="loginMethod === LoginMethod.FORM">
        <el-tabs v-model="loginType" class="form-login-tabs">
          <el-tab-pane :label="t('login.passwordLogin')" :name="LoginType.PWD" />
          <el-tab-pane :label="t('login.smsLogin')" :name="LoginType.SMS" />
        </el-tabs>
        <el-form :model="loginForm" :rules="loginFormRules" @submit.prevent="handleFormLogin" ref="loginFormRef">
          <template v-if="loginType === LoginType.PWD">
            <el-form-item prop="phone">
              <el-input name="phone" :placeholder="t('login.phone')" size="large" v-model="loginForm.phone" />
            </el-form-item>
            <el-form-item prop="password">
              <el-input name="password" :placeholder="t('login.password')" size="large" type="password" v-model="loginForm.password" />
            </el-form-item>
            <el-form-item prop="captchaCode">
              <el-input name="captchaCode" :placeholder="t('login.captcha')" size="large" maxlength="4"
                v-model="loginForm.captchaCode" class="flex-1" />
              <div class="w-140px h-full ml-20px cursor-pointer" @click="getCaptcha">
                <img :src="captchaInfo.captchaImg" v-if="captchaInfo.captchaImg" class="w-full h-full" />
              </div>
            </el-form-item>
            <div class="flex mt-17px mb-10px">
              <button class="primary-color text-13px ml-auto leading-13px">{{ t("login.forgotPassword") }}</button>
            </div>
          </template>
          <template v-else-if="loginType === LoginType.SMS">
            <el-form-item prop="phone">
              <el-input :placeholder="t('login.phone')" name="phone" size="large" v-model="loginForm.phone" />
            </el-form-item>
            <el-form-item prop="smsCode" style="margin-bottom: 40px;" class="relative">
              <el-input :placeholder="t('login.smsCode')" name="smsCode" size="large" v-model="loginForm.smsCode" />
              <button type="button"
                class="absolute right-15px top-14px text-14px leading-20px primary-color cursor-pointer"
                :disabled="!isAllowGetVerifyCode" @click="handleSendVerifyCode">{{ codeTips }}</button>
            </el-form-item>
          </template>
          <el-form-item style="margin-bottom: 0;">
            <el-button type="primary" native-type="submit" size="large" class="w-full login-btn"
              :loading="isLoginLoading">{{ t("login.login") }}</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { getCaptchaApi, userPwdLoginApi, userSmsLoginApi } from "@/api/user";
import { useQrcodeLogin } from "@/composables/auth/useQrcodeLogin";
import { useVerifyCode } from "@/composables/auth/useVerifyCode";
import { useUserStore } from "@/pinia/stores/useUserStore";
import type { ElForm } from "element-plus";
import { useLoginDialogStore } from "@/pinia/stores/ui/useLoginDialogStore";
import { useRootStore } from "@/pinia/stores/useRootStore";
import { Message } from "@/utils/message";
import { handleError } from "@/utils/error-handler";
import { useI18n } from "vue-i18n";

const enum LoginType {
  PWD = "pwd",
  SMS = "sms"
}

const enum LoginMethod {
  FORM = "form",
  QRCODE = "qrcode"
}

const { t } = useI18n();
const loginMethod = ref<LoginMethod>(LoginMethod.FORM);
const loginType = ref<LoginType>(LoginType.PWD);
const loginFormRef = ref<InstanceType<typeof ElForm>>();
const isLoginLoading = ref(false);

const rootStore = useRootStore();
const userStore = useUserStore();
const loginDialogStore = useLoginDialogStore();

const { getVerifyCode, codeTips, isAllowGetVerifyCode, codeKey } = useVerifyCode();
const { qrcodeImg, qrcodeIsExpired, qrcodeLoading, refreshQrcode, startCheckStatus, stopCheckStatus } = useQrcodeLogin(handleLoginSuccess);

const loginFormRules = computed(() => ({
  phone: [
    { required: true, message: t("login.phone"), trigger: "blur" },
    { pattern: /^1[3-9]\d{9}$/, message: t("login.validPhone"), trigger: "blur" }
  ],
  password: [
    { required: true, message: t("login.password"), trigger: "blur" }
  ],
  captchaCode: [
    { required: true, message: t("login.captcha"), trigger: "blur" },
    { len: 4, message: t("login.validCaptcha"), trigger: "blur" }
  ],
  smsCode: [
    { required: true, message: t("login.smsCode"), trigger: "blur" },
    { min: 4, max: 6, message: t("login.validSmsCode"), trigger: "blur" }
  ]
}));

const captchaInfo = ref({
  captchaImg: "",
  captchaKey: "",
});

const loginForm = ref({
  phone: "",
  password: "",
  captchaCode: "",
  smsCode: ""
});

const loginMethodTips = computed(() => loginMethod.value === LoginMethod.FORM ? t("login.scanLogin") : t("login.formLogin"));

const closeLoginModal = () => {
  loginDialogStore.handleCloseLoginDialog();
};

async function handleLoginSuccess(token: string) {
  userStore.saveUserToken(token);
  await rootStore.initialize();
  Message.success(t("login.loginSuccess"));
  closeLoginModal();
};

const handleSendVerifyCode = () => {
  if (/^1[3-9]\d{9}$/.test(loginForm.value.phone)) {
    getVerifyCode(loginForm.value.phone);
  } else {
    Message.error(t("login.validPhone"));
  }
};

const handleToggleLoginMethod = () => {
  loginMethod.value = loginMethod.value === LoginMethod.FORM ? LoginMethod.QRCODE : LoginMethod.FORM;
};

const getCaptcha = async () => {
  try {
    const res = await getCaptchaApi();
    captchaInfo.value.captchaImg = res.data.img;
    captchaInfo.value.captchaKey = res.data.key;
  } catch (error: any) {
    handleError(error);
  }
};

const handleFormLogin = () => {
  loginFormRef.value?.validate(async (valid) => {
    if (!valid || isLoginLoading.value) return;
    isLoginLoading.value = true;
    try {
      let res;
      if (loginType.value === LoginType.PWD) {
        const params = {
          account: loginForm.value.phone,
          password: loginForm.value.password,
          captcha: loginForm.value.captchaCode,
          key: captchaInfo.value.captchaKey
        };
        res = await userPwdLoginApi(params);
      } else {
        const params = {
          phone: loginForm.value.phone,
          verification_code: loginForm.value.smsCode,
          key: codeKey.value
        };
        res = await userSmsLoginApi(params);
      }

      handleLoginSuccess(res.data.token);
    } catch (error: any) {
      handleError(error);
      if (loginType.value === LoginType.PWD) {
        getCaptcha();
      }
    } finally {
      isLoginLoading.value = false;
    }
  });
};

watch(loginMethod, () => {
  if (loginMethod.value === LoginMethod.QRCODE) {
    if (qrcodeImg.value) {
      startCheckStatus();
    } else {
      refreshQrcode();
    }
  } else if (loginMethod.value === LoginMethod.FORM) {
    stopCheckStatus();
  }
}, { immediate: true });

getCaptcha();

onUnmounted(() => {
  if (loginMethod.value === LoginMethod.QRCODE) {
    stopCheckStatus();
  }
});

</script>

<style lang="scss" scoped>
.login-container {
  @apply bg-white rounded-15px pt-45px pb-50px px-40px relative;
  width: min(420px, 80%);
}

.login-method-icon {
  @apply w-68.5px h-68.5px absolute top-10px right-11px cursor-pointer;
}

.form-login-tabs {
  @apply leading-25px;
  --el-border-color-light: transparent;
  --el-font-size-base: 18px;

  :deep(.el-tabs__content) {
    @apply mt-5px;
  }
}

.form-login-container {
  .login-btn {
    --el-button-size: 48px;
    --el-border-radius-base: 8px;
  }

  --el-component-size-large: 48px;

  :deep(.el-form-item) {
    @apply mb-14px;
  }
}
</style>
