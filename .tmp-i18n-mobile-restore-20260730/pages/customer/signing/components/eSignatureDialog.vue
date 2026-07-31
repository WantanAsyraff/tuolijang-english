<template>
  <view>
    <uni-popup 
      ref="popup" 
      type="center" 
      border-radius="16rpx"


    >
      <view class="content">
        <view class="title">签署方签约</view>
      <view class="iconfont icon-shenpizhongxin-jujue" @click="handlePopupClose" />
        <view class="qrcode-box">
          <view v-if="isLoading" class="loading-tip">二维码生成中...</view>
          <canvas 
            ref="qrcodeCanvas" 
            id="qrcodeId" 
            class="qrcode-canvas"
            canvas-id="qrcodeId"
            @longpress="handleLongPress"
          ></canvas>
        </view>
        <view class="tips-text">长按保存二维码，微信扫码签约</view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup>
import { ref, nextTick, onUnmounted } from "vue";
import QRCode from 'weapp-qrcode';



const popup = ref(null);
const qrcodeCanvas = ref(null);
const currentUrl = ref('');
const isLoading = ref(false);

// 打开弹窗（默认使用你提供的链接）
const openBox = (url) => {
  if (!url) {
    uni.showToast({ title: '无效的链接地址', icon: 'none' });
    return;
  }
  currentUrl.value = url;
    isLoading.value = true;

  popup.value?.open();
    nextTick(() => {
    setTimeout(() => {
      generateQRCode(currentUrl.value);
    }, 200);
  });
};



// 核心：生成兼容小程序跳转的二维码
const generateQRCode = (url) => {
  try {
   

    QRCode({
      canvasId: 'qrcodeId',
      width:166, // 增大尺寸，提升微信扫码识别率
      height: 166,
      padding: 10, // 增加内边距，避免边缘裁切
      correctLevel: 3, // 最高容错率
      background: '#ffffff',
      foreground: '#000000',
      text: url,
      callback: (res) => {
        console.log('二维码生成成功：', res);
        isLoading.value = false;
      }
    });
  } catch (e) {
    console.error('二维码生成失败：', e);
    isLoading.value = false;
    uni.showToast({ title: '二维码生成失败', icon: 'none' });
  }
};

// 长按保存逻辑
const handleLongPress = () => {
  if (isLoading.value) {
    uni.showToast({ title: '二维码未生成', icon: 'none' });
    return;
  }

  uni.canvasToTempFilePath({
    canvasId: 'qrcodeId',
    success: (res) => {
      uni.saveImageToPhotosAlbum({
        filePath: res.tempFilePath,
        success: () => {
          uni.showToast({ title: '保存成功，请在微信内识别', icon: 'success' });
        },
        fail: (err) => {
          if (err.errMsg.includes('auth deny')) {
            uni.showModal({
              title: '提示',
              content: '需要授权相册权限才能保存二维码',
              confirmText: '去授权',
              success: (modalRes) => modalRes.confirm && uni.openSetting()
            });
          } else {
            uni.showToast({ title: '保存失败', icon: 'none' });
          }
        }
      });
    },
    fail: () => {
      uni.showToast({ title: '图片转换失败', icon: 'none' });
    }
  });
};

// 弹窗关闭清理
const handlePopupClose = () => {
  currentUrl.value = '';
  isLoading.value = false;
  try {
    const ctx = uni.createCanvasContext('qrcodeId');
    ctx.clearRect(0, 0, 400, 400);
    ctx.draw(true);
  } catch (e) {
    console.warn('清空canvas失败：', e);
  }
    popup.value?.close();
};

onUnmounted(() => {
  currentUrl.value = '';
  isLoading.value = false;
});

defineExpose({ openBox });
</script>

<style lang="scss" scoped>
.content {
  width: 560rpx;
  padding: 52rpx 0 60rpx 0;
  background: #FFFFFF;
  border-radius: 16rpx;
  position: relative;
  .icon-shenpizhongxin-jujue {
    position: absolute;
    top: 30rpx;
    right: 30rpx;
    color: #C0C4CC;
    font-size: 30rpx;
  }

  .title {
    text-align: center;
    font-weight: 500;
    font-size: 30rpx;
    color: #2B2C32;
    margin-bottom: 40rpx;
  }

  .qrcode-box {
    width: 166px;
    height: 166px;
    margin: 0 auto;
    position: relative;

    .loading-tip {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 24rpx;
      color: #999;
      z-index: 1;
    }

    .qrcode-canvas {
      width: 100%;
      height: 100%;
      background-color: #fff;
    }
  }

  .tips-text {
    text-align: center;
font-size: 12px;
color: #303133;
    margin-top: 48rpx;
    font-weight: 400;
  }

 
}
</style>