<template>
  <view>
    <view @click="handleShowPopup">
      <slot />
    </view>
    <uni-popup ref="popupRef" type="bottom">
      <view class="popup-content">
        <view class="popup-title">
          {{ props.title }}
          <view class="popup-close-btn iconfont icon-guanbi" @click="handleClosePopup"></view>
        </view>
        <view class="popup-list">
          <view class="popup-list-item" v-for="(item, index) in props.list" :key="index" @click="handleSelectItem(index)">
            {{ item }}
            <view class="iconfont icon-xuanzhong" v-if="index === props.activeIndex"></view>
          </view>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
const popupRef = ref<any>(null);

const props = defineProps<{
  title: string,
  list: string[],
  activeIndex: number
}>();

const emit = defineEmits(['selectItem']);

const handleShowPopup = () => {
  popupRef.value.open();
};

const handleClosePopup = () => {
  popupRef.value.close();
};

const handleSelectItem = (index: number) => {
  emit('selectItem', index);
  popupRef.value.close();
};
</script>

<style scoped lang="scss">
.popup-content {
  background-color: #fff;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.popup-title {
  position: relative;
  height: 102rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 30rpx;
  color: #303133;
  line-height: 42rpx;
}

.popup-close-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: 20px;
}

.popup-list {
  padding: 40rpx;
}

.popup-list-item {
  display: flex;
  align-items: center;
  justify-content: space-between;

  &+& {
    margin-top: 60rpx;
  }

  .iconfont {
    font-size: 20px;
    color: #359eff;
  }
}
</style>
