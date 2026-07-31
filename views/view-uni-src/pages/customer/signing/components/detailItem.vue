<template>
  <view class="detail-item">
    <view class="content">
      <view class="title">
        {{ $t('ui.customerSigningDetailItemCustomerInformation') }}
      </view>
      <view class="item">
        <text class="left">
          {{ $t('ui.customerContractPayDetailCustomerName') }}
        </text>
        <text class="right">
          {{ detail.customer ? detail.customer.customer_name : '--' }}
        </text>

      </view>

      <view class="item">
        <text class="left">
          {{ $t('ui.customerSigningDetailItemContactPhone') }}
        </text>
        <text class="right">
          {{ detail.customer ? detail.customer.customer_tel : '--' }}
        </text>

      </view>
      <view class="item">
        <text class="left">
          {{ $t('ui.customerSigningDetailItemProvinceCityDistrict') }}
        </text>
        <text class="right">
          {{ detail.customer ? detail.customer.area_cascade : '--' }}
        </text>

      </view>
    </view>

    <view class="content boder-top">
      <view class="title">
        {{ $t('ui.customerSigningDetailItemSigningInformation') }}
      </view>
      <view class="item">
        <text class="left">
          {{ $t('ui.customerSigningDetailItemSigningMethod') }}
        </text>
        <text class="right">
          {{ detail.sign_type == 2 ? $t('ui.customerSigningDetailItemESign') : $t('ui.customerSigningDetailItemOfflineSigning') }}
        </text>
      </view>
      <view class="item">
        <text class="left">
          {{ $t('ui.customerSigningDetailItemContractAmount') }}
        </text>
        <text class="right">
          {{ getContractPrice(detail) }}
        </text>
      </view>
      <view class="item">
        <text class="left">
          {{ $t('ui.customerSigningDetailItemElectronicContract') }}
        </text>
        <text class="right check-text" v-if="detail.app_url" @click="checkContract(detail.app_url)">
          {{ $t('ui.customerSigningDetailItemViewElectronicContract') }}
        </text>
        <text class="right" v-else>--</text>
      </view>
      <view class="item">
        <text class="left">
          {{ $t('ui.customerSigningDetailItemStartDate') }}
        </text>
        <text class="right">
          {{ detail.start_date || '--' }}
        </text>
      </view>
      <view class="item">
        <text class="left">
          {{ $t('ui.customerSigningDetailItemEndDate') }}
        </text>
        <text class="right">
          {{ detail.end_date || '--' }}
        </text>
      </view>
      <view class="item">
        <text class="left">
          {{ $t('ui.customerSigningDetailItemSigner') }}
        </text>
      </view>

      <!-- 签署方 -->
      <view class="signer-info" v-for="(item, index) in detail.signatory" :key="index">
        <view class="header">
          <text>
            <text class="icon" v-if="item.types != 1">
              {{ item.types == 0 ? $t('ui.customerSigningDetailItemOurCompany') : $t('ui.customerSigningDetailItemEnterprise') }}
            </text>
            <text class="icon icon1" v-if="item.types == 1">
              {{ $t('ui.customerSigningDetailItemPersonal') }}
            </text>
            {{ item.types == 0 ? item.company_name : item.name }}
          </text>
          <text class="status-tag" :style="{
            color: statusMap[item.sign_status].color ? statusMap[item.sign_status].color : '#1890ff',
            background: statusMap[item.sign_status].color
              ? getColor(statusMap[item.sign_status].color, '0.1')
              : getColor('#1890ff', '0.1')
          }">
            {{ statusMap[item.sign_status].text }}
          </text>
        </view>
        <view class="item" v-if="item.types !== 1">
          <text class="left-status">
            {{ $t('ui.customerSigningDetailItemHandler') }}
          </text>
          <text class="right">
            {{ item.name }}
          </text>
        </view>
        <view class="item">
          <text class="left-status">
            {{ $t('ui.customerSigningDetailItemPhoneNumber') }}
          </text>
          <text class="right">
            {{ item.phone || '--' }}
          </text>
        </view>

        <view class="sign-btn" v-if="item.sign_status == 0 && detail.sign_type == 2 && detail.status == 2">
          <text class="btn" @click="openProgram(item)"> {{ item.types == 0 ? $t('ui.customerSigningDetailItemSignNow') : $t('ui.customerSigningDetailItemInviteToSign') }}</text>
          <text class="line" />
          <text @click="openBox(item)" class="btn">
            {{ $t('ui.customerSigningDetailItemSigningQrCode') }}
          </text>



        </view>
        <view v-if="item.sign_status == 0 && detail.sign_type == 1" style="height: 4px;" />
        <!-- <view class="sign-btn" v-if="item.sign_status == 0 && detail.sign_type == 1" @click="handleAddFile">
          签约录入
        </view> -->
      </view>
    </view>

    <view class="content boder-top">
      <view class="title">
        {{ $t('ui.customerSigningDetailItemProductInformation') }}
      </view>
      <ProductList :list-data="detail.products" :moreShow="false" />
    </view>

    <!-- 备注信息 -->
    <view class="content boder-top ">
      <view class="title">{{ $t('ui.customerSigningDetailItemRemarkInformation') }}</view>
      <view class="item mb20">
        {{ detail.mark || '--' }}

      </view>
    </view>
    <!-- 电子签弹窗 -->
    <eSignatureDialog ref="eSignatureDialogRef" />
  </view>
</template>

<script setup lang="ts">
import { defineProps, ref, toRefs } from "vue";
import eSignatureDialog from './eSignatureDialog.vue'
import ProductList from "@/pages/customer/opportunity/components/product-list.vue";
import { getColor } from "@/utils/helper"
import { signFileUploadApi } from "@/api/signing";
const props = defineProps<{
  detail: {
    title: string;
    content: string;
    app_url?: string;
    sign_type?: number;
    status?: number;
    signatory?: Array<any>;
    start_date?: string;
    end_date?: string;
    mark?: string;
    customer?: any;
    id?: string;
  };
}>();

const { detail } = toRefs(props);
const statusMap = {
  '0': {
    text: '待签约',
    color: '#FF9900',
  },
  '1': {
    text: '已签约',
    color: '#909399',
  },
  '2': {
    text: '已拒绝',
    color: '#ED4014',
  },

}
const eSignatureDialogRef = ref(null);
const openBox = (val) => {
  eSignatureDialogRef.value.openBox(detail.value.app_url);
};
const getContractPrice = (item) => {
  const price = item.contract_price ?? item.total_amount ?? item.price ?? item.amount
  if (price !== undefined && price !== null && price !== '') return price
  const orders = item.orders || item.contracts || []
  if (Array.isArray(orders) && orders.length > 0) {
    return orders.reduce((sum, order) => sum + Number(order.contract_price || order.total_amount || 0), 0).toFixed(2)
  }
  return '--'
}
const openProgram = (val) => {
  if (detail.value.app_url) {
    // 打开小程序链接
    if (typeof plus !== 'undefined' && plus.runtime) {
      // App 环境
      plus.runtime.openURL(detail.value.app_url, function (res) {
        console.log('打开链接成功', res);
      }, function (err) {
        console.log('打开链接失败', err);
        uni.showToast({
          title: '打开失败，请检查链接',
          icon: 'none'
        });
      });
    } else if (typeof wx !== 'undefined' && wx.openUrl) {
      // 小程序环境
      wx.openUrl({
        url: detail.value.app_url,
        success: function (res) {
          console.log('打开链接成功', res);
        },
        fail: function (err) {
          console.log('打开链接失败', err);
          uni.showToast({
            title: '打开失败，请检查链接',
            icon: 'none'
          });
        }
      });
    } else {
      // H5 环境
      window.open(detail.value.app_url);
    }
  }
}

const emit = defineEmits(['getDetails']);

import { uploadFlie } from "@/utils/file";
// 签约录入
const handleAddFile = () => {
  uploadFlie("common/upload", {}, 100)
    .then((res) => {
      if (res.status == 200) {
        signFileUploadApi(detail.value.id, {
          file_id: res.data.id
        }).then((val) => {
          if (val.status == 200) {
            emit('getDetails', detail.value.id);
            uni.showToast({
              title: '签约文件上传成功',
              icon: 'success',

            })
          }
        })

      }
    })
}

const checkContract = (url: string) => {
  window.open(url);
}
</script>
<style scoped lang="scss">
.content {
  background-color: #FFFFFF;
  padding: 30rpx 0;

}

.detail-item {
  background: #FFFFFF;
  padding: 0 30rpx;
  font-family: PingFang SC, PingFang SC;

  .title {
    font-weight: 500;
    font-size: 26rpx;
    color: #2B2C32;
  }

}
::v-deep .pb60 {
  padding-bottom: 0;
}

.item {
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  margin-top: 16rpx;
  height: 36rpx;
  line-height: 36rpx;
  font-size: 26rpx;

  .left {
    display: inline-block;
    width: 104rpx;
    font-size: 26rpx;
    color: #606266 !important;
    margin-right: 48rpx;
  }

  .left-status {
    font-size: 24rpx;
    margin-right: 0;
    color: #606266 !important;
  }

  .right {
    font-size: 26rpx;
    color: #303133;
  }

  .check-text {
    display: inline-block;
    color: #1890FF;
    cursor: pointer;
  }
}


.signer-info {
  border-radius: 12rpx;
  border: 2rpx solid #EEEEEE;
  padding: 30rpx 24rpx 20rpx 24rpx;
  margin-top: 20rpx;

  .header {
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 26rpx;
    color: #303133;
    display: flex;
    justify-content: space-between;
    align-items: center;

    .icon {
      display: inline-block;
      padding: 0 8rpx;
      height: 38rpx;
      background: #1890FF;
      border-radius: 8rpx;
      text-align: center;
      line-height: 38rpx;
      font-size: 22rpx;
      color: #FFFFFF;
      margin-right: 4rpx;
    }

    .icon1 {
      background: #FF9900;
    }
  }

  .sign-btn {
    width: 100%;
    height: 56rpx;
    line-height: 76rpx;
    display: flex;
    justify-content: space-between;
    text-align: center;
    font-size: 26rpx;
    color: #1890FF;
    border-top: 1px dashed #EEEEEE;
    margin-top: 24rpx;
    cursor: pointer;

    .btn {
      width: 100%;
    }

    .line {
      display: inline-block;
      width: 2rpx;
      height: 56rpx;
      margin-top: 10rpx;
      background: #EEEEEE;
    }
  }
}

.boder-top {
  border-top: 1px solid #eeeeee;
}

.mb20 {
  margin-bottom: 20rpx;
}

.status-tag {
  margin-left: 16rpx;
  // min-width: 68rpx;
  height: 42rpx;
  border-radius: 8rpx;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 400;
  font-size: 24rpx;
  padding: 0 10rpx;
}
</style>
