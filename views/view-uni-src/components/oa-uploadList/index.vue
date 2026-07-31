<template>
  <view class="flie">
    <view class="box" v-for="(item, indexs) in listData" :key="indexs">
      <view class="left">
        <image @click.stop="preview(item)" class="slot-image" :src="`/static/image/cloudfile/${isFileTypeIcon(item.name || item.real_name)}`">
        </image>
        <view style="width: calc(100% - 40px)">
          <view class="name">
            {{ item.name || item.real_name || '--' }}
          </view>
          <view class="size" v-if="item.size || item.att_size"> {{ formatBytes(item.size || item.att_size) || '--' }} </view>
        </view>
        <view v-if="clearBtn" class="iconfont icon-guanbi-yangshiyi1" @click.stop="deleteFile(indexs)"> </view>
      </view>
    </view>
  </view>
</template>
<script setup>
import { toRefs } from 'vue'
import { formatBytes } from '@/utils/file'
import { lookPreview, fileSizeOne, isTypeImage, isFileTypeIcon } from '@/utils/helper'
const props = defineProps({
  listData: {
    type: Array,
    default() {
      return []
    },
  },
  // 是否显示删除按钮
  clearBtn: {
    type: Boolean,
    default: true,
  },
})
const { listData, clearBtn } = toRefs(props)

// 图片与文档预览
const preview = (item) => {
  lookPreview(item.url, item.name, [item.url])
}

// 附件删除
const deleteFile = (indexs) => {
  emit('deleteFile', indexs)
  listData.value.splice(indexs, 1)
}
const emit = defineEmits(['deleteFile'])
</script>

<style scoped lang="scss">
.flie {
  padding: 0rpx 24rpx 0rpx 0;

  .box {
    width: 100%;
    // height: 78rpx;
    background: #f6f7f9;
    border-radius: 4px 4px 4px 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12rpx;
    padding-right: 20rpx;

    .icon-guanbi-yangshiyi1 {
      color: #999999;
    }

    .left {
      width: 100%;
      display: flex;
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      align-items: center;

      .slot-image {
        // display: inline-block;
        flex-shrink: 0; // flex布局下图片挤压变形
        width: 52rpx;
        height: 52rpx;
        // margin-right: 10rpx;
        // margin-left: 12rpx;
        margin: 8rpx 10rpx 8rpx 12rpx;
      }

      .name {
        width: calc(100% - 40px);
        text-align: left;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 24rpx;
        color: #303133;
      }

      .size {
        font-size: 20rpx;
        color: #909399;
        // margin-top: 2rpx;
        text-align: left;
      }
    }
  }
}
</style>
