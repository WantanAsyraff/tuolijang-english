<template>
  <view>
    <view class="center">
      <view class="sign-in center" :class="variant" @click="handleClick">
        <view class="text mb10">{{ text }}</view>
        <view class="time">{{ timeText }}</view>
      </view>
    </view>

    <view v-if="rangeText" class="suc-range">{{ rangeText }}</view>

    <view v-if="showAddress" class="address">
      <text class="iconfont icon-kaoqin-dingwei"></text>
      <text class="line1">{{ addressText }}</text>
      <text class="upload" @click="emit('refreshLocation')">刷新</text>
    </view>

    <!-- #ifdef APP -->
    <view v-if="showWifiInfo" class="wifi-info">
      <view>WIFI: {{ wifiSsid }}</view>
      <view class="wifi-bssid">
        <view @click="emit('copyWifiMac')">MAC地址: {{ wifiBssid }}</view>
        <view class="refresh" @click="handleRefreshWifi">刷新</view>
      </view>
      <view>WIFI范围内: {{ isWifiRange ? "是" : "否" }}</view>
    </view>
    <!-- #endif -->
  </view>
</template>

<script setup lang="ts">
import { debounce } from "@/utils/helper";
import type { ClockButtonVariant } from "../composables/attendanceTypes";

const props = withDefaults(defineProps<{
  text: string;
  variant: ClockButtonVariant;
  disabled: boolean;
  timeText: string;
  rangeText?: string;
  showAddress?: boolean;
  addressText?: string;
  showWifiInfo?: boolean;
  wifiSsid?: string;
  wifiBssid?: string;
  isWifiRange?: boolean;
}>(), {
  rangeText: "",
  showAddress: false,
  addressText: "",
  showWifiInfo: false,
  wifiSsid: "--",
  wifiBssid: "--",
  isWifiRange: false,
});

const emit = defineEmits<{
  (e: "click"): void;
  (e: "refreshLocation"): void;
  (e: "refreshWifi"): void;
  (e: "copyWifiMac"): void;
}>();

const handleRefreshWifi = debounce(() => {
  emit("refreshWifi");
}, 500);

function handleClick() {
  if (props.disabled) return;
  emit("click");
}
</script>

<style lang="scss" scoped>
.center {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  margin-bottom: 20rpx;

  .sign-in.suc {
    background: linear-gradient(139deg, #ffc43c 0%, #ff9615 100%);
    box-shadow: 0px 5px 16px 0px rgba(255, 153, 0, 0.5);
  }

  .sign-in.err {
    background: linear-gradient(139deg, #acb7bf 0%, #8c9dac 99%);
    box-shadow: 0rpx 10rpx 32rpx 0rpx rgba(122, 140, 162, 0.5);
  }

  .sign-in.upload {
    background: linear-gradient(139deg, #29e084 0%, #19be6b 100%);
    box-shadow: 0rpx 10rpx 32rpx 0rpx rgba(25, 190, 107, 0.502);
  }

  .sign-in.normal {
    background: linear-gradient(139deg, #47b5ff 0%, #0f86f5 100%);
    box-shadow: 0rpx 10rpx 32rpx 0rpx rgba(48, 139, 248, 0.5);
  }

  .sign-in {
    width: 272rpx;
    height: 272rpx;
    border-radius: 50%;
    flex-direction: column;

    .text {
      font-size: 40rpx;
      font-weight: 500;
      color: #ffffff;
      line-height: 40rpx;
    }

    .time {
      font-size: 32rpx;
      font-weight: 400;
      color: #ffffff;
      line-height: 32rpx;
      opacity: 0.7;
    }
  }
}

.address {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28rpx;
  font-weight: 400;
  color: #909399;
  line-height: 28rpx;
  max-width: 500rpx;
  margin: 20rpx auto 0 auto;

  .icon-kaoqin-dingwei {
    font-size: 28rpx;
    margin-right: 12rpx;
  }

  .upload {
    color: #308bf8;
    white-space: nowrap;
    margin-left: 12rpx;
  }
}

.wifi-info {
  font-size: 28rpx;
  font-weight: 400;
  color: #909399;
  line-height: 28rpx;
  max-width: 500rpx;
  margin: 20rpx auto 0 auto;
  text-align: center;

  .wifi-bssid {
    margin: 20rpx 0;
    display: flex;
    justify-content: center;

    .refresh {
      color: #308bf8;
      white-space: nowrap;
      margin-left: 12rpx;
    }
  }
}

.suc-range {
  text-align: center;
  font-size: 28rpx;
  font-weight: 400;
  color: #606266;
  line-height: 28rpx;
  margin-top: 60rpx;
}
</style>
