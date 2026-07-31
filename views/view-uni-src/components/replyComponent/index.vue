<template>
  <view>
    <uni-popup ref="popupRef" type="bottom">
      <view class="content-box">
        <view class="title">
          <view class="iconfont icon-shenpizhongxin-jujue" @click="close">
          </view>
          {{title}}
        </view>
        <view class="genjinBox">
          <uni-easyinput :inputBorder="false" v-model="formData.content" type="textarea" :clearable="false"
            :maxlength="256" :placeholder="$t('ui.replyComponentIndexEnterContent')" class="mb10">
          </uni-easyinput>
          <!-- 文件 -->
          <view class="btn-box">
            <view class="addfujian" @click="uploadAvatar"> <text class="iconfont icon-fujian"></text>
              {{ $t('ui.replyComponentIndexAddAttachment') }}</view>
            <view class="btn" @click="handleConfirm">{{ $t('ui.replyComponentIndexSubmit') }}</view>
          </view>
        </view>
        <view class="flie" v-if="formData.imgs.length>0">
          <view class="box" v-for="(item, indexs) in formData.imgs" :key="indexs" @click="preview(item)">
            <view class="left">

              <image class="slot-image" :src="`/static/image/cloudfile/${isFileTypeIcon(item.name)}`">
              </image>
              <view style=" width: calc(100% - 40px);">
                <view class="name">
                  {{ item.name }}
                </view>
                <view class="size"> {{ formatBytes(item.size) || '--' }} </view>
              </view>
              <view class="iconfont icon-guanbi-yangshiyi1" @click.stop="deleteFile(item.id)">
              </view>
            </view>

          </view>
          <!-- <view class="box" v-for="(item, indexs) in formData.imgs" :key="indexs" @click="preview(item)">
            <view class="left">
              <image class="slot-image" :src="item.src">
              </image>
              <view style=" width: calc(100% - 40px);">
                <view class="name">
                  {{ item.name }}
                </view>
                <view class="size"> {{ formatBytes(item.size) || '--' }} </view>
              </view>
              <view class="iconfont icon-guanbi-yangshiyi1" @click.stop="deleteFile(item.id)">
              </view>
            </view>
          </view> -->
        </view>
      </view>
    </uni-popup>
  </view>
</template>

<script setup>
  import { ref, reactive, computed, watch } from "vue";
  import { uploadImage, formatBytes } from "@/utils/file";
  import { debounce, lookPreview, isFileTypeIcon } from "@/utils/helper";
  import message from "@/utils/message";
  const props = defineProps({
    title: {
      type: String,
      default: '评论'
    },
  })
  const { title } = toRefs(props);
  const popupRef = ref(null);
  const formData = ref({
    content: '',
    imgs: [],
  })
  const close = () => {
    formData.value.content = ""
    formData.value.imgs = []
    popupRef.value.close();
  }
  // 图片与文档预览
  const preview = (item) => {
    lookPreview(item.src, item.name, [item.src]);
  };
  // 提交跟进记录
  const handleConfirm = debounce(() => {
    if (!formData.value.content) {
      message.error("内容不能为空");
      return false;
    }
    emit("submit", { content: formData.value.content, files: formData.value.imgs });
    close()

  })
  const emit = defineEmits(["submit"]);
  import { uploadFlie } from "@/utils/file";
  // 添加图片
  const uploadAvatar = () => {
    const config = {};
    uploadFlie("attach/imgs", config).then((res) => {
      if (res.data.name) {
        formData.value.imgs.push(res.data);
      } else {
        message.error('上传文件失败！请检查上传文件类型');
      }

    }).catch((error) => {
      message.error(error);
    });
  };
  // 删除文件
  const deleteFile = (id) => {
    formData.value.imgs = formData.value.imgs.filter(item => item.id !== id);
  }

  // 打开弹窗
  const popupOpen = (val) => {
    popupRef.value.open();
  };
  defineExpose({ popupOpen });
</script>

<style lang="scss" scoped>
  .content-box {
    width: 100%;
    min-height: 550rpx;
    background-color: #fff;
    border-radius: 8px 8px 0 0;
    padding: 0 28rpx;

    .title {
      position: relative;
      height: 80rpx;
      line-height: 80rpx;
      text-align: center;
      font-size: 16px;
      font-weight: 400;
      color: #303133;

      .icon-shenpizhongxin-jujue {
        position: absolute;
        left: 10px;
      }
    }

    .genjinBox {
      width: 100%;
      height: 194px;
      border-radius: 6px 6px 6px 6px;
      border: 1px solid #F5F5F5;
      padding: 24rpx;
      padding-top: 6rpx;
    }

    ::v-deep .uni-easyinput__content-textarea {
      height: 120px !important;

    }

    .line {
      width: 100%;
      height: 10px;
      border-bottom: 1px solid #F0F1F5;
    }



    .btn-box {
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;


      .addfujian {
        cursor: pointer;
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 24rpx;
        color: #282828;
      }

      .btn {
        cursor: pointer;
        width: 64px;
        height: 32px;
        background: #308BF8;
        border-radius: 6px 6px 6px 6px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #308BF8;
        border-radius: 12rpx;
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 12px;
        color: #FFFFFF;

      }
    }


    .box:last-child {
      margin-bottom: 0;
    }
  }

  // 上传附件
  .flie {
    width: 100%;
    padding: 24rpx 0rpx 24rpx 0;

    .box {
      width: 100%;
      height: 40px;
      background: #f6f7f9;
      border-radius: 4px 4px 4px 4px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12rpx;
      padding-right: 10px;
      padding-left: 10px;


      .icon-guanbi-yangshiyi1 {
        cursor: pointer;
        color: #999999;
        margin-top: 7px;
      }

      .left {
        width: 100%;
        display: flex;
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;

        .slot-image {
          flex-shrink: 0; // flex布局下图片挤压变形
          width: 52rpx;
          height: 52rpx;
          margin-right: 10rpx;
        }

        .name {
          width: calc(100% - 40px);
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
</style>