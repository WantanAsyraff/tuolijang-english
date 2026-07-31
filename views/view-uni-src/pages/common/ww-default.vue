<!-- 企业微信默认空白页面 -->
<template>
  <view class="ww-empty">
    <image src="@/static/image/empty08.png" class="empty-img" mode="aspectFit" />
    <view class="empty-text">
      <text>{{ $t('ui.commonWwDefaultNoCustomerData') }}</text>
    </view>
  </view>
</template>

<script setup lang="ts">
</script>

<style scoped lang="scss">
.ww-empty {
  height: 100vh;
  background-color: #fff;
  padding-top: 20vh;
}

.empty-img {
  display: block;
  width: 50vw;
  height: auto;
  aspect-ratio: 264 / 248;
  margin: auto;
}

.empty-text {
  text-align: center;
  font-size: 24rpx;
}
</style>
