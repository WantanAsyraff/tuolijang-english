<template>
  <BaseContainer>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :is-right="true"> </default-nav-bar>
      <!-- <customer-tab :examine-tab-data="data.examineTabData" :index="data.tabIndex" @change="changeTab"></customer-tab> -->

      <form-box @change="formBoxChange" :status="data.tabId" type="approve"></form-box>
    </view>

    <view class="examine-content">
      <invoice-list :list-data="data.listData" :empty-title="data.emptyTitle"></invoice-list>
    </view>
    <tabbar :currentIndex="6" navigateType="redirectTo" />
    <global-index />
  </view>
  </BaseContainer>
</template>

<script setup>
import tabbar from "@/components/tabbar/index.vue";
import BaseContainer from "@/components/BaseContainer/index.vue";
import defaultNavBar from "@/components/defaultNavBar/index.vue";
import invoiceList from "./components/invoiceList.vue";
import customerTab from "@/pages/customer/list/components/customerTab.vue";
import globalIndex from "@/components/globalIndex/index.vue";
import formBox from "./components/formBox.vue";
import { ref, reactive } from "vue";
import message from "@/utils/message";

const data = reactive({
  typeIndex: 0,
  tabIndex: 0,
  tabId: 2,
  emptyTitle: "当前暂无发票～",
  customStyle: { border: "none", lineHeight: "20px", background: "#ED4014" },
  examineTabData: [
    { name: "我负责的", id: 2 },
    { name: "我查看的", id: 1 },
  ],
  listData: [],
  where: {
    page: 1,
    limit: 10,
  },
});

import { clientInvoiceApi } from "@/api/customer";
import { onLoad } from "@dcloudio/uni-app";
onLoad((options) => {
  getTabList();
});



import { clickNavigateTo, backToTop } from "@/utils/helper";


const changeTab = (e) => {
  if (data.where.page > 1) {
    backToTop();
  }
  data.tabId = e.id;
  data.tabIndex = e.index;
  data.where.page = 1;
  getTabList(true);
};

const formBoxChange = (e) => {
  data.where= e
   data.where.page = 1;
   data.where.limit = 10;
  getTabList(true);
};

// 条件判断
const getTabList = (tab = false) => {
  data.where.page = 1;
  
  getConfigList(tab);
};

const listLoading = ref(false);
// 列表加载
const getConfigList = (tab = false) => {
  clientInvoiceApi(data.where)
    .then((res) => {
      // 切换时数据清空
      if (tab) data.listData = [];
      data.listData.push(...res.data.list);
      const allPage = Math.ceil(res.data.count / data.where.limit);
      if (data.listData.length <= 0 || data.where.page >= allPage) {
        listLoading.value = false;
      } else {
        listLoading.value = true;
      }
      uni.stopPullDownRefresh(); // 停止刷新
    })
    .catch((error) => {
      message.error(error.message);
    });
};

import { onReachBottom, onPullDownRefresh } from "@dcloudio/uni-app";
// 下拉加载
onReachBottom(() => {
  if (listLoading.value) {
    data.where.page++;
    getConfigList();
  }
});
// 上拉加载
onPullDownRefresh(() => {
  data.where.page = 1;
  data.value = false;
  data.listData = [];
  getConfigList();
});
</script>

<style scoped lang="scss">
  .content {
    width: 100%;
    position: relative;

    .cr-position-header {
      background-color: #fff;
      position: sticky;
    }

    .examine-content {
      margin-bottom: 103rpx;
      margin-top: 8rpx;
    }
  }
  ::v-deep .uni-list {
    background-color: transparent;
  }

  ::v-deep .empty{
    background-color: #fff;
    height: calc(100vh - 300rpx);
  }
  ::v-deep .item-list {
    border-radius: 0;
    border: none;
    margin-bottom: 0rpx;

  }
  ::v-deep .uni-list-item{
    margin-bottom: 8rpx;

  }
::v-deep .uni-searchbar {
  padding-bottom: 0 !important;
}
  
</style>
