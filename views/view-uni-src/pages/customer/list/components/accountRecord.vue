<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0" :style="{ backgroundColor: typeIndex === 0 ? '#fff' : '#f5f5f5' }">
      <uni-list-item v-for="item in listData" :class="typeIndex === 0 ? 'content-item-list' : 'content-item-list-card'" :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list" @click="changeItem(item)">
            <view class="item-list-header">
              <view class="header-left">
                <text class="right-top-left line1" v-if="cid">{{ item.card ? item.card.name : '--' }}</text>
                <text class="right-top-left line1" v-else>{{
                  item.treaty ? (item.treaty.contract_name ? item.treaty.contract_name : item.treaty.title) : '--'
                }}</text>
                <view class="status-tag">
                  {{ getPayRecordStatus(item.status) }}
                </view>
              </view>
              <view class="item-type" :class="'type' + item.types">{{ getPayRecordTypes(item.types) }}</view>
            </view>
            <view class="item-list-content">
              <uni-row>
                <uni-col :span="5">{{ $t('ui.customerInvoiceCheckPaymentPaymentAmount') }}</uni-col>
                <uni-col :span="19" class="content-text">{{ item.num }}</uni-col>
              </uni-row>
              <uni-row>
                <uni-col :span="5">{{ $t('ui.customerContractPayDetailPaymentMethod') }}</uni-col>
                <uni-col :span="19" class="content-text">{{ item.pay_type }}</uni-col>
              </uni-row>
              <uni-row>
                <uni-col :span="5">{{ $t('ui.customerContractPayDetailPaymentBillNo') }}</uni-col>
                <uni-col :span="19" class="content-text">{{ item && item.bill_no ? item.bill_no : '--' }}</uni-col>
              </uni-row>
              <uni-row>
                <uni-col :span="5">{{ $t('ui.customerListAccountRecordCollectionDate') }}</uni-col>
                <uni-col :span="19" class="content-text">
                  <uni-dateformat format="yyyy/MM/dd hh:mm" :date="item.created_at" />
                </uni-col>
              </uni-row>
            </view>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="9" :title="emptyTitle" style="height: 800rpx"></empty>
  </view>
  <view class="footer-text" v-if="listData.length > 0 && count <= listData.length">{{ $t('ui.customerListFollowRecordNoMore') }}</view>
</template>

<script setup lang="ts">
import empty from '@/components/empty/index.vue'
import { toRefs } from 'vue'
import { clickNavigateTo } from '@/utils/helper'
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
    listData: Array<any>,
    typeIndex: 0,
    cid: 0,
    tab: 0,
    count: 0,
    emptyTitle: '暂无付款记录，快去添加吧！',
  },
)

const { listData, typeIndex, emptyTitle, cid, tab, count } = toRefs(props)

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
  return status == 0 ? '待审核' : status == 1 ? '已通过' : status == 2 ? '未通过' : '已撤回'
}
</script>

<style scoped lang="scss">
.item-list-header {
  display: flex;
  justify-content: space-between;
}
.header-left {
  display: flex;
  align-items: center;
}
.right-top-left {
  max-width: 600rpx;
  font-size: 26rpx;
  font-weight: 500;
}
.status-tag {
  margin-left: 12rpx;
  width: 82rpx;
  height: 34rpx;
  background: rgba(24, 144, 255, 0.1);
  border-radius: 8rpx;
  display: flex;
  justify-content: center;
  font-weight: 400;
  font-size: 22rpx;
  color: #1890ff;
}
.item-type {
  color: #19be6b;
  font-size: 24rpx;
  &.type1 {
    color: #ff9900;
  }
}
.examine-content-list {
  background: #fff;
  /* 触发BFC（块级格式化上下文） */
  overflow: hidden; /* 或 auto, scroll */
  /* 或者 */
  display: flow-root; /* 现代解决方案 */
  ::v-deep .uni-list {
    padding: 0 20rpx;
    .uni-list--border {
      top: auto;
      left: auto;
    }

    .content-item-list {
      .uni-list-item__container {
        padding: 0;
        margin-bottom: 20rpx;
      }
    }

    .content-item-list-card {
      margin-bottom: 20rpx;
    }
  }

  .item-list {
    width: 100%;
    position: relative;
    border: 1px solid #eeeeee;
    border-radius: 12rpx;
    padding: 24rpx;
    .item-list-content {
      font-size: 24rpx;
      color: #606266;
      font-weight: 400;
      width: 100%;
      .uni-row {
        display: flex;
        align-items: center;
        margin-top: 22rpx;
        .content-text {
          color: #303133;
          display: flex;
          align-items: center;
          min-height: 36rpx;
        }
      }
    }
  }
}
</style>
