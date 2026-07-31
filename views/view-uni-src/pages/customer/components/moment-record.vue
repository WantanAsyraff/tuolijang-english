<template>
  <view class="dynamic-record">
   
    <template v-if="dynamicConfig.length > 0">
      <view class="dynamic-record-item" v-for="(item, index) in dynamicConfig" :key="index">
        <view class="item-top">
          {{ getMomentStatus(item) }}
          <view class="item-intro" >
            {{ item.creator?.name }}
            <text class="item-divider" v-if="item.creator&&item.creator.name"></text>
            {{ moment(item.created_at).format("YYYY-MM-DD HH:mm") }}
          </view>
        </view>
        <view class="item-body">
          {{ $t('ui.customerMomentRecordInformation') }}
          <view class="item-intro">
            {{ item.reason }}
            <!-- <view class="item-tip-time">
            <image src="@/static/image/remind.png" class="remind-icon" />
            提醒时间：{{ item.tipTime }}
          </view> -->
          </view>
        </view>
      </view>
    </template>
    <empty v-else :index="7" :title="$t('ui.customerMomentRecordNoActivityRecords')"></empty>
  </view>
</template>

<script setup lang="ts">
  import empty from "@/components/empty/index.vue";
  import moment from "moment";

  const props = defineProps<{
    dynamicConfig : any[];
  }>();

  const { dynamicConfig } = toRefs(props);

  const CLUE_STATUS_MAP : Record<number, string> = {
    1: "新增线索",
    2: "修改线索",
    3: "领取线索",
    4: "退回线索池",
    5: "线索转客户",
    6: "转移",
    10: '数据变更'
  };

  const ODDS_STATUS_MAP : Record<number, string> = {
    1: "新增商机",
    2: "修改商机",
    6: "转移",
    10: '数据变更'
  };
  const DOC_STATUS_MAP : Record<number, string> = {
    '-1':'合同签约审批拒绝',
          '1':'新增合同签约',
          '2':'合同签约审批通过',
          '3':'合同签约完成',
          '4':'拒绝合同签约',
          '5':'签约已过期',
          '6':'签约已撤销'
  };
  const CON_STATUS_MAP : Record<number, string> = {
   '10': '数据变更',
          '5': '移交订单',
          '6': '新增订单'
  };
  const LIAISON_STATUS_MAP : Record<number, string> = {
    6: "新增联系人",
    10: "数据变更"
  };

  const getMomentStatus = (item : any) => {
    if (item.link_type === "clue") {
      return CLUE_STATUS_MAP[item.type] || "-";
    } else if (item.link_type === "odds") {
      return ODDS_STATUS_MAP[item.type] || "-";
    } else if (item.link_type === "contract_doc") {
      return DOC_STATUS_MAP[item.type] || "-";
    }else if (item.link_type === "contract") {
      return CON_STATUS_MAP[item.type] || "-";
    } else if (item.link_type === "liaison") {
      return LIAISON_STATUS_MAP[item.type] || "-";
    }
     
  };
</script>

<style scoped lang="scss">
  .dynamic-record {
    padding: 40rpx 0;
    background-color: #fff;
  }

  .dynamic-record-item {
    padding-inline: 30rpx 20rpx;
    position: relative;

    &:last-child {
      &::before {
        display: none;
      }
    }

    &:first-child {
      &::after {
        background-color: #fff;
        border: 2rpx solid #1890FF;
        box-sizing: border-box;
      }
    }

    &::before {
      content: "";
      position: absolute;
      width: 4rpx;
      top: 26rpx;
      left: 4rpx;
      bottom: -54rpx;
      background: #eee;
    }

    &::after {
      content: "";
      position: absolute;
      width: 12rpx;
      height: 12rpx;
      background: #1890FF;
      left: 4rpx;
      top: 14rpx;
      border-radius: 50%;
    }

    &+.dynamic-record-item {
      margin-top: 40rpx;
    }
  .item-intro {
        display: flex;
        align-items: center;
        font-size: 26rpx !important;
        color: #909399;
      }
    .item-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-weight: 500;
      font-size: 28rpx;
      color: #303133;
      line-height: 40rpx;

    

      .item-divider {
        width: 2rpx;
        height: 16rpx;
        background: #909399;
        margin-inline: 16rpx;
      }
    }

    .item-body {
      display: flex;
      margin-top: 16rpx;
      background: #f7f7f7;
      border-radius: 8rpx;
      padding: 30rpx 24rpx;
      font-size: 26rpx;
      color: #999999;
      line-height: 40rpx;
      font-family: PingFang SC, PingFang SC;

      .item-intro {
        flex: 1;
        color: #333333;
      }

      .item-tip-time {
        background: rgba(24, 144, 255, 0.1);
        border-radius: 8rpx;
        margin-top: 16rpx;
        height: 76rpx;
        color: #1890FF;
        display: flex;
        align-items: center;
        padding-inline: 18rpx;

        .remind-icon {
          height: 34rpx;
          width: 34rpx;
          object-fit: cover;
          margin-right: 18rpx;
        }
      }
    }
  }
</style>
