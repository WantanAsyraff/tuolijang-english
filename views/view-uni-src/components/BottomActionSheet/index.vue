<template>
  <uni-popup ref="actionSheetRef" type="bottom" :mask-click="true" :mask-style="{ background: 'rgba(0,0,0,0.6)' }" :safe-area="false">
    <view class="action-sheet">
      <!-- <view class="action-sheet-title"> -->
        <!-- <text>{{ actionSheetConfig.title }} </text> -->
        <!-- <text class="iconfont icon-guanbi close-btn" @click="closeActionSheet"></text> -->
      <!-- </view> -->
      <view class="action-sheet-option" v-for="(item, idx) in actionSheetConfig.options" :key="idx"
        @click="onActionSelect(item)">
        {{ $ts(item.label) }}
      </view>
    </view>
  </uni-popup>
</template>

<script setup lang="ts">
import { ref } from "vue";

const actionSheetRef = ref(null);
const actionSheetConfig = ref({
  title: "",
  type: "",
  options: []
});

interface ActionSheetConfig {
  title: string;
  type: string;
  options: {
    label: string;
    value: string;
  }[];
}

const emit = defineEmits(["select"]);

// 打开 actionsheet，传入 config
const openActionSheet = (config: ActionSheetConfig) => {
  actionSheetConfig.value = config;
  actionSheetRef.value.open();
};
const closeActionSheet = () => {
  actionSheetRef.value.close();
};
// 选项点击
const onActionSelect = (item: ActionSheetConfig["options"][0]) => {
  // 这里可以 emit 或自定义逻辑
  emit("select", {
    value: item.value,
    type: actionSheetConfig.value.type
  });
  closeActionSheet();
};

// 暴露方法给父组件调用
defineExpose({ openActionSheet });
</script>

<style scoped lang="scss">
.action-sheet {
  background: #fff;
  border-radius: 24rpx 24rpx 0 0;

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

  .action-sheet-option {
    text-align: center;
    font-size: 30rpx;
    padding: 32rpx 0;
    border-bottom: 1rpx solid #EEEEEE;
  }
  .action-sheet-option:last-child {
    border-bottom: none;
  }
}

/* 覆盖遮罩层背景色 */
::v-deep .uni-popup__mask {
  background: rgba(0, 0, 0, 0.6) !important;
}
</style>
