<template>
  <view>
    <uni-popup ref="popupRef" type="bottom" :mask-click="true">
      <view class="slider">
        <view v-for="(item, index) in dataList" :key="index" class="item" @click="handleItem(item)">
				 <image :src="item.image" class="image"></image>
         <text >{{ item.name }}</text>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive, toRefs } from "vue";
import message from "@/utils/message";

const props = defineProps({
  dataList: {
    type: Array,
    default: () => {
      return [
				
			];
    }
  },

});
const { dataList } = toRefs(props);
const emit = defineEmits(["handleClickItem", "change"]);
const popupRef = ref(null);


const data = reactive({});

// 打开弹出
const popupOpen = () => {
  popupRef.value.open();

};
const handleItem = (item) => {
  emit("handleClickItem", item);
  cancel()
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
	padding-top: 20rpx;
  .item {
    height: 120rpx;
    border-bottom: 1rpx solid #EEEEEE;
    line-height: 114rpx;
    font-weight: 400;
    font-size: 30rpx;
		padding: 0 50rpx;
		display: flex;
		align-items: center;
    cursor: pointer;
  }
	.image {
		width: 64rpx;
		height: 64rpx;
		margin-right: 30rpx;
	}
}
</style>