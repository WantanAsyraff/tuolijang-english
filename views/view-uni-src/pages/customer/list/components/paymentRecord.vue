<template>
  <view class="examine-content-list" :class="{ pb60: listData.length > 1 }">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="item in listData" :key="'list' + item.id" class="content-item-list">
        <template v-slot:body>
          <view class="item-list" @click="changeItem(item)">
            <view class="item-list-top">
              <view>
                {{ item.treaty.contract_no || '--' }}
                <text
                  class="status-tag"
                  :style="{
                    color: getPayRecordStatus(item.status).color ? getPayRecordStatus(item.status).color : '#1890ff',
                    background: getPayRecordStatus(item.status).color
                      ? getColor(getPayRecordStatus(item.status).color, '0.1')
                      : getColor('#1890ff', '0.1'),
                  }"
                  >{{ getPayRecordStatus(item.status).name }}</text
                >
              </view>
              <view class="types" :class="{ zhichu: item.types === 2 }">
                {{ getPayRecordTypes(item.types) }}
              </view>
            </view>

            <uni-row class="item-list-content">
              <uni-col :span="5" class="left">{{ $t('ui.customerInvoiceCheckPaymentPaymentAmount') }}</uni-col>
              <uni-col :span="19">{{ item.num || '--' }}</uni-col>
            </uni-row>
            <uni-row class="item-list-content">
              <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailPaymentMethod') }}</uni-col>
              <uni-col :span="19">{{ item.pay_type || '--' }}</uni-col>
            </uni-row>
            <uni-row class="item-list-content">
              <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailPaymentBillNo') }}</uni-col>
              <uni-col :span="19">{{ item.num || '--' }}</uni-col>
            </uni-row>
            <uni-row class="item-list-content">
              <uni-col :span="5" class="left">{{ $t('ui.customerListAccountRecordCollectionDate') }}</uni-col>
              <uni-col :span="19">{{ item.date || '--' }}</uni-col>
            </uni-row>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="9" :title="emptyTitle" style="min-height: 800rpx"></empty>
  </view>
</template>

<script setup lang="ts">
import empty from '@/components/empty/index.vue'
import { toRefs } from 'vue'
import { clickNavigateTo, getColor } from '@/utils/helper'
import type { Detail } from '@/utils/typeHelper'
import { getPayRecordTypes } from '@/utils/assessment'

const props = withDefaults(
  defineProps<{
    listData: Array<any>
    typeIndex?: number
    cid?: string | number
    emptyTitle?: string
    tab?: number
    count?: number
  }>(),
  {
    listData: <any>[],
    typeIndex: 0,
    cid: 0,
    tab: 0,
    count: 0,
    emptyTitle: '暂无付款记录，快去添加吧！',
  },
)
// toRef {

const { listData, typeIndex, emptyTitle, cid, tab } = toRefs(props)

let emit = defineEmits(['change'])
const changeItem = (item: Detail): void => {
  let str = ''
  if (typeIndex.value === 0) {
    str = `/pages/customer/contract/collectionDetails?id=${item.id}&eid=${item.eid}&types=${cid.value ? cid.value : '0'}`
  } else {
    str = `/pages/finance/payment/details?id=${item.id}&tab=${tab.value}`
  }
  clickNavigateTo(str)
}
// 内容点击
const btnChange = (item: Detail, type: number): void => {
  emit('change', { row: item, type: type })
}

const getPayRecordStatus = (status: number) => {
  let obj = {
    '0': {
      name: '审核中',
      color: '#1890FF',
    },
    '1': {
      name: '已通过',
      color: '#909399',
    },
    '2': {
      name: '未通过',
      color: '#ED4014',
    },
    '3': {
      name: '已撤回',
      color: '#909399',
    },
  }
  return obj[status]
}
</script>

<style scoped lang="scss">
.examine-content-list {
  background-color: #fff;

  ::v-deep .uni-list {
    .uni-list--border {
      top: auto;
      left: auto;
    }

    .content-item-list {
      border-radius: 12rpx 12rpx 12rpx 12rpx;
      border: 2rpx solid #eeeeee;
      padding: 24rpx;
      margin-bottom: 20rpx;

      .uni-list-item__container {
        padding: 0;
      }
    }

    .content-item-list-card {
      margin-bottom: 20rpx;

      .uni-list-item__container {
        padding: 15px 30rpx;
      }
    }
  }

  .item-list {
    width: 100%;
    position: relative;

    .item-list-top {
      width: 100%;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      padding-bottom: 20rpx;
      font-weight: 500;
      font-size: 28rpx;
      color: #303133;
      display: flex;
      justify-content: space-between;
    }

    .item-list-content {
      font-weight: 400;
      font-size: 24rpx;
      color: #303133;
      margin-bottom: 12rpx;
      display: flex;
      align-items: flex-end;

      &:last-of-type {
        margin-bottom: 0;
      }

      .left {
        font-family:
          PingFang SC,
          PingFang SC;
        font-weight: 400;
        font-size: 24rpx;
        color: #606266;
      }
    }
  }
}
.status-tag {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 22rpx;
  color: #1890ff;
  border-radius: 8rpx;
  padding: 2rpx 10rpx;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 22rpx;
  color: #1890ff;
}
.types {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 24rpx !important;
  color: #19be6b;
}
.zhichu {
  color: #ff9900 !important;
}
</style>
