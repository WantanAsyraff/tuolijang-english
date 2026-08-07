<template>
  <view class="base-bottom-btn-box">
    <view
      class="base-bottom-btn"
      :style="{ backgroundColor: disabled ? '#F7F7F7' : '#308BF8', color: disabled ? '#C0C4CC' : '#FFFFFF' }"
      @click="emit('click')"
    >
      {{ text }}
    </view>
  </view>
</template>

<script setup lang="ts">import appI18n from '@/locale';

const props = withDefaults(
  defineProps<{
    text: string
    disabled?: boolean
  }>(),
  {
    text: appI18n.global.t('ui.replyComponentIndexSubmit'),
    disabled: false,
  },
)

const emit = defineEmits<{
  click: []
}>()
</script>

<style scoped lang="scss">
.base-bottom-btn-box {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 120rpx;
  background-color: #fff;
  padding: 18rpx 20rpx calc(var(--bottom-area-height) + 16rpx) 20rpx;
}

.base-bottom-btn {
  height: 100%;
  background: #308bf8;
  border-radius: 12rpx;
  font-size: 30rpx;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
