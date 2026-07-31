<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item, index) in listData" :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list">
            <view @click="examineList(item)">
              <view class="item-list-top">{{ item.contract_name || '--' }}
                <text class="status-tag" :style="{
                  color: item.contract_status&& item.contract_status.color ? item.contract_status.color : '#1890ff',
                  background: item.contract_status.color
                    ? getColor(item.contract_status.color, '0.1')
                    : getColor('#1890ff', '0.1')
                }" >{{ item.contract_status.name }}</text>
              </view>

              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">订单金额</uni-col>
                <uni-col :span="19">{{ item.surplus || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">付款状态</uni-col>
                <uni-col :span="19">{{ item.payment_status === 1 ? '已结清' : '未结清'}}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">业务员</uni-col>
                <uni-col :span="19">{{ item.salesman.name || '--' }}</uni-col>
              </uni-row>
            </view>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="7" title="暂无合同数据"></empty>
    <view class="fixed-btn" @click="handleClick">
     <text class="iconfont icon-kaoqin-beizhu1" /> 关联订单
    </view>
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
    detail: {
      type: Object,
      default() {
        return {};
      },
    },
});
const { listData ,detail} = toRefs(props);

import { clickNavigateTo } from "@/utils/helper";
import { jsx } from "vue/jsx-runtime";
const examineList = (item) => {
//   clickNavigateTo(
//     `/pages/customer/signing/details?id=${item.id}`,
//   );
};
// 关联付款单
const handleClick = () => {
  let value = encodeURIComponent(JSON.stringify(detail.value))
  let type = 'order'
  clickNavigateTo(
    `/pages/customer/signing/orderList?detail=${value}&type=${type}&tab=3`,
  );
};

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
    margin-bottom: 2rpx;
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
.fixed-btn {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
 
width: 220rpx;
height: 74rpx;
display: flex;
align-items: center;
justify-content: center;
font-weight: 400;
font-size: 24rpx;
color: #333333;
background: rgba(255,255,255,0.9);
box-shadow: 0px 3px 12px 0px rgba(0,0,0,0.05);
border-radius: 172px;
cursor: pointer;
.icon-kaoqin-beizhu1 {
  margin-right: 6rpx;
  font-size: 32rpx;
  height: 32rpx;
}
 
}
</style>