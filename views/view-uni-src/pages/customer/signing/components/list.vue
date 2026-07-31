<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item, index) in listData" :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list">
            <view @click="examineList(item)">
              <view class="item-list-top">{{ item.doc_name || '--' }}
                <text class="status-tag" :style="{
                  color: statusList[item.status].color ? statusList[item.status].color : '#1890ff',
                  background: statusList[item.status].color
                    ? getColor(statusList[item.status].color, '0.1')
                    : getColor('#1890ff', '0.1')
                }" v-if="item.status">{{ $ts(statusList[item.status].name) }}</text>
              </view>

              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerListCustomerContractContractNo') }}</uni-col>
                <uni-col :span="19">{{ item.doc_no || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailCustomerName') }}</uni-col>
                <uni-col :span="19">{{ item.customer?.customer_name || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerSigningDetailItemSigningMethod') }}</uni-col>
                <uni-col :span="19">{{ item.sign_type == 1 ? $t('ui.customerSigningDetailItemOfflineSigning') : $t('ui.customerSigningDetailItemESign') }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerSigningListExpirationStatus') }}</uni-col>
                <uni-col :span="19">
                  <text v-if="item.fail_status == 0">{{ $t('ui.customerSigningListInProgress') }}</text>
                  <text v-else-if="item.fail_status == 1">{{ $t('ui.customerSigningListNotStarted') }}</text>
                  <text v-else>{{ $t('ui.customerSigningListExpired') }}</text>
                </uni-col>
              </uni-row>
            </view>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="7" :title="$t('ui.customerContractContractListNoContractData')" class="bgf" style="height: calc(100vh - 300rpx);"></empty>
    	<!-- 新增 -->
		<!-- <view class="add">
		  <text class="iconfont icon-xuanfuanniu-jia" @click="createSigning"></text>
		</view> -->

  </view>
</template>

<script setup>
import empty from "@/components/empty/index.vue";
import { getColor } from "@/utils/helper"
const props = defineProps({
  listData: {
    type: Array,
    default() {
      return [];
    },
  },
});
const { listData } = toRefs(props);
const statusList = ref({
  '-1': {
    name: '审批驳回',
    color: '#ED4014',
  },
  '0': {
    name: '待处理',
    color: '#FFC107',
  },
  '1': {
    name: '待审核',
    color: '#409EFF',
  },
  '2': {
    name: '待签约',
    color: '#19BE6B',
  },
  '3': {
    name: '已签约',
    color: '#409EFF',
  },
  '4': {
    name: '已拒绝',
    color: '#909399',
  },
  '5': {
    name: '已过期',
    color: '#909399',
  },
  '6': {
    name: '已撤销',
    color: '#909399',
  },
}
)
import { clickNavigateTo } from "@/utils/helper";
const examineList = (item) => {
  clickNavigateTo(
    `/pages/customer/signing/details?id=${item.id}`,
  );
};
const createSigning = () => {
  clickNavigateTo(
    `/pages/customer/signing/orderList`,
  );
}

</script>

<style scoped lang="scss">
::v-deep .uni-list-item__container {
  padding: 0;
}

.examine-content-list {
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
    font-family: PingFang SC, PingFang SC;

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
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 24rpx;
        color: #606266;
      }
    }
  }
}


	.add {
	  position: fixed;
    cursor: pointer;
	  right: 20rpx;
 bottom: 140rpx;
	  width: 42px;
	  height: 42px;
	  background: linear-gradient(135deg, #47B5FF 0%, #0F86F5 100%);
	  box-shadow: 0px 4px 4px 0px rgba(28, 146, 248, 0.1145);
	  border-radius: 50%;
	  text-align: center;
	  line-height: 42px;
	  color: #fff;
	
	  .icon-xuanfuanniu-jia {
	    font-size: 15px;
	  }
	}
</style>