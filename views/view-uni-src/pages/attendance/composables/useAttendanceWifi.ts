import appI18n from '@/locale';
import { computed, type Ref } from "vue";
import message from "@/utils/message";
import { useWifiInfo } from "./useWifiInfo";
import type { AttendanceGroup, WifiInfo } from "./attendanceTypes";

/** 统一 MAC/BSSID 格式，消除大小写、冒号、短横线差异。 */
function normalizeMac(mac?: string) {
  return (mac || "").toLowerCase().replace(/:/g, "").replace(/-/g, "").trim();
}

/**
 * 考勤 Wi-Fi 能力封装。
 *
 * 负责：
 * - 获取当前设备 Wi-Fi 信息。
 * - 判断当前 BSSID 是否命中考勤组 Wi-Fi 白名单。
 * - 提供复制 BSSID 的调试/排查能力。
 */
export function useAttendanceWifi(group: Ref<AttendanceGroup | null>) {
  // useWifiInfo 是底层 APP Wi-Fi 读取逻辑，这里收窄类型后供考勤页面使用。
  const {
    wifiInfo,
    refreshWifiInfo,
    isWifiInfoLoading,
  } = useWifiInfo() as {
    wifiInfo: Ref<WifiInfo | null>;
    refreshWifiInfo: () => void;
    isWifiInfoLoading: Ref<boolean>;
  };

  /** 当前 Wi-Fi 是否属于考勤组配置的公司 Wi-Fi。 */
  const isCompanyWifiRange = computed(() => {
    // #ifdef WEB
    // H5 浏览器无法稳定读取 Wi-Fi BSSID，Web 端一律不走 Wi-Fi 范围判断。
    return false;
    // #endif
    // #ifdef APP
    const groupInfo = group.value;
    if (!groupInfo) return false;

    const { is_wifi, wifi } = groupInfo;
    // 未开启 Wi-Fi 打卡、没有白名单或当前仍在读取 Wi-Fi 时，都不能判定为范围内。
    if (!is_wifi || !Array.isArray(wifi) || !wifi.length) return false;
    if (isWifiInfoLoading.value || !wifiInfo.value?.BSSID) return false;

    // 白名单和当前设备都先归一化，再做精确匹配。
    const currentMac = normalizeMac(wifiInfo.value.BSSID);
    return wifi.some(item => normalizeMac(item.mac) === currentMac);
    // #endif
  });

  /** 复制当前 BSSID，方便现场排查公司 Wi-Fi 配置是否正确。 */
  function copyWifiMac() {
    const bssid = wifiInfo.value?.BSSID;
    if (!bssid) return;

    uni.setClipboardData({
      data: bssid,
      showToast: false,
      success: () => {
        message.success(appI18n.global.t('ui.attendanceComposablesUseAttendanceWifiTsBssidCopied'));
      },
    });
  }

  return {
    wifiInfo,
    refreshWifiInfo,
    isWifiInfoLoading,
    isCompanyWifiRange,
    copyWifiMac,
  };
}
