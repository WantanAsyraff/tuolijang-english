<template>
  <view class="customer-tab-info">
    <view class="info-item" v-for="(item, index) in contractData" :key="index">
      <uni-row v-for="(val, index1) in item.data" :key="index1" style="margin-bottom:24rpx">
        <uni-col :span="5" class="text-right info-item-left">{{ val.key_name }}</uni-col>
        <uni-col :span="19" v-if="val.type == 'oaWangeditor'" class="info-item-right"><text
            v-html="val.value"></text></uni-col>
        <!-- 关联字典颜色 -->
        <view v-else-if="val.value && Object.prototype.hasOwnProperty.call(val.value, 'color')" class=" status-tag "
          :style="{
            color: val.value.color ? val.value.color : '#1890ff',
            background: val.value.color
              ? getColor(val.value.color, '0.1')
              : getColor('#1890ff', '0.1')
          }">
          {{ val.value.name }}
        </view>
        <uni-col :span="19" class="info-item-right" v-else-if="val.type == 'file'">
          <view class="file" v-if="val.files.length > 0">
            <!-- <view class="item" v-for="(fileItem,index) in val.files" :key="index" @click="preview(fileItem)">
              <view class="left">
                <image class="slot-image" :src="`/static/image/cloudfile/${isFileTypeIcon(fileItem.name)}`">
                </image>

                <view>
                  <view class="name">
                    {{fileItem.name }}
                  </view>
                  <view class="size">
                    {{formatBytes(fileItem.size)||'--' }}
                  </view>
                </view>
              </view> -->
            <!-- </view> -->
          </view>
          <view v-else>--</view>
        </uni-col>

        <uni-col :span="19" v-else-if="val.type == 'images'" class="info-item-right">
          <view class="images" v-if="val.files.length > 0">
            <img class="img" :src="imgItem.url" alt="" v-for="(imgItem, index) in val.files" :key="index"
              @click="preview(imgItem)">
          </view>
          <view v-else>--</view>
        </uni-col>
        <uni-col :span="19" v-else class="info-item-right">{{ cityFn(val.value) || '--' }}</uni-col>
      </uni-row>
    </view>

  </view>
</template>

<script setup>
import { formatBytes } from "@/utils/file";
import { isFileTypeIcon, lookPreview, getColor } from "@/utils/helper";
import { toRefs } from "vue";
const props = defineProps({
  contractData: {
    type: Array,
    default: () => {
      return [];
    }
  }
});
// 图片与文档预览
const preview = (item) => {
  lookPreview(item.url, item.name, [item.url]);
};

const cityFn = (val) => {
  let str = "";
  if (val == "") {
    str = "--";
  } else if (Array.isArray(val)) {
    str = val.toString();
  } else {
    str = val;
  }
  return str;
};
const { contractData } = toRefs(props);
</script>

<style scoped lang="scss">
.customer-tab-info {
  // padding-top: 26rpx;

  .info-item {
    font-size: $uni-font-size-default;
    color: $uni-text-color;
    margin-bottom: 20rpx;

    &:last-of-type {
      margin-right: 0;
    }

    .info-item-left {
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 26rpx;
      color: #606266;
      text-align: left;
    }

    .info-item-right {
      // padding-left: 40rpx !important;
      // line-height: 1.5;
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 26rpx;
      color: #303133;
    }
  }
}


.status-tag {
  height: 42rpx;
  border-radius: 8rpx;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 24rpx;
  font-weight: 400;
  padding: 0 10rpx;
}

.file {

  .item {
    padding-top: 10rpx;
    width: 100%;
    height: 78rpx;
    background: #F6F7F9;
    border-radius: 8rpx;
    margin-bottom: 12rpx;


    .slot-image {
      flex-shrink: 0; // flex布局下图片挤压变形   width: 52rpx;
      width: 52rpx;
      height: 52rpx;
      margin-right: 10rpx;
      margin-top: 4rpx;
    }

    .left {
      width: 100%;
      display: flex;
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;

      .name {
        width: calc(100% - 162px);
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 24rpx;
        color: #303133;
      }

      .size {
        font-size: 20rpx;
        color: #909399;
        margin-top: 2rpx;
      }
    }
  }
}

.images {
  .img {
    width: 82rpx;
    height: 82rpx;
    border-radius: 8rpx;
    margin-right: 16rpx;
  }
}

::v-deep .uni-row {
  margin-bottom: 24rpx;
}
</style>