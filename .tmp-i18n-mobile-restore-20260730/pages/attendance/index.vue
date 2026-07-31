<template>
  <view class="main">
    <!-- 定位打卡开启时展示地图；地图点位和范围由 useAttendanceLocation 统一生成。 -->
    <AMap
      v-if="isLocationEnable"
      :longitude="mapLongitude"
      :latitude="mapLatitude"
      :markers="mapMarkers"
      :circles="mapCircles"
    />

    <!-- 自定义透明导航栏，覆盖地图顶部。 -->
    <uni-nav-bar
      background-color="transparent"
      :border="false"
      status-bar
      left-icon="left"
      title=""
      class="custom-nav-bar"
      @click-left="handleBack"
    />

    <!-- 用户和考勤规则入口。 -->
    <AttendanceUserCard
      :user-info="userInfo"
      :user-job-info="userJobInfo"
      :shift="shift"
      :is-white="isWhite"
      :is-location-enable="isLocationEnable"
    />

    <!-- 本月待处理异常统计。 -->
    <AttendanceAbnormalCard :abnormal="abnormal" />

    <!-- 打卡时间线；新版跨天班次在 composables 中已归一化为 timelineItems。 -->
    <AttendanceTimeline
      v-if="!loading"
      :items="timelineItems"
      :clock-button="clockButton"
      :time-text="timeText"
      @clock-click="handleClockButtonClick"
      @stage-renew="handleStageRenew"
      @stage-apply="openApplyPopup"
      @refresh-location="getLocation"
      @refresh-wifi="refreshWifiInfo"
      @copy-wifi-mac="copyWifiMac"
    />

    <bottom-navigation :type="4" page-path="/pages/attendance/index" />

    <!-- 打卡成功、异常处理、外勤/拍照打卡等弹窗集中管理。 -->
    <AttendancePopups
      ref="popupsRef"
      :clock-time="clockTime"
      :sign-in-type="signInType"
      :on-work="onWork"
      :now-date="nowDate"
      :is-effective-range="isEffectiveRange"
      :external-text-required="externalTextRequired"
      :external-pic-required="externalPicRequired"
      :address="address"
      :record-length="clockRecordLength"
      @success-ok="handleSignInOk"
      @external-ok="handleExternalOk"
    />
  </view>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import bottomNavigation from "@/components/bottomNavigation/index.vue";
import AMap from "./components/AMap.vue";
import AttendanceUserCard from "./components/AttendanceUserCard.vue";
import AttendanceAbnormalCard from "./components/AttendanceAbnormalCard.vue";
import AttendanceTimeline from "./components/AttendanceTimeline.vue";
import AttendancePopups from "./components/AttendancePopups.vue";
import { useAttendanceData } from "./composables/useAttendanceData";
import { useAttendanceLocation } from "./composables/useAttendanceLocation";
import { useAttendanceWifi } from "./composables/useAttendanceWifi";
import { useAttendanceClockFlow } from "./composables/useAttendanceClockFlow";
import { useClockTicker } from "./composables/useClockTicker";

// H5 和 APP 的静态资源路径处理不同，地图 marker 图标按平台分别声明。
// #ifdef H5
import markerIcon from "@/static/image/attendance/pos.png";
import markerUserIcon from "@/static/image/attendance/pos-user.png";
// #endif
// #ifdef APP
const markerIcon = "./static/image/attendance/pos.png";
const markerUserIcon = "./static/image/attendance/pos-user.png";
// #endif

// 考勤首页数据层：兼容旧版 basic + clock_record 和新版 basic.prev/now 跨天结构。
const {
  userInfo,
  userJobInfo,
  loading,
  attendanceData,
  group,
  recordData,
  basicData,
  abnormal,
  clockStatus,
  clockUpdateTime,
  onWork,
  isWhite,
  shift,
  nowDate,
  isAfterOffHours1,
  isAfterOffHours2,
  isLocationEnable,
  isWifiSignEnable,
  init,
  refresh,
} = useAttendanceData();

// Wi-Fi 打卡能力：读取当前设备 Wi-Fi，并判断是否命中考勤组 Wi-Fi 白名单。
const {
  wifiInfo,
  refreshWifiInfo,
  isCompanyWifiRange,
  copyWifiMac,
} = useAttendanceWifi(group);

// 定位打卡能力：同步考勤点、获取当前位置、计算范围并生成地图数据。
const {
  address,
  nowXy,
  rangeXy,
  isLocationRange,
  mapMarkers,
  mapCircles,
  syncRuleLocation,
  getLocation,
} = useAttendanceLocation({
  attendanceData,
  isLocationEnable,
  markerIcon,
  markerUserIcon,
});

// 统一控制三个弹窗的打开和关闭。
const popupsRef = ref<InstanceType<typeof AttendancePopups> | null>(null);
const refreshingClockState = ref(false);

// 打卡流程层：生成时间线、按钮状态，并负责打卡提交。
const {
  clockTime,
  signInType,
  timelineItems,
  clockButton,
  clockRecordLength,
  isEffectiveRange,
  externalTextRequired,
  externalPicRequired,
  handleClockButtonClick,
  handleStageRenew,
  handleExternalOk,
} = useAttendanceClockFlow({
  attendanceData,
  group,
  basicData,
  recordData,
  clockStatus,
  onWork,
  isWhite,
  isAfterOffHours1,
  isAfterOffHours2,
  isLocationEnable,
  isWifiSignEnable,
  isCompanyWifiRange,
  isLocationRange,
  nowXy,
  address,
  wifiInfo,
  openExternalPopup: () => popupsRef.value?.openExternal(),
  openSuccessPopup: () => popupsRef.value?.openSuccess(),
});

// 地图组件需要字符串经纬度；无考勤点时传空字符串避免地图异常定位。
const mapLongitude = computed(() => rangeXy.value ? String(rangeXy.value.longitude) : "");
const mapLatitude = computed(() => rangeXy.value ? String(rangeXy.value.latitude) : "");

// 页面时钟：每秒刷新按钮时间，每分钟按需刷新定位。
const { timeText } = useClockTicker({
  isLocationEnable,
  clockUpdateTime,
  onMinuteChange: getLocation,
  onClockExpired: updateType,
});

onLoad(() => {
  // basic 加载完成后再同步规则定位，因为考勤点坐标依赖 attendance/basic。
  init(afterBasicLoaded);
});

/** basic 加载后的副作用：同步考勤点并按需获取当前位置。 */
function afterBasicLoaded() {
  syncRuleLocation();
  if (isLocationEnable.value) {
    getLocation();
  }
}

/** 返回上一页；没有上一页时回到首页 tab。 */
function handleBack() {
  uni.navigateBack({
    delta: 1,
    fail: () => {
      uni.switchTab({ url: "/pages/index/index" });
    },
  });
}

/** 打开异常处理申请菜单。 */
function openApplyPopup() {
  popupsRef.value?.openApply();
}

/** 打卡成功弹窗确认后刷新页面数据。 */
async function handleSignInOk() {
  await refresh(afterBasicLoaded);
  popupsRef.value?.closeSuccess();
}

/** 打卡状态过期后刷新规则，确保按钮文案跟随班次边界变化。 */
async function updateType() {
  if (!clockUpdateTime.value || refreshingClockState.value) return;
  if (clockUpdateTime.value > Date.now() / 1000) return;

  refreshingClockState.value = true;
  clockUpdateTime.value = 0;
  try {
    await refresh(afterBasicLoaded);
  } finally {
    refreshingClockState.value = false;
  }
}
</script>

<style lang="scss" scoped>
.custom-nav-bar {
  position: fixed;
  left: 0;
  width: 100%;
  top: 0;
  z-index: 500;
}

.main {
  padding-bottom: calc(54px + 24rpx);
}

:deep(.uni-navbar--fixed) {
  top: 0;
}

.uni-popup {
  z-index: 800;
}
</style>
