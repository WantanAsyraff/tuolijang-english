<template>
  <view class="contract-tab-info">
    <template v-if="orderData.length > 0">
      <view class="contract-info-item" v-for="(item, index) in orderData" :key="index" @click="examineList(item)">
        <view class="contract-header">
          <view class="contract-title">{{ item.contract_customer }}</view>
          <view
            class="status-badge"
            v-if="item.contract_status"
            :style="{
              color: item.contract_status.color ? item.contract_status.color : '#1890ff',
              background: item.contract_status.color ? getColor(item.contract_status.color, '0.1') : getColor('#1890ff', '0.1'),
            }"
            >{{ $ts(item.contract_status.name) }}
          </view>
        </view>
        <view class="contract-body">
          <uni-row class="contract-item" style="margin-bottom: 8px">
            <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailOrderNo') }}</uni-col>
            <uni-col :span="19">{{ item.contract_no }}</uni-col>
          </uni-row>
          <uni-row class="contract-item" style="margin-bottom: 8px">
            <uni-col :span="5" class="left">{{ $t('ui.customerListCustomerListDefaultOwner') }}</uni-col>
            <uni-col :span="19">{{ item.salesman.name }}</uni-col>
          </uni-row>
          <uni-row class="contract-item" style="margin-bottom: 8px">
            <uni-col :span="5" class="left">{{ $t('ui.customerSigningOrderListOrderAmount') }}</uni-col>
            <uni-col :span="19">{{ item.contract_price }}</uni-col>
          </uni-row>

          <!-- <uni-row class="contract-item" style="margin-bottom: 8px;">
						<uni-col :span="5" class="left">起止时间</uni-col>
						<uni-col :span="19">
							<uni-dateformat format="yyyy/MM/dd" :date="item.start_date"></uni-dateformat>
							-
							<template v-if="item.end_date !== '0000-00-00'">
							  <uni-dateformat format="yyyy/MM/dd" :date="item.end_date"></uni-dateformat>
							</template>
							<template v-else>永久</template>
						</uni-col>
					</uni-row> -->
        </view>
      </view>
    </template>
    <empty v-else :index="9" :title="emptyTitle" style="height: 950rpx"></empty>
  </view>
  <view class="footer-text" v-if="orderData.length > 0 && count <= orderData.length">{{ $t('ui.customerListFollowRecordNoMore') }}</view>
</template>

<script setup>
import empty from '@/components/empty/index.vue'
import { uploadImage, formatBytes } from '@/utils/file'
import { getColor, clickNavigateTo } from '@/utils/helper'
import deanPopover from '@/components/deanPopover/index.vue'
import { followDeleteApi } from '@/api/customer'
import message from '@/utils/message'
import { reactive, toRefs } from 'vue'
import followRecord from './followRecord.vue'
const props = defineProps({
  orderData: {
    type: Array,
    default: () => {
      return []
    },
  },
  count: {
    type: Number,
    default: 0,
  },
  emptyTitle: {
    type: String,
    default: '暂无订单，快去添加吧！',
  },
})
const emit = defineEmits(['editFollow'])

const { orderData, emptyTitle, count } = toRefs(props)
const statusList = ref({
  '-1': {
    name: '审批驳回',
    color: '#ED4014',
  },
  0: {
    name: '待处理',
    color: '#FFC107',
  },
  1: {
    name: '待审核',
    color: '#409EFF',
  },
  2: {
    name: '待签约',
    color: '#19BE6B',
  },
  3: {
    name: '已签约',
    color: '#409EFF',
  },
  4: {
    name: '已拒绝',
    color: '#909399',
  },
  5: {
    name: '已过期',
    color: '#909399',
  },
  6: {
    name: '已撤销',
    color: '#909399',
  },
})

const examineList = (item) => {
  // store.commit("setCustomerFormType", formType.value);
  clickNavigateTo(`/pages/customer/contract/details?id=${item.id}`)
}
</script>

<style scoped lang="scss">
.contract-tab-info {
  background: #fff;
  /* 触发BFC（块级格式化上下文） */
  overflow: hidden; /* 或 auto, scroll */
  /* 或者 */
  display: flow-root; /* 现代解决方案 */
  .contract-info-item {
    padding: 30rpx 30rpx 14rpx;
    font-size: $uni-font-size-default;
    color: $uni-text-color;
    background: #fff;
    border-bottom: 1px solid #eeeeee;
    // &:last-child {
    // 	border: none;
    // }
  }
}

.contract-header {
  display: flex;
  justify-content: space-between;
  .contract-title {
    font-weight: 500;
    font-size: 26rpx;
  }
  .status-badge {
    min-width: 88rpx;
    height: 42rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    font-size: 24rpx;
    padding: 0 10rpx;
    &.status-0 {
      color: #1890ff;
      background: rgba(24, 144, 255, 0.08);
    }
    &.status-1 {
      color: #ff9900;
      background: rgba(255, 153, 0, 0.08);
    }
    &.status-2 {
      color: #1cbf6c;
      background: rgba(255, 153, 0, 0.08);
    }
    &.status-3 {
      color: #ed4014;
      background: rgba(237, 64, 20, 0.08);
    }
    &.status-4 {
      color: #ed4014;
      background: rgba(237, 64, 20, 0.08);
    }
  }
}
.contract-body {
  margin-top: 22rpx;
  .contract-item {
    font-size: 26rpx;
    color: $uni-text-color;
    display: flex;
    align-items: center;
  }
  .left {
    color: #606266;
  }
}
</style>
