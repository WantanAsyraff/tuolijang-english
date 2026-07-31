<template>
    <BaseContainer>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :is-right="true"  @handleNarItem="handleNarItem"></default-nav-bar>
       <form-box @change="formBoxChange"  :typeData="typeData" :placeholder="$t('ui.customerContractIndexOrderName')" :keyWord="data.keyWord" type="approve"></form-box>
    </view>

    <view class="examine-content ">
      <contract-list :list-data="data.listData" :type="1" :follow="data.followIndex" :form-type="{ list: 'contract' }"
        :empty-title="data.emptyTitle">
      </contract-list>
    </view>
    <global-index />
     <tabbar :currentIndex="5" navigateType="redirectTo"/>
  </view>
  </BaseContainer>
</template>

<script setup>
import BaseContainer from "@/components/BaseContainer/index.vue";
  import defaultNavBar from "@/components/defaultNavBar/index.vue";
  import contractList from "./components/contractList.vue";
  import tabbar from "@/components/tabbar/index.vue";
  import customerTab from "@/pages/customer/list/components/customerTab.vue";
  import globalIndex from "@/components/globalIndex/index.vue";
  import formBox from "@/pages/customer/list/components/formBox.vue";
  import { ref, reactive } from "vue";
  import message from "@/utils/message";
  import { contractTabData } from "@/utils/assessment";

  const data = reactive({
    typeIndex: 0,
    tabIndex: 0,
    tabId: 1,
    keyWord:'contract',
    followIndex: 0,
    emptyTitle: "当前暂无订单～",
    customStyle: { border: "none", lineHeight: "20px", background: "#ED4014" },
    examineTabData: contractTabData,
    listData: [],
    where: {
      page: 1,
      limit: 10,
      types: 6,
      scope_frame: "self",
      pay_status: "",
      name: "",
      renew: "",
      follows: "",
      sort: "",
      abnormal: "",
      time: "",
      salesman_id: "",
      view_search: 1,
    },
  });

  import { clientContractListApi } from "@/api/customer";
  import { onLoad } from "@dcloudio/uni-app";
  onLoad(() => {
    getTabList();
  });


  
 const typeData = ref([
    { name: "我负责的", id: 1 },
    { name: "下属负责的", id: 2 },
    { name: "我关注的", id: 3 },
		 {
          id: 4,
          name: '已签约'
        },
        {
          id: 5,
          name: '未签约'
        },
        {
          id: 6,
          name: '签约作废'
        },
        {
          id: 7,
          name: '过期订单'
        },
        {
          id: 8,
          name: '急需续费'
        },
        {
          id: 9,
          name: '费用过期'
        }
  ]);


  import { clickNavigateTo, backToTop } from "@/utils/helper";
  const handleNarItem = (e) => {
    if (e.type === 1) {
      clickNavigateTo(`/pages/customer/contract/search?index=${data.tabIndex}&type=${data.where.types}`);
    } else {
      clickNavigateTo("/pages/customer/contract/addContract");
    }
  };

  const changeTab = (e) => {
    if (data.where.page > 1) {
      backToTop();
    }
    data.where.types = e.id;
    if (e.id == 5) {
      data.where.scope_frame = "all";
      data.where.view_search = 2
    } else {
      data.where.scope_frame = "self";
      data.where.view_search = 1
    }

    data.tabIndex = e.index;
    data.followIndex = e.index;
    data.where.page = 1;
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
    data.where.page = 1;
    getConfigList(tab);
  };

  const listLoading = ref(false);
  // 列表加载
  const getConfigList = (tab = false) => {
    uni.showLoading({
      title: '加载中',
    })
    clientContractListApi(data.where)
      .then((res) => {
        // 切换时数据清空
        if (tab) data.listData = [];
        uni.hideLoading();
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
        uni.hideLoading();
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
    }
  }
</style>