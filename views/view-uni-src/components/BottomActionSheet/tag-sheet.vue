<template>
  <uni-popup ref="actionSheetRef" type="bottom" :mask-click="true" :mask-style="{ background: 'rgba(0,0,0,0.6)' }" :safe-area="false">
    <view class="action-sheet">
      <view class="action-sheet-title">
        <text>{{ actionSheetConfig.title }}</text>
        <text class="iconfont icon-guanbi close-btn" @click="closeActionSheet"></text>
      </view>
      <view class="tag-list">
        <view class="tag-item line1" v-for="tag of actionSheetConfig.options" :key="tag.id">
          {{ tag.name }}
        </view>
      </view>
    </view>
  </uni-popup>
</template>

<script setup lang="ts">
import { ref } from "vue";

const actionSheetRef = ref(null);
const actionSheetConfig = ref({
  title: "",
  options: []
});

interface ActionSheetConfig {
  title: string;
  options: {
    id: number;
    name: string;
  }[];
}

// 打开 actionsheet，传入 config
const openActionSheet = (config: ActionSheetConfig) => {
  actionSheetConfig.value = config;
  actionSheetRef.value.open();
};
const closeActionSheet = () => {
  actionSheetRef.value.close();
};

// 暴露方法给父组件调用
defineExpose({ openActionSheet });
</script>

<style scoped lang="scss">
.action-sheet {
  background: #fff;
  border-radius: 16rpx 16rpx 0 0;
  padding-bottom: calc(58rpx + var(--window-bottom));

  .action-sheet-title {
    text-align: center;
    font-weight: bold;
    font-size: 32rpx;
    padding: 32rpx 0 24rpx 0;
    position: relative;

    .close-btn {
      position: absolute;
      right: 32rpx;
      top: 32rpx;
      font-size: 24rpx;
      color: #C0C4CC;
    }
  }
}

.tag-list {
  padding-top: 36rpx;
  padding: 30rpx;
  gap: 30rpx;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  max-height: 50vh;
  overflow-y: auto;

  .tag-item {
    width: 100%;
    height: 68rpx;
    background: #F7F7F7;
    border-radius: 8rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 400;
    font-size: 28rpx;
    color: #303133;
    line-height: 40rpx;
  }
}

/* 覆盖遮罩层背景色 */
::v-deep .uni-popup__mask {
  background: rgba(0, 0, 0, 0.6) !important;
}
</style>
