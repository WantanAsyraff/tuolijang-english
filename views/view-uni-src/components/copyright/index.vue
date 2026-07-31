<template>
  <view class="copyright">
    <text class="text text-center">Copyright © {{ new Date().getFullYear() }} {{ $t('ui.copyrightIndexTuoluojiangAllRightsReserved') }}</text>
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
