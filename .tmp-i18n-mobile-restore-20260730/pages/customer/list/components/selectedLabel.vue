<template>
  <view>
    <uni-popup ref="popupRef" type="bottom" :mask-click="false">
      <view class="slider">
        <view class="share-header">
          <view class="share-title">{{ title }}</view>
          <view @click="cancel" class="iconfont icon-shenpizhongxin-jujue"></view>
        </view>
        <view class="search-bar">
          <uni-search-bar
            style="width: 100%"
            placeholder="搜索客户标签"
            :focus="false"
            bgColor="#F5F5F5"
            v-model="searchName"
            @clear="searchFn"
            @blur="searchFn"
          />
          <view class="right" @click="searchFn">搜索</view>
        </view>
        <view class="slider-laber">
          <!-- <scroll-view scroll-y="true" style="width: 100%;height: 100%;" show-scrollbar="true" scroll-with-animation="true"> -->
          <view v-for="(val, index) in data.showListData" :key="index">
            <text class="text" v-if="val.children.length > 0"> {{ val.name }}</text>
            <view class="slider-laber-item plr10">
              <button
                class="laber-item-button line1"
                :class="data.selectLabelData.includes(item.id) ? 'active' : ''"
                v-for="(item, index) in val.children"
                :key="'list' + index"
                @click="selectItemLabel(item)"
              >
                <text class="title line1">{{ item.name }}</text>
              </button>
            </view>
          </view>
          <!-- </scroll-view> -->
        </view>

        <view class="slider-laber-bottom">
          <button class="reset laber-bottom" @click="reset">重置</button>
          <button class="laber-bottom confirm" @click="confirm">确认</button>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup>
import { ref, reactive, toRefs, onMounted } from 'vue'
import message from '@/utils/message'
import { clientlabelApi } from '@/api/customer'
import { jsx } from 'vue/jsx-runtime'
const props = defineProps({
  typeData: {
    type: Object,
    default: () => {
      return []
    },
  },
  title: {
    type: String,
    default: '',
  },
})
const { title } = toRefs(props)

const emit = defineEmits(['changeItem'])
const popupRef = ref(null)
const data = reactive({
  listData: [],
  showListData: [],
  selectLabelData: [],
  selectLabelName: [],
})
const searchName = ref('')

// 打开弹出
const popupOpen = (val, name) => {
  popupRef.value.open()
  if (val) {
    data.selectLabelData = val
  }
  if (name) {
    data.selectLabelName = name
  }
}
onMounted(() => {
  getList()
})

const getList = () => {
  if (data.listData.length > 0) return false
  clientlabelApi().then((res) => {
    data.listData = res.data.list
    data.showListData = JSON.parse(JSON.stringify(data.listData))
  })
}

const searchFn = () => {
  if (!searchName.value) {
    data.showListData = JSON.parse(JSON.stringify(data.listData))
    return
  }

  data.showListData = []
  const list = JSON.parse(JSON.stringify(data.listData))

  list.forEach((item) => {
    const matchedChildren = item.children.filter((child) => child.name.indexOf(searchName.value) > -1)

    if (matchedChildren.length > 0) {
      data.showListData.push({
        ...item,
        children: matchedChildren,
      })
    }
  })
}

// 关闭
const cancel = () => {
  data.selectLabelData = []
  data.selectLabelName = []
  popupRef.value.close()
  searchName.value = ''
  searchFn()
}
// 标签选择
const selectItemLabel = (item) => {
  const index = data.selectLabelData.indexOf(item.id)
  if (index > -1) {
    data.selectLabelData.splice(index, 1)
    data.selectLabelName.splice(index, 1)
  } else {
    data.selectLabelData.push(item.id)
    data.selectLabelName.push(item.name)
  }
}

// 标签重置
const reset = () => {
  if (data.selectLabelData.length > 0) {
    data.selectLabelData = []
    data.selectLabelName = []

    // emit("resetLabel");
    emit('changeItem', [], data.selectLabelName)
  }
  cancel()
}

// 标签确认
const confirm = () => {
  if (data.selectLabelData.length <= 0) {
    message.error('至少选择一个' + title.value)
    return false
  }
  emit('changeItem', data.selectLabelData, data.selectLabelName)
  cancel()
}

defineExpose({
  popupOpen,
})
</script>

<style lang="scss" scoped>
::v-deep .uni-popup {
  z-index: 100;
}

.slider {
  position: relative;
  height: 80vh;
  width: 100%;
  background-color: #fff;
  border-radius: 20rpx 20rpx 0 0;

  .share-header {
    width: 100%;
    height: 102rpx;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    .image {
      width: 100%;
      height: 100%;
    }

    .share-title {
      font-weight: 500;
      font-size: 30rpx;
      color: #303133;
    }

    .iconfont {
      font-size: 30rpx;
      color: #c0c4cc;
      position: absolute;
      top: 30rpx;
      right: 32rpx;
    }
  }

  .slider-laber {
    // margin-top: -92rpx;
    height: calc(100% - 134rpx - 180rpx);
    overflow-y: auto;

    .text {
      display: inline-block;
      font-size: 28rpx;
      margin-left: 30rpx;
      margin-top: 40rpx;
    }

    .slider-laber-item {
      width: 100%;
      display: flex;
      align-items: center;
      flex-wrap: wrap;

      .laber-item-button {
        position: relative;
        width: calc((100% - 60rpx) / 3);
        height: 68rpx;
        line-height: 68rpx;
        background-color: #f7f7f7;
        border-radius: 8rpx;
        font-size: 28rpx;
        padding: 0 15rpx;
        margin: 0;
        color: $uni-text-color;
        overflow: visible;
        margin-top: 24rpx;
        margin-right: 30rpx;
        display: flex;

        &:nth-of-type(3n) {
          margin-right: 0;
        }

        &::after {
          border-color: #f0f1f5;
          border-radius: 8rpx;
        }

        &.active {
          background: rgba(24, 144, 255, 0.1);
          color: $uni-color-primary;

          &::after {
            border-color: $uni-color-primary;
          }

          .icon {
            display: block;
          }
        }

        .title {
          display: block;
          width: 100%;
          height: 100%;
          // display: flex;
          // align-items: center;
          // justify-content: center;
        }

        .icon {
          display: none;
          position: absolute;
          right: 0;
          bottom: 0;
          border: 20rpx solid $uni-color-primary;
          border-left: 20rpx solid transparent;
          border-top: 20rpx solid transparent;

          .iconfont {
            position: absolute;
            left: 0;
            top: -6rpx;
            color: #fff;
            z-index: 2;
            font-size: 22rpx;
            line-height: 1;
          }
        }
      }
    }
  }

  .slider-laber-bottom {
    position: absolute;
    display: flex;
    bottom: 30rpx;
    height: 86rpx;
    left: 30rpx;
    right: 30rpx;

    //padding-bottom: 20rpx;
    align-items: center;
    background-color: #fff;

    .laber-bottom {
      height: 86rpx;
      border-radius: 8rpx;
      border: none;
      font-size: $uni-font-size-default;
      line-height: 86rpx;

      &::after {
        border: 0;
      }
    }

    .reset {
      width: 184rpx;
      margin-right: 30rpx;
      background: #f0f1f5;
      color: $uni-text-color;
    }

    .confirm {
      width: calc(100% - 214rpx);
      background: #308bf8;
      color: #fff;
    }
  }
}
::v-deep .uni-searchbar__cancel {
  display: none;
}
::v-deep .uni-searchbar {
  padding: 0 30rpx;
}
.search-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  // padding: 0 30rpx;
  padding-right: 30rpx;
  margin-bottom: 16rpx;
  .right {
    cursor: pointer;
    flex-shrink: 0;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 28rpx;
    color: #303133;
  }
}
</style>
