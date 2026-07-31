<template>
  <view class="content">
    <!-- 选择付款单页面 -->
    <view class="cr-position-header">
      <default-nav-bar :is-right="true" :default-title="data.defaultTitle">
      </default-nav-bar>
      <view class="search">
        <view class="required-box"><text>{{ $t('ui.customerContractPayDetailCustomerName') }}</text></view>
         <view class="search-default-label">{{ data.customerName }} 
          </view>
        <!-- <picker class="picker-selector" mode="selector" @change="bindPickerChange" :value="selectedIndex" :range="options"
          range-key="customer_name">
          <view class="search-default-label">{{ data.customerName }} <text class="iconfont icon-jinru-copy"></text>
          </view>
        </picker> -->
      </view>
    </view>
    <view class="examine-content">
      <!-- 订单列表 -->
      <view class="item" v-for="(item, index) in data.list" :key="index">
        <view class="iconfont icon-xuanzeanniu-weixuan" @click="check(item, index)" v-if="data.ids.indexOf(item.id) < 0" />
        <view class="iconfont icon-denglu-tongyi" @click="check(item)" v-else />
        <view class="list-item">

          <view class="name"> {{ $t('ui.customerContractPayDetailOrderNo') }}</view>
          <view class="list-text">{{ item.contract_no }}</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerContractIndexOrderName') }} </view>
          <view class="list-text">{{ item.contract_name || '--' }}</view>
        </view>

        <view class="list-item">
          <view class="name"> {{ $t('ui.customerSigningOrderListOrderAmount') }} </view>
          <view class="list-text">{{ item.contract_price || '--' }}</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerSigningOrderListPaymentStatus') }} </view>
          <view class="list-text">
            {{ item.payment_status == 1 ? $t('ui.customerSigningOrderListSettled') : $t('ui.customerSigningOrderListUnsettled') }}
          </view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerSigningOrderListOrderStatus') }} </view>
          <view class="list-text">{{ item.contract_status && item.contract_status.name ? $ts(item.contract_status.name) : '--' }}
          </view>
        </view>

        <view class="list-item">
          <view class="name"> {{ $t('ui.customerContractPayDetailSalesperson') }} </view>
          <view class="list-text">{{ item.salesman && item.salesman.name ? item.salesman.name : '--' }}</view>
        </view>
        <view class="list-item">
          <view class="name"> {{ $t('ui.customerInvoiceCheckPaymentCreatedTime') }} </view>
          <view class="list-text">{{ item.created_at }}</view>
        </view>
      </view>

        <empty v-if="data.list.length == 0" :index="7" :title="$t('ui.customerSigningOrderListNoOrderData')" ></empty>

     

    </view>

    <!-- 底部 -->
    <view class="footer">
      <view class="flex">
        <view class="iconfont icon-xuanzeanniu-weixuan" v-if="data.checkShow" @click="checkAll(data.checkTitle)" />
        <view class="iconfont icon-denglu-tongyi" v-if="!data.checkShow" @click="checkAll(data.checkTitle)" />
        <view class="text">
          {{ data.checkTitle }}（{{ data.ids.length }}/{{ data.list.length }}）
        </view>
      </view>
      <view class="flex">
        <view class="btn cancel" @click="cancel">
          {{ $t('ui.baTreePickerIndexCancel') }}
        </view>
        <view class="btn next" @click="goNext">
          {{ data.type == 'order' ? $t('ui.baTreePickerIndexOk') : $t('ui.customerInvoiceCheckPaymentNext') }}
        </view>
      </view>
    </view>

  </view>
</template>

<script setup>
  import empty from "@/components/empty/index.vue";
import defaultNavBar from "@/components/defaultNavBar/index";
import {
  ref,
  reactive,
} from "vue";
import {
  clientContractListApi,
  customerListApi,
  opportunityListApi
} from "@/api/customer";
import { linkOrderApi } from "@/api/signing";
const data = reactive({
  defaultTitle: "选择订单",
  checkTitle: "全选",
  checkShow: true,
  ids: [], // 选中的id
  eid: '', // 客户id
  customerName: '请选择',
  customerInfo: {},
  list: [],
  type: '',
  tab: 0, // 1 订单 2 合同 3 付款单
  id: 0, // 合同合约id
});
import {
  onLoad
} from "@dcloudio/uni-app";
const options = ref([]);
const selectedIndex = ref(0)
onLoad((e) => {
  getOptions();
   if(e.eid){
    data.eid = Number(e.eid)
 
  
  }
  if (e.id) {
    data.id = e.id
  }

  if (e.type) {
    data.type = e.type
  }
  if (e.detail) {
    data.detail = JSON.parse(decodeURIComponent(e.detail))
    data.eid = data.detail.eid
  
       getContractList();
  
    data.customerName = data.detail.customer.customer_name
    data.customerInfo = data.detail.customer
    data.ids =[]
  }
   

  if (e.tab) {
    data.tab = Number(e.tab);
  }
});

import {
  clickNavigateTo,
  delayedReLaunch
} from "@/utils/helper";

const goNext = () => {
  if (!data.eid) {
    uni.showToast({
      title: '请选择客户',
    })
    return false
  }

  if (data.type == 'order') {

    linkOrderApi(data.detail.id, {
      cid: [...data.ids,...data.detail.cid],
    }).then((res) => {
      if (res.status == 200) {
        uni.showToast({
          title: '关联成功',
        })
      }
      uni.navigateBack();

    })
  } else {
    if (data.detail) {
      // 编辑
      data.detail.cid = data.ids
      data.detail.customer = data.customerInfo
      let detail = encodeURIComponent(JSON.stringify(data.detail))
      if(data.tab == 3){
        delayedReLaunch(`/pages/customer/signing/details?id=${data.id}&tab=${data.tab}`);
      }else{
        clickNavigateTo(`/pages/customer/signing/addForm?type=${data.type}&detail=${detail}`);
      }
    } else {
      // 新增
      clickNavigateTo(`/pages/customer/signing/addForm?id=${data.id}&cid=${JSON.stringify(data.ids)}&eid=${JSON.stringify(data.customerInfo)}`);
    }
  }




}

// 获取客户列表
const getOptions = () => {
  let obj = {
    limit: 0,
    page: 0,
    view_search: 11,
    is_select: 1,
    types: 'customer'
  }
  customerListApi(obj).then((res) => {
    options.value = res.data.list
  if(data.eid){
    selectedIndex.value = options.value.findIndex(item => item.id == data.eid)

    if(selectedIndex.value ==-1){
      selectedIndex.value = 0
      data.eid=''
      data.customerName = '请选择'
      data.list =[]
      return false
    }
    data.customerInfo = options.value.find(item => item.id == data.eid)
    data.customerName = data.customerInfo.customer_name
    
  }
  })
  
}
// 下拉选择
const bindPickerChange = (e) => {
  const len = e.detail.value;
  data.customerInfo = options.value[len]
  data.customerName = data.customerInfo.customer_name
  data.eid = data.customerInfo.id
  
 
};


const cancel = () => {
  uni.navigateBack();
};

const getOddsList = async () => {
  try {
    const res = await opportunityListApi({
      eid: data.eid,
      page: 1,
      limit: 0
    });
    const list = res.data?.list || [];
    data.list = list.filter(item => !item.is_sign);
  } catch (error) {
    console.error('获取商机列表失败:', error);
    data.list = [];
  }
};

// 获取订单列表
const getContractList = (id) => {
  const obj = {
    eid: data.eid,
    page: 0,
    limit: 0,
    types: 'contract'
  }
  clientContractListApi(obj).then((res) => {
    data.list = res.data.list || [];
    data.list  = data.list.filter(item => !item.is_sign)
  })
};

const check = (val, index) => {
  let labelIdList = data.ids.indexOf(val.id);
  if (labelIdList < 0) {
    data.ids.push(val.id); // 添加
  } else {

    data.ids = data.ids.filter((item) => {
      return item !== val.id;
    });
  }
  if(data.ids.length ==data.list.length){
    data.checkShow = false;
    data.checkTitle = "取消全选";
  }else{
    data.checkShow = true;
    data.checkTitle = "全选";
  }


};
const checkAll = (val) => {
  data.ids = [];
  if (val === "全选") {
    data.checkShow = false;
    data.checkTitle = "取消全选";
    data.list.map((item) => {
      data.ids.push(item.id);
    });
  } else if (val === "取消全选") {
    data.checkShow = true;
    data.checkTitle = "全选";
    data.ids = [];
  }
};
const onCheck = (val) => {
  data.ids.splice(val.id, 1);
};
</script>

<style lang="scss">
.cr-position-header {
  padding-top: var(--status-bar-height);
  background-color: #fff;
  position: sticky;
  top: 0;
  z-index: 1;
}

.content {
  .flex {
    display: flex;
    align-items: center
  }

  .examine-content {
    padding-bottom: 126rpx;

    .item {
      margin-top: 8rpx;
      width: 100%;
      position: relative;
      background-color: #fff;
      padding: 32rpx;
      padding-left: 96rpx;

      .iconfont {
        position: absolute;
        top: 40rpx;
        left: 30rpx;
        font-size: 36rpx;
        color: #C0C4CC;
      }

      .icon-denglu-tongyi {
        font-size: 36rpx;
        color: #1890FF;
      }

      .list-item {
        display: flex;
        margin-bottom: 16rpx;

        .list-text {
          height: 28rpx;
          width: 378rpx;
          margin-left: 40rpx;
          font-family: PingFang SC, PingFang SC;
          font-weight: 400;
          font-size: 26rpx;
          color: #303133;

        }

        .name {
          font-family: PingFang SC, PingFang SC;
          font-weight: 400;
          font-size: 26rpx;
          color: #606266;
          width: 104rpx;
          text-align: left;
        }
      }
    }
  }

  .footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    height: 116rpx;
    padding: 0 30rpx;
    background-color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;

    .btn {
      width: 168rpx;
      height: 74rpx;
      font-size: 30rpx;
      font-family: PingFang SC-常规体, PingFang SC;
      font-weight: 400;
      text-align: center;
      line-height: 74rpx;
      border-radius: 8rpx;
    }

    .cancel {
      background-color: #F0F1F5;
      color: #303133;
      margin-right: 24rpx;
    }

    .next {
      color: #fff;
      background-color: #1890FF;
    }

    .text {
      font-weight: 400;
      font-size: 30rpx;
      color: #303133;
    }

    .icon-denglu-tongyi {
      margin-top: 4rpx;
      color: #1890FF;
      margin-right: 26rpx;
    }

    .icon-xuanzeanniu-weixuan {
      margin-top: 4rpx;
      color: #C0C4CC;
      font-size: 36rpx;
      margin-right: 26rpx;
    }
  }
}


.search {
  width: 100%;
  height: 100rpx;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 30rpx;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 30rpx;
  color: #303133;

  .icon-jinru-copy {
    font-size: 28rpx;
    color: #C0C4CC;
  }

  .required-box {
    position: relative;

  }

  .required {
    position: absolute;
    right: -10px;
    top: 12rpx;
    color: #FF4D4F;
    font-size: 36rpx;

  }
}

.search-default-label {
  font-size: 30rpx;
  color: #303133;
}
</style>
