<template>
  <BaseContainer class="base-container">
    <view class="head-wrap">
      <default-nav-bar :index="data.tabIndex" :default-type="1" :is-right="false" :tab-data="tabData"
        @handleNarItem="changeTab"></default-nav-bar>
      <form-box :keyWord="data.keyWord" :placeholder="$t('ui.customerLeadIndexLeadName')" :typeData="data.keyWord === 'clue' ? typeData : []"
        @change="formBoxChange" type="approve"></form-box>
    </view>
    <view class="lead-list-wrap">
      <LeadList :list-data="data.list" :total="data.total" :keyWord="data.keyWord" :loading="data.loading"
        :loaded="data.loaded" empty-title="当前暂无线索～"></LeadList>
    </view>
    <TabBar :currentIndex="0" navigateType="redirectTo" />
  </BaseContainer>
</template>

<script setup lang="ts">
import BaseContainer from "@/components/BaseContainer/index.vue";
import TabBar from "@/components/tabbar/index.vue";
import defaultNavBar from "@/components/defaultNavBar/index.vue";
import formBox from "@/pages/customer/list/components/formBox.vue";
import { leadListApi } from "@/api/customer";
import LeadList from "./components/lead-list.vue";
import { backToTop, clickNavigateTo } from "@/utils/helper";
const tabData = reactive([
  { name: "线索", type: "clue", types: "tab" },
  { name: "线索池", type: "clue_seas", types: "tab" },
]);


const data = reactive({
  loading: false,
  loaded: false,
  tabIndex: 0,
  keyWord: "clue",
  list: [],
  where: {
    page: 1,
    limit: 10,
    view_search: 1,
    types: 'clue',
  },
  total: 0,
});




const typeData = ref([
  { name: "我负责的", id: 1 },
  { name: "下属负责的", id: 2 },
  { name: "我关注的", id: 3 },
  { name: "急需跟进", id: 6 },
]);

const changeTab = (e) => {
  if (data.where.page > 1) {
    backToTop();
  }
  data.keyWord = e.type;
  data.where.page = 1;
  data.where.limit = 10;
  if (e.type === "clue") {
    data.where.view_search = 1;
  } else if (e.type === "clue_seas") {
    data.where.view_search = '';
  }
  data.loaded = false;
  handleGetLeadList();
};

const formBoxChange = (e) => {
  data.where = e;
  data.where.page = 1;
  data.where.limit = 10;
  data.where.types = data.keyWord;
  refrestData();
};

const handleGetLeadList = async () => {
  if (data.loading || data.loaded) return;
  data.loading = true;
 

  try {
    data.where.types = data.keyWord
 uni.showLoading({
	title: '加载中'
});
    const res = await leadListApi(data.where);
    if (data.where.page === 1) {
      data.list = res.data.list;
    } else {
      data.list = [...data.list, ...res.data.list];
    }
 uni.hideLoading();
    data.total = res.data.count;
    data.loaded = data.list.length >= data.total;
  } catch (error) {
    uni.showToast({
      title: error.message,
      icon: "none",
    });
  } finally {
    uni.hideLoading();
    data.loading = false;
  }
};

onReachBottom(() => {
  if (data.loading || data.loaded) return;
  data.where.page++;
  handleGetLeadList();
});

const refrestData = () => {
  data.where.page = 1;
  data.loaded = false;
  handleGetLeadList();
};






onShow(() => {
  refrestData();
});
</script>

<style scoped lang="scss">
.head-wrap {
  padding-top: var(--status-bar-height);
  background-color: #fff;
  position: sticky;
  z-index: 1;
  top: 0;
}

.lead-list-wrap {
  // padding: 20rpx;
  padding-bottom: calc(var(--bottom-area-height) + 103rpx + 20rpx);
}
</style>