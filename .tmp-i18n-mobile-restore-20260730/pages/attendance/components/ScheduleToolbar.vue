<template>
  <view class="toolbar-wrapper">
    <navigator class="submit-btn navigator" hover-class="none" :url="`/pages/attendance/shift?groupId=${props.groupId}`">排班设置</navigator>
    <view class="submit-btn" @click="handleSubmit">提交</view>
  </view>
</template>

<script setup lang="ts">

const props = withDefaults(defineProps<{
  groupId: string;
}>(), {
  groupId: ''
});

const emit = defineEmits(["submit"]);

const handleSubmit = () => {
  emit("submit");
}
</script>

<style lang="scss" scoped>
.toolbar-wrapper {
  padding: 20rpx;
  background-color: #fff;
  display: flex;
  gap: 20rpx;
}

.submit-btn {
  height: 86rpx;
  flex: 1;
  background: #1890FF;
  border-radius: 12rpx;
  font-size: 30rpx;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;

  &.navigator {
    background: rgba(24, 144, 255, 0.1);
    color: #1890FF;
  }
}
</style>