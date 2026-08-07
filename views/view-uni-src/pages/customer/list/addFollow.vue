<template>
  <view class="content">
    <!-- 跟进记录和跟进提醒用type来区分  跟进记录：type==1 跟进提醒type==2 -->
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :is-right="true" :default-title="data.defaultTitle"> </default-nav-bar>
    </view>
    <!-- 表单内容 -->
    <view class="examine-content mt20">
      <uni-forms :border="false" label-width="80px">
        <view class="list-item">
          <uni-forms-item class="is-direction-top p24">
            <template v-slot:label>
              <view class="uni-forms-item__label mt36"> {{ data.type == 1 ? $t('ui.customerListAddFollowFollowUpInformation') : $t('ui.customerContractAddRemindReminderContent') }} <text class="iconfont">*</text> </view>
            </template>
            <uni-easyinput
              :inputBorder="false"
              class="max-height"
              v-model="formData.content"
              type="textarea"
              :clearable="false"
              :styles="styles"
              :placeholder-style="placeholderStyle"
              :maxlength="256"
              :autoHeight="true"
              :placeholder="data.type == 1 ? $t('ui.customerListAddFollowFillInFollowUpInformation') : $t('ui.customerContractAddRemindEnterReminderDetails')"
            >
            </uni-easyinput>
          </uni-forms-item>
        </view>

        <view class="list-item mt20 p24">
          <uni-forms-item class="is-direction-top" v-if="data.type == 1">
            <template v-slot:label>
              <view class="uni-forms-item__label mt36">
                {{ $t('ui.customerListAddFollowAddImage') }} <text class="tips">{{ $t('ui.customerListAddFollowRecommended7341034Max') }}{{ fileSizeOne }}{{ $t('ui.customerListAddFollowMbJpgJpegPngAndOtherFormatsSupported') }}</text>
              </view>
              <view class="upload">
                <view v-for="(item, index) in data.imgs" :key="index" class="box">
                  <image class="img" v-if="isTypeImage(item.name)" :src="item.url" mode=""></image>
                  <image class="img" v-else :src="'/static/image/cloudfile/' + isFileTypeIcon(item.name)" mode=""></image>
                  <view class="delete" @click="deleteImg(item)">
                    <text class="iconfont icon-shenpizhongxin-jujue"></text>
                  </view>
                </view>
                <view class="upload-box" @click="uploadAvatar">
                  <view class="iconfont icon-paizhao"></view>
                  <view class="text"> {{ $t('ui.customerListAddFollowAddImage') }} </view>
                </view>
              </view>
            </template>
          </uni-forms-item>
          <uni-forms-item class="input-label" v-if="data.type == 2">
            <template v-slot:label>
              <view class="uni-forms-item__label">{{ $t('ui.customerListAddFollowReminderTime') }} <text class="iconfont">*</text></view>
            </template>
            <uni-datetime-picker type="datetime" :clear-icon="false" :border="false" v-model="formData.time">
              <view v-if="!formData.time" class="picker-input picker-input-placeholder">
                {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
                <view class="iconfont icon-fanhui"></view>
              </view>
              <view class="picker-input" v-if="formData.time">
                {{ formData.time }}
              </view>
            </uni-datetime-picker>
          </uni-forms-item>
        </view>
      </uni-forms>
    </view>
    <!-- 底部 -->
    <view class="examine-button">
      <button type="primary" :loading="loading" @click="handleConfirm">{{ $t('ui.replyComponentIndexSubmit') }}</button>
    </view>
  </view>
</template>

<script setup>import appI18n from '@/locale';

import defaultNavBar from '@/components/defaultNavBar/index'
import { ref, reactive } from 'vue'
import message from '@/utils/message'
import { followSaveApi, followPutApi } from '@/api/customer'
import { uploadImage } from '@/utils/file'
import { onLoad } from '@dcloudio/uni-app'
const placeholderStyle = ref('color: #C0C4CC;font-size: 30rpx')
const styles = reactive({
  color: '#303133',
  disableColor: '#ffffff',
})

// 定义表单
const data = reactive({
  type: 1,
  eid: 0, // 客户id
  id: 0, // 跟进记录id
  defaultTitle: '添加跟进记录',
  imgs: [],
  fid: 0,
  lead_id: 0, // 线索id
  odds_id: 0, // 商机id
})
const formData = reactive({
  content: '',
  time: '',
  attach_ids: [],
  eid: '',
  types: 0,
  follow_id: 0,
})
const loading = ref(false)

onLoad((e) => {
  if (e.lead_id) {
    data.lead_id = Number(e.lead_id)
    if (Number.isNaN(data.lead_id)) {
      data.lead_id = 0
    }
  }
  if (e.odds_id) {
    data.odds_id = Number(e.odds_id)
    if (Number.isNaN(data.odds_id)) {
      data.odds_id = 0
    }
  }
  if (!e.data) {
    data.type = Number(e.type)
    if (data.type != 1) {
      data.defaultTitle = '添加跟进提醒'
      formData.types = 1
    }
  }
  if (e.kid) {
    data.eid = Number(e.kid)
    formData.eid = Number(e.kid)
  }
  if (e.eid) {
    data.eid = Number(e.eid)
    formData.eid = Number(e.eid)
  }

  if (e.fid) {
    data.fid = Number(e.fid)
  }
  if (e.data) {
    let editData = JSON.parse(e.data)

    if (e.type == 1) {
      data.defaultTitle = '编辑跟进记录'
      data.type = 1
      formData.types = 0
      formData.content = editData.reason || editData.content
    } else {
      data.defaultTitle = '编辑跟进提醒'
      data.type = 2
      formData.types = 1
      formData.content = editData.content
    }

    data.id = editData.follow_id

    formData.time = editData.time
    data.imgs = editData.attachs
  }
})

const deleteImg = (e) => {
  data.imgs = data.imgs.filter((item) => {
    return item.id !== e.id
  })
}
import { delayedReLaunch, debounce, isTypeImage, isFileTypeIcon, clickNavigateTo, fileSizeOne } from '@/utils/helper'
// 提交表单
const handleConfirm = debounce(() => {
  // 内容校验
  if (!formData.content) {
    message.error(data.type === 1 ? '跟进信息不能为空' : '提醒内容不能为空')
    return false
  }
  // 提醒时间校验
  if (data.type === 2 && !formData.time) {
    message.error(appI18n.global.t('ui.customerListAddFollowSelectReminderTime'))
    return false
  }

  formData.attach_ids = data.imgs.map((item) => item.id)

  if (data.fid > 0) {
    formData.follow_id = data.fid
  }

  if (data.lead_id > 0 || data.odds_id > 0) {
    const targetData = {
      content: formData.content,
      types: 0,
      eid: data.lead_id || data.odds_id,
      attach_ids: formData.attach_ids,
      link_type: data.lead_id ? 'clue' : 'odds',
    }
    loading.value = true
    const task = data.id ? followPutApi(data.id, targetData) : followSaveApi(targetData)
    task
      .then((res) => {
        loading.value = false
        message.success(res.message)
        setTimeout(() => {
          uni.navigateBack()
        }, 1000)
      })
      .catch((err) => {
        loading.value = false
        message.error(err.message)
      })
  } else if (data.defaultTitle === '编辑跟进提醒' || data.defaultTitle === '编辑跟进记录') {
    let id = data.id
    loading.value = true
    followPutApi(id, {
      ...formData,
      link_type: 'customer',
    })
      .then((res) => {
        loading.value = false
        message.success(res.message)
        let type = 4
        clickNavigateTo(`/pages/customer/list/details?id=${data.eid}&type=${type}`)
      })
      .catch((err) => {
        loading.value = false
        message.error(err.message)
      })
  } else {
    loading.value = true
    followSaveApi({
      ...formData,
      link_type: 'customer',
    })
      .then((res) => {
        message.success(res.message)
        loading.value = false
        let type = 4
        const str = data.fid > 0 ? '/pages/users/schedule/index' : `/pages/customer/list/details?id=${data.eid}&type=${type}`
        clickNavigateTo(str)
      })
      .catch((err) => {
        loading.value = false
        message.error(err.message)
      })
  }
}, 500)

// 添加图片
const uploadAvatar = () => {
  const config = {
    eid: data.eid,
    relation_type: 'follow',
  }
  if (data.id > 0) {
    config.relation_id = data.id
  }
  uploadImage('attach/imgs', config, fileSizeOne)
    .then((res) => {
      data.imgs.push(res.data)
    })
    .catch((error) => {
      message.error(error)
    })
}
</script>

<style lang="scss" scoped>
.content {
  width: 100%;
  position: relative;

  .cr-position-header {
    background-color: #fff;
  }

  .tips {
    font-size: 20rpx;
    font-family:
      PingFang SC-常规体,
      PingFang SC;
    font-weight: 400;
    color: #999999;
  }

  ::v-deep .uni-input-wrapper {
    text-align: right;
  }

  .examine-content {
    padding-top: calc($uni-default-bar-height + var(--status-bar-height));
    padding-bottom: 126rpx;
  }

  .uni-forms-item__label {
    height: auto;
    padding: 0;
    font-size: 30rpx;
    color: $uni-text-color;
    line-height: 1;
    font-family:
      PingFang SC-常规体,
      PingFang SC;

    .iconfont {
      color: #ff2529;
    }
  }

  .examine-button {
    height: 126rpx;
    line-height: 126rpx;
    width: 100%;
    padding: 0 20rpx;
    position: fixed;
    left: 0;
    bottom: 0;
    right: 0;
    display: flex;
    align-items: center;

    uni-button {
      width: 100%;
      height: 86rpx;
      line-height: 86rpx;
      font-size: $uni-font-size-default;
      border-radius: 12rpx;
    }
  }

  .p24 {
    padding: 0 24rpx;
  }

  .upload {
    width: 100%;
    // min-height: 276rpx;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    padding: 32rpx 0;

    .box {
      position: relative;

      .delete {
        position: absolute;
        top: 0;
        right: 20rpx;
        width: 32rpx;
        height: 32rpx;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 0 8rpx 0 16rpx;
        display: flex;
        align-items: center;
        justify-content: center;

        .icon-paizhao {
          font-size: 35rpx;
          color: #bfbfbf;
        }
      }
    }

    .img {
      display: block;
      width: 140rpx;
      height: 140rpx;
      margin-right: 20rpx;
      margin-bottom: 10rpx;
    }

    .upload-box {
      width: 140rpx;
      height: 140rpx;
      border-radius: 8rpx 8rpx 8rpx 8rpx;
      border: 2rpx solid #dddddd;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;

      .icon-paizhao {
        font-size: 40rpx;
        color: #bfbfbf;
      }

      .text {
        margin-top: 20rpx;
        font-size: 24rpx;
        font-family:
          PingFang SC-常规体,
          PingFang SC;
        font-weight: 400;
        color: #999999;
      }
    }
  }

  .list-item {
    background-color: #fff;
    // border-radius: 12rpx;
    width: 100%;
  }

  .mt36 {
    margin-top: 36rpx;
  }

  .mt20 {
    margin-top: 20rpx;
  }

  ::v-deep .uni-easyinput__content-textarea {
    min-height: 460rpx !important;
    font-size: 28rpx;
  }

  .max-height {
    min-height: 460rpx;
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
      color: #c0c4cc;
    }
  }

  .picker-input-placeholder {
    color: #c0c4cc;
  }

  ::v-deep .uni-forms-item {
    margin-bottom: 0;
    border-bottom: 1px solid #ebeef5;
  }

  .input-label {
    padding: 18rpx 0;
    align-items: center;

    ::v-deep .uni-easyinput__content-input {
      text-align: right;
      padding-right: 0 !important;
    }

    ::v-deep .uni-icons {
      display: none;
    }

    ::v-deep .uni-forms-item__label {
      max-width: 198rpx;
      display: flex;
      line-height: 1.2;

      .iconfont {
        width: 16rpx;
      }
    }
  }
}
</style>
