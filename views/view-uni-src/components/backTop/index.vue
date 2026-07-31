<template>
  <view class="back-top" @click="goTop">
		<text class="iconfont icon-dingbutubiao"></text>
    <view>{{ $t('ui.backTopIndexTop') }}</view>
  </view>
</template>

<script setup lang="ts">
import { toRefs } from "vue";

let emit = defineEmits(["scrollToTop"]);
const goTop = ()=> {
	emit("scrollToTop");
}
</script>

<style scoped lang="scss">
.back-top {
  position: fixed;
	bottom: 200rpx;
	right: 34rpx;
	z-index: 10;
	width: 84rpx;
	height: 84rpx;
	border-radius: 100%;
	background: #fff;
	display: flex;
	flex-direction: column;
	color: #909399;
	font-size: 26rpx;
	text-align: center;
	justify-content: center;
	.iconfont {
		font-size: 32rpx;
		color: #333333;
	}
}
</style>
