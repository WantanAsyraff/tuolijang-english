<template>
  <view>
    <view class="stage mb10">
      <view class="time">{{ stage.time }}</view>
      <view class="status">
        <view class="status-time">
          <view class="title">
            <view>{{ stage.title }} {{ stage.record ? stage.record.clock_time : "" }}</view>
            <!-- 跨天辅助标签，例如“昨日班次”，用于说明该节点不是今天开始的班次。 -->
            <view v-if="stage.label" class="stage-label">{{ stage.label }}</view>
            <view v-if="stage.record" class="title">
              <view v-for="tag in stage.tags" :key="tag.text" class="tag" :class="tag.className">
                {{ tag.text }}
              </view>
            </view>
          </view>
          <view v-if="stage.record && stage.action">
            <view class="renew" @click="emit('action', stage)">
              {{ stage.action.text }}
            </view>
          </view>
        </view>

        <template v-if="stage.record">
          <view v-if="stage.macText" class="address mt10">
            <image src="@/static/image/wireless.png" class="wireless-icon" />
            <text class="line1">{{ stage.macText }}</text>
          </view>
          <view v-else-if="stage.addressText" class="address mt10">
            <text class="iconfont icon-kaoqin-dingwei"></text>
            <text class="line1">{{ stage.addressText }}</text>
          </view>
        </template>
      </view>
    </view>

    <view class="stage content-box mb10">
      <view v-if="stage.showLine" class="line"></view>
      <view class="content">
        <view v-if="stage.remark" class="adr">
          <text class="iconfont icon-kaoqin-beizhu1"></text>
          {{ stage.remark }}
        </view>
        <view v-if="stage.images.length" class="imgs">
          <ImageGallery v-for="(img, index) in stage.images" :key="index" :image-url="img" />
        </view>
        <view v-if="stage.showClockButton" class="clock">
          <!-- 圆形打卡按钮由父组件插入，确保按钮只出现在当前可打卡节点下方。 -->
          <slot />
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import ImageGallery from "@/components/ImageGallery/index.vue";
import type { AttendanceStageViewModel } from "../composables/attendanceTypes";

defineProps<{
  stage: AttendanceStageViewModel;
}>();

const emit = defineEmits<{
  (e: "action", stage: AttendanceStageViewModel): void;
}>();
</script>

<style lang="scss" scoped>
.stage {
  display: flex;
  align-items: center;

  .time {
    font-size: 32rpx;
    font-weight: 500;
    color: #303133;
    line-height: 32rpx;
    margin-right: 24rpx;
    width: 88rpx;
    text-align: center;
  }

  .status {
    flex: 1;

    .status-time {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .title {
        font-size: 30rpx;
        font-weight: 500;
        color: #303133;
        display: flex;
        align-items: center;

        .tag {
          font-size: 20rpx;
          font-weight: 400;
          line-height: 20rpx;
          padding: 6rpx;
          border-radius: 2px;
          opacity: 1;
          margin-left: 10rpx;
          transform: scale(0.9);
        }

        .stage-label {
          font-size: 20rpx;
          font-weight: 400;
          line-height: 20rpx;
          padding: 6rpx;
          border-radius: 2px;
          color: #308bf8;
          border: 1rpx solid #308bf8;
          margin-left: 10rpx;
          transform: scale(0.9);
          white-space: nowrap;
        }

        .lack {
          color: #ed4014;
          border: 1rpx solid #ed4014;
        }

        .out {
          color: #19be6b;
          border: 1rpx solid #19be6b;
        }

        .be-late {
          color: #ff9900;
          border: 1rpx solid #ff9900;
        }

        .be-add {
          color: #19be6b;
          border: 1rpx solid #19be6b;
        }
      }

      .renew {
        font-size: 24rpx;
        font-weight: 400;
        color: #308bf8;
        line-height: 24rpx;
      }
    }

    .address {
      font-size: 24rpx;
      font-weight: 400;
      color: #909399;
      line-height: 26rpx;
      margin-top: 16rpx;
      max-width: 550rpx;
      display: flex;
      align-items: center;

      .wireless-icon {
        width: 24rpx;
        height: 24rpx;
        margin-right: 6rpx;
      }

      .icon-kaoqin-dingwei {
        font-size: 24rpx;
        margin-right: 2rpx;
      }
    }
  }

  .line {
    width: 4rpx;
    background-color: #eeeeee;
  }

  .content {
    flex: 1;
    width: auto;
    min-height: 74rpx;
    padding-left: 62rpx;
  }
}

.content-box {
  align-items: stretch;
  padding-left: 48rpx;

  .content {
    .adr {
      font-size: 24rpx;
      font-weight: 400;
      color: #909399;
      line-height: 24rpx;

      .icon-kaoqin-beizhu1 {
        font-size: 20rpx;
      }
    }

    .imgs {
      margin-top: 28rpx;
      width: 100rpx;
      height: 100rpx;
      border-radius: 9rpx;
      margin-right: 12rpx;
      margin-bottom: 12rpx;
    }

    .clock {
      margin-left: -104rpx;
      padding: 82rpx 0 60rpx;
    }
  }
}
</style>
