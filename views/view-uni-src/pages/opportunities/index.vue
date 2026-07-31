<template>
  <view class="container" v-if="isLogin">
    <!-- 搜索框 -->
    <view class="search-bar">
      <uni-search-bar
        style="width: 100%"
        @confirm="searchData"
        @focus="searchData"
        :placeholder="$t('ui.opportunitiesIndexSearchOpportunities')"
        :focus="true"
        bgColor="#F0F1F5"
        v-model="where.customer_name"
        @clear="searchData"
      >
      </uni-search-bar>
      <FormBox :config="formBoxConfig" @change="handleFormBoxChange"></FormBox>
    </view>

    <!-- 客户列表 -->
    <scroll-view class="scroll-container" style="height: 100vh" :scroll-y="true" :show-scrollbar="false" @scrolltolower="scrolltolower">
      <uni-list :border="false" v-if="filteredCustomers.length > 0" class="customer-list">
        <uni-list-item v-for="(item, index) in filteredCustomers" :key="'list' + item.id">
          <template v-slot:body>
            <view class="item-list" @click="examineList(item)">
              <view class="p24">
                <view class="item-list-top">
                  {{ item.name }}

                  <text
                    class="status-tag"
                    :style="{
                      color: item.status ? item.status.color : '#1890ff',
                      background: item.status.color ? getColor(item.status.color, '0.1') : getColor('#1890ff', '0.1'),
                    }"
                    v-if="item.status"
                    >{{ $ts(item.status.name) }}</text
                  >
                </view>

                <uni-row class="item-list-content">
                  <uni-col :span="6" class="left">{{ $t('ui.customerLeadLeadListWeComCustomer') }}</uni-col>
                  <uni-col :span="18" class="lh-center">
                    <image class="avatar" :src="item.work_customer.avatar" mode="widthFix"></image>
                    <text class="customer-name">{{ item.work_customer.name }} </text>
                  </uni-col>
                </uni-row>
                <uni-row class="item-list-content">
                  <uni-col :span="6" class="left">{{ $t('ui.customerContractPayDetailCustomerName') }}</uni-col>
                  <uni-col :span="18">{{ item.eid || '--' }}</uni-col>
                </uni-row>
                <uni-row class="item-list-content">
                  <uni-col :span="6" class="left">{{ $t('ui.opportunitiesIndexSalesAmount') }}</uni-col>
                  <uni-col :span="18"> {{ item.total_amount }}{{ $t('ui.customerContractPayDetailYuan') }} </uni-col>
                </uni-row>
                <uni-row class="item-list-content">
                  <uni-col :span="6" class="left">{{ $t('ui.customerContractPayDetailSalesperson') }}</uni-col>
                  <uni-col :span="18" class="time-box">
                    <text> {{ item.salesman.name || '--' }}</text>
                  </uni-col>
                </uni-row>
                <uni-row class="item-list-content">
                  <uni-col :span="6" class="left">{{ $t('ui.customerListBusinessFollowFollowUpTime') }}</uni-col>
                  <uni-col :span="18">
                    <text> {{ item.last_follow_time || '--' }}</text>
                    <text v-if="item.work_customer" class="qiweiBox" @click.stop="openCustomerChat(item.work_customer)"> {{ $t('ui.customerOpportunityOpportunityListStartChat') }} </text>
                  </uni-col>
                </uni-row>
              </view>
            </view>
          </template>
        </uni-list-item>
      </uni-list>

      <empty v-else :index="7" :title="$t('ui.customerListStatisticsNoData')"></empty>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import empty from '@/components/empty/index.vue'
import FormBox, { FormItemType, FormBoxConfig } from '@/components/BaseFilterFormBox/index.vue'
import moment from 'moment'
import { opportunityListApi, dictSelectApi } from '@/api/customer'
import { WxWork, isWxWorkEnv } from '@/libs/wxwork'
import { toLogin } from '@/libs/login'
import { ref, reactive, computed } from 'vue'
import message from '@/utils/message'
import { useStore } from 'vuex'
// 状态管理
const count = ref(0)
const loading = ref(true)
const filteredCustomers = ref([])
const oddStatus = ref([])
const store = useStore()
const isLogin = computed(() => store.state.app.isLogin)
const where = reactive({
  page: 1,
  limit: 10,
  is_work: 1,
  view_search: 1,
  created_at: '',
  customer_name: '',
  status: '',
})

/**
 * 拉取商机页筛选项字典数据。
 */
const getDictData = (types: string) => dictSelectApi({ types }).then((res: any) => res.data)

/**
 * 页面初始化时先做登录保护，避免企微静默登录尚未完成就提前发业务请求。
 */
onLoad(async (e) => {
  if (!isLogin.value) {
    if (!isWxWorkEnv) {
      toLogin()
    }
    return
  }

  const status = await Promise.all([getDictData('odds_status')])
  oddStatus.value = status[0]
  loadCustomerData()
})

/**
 * 重置列表并按当前筛选条件重新拉取商机数据。
 */
const searchData = () => {
  filteredCustomers.value = []
  loadCustomerData()
}

/**
 * 根据筛选表单配置动态生成顶部过滤项。
 */
const formBoxConfig = computed<FormBoxConfig[]>(() => {
  return [
    {
      label: '创建时间',
      key: 'created_at',
      type: 'daterange',
    },
    {
      label: '状态筛选',
      key: 'status',
      type: FormItemType.PICKER,
      range: [
        {
          name: '全部',
          id: '',
        },
        ...oddStatus.value,
      ],
      rangeKey: 'name',
    },
  ]
})

/**
 * 同步筛选表单值并触发列表刷新。
 */
const handleFormBoxChange = (e: FormBoxConfig, value: any) => {
  if (e.key === 'status') {
    where[e.key] = value.id
  } else {
    where[e.key] = value
  }
  searchData()
}

/**
 * 跳转到商机详情页。
 */
const examineList = (item) => {
  clickNavigateTo(`/pages/customer/opportunity/detail?id=${item.id}`)
}

/**
 * 按当前分页和筛选条件加载商机列表数据。
 */
const loadCustomerData = () => {
  opportunityListApi(where).then((res) => {
    count.value = res.data.count
    if (filteredCustomers.value.length !== count.value) {
      filteredCustomers.value.push(...res.data.list)
    }
    loading.value = false
  })
}
import { onReachBottom } from '@dcloudio/uni-app'

/**
 * 处理列表触底分页加载。
 */
const scrolltolower = () => {
  if (filteredCustomers.value.length >= count.value) {
    loading.value = false
  } else {
    loading.value = true
  }
  if (loading.value) {
    where.page++
    loadCustomerData()
  }
}
import { clickNavigateTo, getColor } from '@/utils/helper'

/**
 * 从列表项跳转到商机详情页。
 */
const openCustomer = (item) => {
  clickNavigateTo(`/pages/customer/opportunity/detail?id=${item.id}`)
}

/**
 * 在企业微信环境中打开与当前客户的会话窗口。
 */
const openCustomerChat = async (item) => {
  if (!isWxWorkEnv) return message.error('只有在企业微信中可进行聊天')
  try {
    const wxWork = await WxWork.getInstance()
    await new Promise((resolve, reject) => {
      wxWork.ww.openEnterpriseChat({
        userIds: '', // 外部联系人
        externalUserIds: [item.external_userid],
        groupName: '',
        chatId: '',
        success: resolve,
        fail: reject,
      })
    })
    message.success('打开会话框')
  } catch (err) {
    message.error(`打开个人资料页失败: ${err.errMsg || err.message || '操作失败'}`)
  }
}
</script>

<style scoped lang="scss">
.container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #f5f7fa;
}

.lh-center {
  display: flex;
  align-items: center;
}

.time-box {
  display: flex;
  justify-content: space-between;
}

/* 搜索框 */
.search-bar {
  width: 100%;
  position: fixed;
  padding-top: 250px;
  padding: 0rpx 24rpx;
  background-color: #ffffff;
  border-bottom: 1px solid #eeeeee;
  z-index: 100;
}

.qiweiBox {
  width: 112rpx;
  height: 46rpx;
  border-radius: 8rpx 8rpx 8rpx 8rpx;
  border: 1rpx solid #1890ff;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 22rpx !important;
  color: #1890ff;
  text-align: center;
  line-height: 46rpx;
  position: absolute;
  bottom: 0rpx;
  right: 0rpx;
}

::v-deep .uni-searchbar__cancel {
  display: none;
}

.search-icon {
  margin-right: 16rpx;
}

.search-input {
  flex: 1;
  padding: 0 30rpx;
  height: 68rpx;
  background: #f6f6f7;
  border-radius: 8rpx;
  font-weight: 400;
  font-size: 26rpx;
  color: #909399;
}

.search-input::placeholder {
  color: #86909c;
}

/* 客户列表 */
.customer-list {
  flex: 1;
  margin-bottom: 103rpx;
  margin-top: 90px;
}

/* 空状态 */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* 客户列表项 */
.customer-item {
  cursor: pointer;
  display: flex;

  background-color: #ffffff;
  transition: background-color 0.2s;
}

/* 头像 */
.avatar-container {
  cursor: pointer;
  position: relative;
  margin-right: 10px;
}

.avatar {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  margin-right: 8rpx;
}

.last-contact {
  display: block;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 24rpx;
  color: #606266;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

::v-deep .uni-list {
  background-color: $uni-default-bg;

  .uni-list--border {
    top: auto;
    left: auto;
  }

  .uni-list-item {
    margin-top: 8rpx;

    .uni-list-item__container {
      padding: 30rpx;
    }
  }
}

.item-list {
  width: 100%;
  position: relative;

  .item-list-top {
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 500;
    font-size: 28rpx;
    color: #303133;
    padding-bottom: 20rpx;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .status-tag {
    margin-left: 16rpx;
    padding: 0 10rpx;
    height: 42rpx;
    background: rgba(24, 144, 255, 0.1);
    border-radius: 8rpx;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24rpx;
    color: #1890ff;
    font-weight: 400;
  }

  .item-list-content {
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 24rpx;
    color: #303133;
    margin-bottom: 12rpx;
    display: flex;
    align-items: flex-end;
    position: relative;

    &:last-of-type {
      margin-bottom: 0;
    }

    .left {
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      font-size: 24rpx;
      color: #606266;
    }
  }
}
</style>
