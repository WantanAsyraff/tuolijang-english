<template>
  <view class="attendance-card card">
    <view v-for="item in items" :key="item.key">
      <!-- showOn/showOff 已在数据层按日期归一化；hidden 的节点不参与当天时间线展示。 -->
      <AttendanceStageRecord v-if="!item.on.hidden" :stage="item.on" @action="handleStageAction">
        <SignInButton
          :text="clockButton.text"
          :variant="clockButton.variant"
          :disabled="clockButton.disabled"
          :time-text="timeText"
          :range-text="clockButton.rangeText"
          :show-address="clockButton.showAddress"
          :address-text="clockButton.addressText"
          :show-wifi-info="clockButton.showWifiInfo"
          :wifi-ssid="clockButton.wifiSsid"
          :wifi-bssid="clockButton.wifiBssid"
          :is-wifi-range="clockButton.isWifiRange"
          @click="emit('clockClick')"
          @refresh-location="emit('refreshLocation')"
          @refresh-wifi="emit('refreshWifi')"
          @copy-wifi-mac="emit('copyWifiMac')"
        />
      </AttendanceStageRecord>

      <!-- 跨天场景下，昨日班次的下班卡会作为 off 节点展示在今天页面。 -->
      <AttendanceStageRecord v-if="!item.off.hidden" :stage="item.off" @action="handleStageAction">
        <SignInButton
          :text="clockButton.text"
          :variant="clockButton.variant"
          :disabled="clockButton.disabled"
          :time-text="timeText"
          :range-text="clockButton.rangeText"
          :show-address="clockButton.showAddress"
          :address-text="clockButton.addressText"
          :show-wifi-info="clockButton.showWifiInfo"
          :wifi-ssid="clockButton.wifiSsid"
          :wifi-bssid="clockButton.wifiBssid"
          :is-wifi-range="clockButton.isWifiRange"
          @click="emit('clockClick')"
          @refresh-location="emit('refreshLocation')"
          @refresh-wifi="emit('refreshWifi')"
          @copy-wifi-mac="emit('copyWifiMac')"
        />
      </AttendanceStageRecord>
    </view>
  </view>
</template>

<script setup lang="ts">
import SignInButton from "./signInButton.vue";
import AttendanceStageRecord from "./AttendanceStageRecord.vue";
import type {
  AttendanceShiftViewModel,
  AttendanceStageViewModel,
  ClockButtonViewModel,
} from "../composables/attendanceTypes";

defineProps<{
  items: AttendanceShiftViewModel[];
  clockButton: ClockButtonViewModel;
  timeText: string;
}>();

const emit = defineEmits<{
  (e: "clockClick"): void;
  (e: "stageRenew", index: number, type: AttendanceStageViewModel["workType"]): void;
  (e: "stageApply"): void;
  (e: "refreshLocation"): void;
  (e: "refreshWifi"): void;
  (e: "copyWifiMac"): void;
}>();

/** 将节点操作转换为父组件事件，具体弹窗/打卡逻辑由 useAttendanceClockFlow 处理。 */
function handleStageAction(stage: AttendanceStageViewModel) {
  if (stage.action?.type === "renew") {
    emit("stageRenew", stage.index, stage.workType);
    return;
  }

  if (stage.action?.type === "apply") {
    emit("stageApply");
  }
}
</script>

<style lang="scss" scoped>
.card {
  background-color: #fff;
  padding: 28rpx 24rpx;
  border-radius: 12rpx;
  margin: 0 20rpx;
}

.attendance-card {
  padding: 40rpx 24rpx 38rpx 24rpx;
}
</style>
