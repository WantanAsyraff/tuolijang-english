<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <uni-nav-bar :left-text="$t('ui.baTreePickerIndexCancel')" @clickLeft="loginCancel">
        <view class="uni-nav-bar-text">{{typeIndex === 0 ? $t('ui.usersScanCodeIndexScanFailed') : $t('ui.usersScanCodeIndexScanSuccessful')}}</view>
      </uni-nav-bar>
    </view>
    <view class="code-content">
      <view class="code-tips">
        <template v-if="typeIndex === 1">
          <image class="image" src="@/static/image/users/code-success.png" mode=""></image>
          <view class="code-tips-text">{{ $t('ui.usersScanCodeIndexTuoluojiangLoginConfirmation') }}</view>
        </template>
        <template v-else>
          <image class="image" src="@/static/image/users/code-filled.png" mode=""></image>
          <view class="code-tips-text">{{ $t('ui.usersScanCodeIndexOnlyScanATuolujiangQrCode') }}</view>
          <view class="filled-text">
            {{ $t('ui.usersScanCodeIndexTuolujiangCannotProcessOtherQrCodesScanTheQr') }}{{HTTP_REQUEST_URL}}
          </view>
        </template>

      </view>

      <view class="code-button">
        <template v-if="typeIndex === 1">
          <button class="button" type="primary" :loading="loading" @click="ok">{{ $t('ui.usersScanCodeIndexConfirmAndSignIn') }}</button>
          <button class="button cancel" @click="loginCancel">{{ $t('ui.usersScanCodeIndexCancelSignIn') }}</button>
        </template>
        <template v-else>
          <button class="button" type="primary" @click="loginCancel">{{ $t('ui.usersScanCodeIndexResponse') }}</button>
        </template>

      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, type Ref } from "vue";
import { useStore } from "vuex";
import { HTTP_REQUEST_URL } from "@/config/app";
import { onLoad } from "@dcloudio/uni-app";
import { userScanLoginApi } from "@/api/user";
const loading: Ref<boolean> = ref(false);
const typeIndex: Ref<number> = ref(0);
const store = useStore();

interface O {
  type: number;
}
onLoad((options: O): void => {
  typeIndex.value = Number(options.type);
});

const cancel = (): any => {
  uni.navigateBack();
};
const getScanLogin = (data: object): void => {
  userScanLoginApi(data).then(() => {
    cancel();
  }).catch((error: object) => {
    console.log(error);
  });
};

// 确认登录
const ok = (): void => {
  const data: object = {
    key: store.state.app.codeSuccessKey,
    status: 1
  };
  getScanLogin(data);
};

// 取消
const loginCancel = (): void => {
  const data: object = {
    key: store.state.app.codeSuccessKey,
    status: 0
  };
  getScanLogin(data);
};
</script>

<style scoped lang="scss">
  .cr-position-header {
    position: sticky;

    ::v-deep .uni-navbar {
      .uni-navbar__header-container {
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .uni-navbar-btn-text {
        uni-text {
          padding-top: 8rpx;
          font-size: 32rpx !important;
          color: $uni-color-primary !important;
        }
      }

      .uni-nav-bar-text {
        font-size: 34rpx;
        font-weight: 500;
      }

      .uni-navbar__header-btns-right {
        justify-content: flex-end;
      }
    }
  }

  .code-content {
    width: 100%;

    .code-tips {
      display: flex;
      width: 80%;
      margin: 120rpx auto 0 auto;
      justify-content: center;
      align-items: center;
      flex-direction: column;

      .success-icon {
        color: $uni-color-primary !important;
      }

      .filled-icon {
        color: $uni-color-error !important;
      }

      .image {
        width: 260rpx;
        height: 260rpx;
      }

      .code-tips-text {
        padding-top: 20rpx;
        color: $uni-text-color;
        font-size: 34rpx;
      }

      .filled-text {
        padding-top: 10rpx;
        color: $nui-text-color-two;
        font-size: $uni-font-size-default;
        line-height: 1.5;
      }
    }

    .code-button {
      width: 100%;
      position: fixed;
      left: 0;
      bottom: 180rpx;

      .button {
        width: 50%;
        margin: 0 auto;
        height: 86rpx;
        line-height: 86rpx;
        font-size: $uni-font-size-default;
        margin-bottom: 30rpx;

        &:last-of-type {
          margin-bottom: 0;
        }

        &.cancel {
          color: $uni-color-primary;
          background-color: rgba(0, 0, 0, 0);

          &::after {
            border: none;
          }
        }

      }

    }

  }
</style>
