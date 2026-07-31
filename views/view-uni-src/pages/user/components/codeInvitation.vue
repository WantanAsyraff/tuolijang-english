<template>
  <view>
    <uni-popup ref="popupRef" type="center" :mask-click="true">
      <view class="slider">
        <image class="bg" src="/static/image/user-bg.png" mode=""></image>
        <view class="position title">{{userInfo.real_name}} {{ $t('ui.userCodeInvitationInvitesYouToScanAndJoin') }}</view>
        <view class="position company">
          <view class="line1">{{enterpriseInfo.enterprise_name}}</view>
        </view>
        <view class="position code">
          <uqrcode ref="uqrcode" canvas-id="qrcode" size="212" :value="HTTP_REQUEST_URL+'/work/pages/workbench/index'" :options="{ margin: 6 }"></uqrcode>
        </view>
        <view class="position name">{{ $t('ui.userCodeInvitationFrom') }}<text>{{ $t('ui.userCodeInvitationTuoluojiang') }}</text>{{ $t('ui.userCodeInvitationInvitation') }}</view>
        <view class="position share-button">
          <button type="default" @click="generatePoster">{{ $t('ui.userCodeInvitationGeneratePoster') }}</button>
        </view>
      </view>
    </uni-popup>
    <draw-poster ref="drawPosterRef" :width="710" :height="972" :data="data.posterData"></draw-poster>
  </view>
</template>

<script setup>
import drawPoster from "@/components/drawPoster/index.vue";
import { HTTP_REQUEST_URL } from "@/config/app";
import { useStore } from "vuex";
const store = useStore();

const enterpriseInfo = computed(() => store.state.app.enterprise);
const userInfo = computed(() => store.state.app.userInfo);

const data = reactive({
  posterData: []
});

const popupRef = ref(null);
const drawPosterRef = ref(null);

// 打开弹出
const popupOpen = () => {
  popupRef.value.open();
};

// 关闭
const generatePoster = () => {
  data.posterData = [{
    type: "image",
    path: "/static/image/user-bg.png",
    x: 0,
    y: 0,
    w: 710,
    h: 972
  }, {
    type: "text",
    path: `${userInfo.value.real_name} 邀请你扫码加入`,
    x: 10,
    y: 58,
    mw: 710,
    fs: 28,
    fw: 400,
    color: "#303133",
    align: "center"
  }, {
    type: "text",
    path: `${enterpriseInfo.value.enterprise_name}`,
    x: 10,
    y: 120,
    mw: 710,
    fs: 36,
    fw: "bold",
    color: "#303133",
    align: "center"
  }];
  drawPosterRef.value.poster();
};

defineExpose({ popupOpen });
</script>

<style lang="scss" scoped>
  ::v-deep .uni-popup {
    z-index: 100;
  }

  .slider {
    position: relative;
    height: 972rpx;
    width: 710rpx;

    .position {
      position: absolute;
      left: 0;
      width: 100%;
      display: flex;
      justify-content: center;
      padding: 0 30rpx;
    }

    .title {
      top: 58rpx;
      font-size: 28rpx;
      font-weight: 400;
      color: $uni-text-color;
    }

    .company {
      top: 118rpx;
      font-size: 36rpx;
      font-weight: 600;
      color: $uni-text-color;
    }

    .code {
      top: 296rpx;
    }

    .name {
      top: 750rpx;
      font-size: 26rpx;
      font-weight: 400;
      color: $nui-text-color-four;

      uni-text {
        color: $nui-text-color-two;
        padding: 0 10rpx;
      }
    }

    .share-button {
      top: 816rpx;

      uni-button {
        width: 212px;
        height: 64rpx;
        background-color: transparent;
        font-size: 26rpx;
        line-height: 64rpx;
        border: 1px solid #EBEEF5;

        &::after {
          border: none;
          border-radius: 8rpx;
        }
      }
    }

    .bg {
      height: 100%;
      width: 100%;
    }
  }
</style>
