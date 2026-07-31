<template>
  <view class="content">
    <!-- 选择付款单页面 -->
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :is-right="true" :default-title="data.defaultTitle"> </default-nav-bar>
    </view>
    <view class="examine-content">
      <view class="item" v-for="(item, index) in data.list" :key="index">
        <view class="iconfont icon-xuanzeanniu-weixuan" @click="check(item, index)" v-if="data.ids.indexOf(item.id) < 0" />
        <view class="iconfont icon-denglu-tongyi" @click="check(item, index)" v-else />
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerContractPayDetailPaymentBillNo') }}</view>
          <view class="list-text">{{ item.bill_no }}</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerContractPayDetailOrderNo') }} </view>
          <view class="list-text">{{ item.treaty ? item.treaty.contract_no : '--' }}</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerInvoiceCheckPaymentBusinessType') }} </view>
          <view class="list-text">{{ item.types == 0 ? $t('ui.customerInvoiceCheckPaymentPaymentRecord') : $t('ui.customerInvoiceCheckPaymentRenewalRecord') + '-' + item.renew.title }}</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerInvoiceCheckPaymentPaymentAmount') }} </view>
          <view class="list-text">{{ item.num ? item.num : '--' }}</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerInvoiceCheckPaymentInvoiceStatus') }} </view>
          <view class="list-text" v-if="item.status == -1">{{ $t('ui.customerInvoiceCheckPaymentWithdrawInvoice') }}</view>
          <view class="list-text" v-if="item.status == 0">{{ $t('ui.customerInvoiceCheckPaymentPendingInvoicing') }}</view>
          <view class="list-text" v-if="item.status == 1">{{ $t('ui.customerInvoiceCheckPaymentInvoiced') }}</view>
          <view class="list-text" v-if="item.status == 2">{{ $t('ui.customerInvoiceCheckPaymentRejected') }}</view>
          <view class="list-text" v-if="item.status == 3">{{ $t('ui.customerInvoiceCheckPaymentApplyToVoid') }}</view>
          <view class="list-text" v-if="item.status == 4">{{ $t('ui.customerInvoiceCheckPaymentApproveInvalidation') }}</view>

          <view class="list-text" v-if="item.status == 5">{{ $t('ui.customerInvoiceCheckPaymentRejectInvalidation') }}</view>
          <view class="list-text" v-if="item.status == 6">{{ $t('ui.customerInvoiceCheckPaymentWithdrawVoidRequest') }}</view>
          <view class="list-text" v-if="!item.status">--</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerInvoiceCheckPaymentCreator') }} </view>
          <view class="list-text">{{ item.card ? item.card.name : '--' }}</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerInvoiceCheckPaymentCreatedTime') }} </view>
          <view class="list-text">{{ item.created_at }}</view>
        </view>
      </view>
    </view>

    <!-- 底部 -->
    <view class="footer">
      <view class="flex">
        <view class="iconfont icon-xuanzeanniu-weixuan" v-if="data.checkShow" @click="checkAll(data.checkTitle)" />
        <view class="iconfont icon-denglu-tongyi" v-else @click="checkAll(data.checkTitle)" />
        <view class="text"> {{ data.checkTitle }}（{{ data.ids.length }}/{{ data.list.length }}） </view>
      </view>
      <view class="flex">
        <view class="btn cancel" @click="cancel"> {{ $t('ui.baTreePickerIndexCancel') }} </view>
        <view class="btn next" @click="goAddInvoice"> {{ $t('ui.customerInvoiceCheckPaymentNext') }} </view>
      </view>
    </view>
  </view>
</template>

<script setup>
import defaultNavBar from '@/components/defaultNavBar/index'
import { ref, reactive } from 'vue'
import { unInvoicedListApi, configApproveApi } from '@/api/customer'
const data = reactive({
  defaultTitle: '选择付款单',
  isShowTitle: true,
  checkTitle: '全选',
  checkShow: true,
  ids: [],
  id: 0, // 审批id
  eid: '', // 客户id
  cid: '',
  name: '', // 客户名称
  list: [],
  newtol: [],
  num: '',
  buildData: [],
  current: 0,
})
import { onLoad } from '@dcloudio/uni-app'
const popupRef = ref()
onLoad((e) => {
  if (e.cid) {
    data.cid = e.cid
  }
  if (e.id) {
    data.id = e.id
  }
  data.eid = e.eid
  data.name = e.name
  getList(e.eid)
  data.list.forEach((item) => {
    item.show = false
  })
  getConfigApprove()
})

import { clickNavigateTo } from '@/utils/helper'

const goAddInvoice = () => {
  clickNavigateTo(`/pages/users/examine/default?id=${data.id}&bill_id=${data.ids}&eid=${data.eid}`)
}
// const cancelPopup = () => {
//   popupRef.value.close()
// }
const getConfigApprove = () => {
  configApproveApi().then((res) => {
    data.buildData = res.data
  })
}
const changePicker = (e) => {
  data.current = e.detail.value
}

// const confirmPopup = () => {
//   const row = data.buildData[data.current]
//   clickNavigateTo(`/pages/users/examine/default?id=${row.id}&name=${row.name}&bill_id=${data.eid}`)
// }

const cancel = () => {
  uni.navigateBack()
}

const getList = (id) => {
  let dataInfo = {
    eid: id,
  }
  unInvoicedListApi(dataInfo).then((res) => {
    data.list = res.data
    syncCheckAllStatus()
  })
}

const syncCheckAllStatus = () => {
  const isAllChecked = data.list.length > 0 && data.ids.length === data.list.length
  data.checkShow = !isAllChecked
  data.checkTitle = isAllChecked ? '取消全选' : '全选'
}

const check = (val, index) => {
  let labelIdList = data.ids.indexOf(val.id)
  if (labelIdList < 0) {
    data.ids.push(val.id) // 添加
    data.newtol.push(Number(val.num))
  } else {
    data.newtol = data.newtol.filter((item) => {
      return item !== Number(val.num)
    })
    data.ids = data.ids.filter((item) => {
      return item !== val.id
    })
  }
  if (data.newtol && data.newtol.length > 0) {
    data.num = data.newtol.reduce((x, y) => x + y).toFixed(2)
  }

  syncCheckAllStatus()
  console.log(data.ids, 88888)
  if (data.ids.length == 0) {
    data.checkShow = true
  }
}
const checkAll = (val) => {
  data.ids = []
  data.newtol = []
  if (val === '全选') {
    data.list.forEach((item) => {
      data.ids.push(item.id)
      data.newtol.push(Number(item.num))
    })
  }

  data.num = data.newtol.length > 0 ? data.newtol.reduce((x, y) => x + y).toFixed(2) : '0.00'
  syncCheckAllStatus()
}
const onCheck = (val) => {
  data.ids.splice(val.id, 1)
}
</script>

<style lang="scss">
.content {
  .flex {
    display: flex;
    align-items: center;
  }

  .examine-content {
    padding-top: calc($uni-default-bar-height + var(--status-bar-height));
    padding-bottom: 126rpx;

    .item {
      margin-top: 8rpx;
      width: 100%;
      position: relative;
      background-color: #fff;
      padding: 32rpx;
      padding-left: 96rpx;

      .iconfont {
        position: absolute;
        top: 32rpx;
        left: 30rpx;
        font-size: 36rpx;
        color: #c0c4cc;
      }

      .icon-denglu-tongyi {
        color: #1890ff;
      }

      .list-item {
        display: flex;
        margin-bottom: 24rpx;

        .list-text {
          height: 28rpx;
          width: 378rpx;
          margin-left: 40rpx;
          font-family:
            PingFang SC,
            PingFang SC;
          font-weight: 400;
          font-size: 26rpx;
          color: #303133;
          line-height: 42rpx;
        }

        .name {
          font-family:
            PingFang SC,
            PingFang SC;
          font-weight: 400;
          font-size: 26rpx;
          color: #606266;
          width: 112rpx;
        }
      }
    }
  }

  .footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    height: 116rpx;
    padding: 0 30rpx;
    background-color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;

    .btn {
      width: 168rpx;
      height: 74rpx;
      font-size: 30rpx;
      font-family:
        PingFang SC-常规体,
        PingFang SC;
      font-weight: 400;
      text-align: center;
      line-height: 74rpx;
      border-radius: 8rpx;
    }

    .cancel {
      background-color: #f0f1f5;
      color: #303133;
      margin-right: 24rpx;
    }

    .next {
      color: #fff;
      background-color: #1890ff;
    }

    .text {
      font-size: 30rpx;
      font-family:
        PingFang SC-常规体,
        PingFang SC;
      font-weight: 400;
      color: #303133;
    }

    .icon-denglu-tongyi {
      margin-top: 4rpx;
      color: #1890ff;
      margin-right: 26rpx;
    }

    .icon-xuanzeanniu-weixuan {
      margin-top: 4rpx;
      color: #c0c4cc;
      margin-right: 26rpx;
    }
  }
}

.uni-picker-header {
  border-bottom: 1px solid #e5e5e5;
  width: 100%;
  height: 90rpx;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;

  .uni-picker-action {
    max-width: 50%;
    top: 0;
    height: 100%;
    box-sizing: border-box;
    padding: 0 14px;
    font-size: 30rpx;
    line-height: 90rpx;
    cursor: pointer;

    &.uni-picker-action-cancel {
      color: #888;
    }

    &.uni-picker-action-confirm {
      color: #007aff;
    }
  }
}

.picker-view {
  width: 750rpx;
  height: 480rpx;
  background-color: #fff;
}

.item-value {
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
