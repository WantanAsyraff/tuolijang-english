<template>
  <view class="modal">
    <view class="head">
      <view class="title">{{ title }}</view>
      <view class="time">
        <text class="iconfont icon-kaoqin-shijian"></text>
        打卡时间 {{ timeString }}
      </view>
      <view class="address">
        <text class="iconfont icon-kaoqin-dingwei"></text>
        {{ address }}
      </view>
    </view>

    <view class="content">
      <uni-easyinput
        v-model="formData.text"
        type="textarea"
        :placeholder-style="placeholderStyle"
        :input-border="false"
        placeholder="请输入内容"
        :maxlength="140"
        :rows="10"
      />
      <view class="upload">
        <view v-for="(item, index) in formData.imgs" :key="item" class="box">
          <image class="img" :src="item" mode=""></image>
          <view class="delete" @click="deleteImg(index)">
            <text class="iconfont icon-shenpizhongxin-jujue"></text>
          </view>
        </view>
        <view v-if="formData.imgs.length < 4" class="upload-box" @click="uploadAvatar">
          <view class="iconfont icon-paizhao"></view>
        </view>
      </view>
    </view>

    <view class="bottom">
      <view class="cancel btn" @click="emit('cancel')">取消</view>
      <view class="line"></view>
      <view class="ok btn" @click="handleOk">确定</view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from "vue";
import { uploadImage } from "@/utils/file";

interface ExternalFormData {
  text: string;
  imgs: string[];
  is_external: number;
}

const props = withDefaults(defineProps<{
  isEffectiveRange?: boolean;
  isPic?: number;
  isText?: number;
  address?: string;
  onWork?: string;
  recordLength?: number;
}>(), {
  isEffectiveRange: false,
  isPic: 0,
  isText: 0,
  address: "",
  onWork: "",
  recordLength: 0,
});

const emit = defineEmits<{
  (e: "cancel"): void;
  (e: "ok", value: ExternalFormData): void;
}>();

const formData = reactive<ExternalFormData>({
  text: "",
  imgs: [],
  is_external: 0,
});
const placeholderStyle = ref("color: #C0C4CC;font-size: 28rpx");
const timeString = ref(formatCurrentTime());
const title = computed(() => (
  props.isEffectiveRange
    ? "您需要进行拍照打卡，是否继续打卡？"
    : "您不在考勤范围内，是否继续打卡？"
));

function handleOk() {
  formData.is_external = props.isEffectiveRange ? 0 : 1;
  emit("ok", {
    text: formData.text,
    imgs: [...formData.imgs],
    is_external: formData.is_external,
  });
}

function uploadAvatar() {
  uploadImage("attach/imgs", {
    way: 3,
  }, 5, ["camera"])
    .then((res) => {
      formData.imgs.push(res.data.src);
      uni.showToast({
        title: "上传成功",
        icon: "none",
      });
    })
    .catch((error) => {
      uni.showToast({
        title: error,
        icon: "none",
      });
    });
}

function deleteImg(index: number) {
  formData.imgs.splice(index, 1);
}

function formatCurrentTime() {
  const now = new Date();
  const hour = now.getHours();
  const minute = now.getMinutes();
  const second = now.getSeconds();
  const formattedHour = hour < 10 ? `0${hour}` : hour;
  const formattedMinute = minute < 10 ? `0${minute}` : minute;
  const formattedSecond = second < 10 ? `0${second}` : second;

  return `${formattedHour}:${formattedMinute}:${formattedSecond}`;
}
</script>

<style lang="scss" scoped>
.modal {
  display: flex;
  flex-direction: column;
  background-color: #fff;
  width: 654rpx;
  height: 742rpx;
  border-radius: 16rpx 16rpx 16rpx 16rpx;
  background-image: url("@/static/image/attendance/external-bag.png");
  background-repeat: no-repeat;
  background-size: 654rpx 132.35rpx;
  padding: 40rpx;

  .head {
    .title {
      font-size: 32rpx;
      font-weight: 500;
      color: #303133;
      line-height: 32rpx;
    }

    .time {
      font-size: 28rpx;
      font-weight: 500;
      color: #308bf8;
      line-height: 28rpx;
      margin-top: 28rpx;
    }

    .iconfont {
      font-size: 26rpx;
    }

    .address {
      font-size: 24rpx;
      font-weight: 400;
      color: #909399;
      line-height: 24rpx;
      margin-top: 22rpx;
    }
  }

  .content {
    width: 100%;
    height: 380rpx;
    background: #f0f1f5;
    border-radius: 8rpx 8rpx 8rpx 8rpx;
    margin-top: 42rpx;
    padding: 14rpx 24rpx 36rpx;

    ::v-deep .is-textarea {
      background-color: inherit !important;
    }

    ::v-deep .uni-easyinput__content-textarea {
      height: 200rpx;
    }

    .upload {
      width: 100%;
      display: flex;
      align-items: center;

      .box {
        position: relative;

        .img {
          display: block;
          width: 100rpx;
          height: 100rpx;
          margin-right: 20rpx;
        }

        .delete {
          position: absolute;
          top: -10rpx;
          right: 8rpx;
          background: #c0c4cc;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #fff;
          padding: 3rpx 4rpx;

          .icon-shenpizhongxin-jujue {
            font-size: 20rpx;
          }
        }
      }

      .upload-box {
        width: 100rpx;
        height: 100rpx;
        border-radius: 8rpx 8rpx 8rpx 8rpx;
        border: 2rpx dashed #dddddd;
        background-color: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;

        .icon-paizhao {
          font-size: 30rpx;
          color: #bfbfbf;
        }
      }
    }
  }

  .bottom {
    border-top: 1px solid #ebeef5;
    display: flex;
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;

    .line {
      width: 1px;
      height: 88rpx;
      background-color: #ebeef5;
    }

    .btn {
      flex: 1;
      text-align: center;
      line-height: 88rpx;
      font-size: 30rpx;
    }

    .cancel {
      color: #909399;
    }

    .ok {
      color: #308bf8;
    }
  }
}
</style>
