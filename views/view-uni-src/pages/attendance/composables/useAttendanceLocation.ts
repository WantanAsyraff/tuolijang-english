import appI18n from '@/locale';
import { computed, ref, type ComputedRef, type Ref } from "vue";
import { getDistance } from "@/utils/helper";
import type { AttendanceCoordinate, AttendanceData } from "./attendanceTypes";

/** 定位 composable 所需依赖，由考勤首页注入。 */
interface UseAttendanceLocationOptions {
  /** attendance/basic 数据，包含考勤组经纬度和有效范围。 */
  attendanceData: Ref<AttendanceData | null>;
  /** 是否开启定位打卡；关闭时不主动获取定位。 */
  isLocationEnable: ComputedRef<boolean>;
  /** 地图上考勤点标记图标。 */
  markerIcon: string;
  /** 地图上用户当前位置标记图标。 */
  markerUserIcon: string;
}

/** 将后端可能返回的字符串坐标/范围转换为数字，无法解析时兜底 0。 */
function toCoordinateValue(value: number | string | undefined) {
  const numberValue = Number(value);
  return Number.isNaN(numberValue) ? 0 : numberValue;
}

/**
 * 考勤定位能力封装。
 *
 * 负责：
 * - 同步考勤组坐标和有效范围。
 * - 获取用户当前定位。
 * - 判断用户是否在考勤范围内。
 * - 为地图组件生成 markers/circles。
 * - 反解析当前位置地址供打卡提交和页面展示。
 */
export function useAttendanceLocation(options: UseAttendanceLocationOptions) {
  /** 当前定位反解析出来的地址。 */
  const address = ref("");
  /** 用户当前坐标。 */
  const nowXy = ref<AttendanceCoordinate>();
  /** 考勤点坐标。 */
  const rangeXy = ref<AttendanceCoordinate>();
  /** 考勤有效范围，单位通常是米。 */
  const effectiveRange = ref(0);
  /** 当前用户是否在考勤范围内。 */
  const isLocationRange = ref(false);

  /** 地图标记：考勤点 + 用户当前位置。 */
  const mapMarkers = computed(() => {
    const markers = [];

    if (rangeXy.value) {
      // 考勤点标记。
      markers.push({
        latitude: rangeXy.value.latitude,
        longitude: rangeXy.value.longitude,
        iconPath: options.markerIcon,
      });
    }

    if (nowXy.value) {
      // 用户当前位置标记。
      markers.push({
        latitude: nowXy.value.latitude,
        longitude: nowXy.value.longitude,
        iconPath: options.markerUserIcon,
      });
    }

    return markers;
  });

  /** 地图圆形范围：以考勤点为圆心，effectiveRange 为半径。 */
  const mapCircles = computed(() => {
    if (!rangeXy.value) return [];

    return [{
      latitude: rangeXy.value.latitude,
      longitude: rangeXy.value.longitude,
      radius: effectiveRange.value,
    }];
  });

  /**
   * 从 attendance/basic 的考勤组配置同步考勤点和范围。
   *
   * basic 刷新后必须先调用它，再执行 getLocation，否则无法计算是否在范围内。
   */
  function syncRuleLocation() {
    const group = options.attendanceData.value?.group;

    effectiveRange.value = toCoordinateValue(group?.effective_range);

    if (!group) {
      rangeXy.value = undefined;
      isLocationRange.value = false;
      return;
    }

    // 后端字段是 lat/lng，页面地图组件统一使用 latitude/longitude。
    rangeXy.value = {
      latitude: toCoordinateValue(group.lat),
      longitude: toCoordinateValue(group.lng),
    };
  }

  /** 更新用户当前坐标、范围判断和地址。 */
  function updateLocationInfo(res: AttendanceCoordinate) {
    nowXy.value = {
      longitude: res.longitude,
      latitude: res.latitude,
    };

    if (rangeXy.value) {
      // 通过工具函数计算两点距离，小于等于有效范围即允许范围内打卡。
      const distance = getDistance(nowXy.value, rangeXy.value);
      isLocationRange.value = distance <= effectiveRange.value;
    } else {
      isLocationRange.value = false;
    }

    turnAddress(nowXy.value.longitude, nowXy.value.latitude);
  }

  /**
   * 获取当前定位。
   *
   * H5 壳环境优先尝试 plus.geolocation 的 amap/baidu provider；
   * 普通 uni 环境使用 uni.getLocation，并开启高精度定位。
   */
  function getLocation() {
    if (!options.isLocationEnable.value) return;

    // #ifdef WEB
    const plusApi = typeof window !== "undefined" ? (window as any).plus : null;
    if (plusApi) {
      let isGetLocationSuccess = false;
      const providers = ["amap", "baidu"];

      // 多 provider 并发时只接受第一次有效结果，避免坐标反复覆盖。
      const processResult = (res: any) => {
        if (!isGetLocationSuccess && res?.latitude && res?.longitude) {
          isGetLocationSuccess = true;
          updateLocationInfo({
            latitude: Number(res.latitude),
            longitude: Number(res.longitude),
          });
        }
      };

      providers.forEach((provider) => {
        // 某个 provider 失败不弹错，等待其他 provider 或后续 uni.getLocation 兜底。
        plusApi.geolocation.getCurrentPosition((result: any) => {
          if (result.coords) {
            processResult(result.coords);
          }
        }, () => {}, { provider });
      });

      return;
    }
    // #endif

    uni.getLocation({
      type: "gcj02",
      isHighAccuracy: true,
      success: (res) => {
        updateLocationInfo({
          latitude: Number(res.latitude),
          longitude: Number(res.longitude),
        });
      },
      fail: (err) => {
        if (err.errMsg === "getLocation:fail Geolocation permission denied") {
          // 权限拒绝需要明确提示用户，否则按钮会因缺少 nowXy 无法打卡。
          uni.showToast({
            title: appI18n.global.t('ui.attendanceComposablesUseAttendanceLocationTsLocationPermissionWasDenied'),
            icon: "none",
          });
          return;
        }

        uni.showToast({
          title: appI18n.global.t('ui.attendanceComposablesUseAttendanceLocationTsFailedToGetLocation'),
          icon: "none",
        });
      },
    });
  }

  /** 使用高德逆地理编码把坐标转换为中文地址。 */
  function turnAddress(longitude: number, latitude: number) {
    uni.request({
      method: "GET",
      url: "https://restapi.amap.com/v3/geocode/regeo",
      data: {
        key: "ed1a4be1ee886c9ae5f13e900f0690c3",
        location: `${longitude},${latitude}`,
        output: "JSON",
        radius: 1000,
        extensions: "base",
        pois: 0,
      },
      success: (res: any) => {
        address.value = res.data?.regeocode?.formatted_address || "";
      },
      fail: () => {},
    });
  }

  return {
    address,
    nowXy,
    rangeXy,
    effectiveRange,
    isLocationRange,
    mapMarkers,
    mapCircles,
    syncRuleLocation,
    getLocation,
  };
}
