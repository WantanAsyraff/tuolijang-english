<template>
  <view class="list">
    <!-- 付款提醒列表组件 -->
    <!-- 回款提醒 -->
    <template v-if="listData.length > 0">
      <view class="list-item" v-for="(item,index) in listData" :key="item.id">
        <view class="header">
          <view class="title">
            <text class="iconfont"
              :class="item.types === 0 ? 'icon-hetong-hetonghuikuan' : 'icon-tixing-fukuantixing'" />
            {{item.types === 0 ? $t('ui.customerContractPayRemindPaymentReminder') : $t('ui.customerContractPayRemindRenewalReminder')}}
          </view>
           <text class="iconfont icon-yunwenjian-gengduo" @click="customerMoreFn(item)"></text>

        </view>
          <view class="form">
            <view class="label">{{ $t('ui.customerContractPayRemindRecipients') }} </view>
            <view class="item">{{item.card.name}}</view>
          </view>
          <view class="form">
            <view class="label">{{ $t('ui.customerContractAddRemindRenewalAmount') }} </view>
            <view class="item red">￥{{item.num}}</view>
          </view>
    

        <view class="form">
          <view class="label">{{ $t('ui.customerContractAddRemindReminderContent') }} </view>
          <view class="item">{{item.mark}}</view>
        </view>
        <view class="form">
          <view class="label">{{item.types === 0?$t('ui.customerContractPayRemindPaymentCollection'):$t('ui.customerContractPayRemindRenewal')}}{{ $t('ui.attendanceDetailedUserOvertimeListDate') }} </view>
          <view class="item"><uni-dateformat format="yyyy-MM-dd hh:mm" :date="item.time"></uni-dateformat></view>
        </view>
      
      </view>
      </template>
 
   
    <empty v-else :index="9" :title="emptyTitle" style="height: 950rpx;"></empty>
        <!-- 操作弹窗 -->
    <more-popup ref="customerMoreRef"  @handleItem="dropDownItem"></more-popup>
  </view>
</template>

<script setup lang="ts">
  import morePopup from "@/components/morePopup/index.vue";
  import empty from "@/components/empty/index.vue";
  import { ref, toRefs } from "vue";
  import message from "@/utils/message";
  import { clickNavigateTo, showModal } from "@/utils/helper";
  import { clientRemindDeletaApi, clientRemindAbjureApi } from "@/api/customer";
  import type { Res, PropType } from "@/utils/typeHelper";

  const props = defineProps({
    listData: {
      type: Array,
      default() {
        return [];
      }
    },
    buildData: {
      type: Object,
      default() {
        return {};
      }
    },
    emptyTitle: {
      type: String,
      default: "暂无付款提醒，快去添加吧！"
    },
  });
  const { listData, buildData } = toRefs(props);
  const rowData = ref({});

  // 判断年月
  enum RedindPeriod {
    天,
    周,
    月,
    年
  }

  // 订单状态
  enum CustomerStatus {
    待处理,
    已放弃,
    已处理
  }
  const getCustomerStatus = (status : number) : string => {
    return CustomerStatus[status];
  };

  const indexItem = ref(-1);
  const deanPopoverRef = ref(null);
  const customerMoreRef = ref(null);

  const customerMoreFn = (item : PropType) => {
    rowData.value = item;
    let forumMeus = [
      {
       id: item.types==0?1:2,
        name: item.types === 0?'回款':'续费'
      },
      {
        id: 3,
        name: "编辑"
      },{
        id: 4,
        name: "删除"
      }
    ];
    
    customerMoreRef.value.popupOpen(forumMeus);
  }

  const dropDownItem = (val : any) => {
    let query = "";
    let item = rowData.value;
    if (val.id === 1) {
    // 回款
     query = `/pages/users/examine/default?id=${buildData.value.contract_refund_switch}&cid=${val.cid}&eid=${val.eid}`;
    } else if (val.id === 2) {
      // 续费
            query = `/pages/users/examine/default?id=${buildData.value.contract_renew_switch}&cid=${item.cid}&eid=${item.eid}`;
    } else if (val.id === 3) {
      // 编辑
        query = `/pages/customer/contract/addRemind?id=${item.id}&cid=${item.cid}&eid=${item.eid}`;
    }else if (val.id === 4) {
      // 删除
     onDelete(item, 1);
    }
      clickNavigateTo(query);
  }



  // 删除
  const onDelete = (item : PropType, type : number) => {
    if (type === 1) {
      showModal("确定要删除该付款提醒吗").then(() => {
        remindDeleta(item.id);
      }).catch(() => {

      });
    } else {
      showModal("确定之后变为已放弃状态，您确定此订单不再续费了吗").then(() => {
        remindAbjure(item.id);
      }).catch(() => {

      });
    }
  };

  const remindDeleta = (id : number) => {
    clientRemindDeletaApi(id).then((res : Res) => {
      message.success(res.message);
      listData.value.splice(indexItem.value, 1);
    }).catch((error : Res) => {
      message.error(error.message);
    });
  };
  // 付款放弃
  const remindAbjure = (id : number) => {
    clientRemindAbjureApi(id).then((res : Res) => {
      message.success(res.message);
      listData.value[indexItem.value].status = 1;
    }).catch((error : Res) => {
      message.error(error.message);
    });
  };
</script>

<style lang="scss" scoped>
  .list {
    width: 100%;
    // padding-top: 10rpx;
    padding-bottom: 20rpx  !important;

    .red {
      color: #E93323 !important;
    }

    .list-item {
      width: 100%;
      border-radius: 12rpx;
    margin-bottom: 8px;
   padding: 24rpx;
      border: 1px solid #eeeeee;

      .icon-tixing-fukuantixing {
        color: #FF9900;
        font-size: 52rpx;
        margin-right: 14rpx;
      }

      .header {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
     
        margin-bottom: 20rpx;

        .title {
          display: flex;
          align-items: center;
          font-family: PingFang SC, PingFang SC;
         font-weight: 500;
font-size: 26rpx;
color: #303133;
        }

        .icon-yunwenjian-gengduo {
          color: #606266;
          font-size: 26rpx;
        }


        .icon-hetong-hetonghuikuan {
          font-size: 40rpx;
          color: #19BE6B;
          margin-right: 14rpx;
        }
      }

      .form {
        display: flex;
        margin-bottom: 16rpx;

        .label {
          width: 152rpx;
         font-family: PingFang SC, PingFang SC;
font-weight: 400;
font-size: 26rpx;
color: #606266;
          margin-right: 30rpx;
        }

        .item {
          width: 100%;

          word-wrap: break-word;
          word-break: normal;
         font-family: PingFang SC, PingFang SC;
font-weight: 400;
font-size: 26rpx;
color: #303133;

        }
      }

   
    }
  }
</style>