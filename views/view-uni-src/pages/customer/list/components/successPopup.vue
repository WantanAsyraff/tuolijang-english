<template>
  <view>
    <uni-popup ref="popupRef" type="center" :mask-click="true">
      <view class="content">
        <view class="header">

          <image class="img" v-if="type === 0" src="@/static/image/customer-pop-bg.png" mode="">
          </image>
          <image class="img" v-if="type === 1" src="@/static/image/customer-pop-bg01.png" mode="">
          </image>
        </view>
        <view class="body">
          <view class="title">{{title}}{{ $t('ui.customerListSuccessPopupAddedSuccessfully') }}</view>
          <view class="capitin text-center">{{ $t('ui.customerListSuccessPopupAddedSuccessfullyYouCanContinueTo') }}{{buttonTitle}}</view>
          <view class="plr10 button-content">
            <button class="button cancel" @click="cancel">{{ $t('ui.customerListSuccessPopupNotNow') }}</button>
            <button class="button define" @click="dialogInputConfirm">{{buttonTitle}}</button>
          </view>
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup>
import { ref, toRefs } from "vue";

const props = defineProps({
  type: {
    type: Number,
    default: 0,
    required: true
  },
  title: {
    type: String,
    default: "",
  },
  buttonTitle: {
    type: String,
    default: "",
  }
});

const emit = defineEmits(["change"]);

const { type, title, buttonTitle } = toRefs(props);

const popupRef = ref(null);
const eid = ref(0);

const popupOpen = (id) => {
  eid.value = id;
  popupRef.value.open();
};

// 关闭验证码
const cancel = () => {
  popupRef.value.close();
  emit("change", 1, eid.value);
};

const dialogInputConfirm = () => {
  emit("change", 2, eid.value);
  popupRef.value.close();
};

defineExpose({ popupOpen });
</script>

<style lang="scss" scoped>
  .content {
    width: 560rpx;
    position: relative;
    height: auto;
    padding-top: 166rpx;

    .header {
      .img {
        display: block;
        position: absolute;
        left: 0;
        top: 2rpx;
        width: 100%;
        height: 166rpx;
      }
    }

    .body {
      background-color: #fff;
      padding-top: 52rpx;
      padding-bottom: 50rpx;
      border-radius: 0 0 12rpx 12rpx;

      .title {
        position: absolute;
        left: 50%;
        top: 140rpx;
        font-size: 36rpx;
        font-weight: $uni-default-font-weight;
        color: $uni-text-color;
        transform: translate(-50%, 0);
      }

      .capitin {
        font-size: 28rpx;
        color: $nui-text-color-four;
      }

      .button-content {
        width: 100%;
        display: flex;
        align-items: center;
        padding-top: 64rpx;

        .button {
          width: calc((100% - 20rpx) / 2);
          margin-right: 20rpx;
          font-size: $uni-font-size-default;
          line-height: 86rpx;
          height: 86rpx;

          &:last-of-type {
            margin-right: 0;
          }

          &.cancel {
            background-color: #F0F1F5;
            color: $nui-text-color-four;
          }

          &.define {
            background-color: $uni-color-primary;
            color: #fff;
          }

          &::after {
            border: none;
          }
        }
      }
    }

  }
</style>
