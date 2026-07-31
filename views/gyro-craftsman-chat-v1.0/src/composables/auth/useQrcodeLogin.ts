import qrcode from "qrcode";
import { getQrcodeScanKeyApi, checkQrcodeScanStatusApi } from "@/api/user";
import { handleError } from "@/utils/error-handler";

/**
 * 二维码登录
 */
export const useQrcodeLogin = (callback: (token: string) => void) => {
  let isStopCheckStatus = false;
  const qrcodeScanKey = ref("");
  const qrcodeImg = ref("");
  const qrcodeLoading = ref(false);
  const qrcodeIsExpired = ref(false);
  let expiredTime = 0;

  // 刷新二维码扫描key
  const refreshQrcodeScanKey = async () => {
    const res = await getQrcodeScanKeyApi();
    return res.data;
  };

  // 检查二维码是否过期
  const startCheckQrcodeExpired = () => {
    if (isStopCheckStatus || !expiredTime || expiredTime < Date.now()) return;
    const timer = setInterval(() => {
      if (isStopCheckStatus) {
        clearInterval(timer);
        return;
      }
      if (expiredTime && Date.now() > expiredTime) {
        qrcodeIsExpired.value = true;
        clearInterval(timer);
        isStopCheckStatus = true;
      }
    }, 1000);
  };

  // 从接口检查二维码是否扫描成功
  const checkQrcodeScanStatus = async (key: string) => {
    const res = await checkQrcodeScanStatusApi({ key });
    return res.data;
  };

  // 检查二维码是否扫描成功
  const startCheckQrcodeScanStatus = () => {
    if (isStopCheckStatus || !qrcodeScanKey.value) return;
    const timer = setInterval(async () => {
      if (isStopCheckStatus) {
        clearInterval(timer);
        return;
      }
      try {
        const result = await checkQrcodeScanStatus(qrcodeScanKey.value);
        if (result.status === undefined) {
          callback(result.token);
          clearInterval(timer);
          isStopCheckStatus = true;
        }
      } catch { }
    }, 2000);
  };

  const startCheckStatus = () => {
    isStopCheckStatus = false;
    startCheckQrcodeExpired();
    startCheckQrcodeScanStatus();
  };

  const stopCheckStatus = () => {
    isStopCheckStatus = true;
  };

  // 刷新并生成二维码
  const refreshQrcode = async () => {
    if (qrcodeLoading.value) return;
    qrcodeLoading.value = true;
    try {
      const { key, expire_time } = await refreshQrcodeScanKey();
      qrcodeImg.value = await qrcode.toDataURL(key);
      qrcodeScanKey.value = key;
      expiredTime = +new Date(expire_time);
      startCheckStatus();
    } catch (error: any) {
      handleError(error);
    } finally {
      qrcodeLoading.value = false;
      qrcodeIsExpired.value = false;
    }
  };

  return {
    qrcodeImg,
    qrcodeIsExpired,
    qrcodeLoading,

    startCheckStatus,
    stopCheckStatus,

    checkQrcodeScanStatus,
    refreshQrcode
  };
};
