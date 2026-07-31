<template>
  <view class="ab-condition card">
    <view class="text">{{ $t('ui.attendanceAttendanceAbnormalCardPendingExceptionsThisMonth') }}</view>
    <navigator
      hover-class="none"
      class="nav-right"
      url="/pages/attendance/personalReport"
      animation-type="slide-in-right"
    >
      <text class="days">{{ abnormal }}{{ $t('ui.attendanceAttendanceAbnormalCardDay') }}</text>
      <text class="iconfont icon-jinru-copy"></text>
    </navigator>
  </view>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  abnormal?: number | string;
}>(), {
  abnormal: "",
});
</script>

<style lang="scss" scoped>
.card {
  background-color: #fff;
  padding: 28rpx 24rpx;
  border-radius: 12rpx;
  margin: 0 20rpx;
}

.card.ab-condition {
  margin-bottom: 20rpx;
}

.ab-condition {
  display: flex;
  justify-content: space-between;
  align-items: center;

  .text {
    font-size: 28rpx;
    font-weight: 400;
    color: #303133;
  }

  .nav-right {
    display: flex;
    align-items: center;

    .days {
      font-size: 24rpx;
      line-height: 24rpx;
      font-weight: 400;
      color: #606266;
      margin-right: 12rpx;
    }

    .icon-jinru-copy {
      color: #c0c4cc;
      font-size: 24rpx;
      line-height: 24rpx;
    }
  }
}
</style>
