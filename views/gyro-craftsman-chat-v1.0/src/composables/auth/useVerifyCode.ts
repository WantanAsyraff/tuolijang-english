import { getCmsApi, getCmsKeyApi } from "@/api/user";
import { handleError } from "@/utils/error-handler";
import { Message } from "@/utils/message";
import { translate } from "@/locale";

export const useVerifyCode = () => {
  const codeTips = ref(translate("login.sendCode"));
  const isAllowGetVerifyCode = ref(true);
  const codeKey = ref("");

  const resetCodeTips = () => {
    codeTips.value = translate("login.sendCode");
  };

  const startTimer = () => {
    let time = 60;
    const timer = setInterval(() => {
      codeTips.value = `${time}s`;
      time--;
    }, 1000);
    setTimeout(() => {
      clearInterval(timer);
      resetCodeTips();
      isAllowGetVerifyCode.value = true;
    }, 60000);
  };

  const getVerifyCode = async (phone: string) => {
    if (!isAllowGetVerifyCode.value) return;
    isAllowGetVerifyCode.value = false;
    try {
      const smsCodeKeyRes = await getCmsKeyApi();
      codeKey.value = smsCodeKeyRes.data.key;
      const params = {
        phone,
        key: codeKey.value,
        from: 1,
        types: 0
      };
      await getCmsApi(params);
      Message.success(translate("login.smsSent"));
      startTimer();
    } catch (error: any) {
      handleError(error);
      resetCodeTips();
      isAllowGetVerifyCode.value = true;
    }
  };

  return {
    codeTips,
    isAllowGetVerifyCode,
    codeKey,
    getVerifyCode
  };
};
