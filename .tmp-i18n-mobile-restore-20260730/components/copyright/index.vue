<template>
  <view class="copyright">
    <text class="text text-center">Copyright © {{ new Date().getFullYear() }} 陀螺匠. All Rights Reserved</text>
  </view>
</template>

<script setup lang="ts">
</script>

<style lang="scss" scoped>
.copyright {
  .text {
    display: block;
    color: #909399;
    font-size: 24rpx;
    margin: 40rpx 0;
  }
}
</style>
