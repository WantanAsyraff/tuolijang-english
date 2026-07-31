<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item, index) in listData" :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list">
            <view @click="examineList(item)">
              <view class="item-list-top">
                <text
                  >{{ item.contract_no || '--' }}

                  <text class="iconfont icon-shequ-shoucang-yishoucang" v-if="item.contract_followed == 1"></text
                ></text>
                <text
                  class="status-tag"
                  :style="{
                    color: item.contract_status.color ? item.contract_status.color : '#1890ff',
                    background: item.contract_status.color ? getColor(item.contract_status.color, '0.1') : getColor('#1890ff', '0.1'),
                  }"
                  >{{ $ts(item.contract_status.name) }}</text
                >
              </view>

              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailOrderNo') }}</uni-col>
                <uni-col :span="19">{{ item.contract_no || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailCustomerName') }}</uni-col>
                <uni-col :span="19">{{ item.contract_customer || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerSigningOrderListOrderAmount') }}</uni-col>
                <uni-col :span="19">{{ item.contract_price || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailSalesperson') }}</uni-col>
                <uni-col :span="19">{{ item.salesman ? item.salesman.name : '--' }}</uni-col>
              </uni-row>
              <!-- <uni-row class="item-list-content">
                <uni-col :span="5" class="left">起止时间</uni-col>
                <uni-col :span="19">
                  <text v-if="item.start_date&&item.end_date">  <uni-dateformat format="yyyy/MM/dd" :date="item.start_date"></uni-dateformat>-
                <uni-dateformat format="yyyy/MM/dd" :date="item.end_date"></uni-dateformat></text>
                <text v-else>--</text>
              

                </uni-col>
              </uni-row> -->
            </view>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="7" :title="$t('ui.customerContractContractListNoContractData')" class="bgf" style="height: calc(100vh - 300rpx)"></empty>
  </view>
</template>

<script setup>
import empty from '@/components/empty/index.vue'
import avatar from '@/components/avatar/index.vue'
import { ref, toRefs } from 'vue'
import message from '@/utils/message'
import { useStore } from 'vuex'
import { getColor } from '@/utils/helper'
import { clickNavigateTo } from '@/utils/helper'
import { clientContractStatusApi } from '@/api/customer'

const store = useStore()
const props = defineProps({
  listData: {
    type: Array,
    default() {
      return []
    },
  },
  emptyTitle: {
    type: String,
    default: '',
  },
  type: {
    type: Number,
    default: 0,
  },
  follow: {
    type: Number,
    default: 0,
  },
  formType: {
    type: Object,
    default: () => ({}),
  },
})
const { listData, emptyTitle, type, follow, formType } = toRefs(props)

const indexItem = ref(-1)
const clickFollow = (index, item) => {
  indexItem.value = index
  getStatuss(item.id, item.contract_followed == 0 ? 1 : 0, {
    status: item.contract_followed == 0 ? 1 : 0,
  })
}
// 取消/关注
const getStatuss = (id, status, datas) => {
  clientContractStatusApi(id, status, datas)
    .then((res) => {
      message.error(res.message)
      listData.value[indexItem.value].contract_followed = listData.value[indexItem.value].contract_followed === 0 ? 1 : 0
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const createSigning = () => {
  clickNavigateTo(`/pages/customer/contract/addSigning`)
}
const examineList = (item) => {
  store.commit('setCustomerFormType', formType.value)
  clickNavigateTo(`/pages/customer/contract/details?id=${item.id}`)
}
</script>

<style scoped lang="scss">
::v-deep .uni-list-item__container {
  padding: 0;
}

.examine-content-list {
  // background-color: #fff;
  ::v-deep .uni-list {
    background-color: $uni-default-bg;

    .uni-list--border {
      top: auto;
      left: auto;
    }

    .uni-list-item {
      margin-top: 8rpx;
    }
  }

  .status-tag {
    margin-left: 16rpx;
    min-width: 68rpx;
    height: 42rpx;
    border-radius: 8rpx;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 400;
    font-size: 24rpx;
    padding: 0 10rpx;
  }

  .item-list {
    width: 100%;
    position: relative;
    padding: 30rpx;
    font-family:
      PingFang SC,
      PingFang SC;

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

.icon-shequ-shoucang-yishoucang {
  color: #f90;
}
</style>
