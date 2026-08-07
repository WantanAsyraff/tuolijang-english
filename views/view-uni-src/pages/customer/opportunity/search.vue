<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <view class="default-search">
        <uni-search-bar @confirm="search" @focus="search" :placeholder="data.placeholder" :focus="true"
          bgColor="#F0F1F5" v-model="data.where.name" @clear="clearSearch" @cancel="cancelSearch">
        </uni-search-bar>
      </view>
      <customer-tab v-if="!data.eid" :examine-tab-data="data.examineTabData" @change="changeTab"
        :index="data.tabIndex"></customer-tab>
    </view>

    <view class="examine-content m10">
      <OpportunityList :list-data="data.listData" :type-index="data.typeIndex" :empty-title="data.emptyTitle">
      </OpportunityList>
    </view>
  </view>
</template>

<script setup>import appI18n from '@/locale';

  import { ref, reactive } from "vue";
  import message from "@/utils/message";
  import OpportunityList from "./components/opportunity-list.vue";
  import customerTab from "../list/components/customerTab.vue";
  import { opportunityExamineTabConfig } from "@/utils/assessment";
  import { backToTop } from "@/utils/helper";
  import { opportunityListApi } from "@/api/customer";
  import { onLoad } from "@dcloudio/uni-app";
  import { onReachBottom } from "@dcloudio/uni-app";
  const data = reactive({
    typeIndex: 0,
    tabIndex: 0,
    placeholder: appI18n.global.t('ui.customerOpportunitySearchSearchOpportunityName'),
    examineTabData: opportunityExamineTabConfig,
    listData: [],
    emptyTitle: appI18n.global.t('ui.customerAddressSearchIndexNoSearchResults'),
    eid: '',
    where: {
      limit: 10,
      page: 1,
      name: ""
    }
  });

  onLoad((options) => {
    const len = data.examineTabData.length - 1;
    if (options.index) {
      if (options.index > len) {
        data.tabIndex = len;
      } else {
        data.tabIndex = Number(options.index);
      }
    }
    if (options.eid) {
      data.eid = options.eid
    }
  });

  const clearSearch = () => {
    data.where.name = "";
    data.listData = [];
  };

  const cancelSearch = () => {
    uni.navigateBack();
  };

  const search = () => {
    data.where.page = 1;
    if (data.where.name) {
      getTabList(true);
    } else {
      clearSearch();
    }
  };

  const changeTab = (e) => {
    if (data.where.page > 1) {
      backToTop();
    }
    data.tabIndex = e.index;
    data.where.page = 1;
    if (data.where.name) {
      getTabList(true);
    } else {
      clearSearch();
    }
  };

  // 条件判断
  const getTabList = (tab = false) => {
    const id = data.examineTabData[data.tabIndex].id;
    data.typeIndex = Number(id);

    getConfigList(tab);
  };

  const listLoading = ref(false);
  // 列表加载
  const getConfigList = (tab = false) => {
    // if (data.eid) {
    //   data.where.eid = data.eid
    // }

    opportunityListApi({
      ...data.where,
      view_search: data.typeIndex
    }).then((res) => {
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
    }).catch((error) => {
      message.error(error.message);
    });
  };

  // 下拉加载
  onReachBottom(() => {
    if (listLoading.value) {
      data.where.page++;
      getConfigList();
    }
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

    .examine-content {}
  }
</style>