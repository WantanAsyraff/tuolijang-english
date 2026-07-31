<template>
  <view>
    <uni-popup ref="popupRef" type="bottom" :mask-click="true">
      <view class="todo-type-popup">
        <view class="popup-content">
          <view
            class="type-item"
            v-for="(item, index) in typeList"
            :key="index"
            :class="{ active: currentIndex === index }"
            @click="selectType(item, index)"
          >
            {{ item.name }}({{ item.count }})
          </view>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits(['change'])
const props = defineProps({
  // 自定义导航栏列表与defaultType为1时，同时使用
  typeList: {
    type: Array,
    default: () => {
      return []
    },
  },
})

const { typeList } = toRefs(props)
const popupRef = ref(null)
const currentIndex = ref(0)

const popupOpen = () => {
  popupRef.value.open()
}

const selectType = (item, index) => {
  currentIndex.value = index
  emit('change', item)
  setTimeout(() => {
    cancel()
  }, 300)
}

const cancel = () => {
  popupRef.value.close()
}

defineExpose({ popupOpen })
</script>

<style lang="scss" scoped>
.todo-type-popup {
  width: 100%;
  height: calc(100vh - 200px);
  overflow-y: auto;
  background-color: #fff;
  border-radius: 24rpx 24rpx 0px 0px;
  padding-bottom: env(safe-area-inset-bottom);

  .popup-content {
    .type-item {
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 114rpx;
      line-height: 114rpx;
      border-bottom: 1px solid #eeeeee;
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      font-size: 30rpx;
      color: #303133;
    }
    .active {
      color: #1890ff !important;
    }
  }
}
</style>
