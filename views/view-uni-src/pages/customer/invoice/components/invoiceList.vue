<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item) in listData" 
        :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list" @click="itemList(item)">
            <view class="item-list-content">
              <view style="display: flex; align-items: center;">
                <text class="iconfont" :class="typesList[item.types].icon"></text>
                <text class="name" >{{ typesList[item.types].name }}</text>
              </view>
              <view class="right">
                <text class="status-tag" :style="{
                  '--fill-color': getAbnormalColor(item),
                  '--bag': getAbnormalBag(item)
                }">{{ getAbnormalText(item) }}</text>


              </view>
            </view>
             <uni-row class="item-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerInvoiceCheckPaymentPaymentAmount') }}</uni-col>
                <uni-col :span="19" class="jine">{{ item.amount || '--' }}{{ $t('ui.customerContractPayDetailYuan') }}</uni-col>
              </uni-row>
             <uni-row class="item-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerInvoiceAddInvoiceInvoiceHeader') }}</uni-col>
                <uni-col :span="19" >{{ item.title || '--' }}</uni-col>
              </uni-row>
             <uni-row class="item-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerInvoiceInvoiceDetailApplicant') }}</uni-col>
                <uni-col :span="19" >{{ item.card?item.card.name: '--' }}</uni-col>
              </uni-row>
             <uni-row class="item-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerInvoiceInvoiceDetailInvoiceDate') }}</uni-col>
                <uni-col :span="19" > <uni-dateformat v-if="item.created_at" format="yyyy/MM/dd hh:mm:ss" :date="item.created_at"></uni-dateformat>
                <view v-else>
                  --
                </view>
                </uni-col>
              </uni-row>
         
           
     
            <view class="item-button-content" v-if="isFinance && !data.showArray.includes(item.status)">
              <button class="button default-error" v-if="item.status === 5"
                @click.stop="toExamine(item, 2)">{{ $t('ui.financeInvoiceDetailsInvalidate') }}</button>
              <button v-if="item.status == 1" class="button default-color"
                @click.stop="toExamine(item, 1)">{{ $t('ui.financeInvoiceDetailsEnterInvoice') }}</button>
            </view>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="10" :title="emptyTitle" style="min-height: 950rpx;"></empty>
  </view>
</template>

<script setup lang="ts">
import empty from "@/components/empty/index.vue";
import { reactive, toRefs } from "vue";
import type { Detail } from "@/utils/typeHelper";
import { clickNavigateTo } from "@/utils/helper";

const props = withDefaults(
  defineProps<{
    btnShow?: boolean;
    type?: number;
    eid?: string | number;
    cid?: string | number;
    name?: string;
    isFinance?: boolean;
    emptyTitle?: string;
    tab?: number;
    listData: Array<any>;
  }>(), {
  btnShow: false,
  type: 0,
  eid: 0,
  cid: 0,
  name: "",
  isFinance: false,
  emptyTitle: "",
  tab: 0,
  listData: <any>[]
});
// 导出 {
const { listData, type, emptyTitle, eid, cid, name, isFinance, tab } = toRefs(props);

const data = reactive({
  showArray: [2, 4],
  addBtn: "添加发票",
  addUrl: `/pages/customer/invoice/addInvoice?eid=${eid.value}&name=${name.value}&cid=${cid.value}`
});
const typesList = ref({
  '1':{
    'name':"个人普通发票",
    'icon':'icon-fapiao-gerenputongfapiao'
  },
  '2':{
    'name':"企业普通发票",
    'icon':'icon-fapiao-qiyeputongfapiao1'
  },
  '3':{
    'name':"企业专用发票",
    'icon':'icon-fapiao-qiyezhuanyongfapiao'
  },
})
let emit = defineEmits(["change"]);

const getAbnormalText = (row) => {
  let str = "";
  if (row.status == 1) {
    str = "待开票";
  }else if(row.status==0){
    str = "待审核";
  } else if (row.status == 2) {
    str = "已拒绝";
  } else if (row.status == 3) {
    str = "撤回开票";
  } else if (row.status == 4) {
    str = "申请作废";
  } else if (row.status == 5) {
     str = "已开票";
  } else {
    str = "已作废";
  }
  return str;
};

const getAbnormalBag = (row) => {
  let str = "";
  if (row.status == 1) {
    str = "rgba(255, 153, 0, 0.1)";
  } else if(row.status==0){
     str = "rgba(255, 153, 0, 0.1)";
  }else if (row.status == 2) {
    str = "rgba(24,144,255,0.1)";
  } else if (row.status == 3) {
    str = "rgba(153,153,153,0.1)";
  } else if (row.status == 4) {
    str = "rgba(255, 153, 0, 0.1)";
  } else if (row.status == 5) {
    str = "rgba(24,144,255,0.1) ";
  } else {
    str = "rgba(153,153,153,0.1)";
  }
  return str;
};
const getAbnormalColor = (row) => {
  let str = "";
  if (row.status == 1) {
    str = "rgba(255, 153, 0, 1)";
  } else if(row.status==0){
     str = "rgba(255, 153, 0, 1)";
  }else if (row.status == 2) {
    str = "rgba(237,64,20,1)";
  } else if (row.status == 3) {
    str = "rgba(153,153,153,1)";
  } else if (row.status == 4) {
    str = "rgba(255, 153, 0, 1)";
  } else if (row.status == 5) {
    str = "rgba(24,144,255,1) ";
  } else {
    str = "rgba(153,153,153,1)";
  }
  return str;
};



const itemList = (item: Detail): void => {
  if (isFinance.value) {
    clickNavigateTo(`/pages/finance/invoice/details?id=${item.id}&tab=${tab.value}`);
  } else {
    clickNavigateTo(`/pages/customer/invoice/details?id=${item.id}`);
  }
};
const toExamine = (row: object, type: number): void => {
  emit("change", {
    row,
    type
  });
};
</script>

<style scoped lang="scss">
  .item-list {
    width: 100%;
    position: relative;
    border: 2rpx solid #EEEEEE;
    padding: 24rpx;
    margin-bottom: 20rpx;
    border-radius: 12rpx 12rpx 12rpx 12rpx;

    .item-list-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20rpx;
      .iconfont {
        font-size: 40rpx;
        color: $uni-color-primary;
        margin-right: 12rpx;
      }
      
      .icon-fapiao-gerenputongfapiao {
        color: #FF9900;
      }
      .name {
        font-family: PingFang SC, PingFang SC;
        font-weight: 500;
        font-size: 26rpx;
        color: #303133;
      }
    }

      .item-content {
      font-weight: 400;
      font-size: 24rpx;
      color: #303133;
      margin-bottom: 16rpx;
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
      .jine {
        color: #ED4014;
      }

      }

    
  }
  ::v-deep .uni-list-item__container{
    padding: 0;
  }
 .status-tag {
      font-family: PingFang SC, PingFang SC;
      padding: 4rpx 8rpx;
      background: var(--bag);
      border-radius: 8rpx;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 22rpx !important;
      color: var(--fill-color);
      font-weight: 400;
    }
    .item-button-content {
      padding-top: 30rpx;
      display: flex;
      justify-content: space-between;

      .button {
        width: 100%;
        height: 74rpx;
        line-height: 74rpx;
        font-size: 28rpx;
        background-color: #F0F1F5;
        margin-right: 32rpx;

        &:last-of-type {
          margin-right: 0;
        }

        &::after {
          border-radius: 8rpx;
          border: none;
        }
      }
    }

</style>