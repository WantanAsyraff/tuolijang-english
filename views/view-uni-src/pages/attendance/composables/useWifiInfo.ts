// #ifdef APP
import message from "@/utils/message";
import { getWifiInfo } from "@/utils/wifi";
// #endif

/**
 * APP 端 Wi-Fi 信息读取。
 *
 * 该 composable 只负责和原生能力交互，考勤组白名单匹配放在 useAttendanceWifi 中处理。
 * H5/WEB 环境没有可靠的 BSSID 获取能力，因此实际逻辑只在 APP 条件编译块内生效。
 */
export const useWifiInfo = () => {
  /** 当前连接的 Wi-Fi 信息，结构由 utils/wifi 的 getWifiInfo 返回。 */
  const wifiInfo = ref(null);
  /** 是否正在读取 Wi-Fi，防止重复触发原生调用。 */
  const isWifiInfoLoading = ref(false);

  /** 主动刷新当前 Wi-Fi 信息。 */
  const refreshWifiInfo = async () => {
    // #ifdef APP
    if (isWifiInfoLoading.value) return;
    isWifiInfoLoading.value = true;
    // getWifiInfo 依赖 APP 原生能力；失败时给出提示，页面仍可走定位/外勤打卡。
    const info = await getWifiInfo() as any;
    if (!info) {
      message.error("获取 WIFI 信息失败", "error");
    } else {
      wifiInfo.value = info.wifi;
    }
    isWifiInfoLoading.value = false;
    // #endif
  };

  // #ifdef APP
  onLoad(() => {
    // 页面进入时先读一次，后续用户也可以手动刷新。
    refreshWifiInfo();
  });
  // #endif

  return {
    wifiInfo,
    isWifiInfoLoading,
    refreshWifiInfo
  };
};
