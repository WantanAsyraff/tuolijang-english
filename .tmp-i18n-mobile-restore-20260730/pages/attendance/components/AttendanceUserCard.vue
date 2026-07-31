<template>
  <view v-if="userInfo" class="header mb10" :class="{ 'no-map': !isLocationEnable }">
    <view class="user card">
      <view class="user-msg">
        <image class="avatar" :src="userInfo.avatar" mode=""></image>
        <view>
          <view class="name">{{ userInfo.name }}</view>
          <view class="position">{{ userJobInfo }}</view>
        </view>
      </view>
      <navigator
        v-if="shift && !isWhite"
        class="rule"
        url="/pages/attendance/rules"
        open-type="navigate"
        hover-class="navigator-hover"
      >
        考勤规则
      </navigator>
    </view>
  </view>
</template>

<script setup lang="ts">
interface AttendanceUserInfo {
  avatar?: string;
  name?: string;
}

defineProps<{
  userInfo: AttendanceUserInfo | null;
  userJobInfo: string;
  shift: unknown;
  isWhite: boolean;
  isLocationEnable: boolean;
}>();
</script>

<style lang="scss" scoped>
.header {
  padding-top: 20rpx;

  &.no-map {
    padding-top: calc(20rpx + 44px + var(--status-bar-height));
  }

  .user {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 20rpx;

    .user-msg {
      display: flex;
      align-items: center;

      .avatar {
        width: 80rpx;
        height: 80rpx;
        border-radius: 8rpx;
        margin-right: 22rpx;
      }

      .name {
        font-size: 30rpx;
        font-weight: 500;
        color: #303133;
        line-height: 30rpx;
        margin-bottom: 18rpx;
      }

      .position {
        font-size: 24rpx;
        font-weight: 400;
        color: #909399;
        line-height: 24rpx;
      }
    }

    .rule {
      font-size: 24rpx;
      font-weight: 400;
      color: #308bf8;
      line-height: 24rpx;
    }
  }
}

.card {
  background-color: #fff;
  padding: 28rpx 24rpx;
  border-radius: 12rpx;
  margin: 0 20rpx;
}
</style>
