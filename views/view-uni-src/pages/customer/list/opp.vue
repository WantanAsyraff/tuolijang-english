<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :is-right="true" :jump-url="data.jumpUrl" :is-jump-bar="false" :right-data="rightIcon"
        @handleNarItem="handleNarItem">
      </default-nav-bar>
    </view>
    <view class="examine-content m10">
      <OpportunityList :list-data="data.list" :total="data.total" :loading="data.loading" :loaded="data.loaded"
        :empty-title="$t('ui.customerListOppCurrentNoOpportunity')"></OpportunityList>
    </view>
    <global-index />
  </view>
</template>

<script setup>
  import OpportunityList from "@/pages/customer/opportunity/components/opportunity-list.vue";
  import defaultNavBar from "@/components/defaultNavBar/index.vue";
  import globalIndex from "@/components/globalIndex/index.vue";
  import { opportunityListApi } from "@/api/customer";
  import { ref, reactive } from "vue";
  import message from "@/utils/message";
  const data = reactive({
    tabIndex: 0,
    list: [],
    loading: false,
    loaded: false,
    total: 0,
    where: {
      limit: 10,
      page: 1,
      eid: "",
    },
    jumpUrl: ""
  });

  import { onLoad } from "@dcloudio/uni-app";
  onLoad((e) => {
    data.where.eid = e.eid;
    data.jumpUrl = `/pages/customer/list/details?id=${data.where.eid}`;
    if (e.types == 3) {
      rightIcon = rightIcon.splice(1, 1);
    }
    handleGetOpportunityList();
  });

  onShow(() => {
    setTimeout(() => {
      freshData();
    }, 100);

  });

  function freshData() {
    data.loaded = false
    handleGetOpportunityList();
  }

  const rightIcon = reactive([
    { type: 1, icon: "icon-sousuo" },
    { type: 2, icon: "icon-a-gengduo2" },
  ]);

  import { clickNavigateTo } from "@/utils/helper";
  import { useStore } from "vuex";
  const store = useStore();
  const handleNarItem = (e) => {
    if (e.type === 1) {
      clickNavigateTo(`/pages/customer/opportunity/search?eid=${data.where.eid}`);
    }
    if (e.type === 2) {
      store.commit("setCustomerFormType", { type: "list-contract" });
      clickNavigateTo(`/pages/customer/opportunity/add?eid=${data.where.eid}`);
    }
  };

  const listLoading = ref(false);
  const handleGetOpportunityList = async () => {
    if (data.loading || data.loaded) return;
    data.loading = true;
    try {

      const res = await opportunityListApi(data.where);
      if (data.where.page === 1) {
        data.list = res.data.list;
      } else {
        data.list = [...data.list, ...res.data.list];
      }
      data.total = res.data.count;
      data.loaded = data.list.length >= data.total;
    } catch (error) {
      uni.showToast({
        title: error.message,
        icon: "none",
      });
    } finally {
      data.loading = false;
    }
  };
  import { onReachBottom, onPullDownRefresh } from "@dcloudio/uni-app";
  // 下拉加载
  onReachBottom(() => {
    if (listLoading.value) {
      data.where.page++;
      handleGetOpportunityList();
    }
  });
  // 上拉加载
  onPullDownRefresh(() => {
    data.where.page = 1;
    data.value = false;
    data.listData = [];
    handleGetOpportunityList();
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