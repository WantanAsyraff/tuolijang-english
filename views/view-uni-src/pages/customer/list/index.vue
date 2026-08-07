<template>
  <BaseContainer>
    <view class="content">
      <view class="cr-position-header">
        <view class="status_bar"></view>
        <default-nav-bar  :index="data.tabIndex" :default-type="1" :is-right="false"
          :tab-data="tabData" @handleNarItem="changeTab"></default-nav-bar>
        <form-box @change="formBoxChange" :typeData="typeData"
          :keyWord="data.keyWord" type="approve"></form-box>
      </view>
      <view class="examine-content">
        <customer-list-default :list-data="data.listData" :type-index="data.typeIndex" :types="data.where.types"
          :empty-title="data.emptyTitle"></customer-list-default>
      </view>
      <global-index />
      <tabbar :currentIndex="1" navigateType="redirectTo" />
    </view>
  </BaseContainer>
</template>

<script setup lang="ts">import appI18n from '@/locale';

import BaseContainer from "@/components/BaseContainer/index.vue";
import tabbar from "@/components/tabbar/index.vue";
import defaultNavBar from "@/components/defaultNavBar/index.vue";
import customerListDefault from "./components/customerListDefault.vue";
import globalIndex from "@/components/globalIndex/index.vue";
import formBox from "./components/formBox.vue";
import { ref, reactive } from "vue";
import message from "@/utils/message";
import { customerTabList } from "@/utils/assessment";
import { customerListApi } from "@/api/customer";
const data = reactive({
  typeIndex: 0,
  tabIndex: 0,
  tabId: 2,
  emptyTitle: appI18n.global.t('ui.customerListIndexCurrentNoCustomer'),
  customStyle: { border: "none", lineHeight: "20px", background: "#ED4014" },
  examineTabData: customerTabList,
  listData: [],
  keyWord: "customer",
  where: {
    limit: 10,
    page: 1,
    types: "customer",
    view_search: 1
  },
});

const typeData = ref([
  { name: "我负责的", id: 1 },
  { name: "我协作的", id: 9 },
  { name: "下属负责的", id: 2 },
  { name: "我关注的", id: 3 },
  { name: "急需跟进", id: 6 },
]);

const tabData = reactive([
  { name: "客户", type: "customer", types: "tab" },
  { name: "公海池", type: "customer_seas", types: "tab" },
]);

import { onLoad } from "@dcloudio/uni-app";
onLoad((options) => {
  if (options.tab) {
    data.tabIndex = Number(options.tab) || 0;
    data.tabId = data.examineTabData[data.tabIndex].id;
    data.where.types = data.tabId === 2 ? "customer_seas" : "customer";
    getTabList();
  } else {
    getTabList();
  }
});

const rightIcon = reactive([
  { type: 1, icon: "icon-sousuo" },
  { type: 2, icon: "icon-a-gengduo2" },
]);
// 创建 ref 引用
const formRef = ref(null);

// 调用子组件方法
const callChildMethod = () => {
  if (formRef.value) {
    formRef.value.getSalesman();
  }
};
import { clickNavigateTo, backToTop } from "@/utils/helper";


const changeTab = (e) => {

  if (data.where.page > 1) {
    backToTop();
  }
  data.keyWord = e.type;
  data.tabId = e.id;
  data.tabIndex = e.index;
  data.where.page = 1;
  data.where.limit = 10;
  data.where.types = e.type;
  if (e.index == 0) {
    data.where.view_search = 1;
  } else if (e.index == 1) {
    data.where.view_search = 2;
  } 

  getTabList(true);
};

const formBoxChange = (e) => {
  data.where = e;
  data.where.page = 1;
  data.where.limit = 10;
  data.where.types = data.keyWord;
  getTabList(true);
};


// 条件判断
const getTabList = (tab = false) => {
  
  callChildMethod();
  const id = data.examineTabData[data.tabIndex].id;
  data.typeIndex = Number(id);
  getConfigList(tab);
};

const listLoading = ref(false);
// 列表加载
const getConfigList = (tab = false) => {
  if (data.where.page === 1) {
    uni.showLoading({
      title: appI18n.global.t('ui.customerContractIndexLoading'),
      mask: true
    });
  }
  customerListApi({
    ...data.where,
    types: data.where.types
  })
    .then((res) => {
      // 切换时数据清空
      data.listData = tab ? res.data.list : [...data.listData, ...res.data.list];
      const allPage = Math.ceil(res.data.count / data.where.limit);
      if (data.listData.length <= 0 || data.where.page >= allPage) {
        listLoading.value = false;
      } else {
        listLoading.value = true;
      }
      uni.hideLoading();
      uni.stopPullDownRefresh(); // 停止刷新
    })
    .catch((error) => {
      message.error(error.message);
      uni.hideLoading();
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
  }
}
</style>