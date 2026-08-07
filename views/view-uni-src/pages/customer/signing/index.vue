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

<script setup lang="ts">import appI18n from '@/locale';

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
    emptyTitle: appI18n.global.t('ui.customerSigningIndexCurrentNoContract'),
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
        text: appI18n.global.t('ui.customerSigningIndexOwnedByMe'),
        title: appI18n.global.t('ui.customerSigningIndexOwnedByMe'),
        value: '1',
        key: 'view_search',
        type: 'select',
        options: [
            {
                text: appI18n.global.t('ui.customerSigningIndexOwnedByMe'),
                value: '1'
            }, {
                text: appI18n.global.t('ui.customerSigningIndexRecordsICanView'),
                value: '2'
            }
        ]
    }, {
        text: appI18n.global.t('ui.customerSigningListExpirationStatus'),
        title: appI18n.global.t('ui.customerSigningListExpirationStatus'),
        value: '',
        key: 'fail_status',
        type: 'select',
        options: [
            {
                text: appI18n.global.t('ui.attendanceDetailedUserCheckListAll'),
                value: ''
            }, {
                text: appI18n.global.t('ui.customerSigningListNotStarted'),
                value: '1'
            }, {
                text: appI18n.global.t('ui.customerSigningListInProgress'),
                value: '0'
            }, {
                text: appI18n.global.t('ui.customerSigningIndexExpired'),
                value: '2'
            }
        ]
    },
    {
        text: appI18n.global.t('ui.customerSigningIndexSigningStatus'),
        title: appI18n.global.t('ui.customerSigningIndexSigningStatus'),
        value: '',
        key: 'status',
        type: 'select',
        options: [
            {
                text: appI18n.global.t('ui.attendanceDetailedUserCheckListAll'),
                value: ''
            }, {
                text: appI18n.global.t('ui.customerSigningIndexApprovalRejected'),
                value: '-1'
            }, {
                text: appI18n.global.t('ui.customerSigningIndexPending'),
                value: '0'
            }, {
                text: appI18n.global.t('ui.customerContractPayDetailPendingReview'),
                value: '1'
            }, {
                text: appI18n.global.t('ui.customerSigningDetailItemPendingSigning'),
                value: '2'
            }, {
                text: appI18n.global.t('ui.customerSigningDetailItemSigned'),
                value: '3'
            }, {
                text: appI18n.global.t('ui.customerInvoiceCheckPaymentRejected'),
                value: '4'
            }, {
                text: appI18n.global.t('ui.customerSigningListExpired'),
                value: '5'
            }, {
                text: appI18n.global.t('ui.customerContractPayDetailRevoked'),
                value: '6'
            }
        ]
    },{
        text: appI18n.global.t('ui.customerSigningIndexSigningTime'),
        title: appI18n.global.t('ui.customerSigningIndexSigningTime'),
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
	title: appI18n.global.t('ui.customerContractIndexLoading')
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