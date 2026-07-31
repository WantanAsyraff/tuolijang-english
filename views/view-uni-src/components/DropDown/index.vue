<template>
  <!--  -->
  <view class="drop-down"
    :style="{ display: moreButton ? 'block' : 'none', '--top': position.pageY, '--left': position.pageX }">
    <view class="modal-ang"></view>
    <view class="drop-down-list">
      <view class="drop-down-list-item" v-for="(item, index) in listData" :key="'meus' + index"
        @click="dropDownItem(item)">
        <text class="iconfont" :class="item.icon"></text>
        <text>{{ $ts(item.name) }} </text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
const moreButton: Ref<boolean> = ref(false)
const position = ref({
  pageX: 0,
  pageY: 0
})

// 关闭弹出
const closeDropdown = () => {
  moreButton.value = false
}
// 打开弹出
const openDropdown = (pageX: number, pageY: number) => {
  moreButton.value = true
  position.value = {
    pageX,
    pageY
  }
}

const emit = defineEmits(['btnClick'])
const dropDownItem = (item: ShiftDropMenuItem): void => {
  emit('btnClick', item)
}

defineExpose({ openDropdown, closeDropdown })
const props = withDefaults(
  defineProps<{
    listData: ShiftDropMenuItem[]
  }>(),
  {
    listData: <any>[]
  }
)
const { listData } = toRefs(props)
</script>

<style scoped lang="scss">
.drop-down {

  .drop-down-list {
    font-size: 28rpx;
    font-weight: 400;
    color: $nui-text-color-two;
    line-height: 28rpx;
    position: absolute;
    top: calc(var(--top) * 1px);
    // left: calc(var(--left) * 1px - 150rpx);
    right: 20rpx;
    box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.16);
    border-radius: 12rpx;
    padding: 32rpx 30rpx;
    min-width: 150rpx;
    background-color: #fff;
    border: 1px solid $uni-line-style-color-three;

    .drop-down-list-item {
      margin-bottom: 44rpx;

      &:last-of-type {
        margin-bottom: 0;
      }

      .iconfont {
        font-size: 32rpx;
        color: $nui-text-color-four;
        margin-right: 14rpx;
      }
    }
  }

  .modal-ang {
    width: 0px;
    height: 0px;
    position: absolute;
    top: calc(var(--top) * 1px - 6px);
    left: calc(var(--left) * 1px);
    border-color: transparent;
    border-style: solid;
    border-width: 6px;
    filter: drop-shadow(0 2px 12px rgba(0, 0, 0, 0.16));
    border-top-width: 0;
    border-bottom-color: $uni-line-style-color-three;
    z-index: 2;

    &::after {
      content: '';
      position: absolute;
      display: block;
      width: 0;
      height: 0;
      border-color: transparent;
      border-style: solid;
      border-width: 6px;
      top: 1px;
      margin-left: -6px;
      border-top-width: 0;
      border-bottom-color: #fff;
    }
  }
}
</style>