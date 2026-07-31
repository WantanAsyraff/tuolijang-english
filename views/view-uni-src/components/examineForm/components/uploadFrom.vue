<template>
  <view class="upload-from">
    <uni-forms-item class="input-label"
      :style="{marginBottom: data.fileList.length > 0 ? 0 : '20rpx'}">
      <template v-slot:label>
        <view class="uni-forms-item__label">

          <text class="label-item">{{configData.title}}</text>
          <text v-if="configData.effect && configData.effect.required" class="iconfont">*</text>

        </view>
      </template>
      <view class="input-right-conment">
        <text class="iconfont icon-biaodan-tianjia" @click="uploadFormFlie()"></text>
      </view>
    </uni-forms-item>
    <view class="upload-from-list" v-if="data.fileList.length > 0">
       <oa-uploadList :listData="data.fileList" :clearBtn="true"></oa-uploadList>
    </view>
  </view>
</template>

<script setup>
  import { reactive, toRefs, watch } from "vue";
  import message from "@/utils/message";
    import oaUploadList from "@/components/oa-uploadList/index.vue";
  import uploadFromList from "@/pages/users/examine/components/uploadFromList.vue";

  const props = defineProps({
    configData: {
      type: Object,
      default: () => {
        return {};
      },
    }
  });
  const { configData } = toRefs(props);

  let data = reactive({
    fileList: []
  });
  let emit = defineEmits(["change"]);

  import { uploadFlie } from "@/utils/file";
  import { fileSizeOne } from "@/utils/helper";

  const uploadFormFlie = () => {
    uploadFlie("common/upload", {}, fileSizeOne).then((response) => {
      let fileUrl = response.data.url;
      if (response.status === 200) {
        data.fileList.push({
          id: response.data.id,
          real_name: response.data.name,
          name: response.data.name,
          size: response.data.size,
          url: fileUrl,
          file_url: fileUrl,
        });
        emit("change", data.fileList);
      }
    }).catch((error) => {
      message.error(error);
    });
  };

  const handleClear = (index) => {
    data.fileList.splice(index, 1);
    emit("change", data.fileList);
  };

  // 数据监听
  watch(configData, (newvalue) => {
    if (newvalue) {
      if (newvalue.newvalue && newvalue.newvalue.length > 0) {
        data.fileList = newvalue.newvalue;
      }
    }
  }, { immediate: true, deep: true });
</script>

<style lang="scss" scoped>
  .upload-from {
    width: 100%;

    .iconfont {
      font-size: 36rpx;
    }

    .input-label {
      padding: 18rpx 24rpx !important;
      align-items: center;

      ::v-deep .uni-easyinput__content-input {
        text-align: right;
        padding-right: 0 !important;
      }

      ::v-deep .uni-forms-item__label {
        width: 148rpx;
        display: flex;
        line-height: 1.2;

        .label-item {
          width: calc(100% - 16rpx);
        }

        .iconfont {
          width: 16rpx;
        }
      }

      .picker-input {
        text-align: right;
        height: 35px;
        color: $uni-text-color;
        font-size: 30rpx;
        align-items: center;
        display: flex;
        justify-content: flex-end;

        .iconfont {
          padding-right: 16rpx;
          margin-top: 7rpx;
          transform: rotate(180deg);
          font-size: 24rpx;
          color: #C0C4CC;
        }
      }

      .picker-input-placeholder {
        color: #C0C4CC;
      }

      .input-right-conment {
        text-align: right;
        height: 35px;
        line-height: 35px;
        color: $uni-text-color;
      }
    }

    .upload-from-list {
      width: 100%;
      background-color: #fff;
      padding: 0 24rpx 30rpx 24rpx;
      margin-bottom: 20rpx;
    }
  }
</style>