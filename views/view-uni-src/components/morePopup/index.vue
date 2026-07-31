<template>
  <view>
    <uni-popup ref="popupRef" type="bottom" :mask-click="false">
      <view class="slider">
        <view class="title">
          {{ $t('ui.morePopupIndexOperation') }}
        </view>
        <view class="iconfont icon-shenpizhongxin-jujue" @click="cancel" />
        <view v-for="(item, index) in dataList" :key="index" class="item" >
         <text @click="handleItem(item)">{{ $ts(item.name) }}</text>
        </view>

      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref } from "vue";

const emit = defineEmits(["handleItem", "change"]);
const popupRef = ref(null);

const dataList = ref(null);

// 打开弹出
const popupOpen = (list) => {
  popupRef.value.open();
  dataList.value = list;
};
const handleItem = (item) => {
  emit("handleItem", item);
  cancel();
};

// 关闭
const cancel = () => {
  popupRef.value.close();
};

defineExpose({ popupOpen });
</script>

<style lang="scss" scoped>
::v-deep .uni-popup {
  z-index: 100;
}

.slider {
  position: relative;
  width: 100%;
  background-color: #fff;
  border-radius: 16rpx 16rpx 0px 0px;
  font-family: PingFang SC, PingFang SC;
  color: #303133;

  .title {
    height: 102rpx;
    font-weight: 500;
    font-size: 30rpx;
    line-height: 102rpx;
    text-align: center;
  }
  .icon-shenpizhongxin-jujue {
    position: absolute;
    top: 30rpx;
    right: 30rpx;
    color: #C0C4CC;
  }

  .item {
    height: 114rpx;
    border-bottom: 1rpx solid #EEEEEE;
    line-height: 114rpx;
    font-weight: 400;
    font-size: 30rpx;
    text-align: center;
  }
}
</style>