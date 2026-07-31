<template>
    <BaseContainer>
        <view class="content">
            <view class="cr-position-header">
                <view class="status_bar"></view>
                <default-nav-bar  :index="1" :default-type="1" :is-right="true"
                    :tab-data="tabData" @handleNarItem="handleNarItem"></default-nav-bar>
                <!-- 筛选 -->
                <formBox :search="searchList" @change="changeSearch"></formBox>
            </view>
            <view class="examine-content">
                <list :list-data="data.listData" :type-index="data.typeIndex" :types="data.where.types"
                    :empty-title="data.emptyTitle"></list>
            </view>
            <global-index />
            <tabbar :currentIndex="4" navigateType="redirectTo" />
        </view>
    </BaseContainer>
</template>

<script setup lang="ts">
import BaseContainer from "@/components/BaseContainer/index.vue";
import tabbar from "@/components/tabbar/index.vue";
import defaultNavBar from "@/components/defaultNavBar/index.vue";
import list from "./components/list.vue";
import globalIndex from "@/components/globalIndex/index.vue";
import formBox from "./components/formBox.vue";
import { ref, reactive } from "vue";
import message from "@/utils/message";
import { getContractDocApi } from "@/api/signing";
const data = reactive({
    emptyTitle: "当前暂无合同～",
    listData: [],
    keyWord: 'customer',
    where: {
        limit: 10,
        page: 1,
        view_search: '1',
    },
});

const tabData = reactive([
    { name: "合同合约", type: "customer", types: "tab" },
]);

import { onLoad } from "@dcloudio/uni-app";



onLoad(() => {
    getConfigList();
});

const searchList = ref([
    {
        text: '我负责的',
        title: '我负责的',
        value: '1',
        key: 'view_search',
        type: 'select',
        options: [
            {
                text: '我负责的',
                value: '1'
            }, {
                text: '我查看的',
                value: '2'
            }
        ]
    }, {
        text: '到期状态',
        title: '到期状态',
        value: '',
        key: 'fail_status',
        type: 'select',
        options: [
            {
                text: '全部',
                value: ''
            }, {
                text: '未开始',
                value: '1'
            }, {
                text: '进行中',
                value: '0'
            }, {
                text: '已到期',
                value: '2'
            }
        ]
    },
    {
        text: '签约状态',
        title: '签约状态',
        value: '',
        key: 'status',
        type: 'select',
        options: [
            {
                text: '全部',
                value: ''
            }, {
                text: '审批驳回',
                value: '-1'
            }, {
                text: '待处理',
                value: '0'
            }, {
                text: '待审核',
                value: '1'
            }, {
                text: '待签约',
                value: '2'
            }, {
                text: '已签约',
                value: '3'
            }, {
                text: '已拒绝',
                value: '4'
            }, {
                text: '已过期',
                value: '5'
            }, {
                text: '已撤销',
                value: '6'
            }
        ]
    },{
        text: '签约时间',
        title: '签约时间',
        value: '',
        key: 'time',
        type: 'time',
       
    },
])

const listLoading = ref(false);
// 筛选条件
const changeSearch = (dataValue) => {
   data.where = dataValue
     data.where.page = 1;
     data.where.limit = 10;
   listLoading.value = false;
    getConfigList();
}
// 列表加载
const getConfigList = (tab = false) => {
  if (data.loaded) return;
  uni.showLoading({
	title: '加载中'
});

  getContractDocApi(data.where)
    .then(res => {
      // 拼接/重置列表
      uni.hideLoading();
      data.listData = data.where.page === 1 ? res.data.list : [...data.listData, ...res.data.list];
      // 是否还有更多
      const allPage = Math.ceil(res.data.count / data.where.limit);
      listLoading.value = !(data.listData.length === 0 || data.where.page >= allPage);
      uni.stopPullDownRefresh();
    })
    .catch(err => message.error(err.message));
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