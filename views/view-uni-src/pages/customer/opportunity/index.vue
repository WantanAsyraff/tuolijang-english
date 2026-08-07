<template>
  <BaseContainer class="base-container">
    <view class="head-wrap">
      <NavBar  :is-right="true">
      </NavBar>
     
        <form-box @change="formBoxChange" :typeData="typeData" :placeholder="$t('ui.customerOpportunityIndexOpportunityName')" :keyWord="data.keyWord" type="approve"></form-box>
    </view>
    <view class="opportunity-list-wrap">
      <OpportunityList :list-data="pageData.list" :total="pageData.total" :loading="pageData.loading"
        :loaded="pageData.loaded" :empty-title="$t('ui.customerListOppCurrentNoOpportunity')"></OpportunityList>
    </view>
    <TabBar :currentIndex="2" navigateType="redirectTo" />
  </BaseContainer>
</template>

<script setup lang="ts">import appI18n from '@/locale';

  import BaseContainer from "@/components/BaseContainer/index.vue";
  import TabBar from "@/components/tabbar/index.vue";
    import formBox from "@/pages/customer/list/components/formBox.vue";
  import NavBar from "@/components/defaultNavBar/index.vue";
  import OpportunityList from "./components/opportunity-list.vue";
  import { watch } from "vue";
  import { customerSelectApi, dictSelectApi, opportunityListApi } from "@/api/customer";
  import { getOptionIntParams } from "@/utils/helper";



  const getDefaultWhere = () => ({
    limit: 10,
    page: 1,
    eid: "",
    types: "",
  });

  const pageData = reactive({
    loading: false,
    loaded: false,
    list: [],
    total: 0,
  });
  const data = reactive({
    keyWord: "odds"
  })
  const typeData = ref([
    { name: "我负责的", id: 1 },
        { name: "下属负责的", id: 2 },
        { name: "我关注的", id: 3 },
        { name: "急需跟进", id: 4 },
  ])

  const query = ref<Record<string, any>>(getDefaultWhere());

  const formConfigOptionValue = ref({
    customerList: [],
    oddsType: [],
    eid: -1
  });
  const formBoxChange = (e) => {
     
  	 query.value = e;
     query.value.page = 1;
	 query.value.limit = 10;
 
   refrestData();
  };

  const handleGetOpportunityList = async () => {
    if (pageData.loading || pageData.loaded) return;
    pageData.loading = true;
    try {
      const page = query.value.page;
 uni.showLoading({
	title: appI18n.global.t('ui.customerContractIndexLoading')
});
      const res = await opportunityListApi(
        query.value
      );
      if (page === 1) {
        pageData.list = res.data.list;
      } else {
        pageData.list = [...pageData.list, ...res.data.list];
      }
 uni.hideLoading();
      pageData.total = res.data.count;
      pageData.loaded = pageData.list.length >= pageData.total;
    } catch (error) {
      uni.showToast({
        title: error.message,
        icon: "none",
      });
    } finally {
      uni.hideLoading();
      pageData.loading = false;
    }
  };

  const getCustomerSelect = async () => {
    const res = await customerSelectApi();
    formConfigOptionValue.value.customerList = [
      {
        id: "",
        name: "全部"
      },
      ...res.data.map((item : any) => ({
        id: item.value,
        name: item.text
      }))
    ];
  };

  getCustomerSelect();

  onReachBottom(() => {
    if (pageData.loading || pageData.loaded) return;
    query.value.page++;
  });

  const refrestData = () => {
    query.value.page = 1;
    pageData.loaded = false;
    handleGetOpportunityList();
  };

  watch(query, () => {
    handleGetOpportunityList();
  }, {
    deep: true,
  });

  const getDictData = (types : string) => dictSelectApi({ types }).then((res : any) => res.data);

  onLoad(async (options : Record<string, any>) => {
    const eid = getOptionIntParams(options, "eid");
    if (eid !== null) {
      formConfigOptionValue.value.eid = eid;
      query.value.eid = eid;
    }
    const tabIndexValue = getOptionIntParams(options, "tab_index");
    if (tabIndexValue !== null) {
    }
    const [oddsType] = await Promise.all([
      getDictData("odds_type")
    ]);
    formConfigOptionValue.value.oddsType = [
      {
        id: "",
        name: "全部"
      },
      ...oddsType
    ];
  });

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

  .opportunity-list-wrap {
    padding-bottom: calc(var(--bottom-area-height) + 103rpx + 20rpx);
  }
</style>