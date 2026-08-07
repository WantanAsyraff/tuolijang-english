<template>
  <!-- <BaseContainer> -->
  <view class="content">
    <view class="sticky-navbar">
      <NavBar :is-right="true" :right-data="rightIcon" @handleNarItem="handleNavIconClick" />
    </view>
    <view class="cr-position-header">
      <view id="customer-header-body">
        <view class="detail-banner" v-if="data.detailData">
          <view class="banner-content">
            <view class="customer-name">
              {{ data.detailData.data.name }}
              <text class="iconfont icon-shequ-shoucang-yishoucang" @click="clickFollow(0)" v-if="data.detailData.data.followed == 1"></text>
              <text class="iconfont icon-shequ-shoucang1" @click="clickFollow(1)" v-if="data.detailData.data.followed != 1"></text>
            </view>
            <view
              class="status-tag"
              v-if="data.detailData.data.status"
              :style="{
                color: data.detailData.data.status.color ? data.detailData.data.status.color : '#1890ff',
                background: data.detailData.data.status.color ? getColor(data.detailData.data.status.color, '0.1') : getColor('#1890ff', '0.1'),
              }"
              >{{ $ts(data.detailData.data.status.name || '--') }}
            </view>
          </view>

          <view class="customer-info"> {{ $t('ui.customerContractDetailsCustomerName') }}{{ data.detailData.data.customer_name || '--' }} </view>
          <view class="customer-info"> {{ $t('ui.customerOpportunityDetailOpportunityNo') }}{{ data.detailData.data.odds_no || '--' }} </view>
          <view class="customer-info">
            {{ $t('ui.customerOpportunityDetailOpportunityQuote') }}<text class="default-warning">{{ Number(data.detailData.data.price).toFixed(2) }}{{ $t('ui.customerContractPayDetailYuan') }}</text>
            <text class="divider"></text>
            {{ $t('ui.customerOpportunityDetailFollowUpTime') }}
            {{ data.detailData.data.last_follow_time ? moment(data.detailData.data.last_follow_time).format('YYYY/MM/DD HH:mm:ss') : '--' }}
          </view>
        </view>
      </view>
    </view>

    <!-- 导航栏 -->
    <view class="customer-nav">
      <view class="customer-content">
        <view class="customer-tab">
          <scroll-view scroll-x="true" style="white-space: nowrap; display: flex; height: 60rpx" scroll-with-animation show-scrollbar="true">
            <view
              class="customer-tab-item"
              v-for="(item, index) in data.tabData"
              :class="item.id === data.currentId ? 'active' : ''"
              :key="item.id"
              @click="handleChangeTab(item, index)"
            >
              {{ item.name }}
            </view>
            <view class="underlineBox">
              <view class="underline bg-color-white"></view>
            </view>
          </scroll-view>
        </view>
      </view>
    </view>

    <!-- 跟进记录 -->
    <view class="follow-record" v-if="data.currentId === 3" :class="data.momentList.length > 2 ? 'pb60' : ''">
      <view class="m15">
        <view class="genjinBox">
          <uni-easyinput
            :inputBorder="false"
            v-model="formData.content"
            type="textarea"
            :clearable="false"
            :maxlength="256"
            :autoHeight="true"
            :placeholder="$t('ui.customerLeadDetailWriteFollowUpRecords')"
            class="mb10"
          >
          </uni-easyinput>
          <view class="btn-box">
            <view class="addfujian" @click="uploadAvatar"> <text class="iconfont icon-fujian"></text> {{ $t('ui.replyComponentIndexAddAttachment') }}</view>
            <view class="btn" @click="handleConfirm">{{ $t('ui.baTreePickerIndexOk') }}</view>
          </view>
        </view>
        <!-- 文件 -->
        <view class="flie" v-if="data.imgs.length > 0">
          <view class="box" v-for="(item, indexs) in data.imgs" :key="indexs" @click="preview(item)">
            <view class="left">
              <image class="slot-image" :src="item.src"> </image>
              <view style="width: calc(100% - 40px)">
                <view class="name">
                  {{ item.name }}
                </view>
                <view class="size"> {{ formatBytes(item.size) || '--' }} </view>
              </view>
              <view class="iconfont icon-guanbi-yangshiyi1" @click.stop="deleteFile(item.id)"> </view>
            </view>
          </view>
        </view>
      </view>
      <follow-record :followList="data.momentList" :showTitle="false" :title="$t('ui.customerContractDetailsActivityRecords')" />
    </view>

    <!-- 商机详情 -->
    <view class="basic-info">
      <!-- 基本信息 -->
      <customerInfo
        class="bagf"
        v-if="data.currentId === 1"
        :id="data.id"
        :type="`odds`"
        :customerData="data.detailData.form"
        @refreshDetails="refreshDetails"
      />
      <!-- 合同信息 -->
      <view v-if="data.currentId === 5" class="bgf pb60">
        <detailItem v-if="detail.length > 0 || detail.id" :detail="detail" @getDetails="getSigningInfo" />
        <view v-else class="bgf">
          <empty :index="7" :title="$t('ui.customerContractContractListNoContractData')" class="bgf" style="height: calc(100vh - 300rpx)"></empty>
        </view>
      </view>
      <!-- 产品清单 -->
      <ProductList v-if="data.currentId === 2" :list-data="data.detailData.product" :moreShow="true" />
      <!-- 订单 -->
      <view v-if="data.currentId === 4" :class="data.contractList.length > 2 ? 'pb60' : ''">
        <contract-list class="bagf" :list-data="data.contractList" :count="data.count" :type-index="0" />
        <view v-if="data.contractList.length > 0 && data.count <= data.contractList.length" class="footer-text">{{ $t('ui.customerListFollowRecordNoMore') }} </view>
      </view>
    </view>

    <!-- 底部按钮 -->
    <view class="details-fixed-btn">
      <view @click="AddContract" class="box"><text class="iconfont icon-guanliandingdan"></text>{{ $t('ui.customerOpportunityDetailGenerateOrder') }}</view>
      <view class="btn-line" />
      <template v-if="isWxWorkEnv">
        <view @click="openCustomerChat" class="box"><text class="iconfont icon-quliaotian" />{{ $t('ui.customerOpportunityDetailOpenChat') }}</view>
        <view class="btn-line" />
      </template>
      <view @click="AddSigning" class="box"><text class="iconfont icon-tianjiahetong1"></text>{{ $t('ui.customerOpportunityDetailGenerateContract') }}</view>
    </view>

    <!-- 操作弹窗 -->
    <more-popup ref="customerMoreRef" @handleItem="dropDownItem"></more-popup>
    <!-- 修改状态 -->
    <bottom-action-sheet ref="sheetRef" @select="handleChangeStatus" />
    <!-- </BaseContainer> -->
  </view>
</template>

<script setup lang="ts">import appI18n from '@/locale';

import { reactive, ref } from 'vue'
import empty from '@/components/empty/index.vue'
import detailItem from '@/pages/customer/signing/components/detailItem.vue'
import { debounce, getColor, clickNavigateTo, delayedNavigateBack } from '@/utils/helper'
import { uploadImage, formatBytes } from '@/utils/file'
import BottomActionSheet from '@/components/BottomActionSheet/index.vue'
import NavBar from '@/components/defaultNavBar/index.vue'
import customerInfo from '@/pages/customer/list/components/customerInfo.vue'
import ProductList from './components/product-list.vue'
import morePopup from '@/components/morePopup/index.vue'
import {
  opportunityEditFormApi,
  clientContractListApi,
  opportunityFollowApi,
  opportunityShiftApi,
  momentRecordApi,
  opportunityEditApi,
  followSaveApi,
  opportunityDelApi,
} from '@/api/customer'
import { getContractDocEditApi } from '@/api/signing'
import message from '@/utils/message'
import followRecord from '@/pages/customer/list/components/followRecord.vue'
import contractList from '@/pages/customer/contract/components/contractList.vue'
import moment from 'moment'
import { WxWork, isWxWorkEnv } from '@/libs/wxwork'

const rightIcon = [
  {
    type: 1,
    icon: 'icon-gengduo1',
    types: 'icon',
  },
]
const detail = ref(null)

const formData = ref({
  content: '',
  attach_ids: [],
  types: 0,
  eid: 0,
  link_type: 'odds',
})

const data = reactive({
  id: '', // 商机id
  eid: 0, // 客户id
  imgs: [],
  tabData: [
    {
      name: '动态记录',
      id: 3,
    },
    {
      name: '基本信息',
      id: 1,
    },
    {
      name: '合同信息',
      id: 5,
    },
    {
      name: '产品清单',
      id: 2,
    },

    {
      name: '订单',
      id: 4,
    },
  ],
  scrollTop: 0,
  currentId: 3,
  detailData: null,

  contractList: [],
  count: 0,
  contractLoaded: false,
  momentList: [],
  momentLoaded: false,
})

const contractWhere = ref({
  page: 1,
  limit: 10,
})

const momentWhere = ref({
  page: 1,
  limit: 10,
})

const handleShift = async (users: any) => {
  if (!users.length) return
  const res = await uni.showModal({
    title: appI18n.global.t('ui.customerLeadDetailHint'),
    content: `确定将商机移交给${users[0].name}吗？`,
    confirmText: appI18n.global.t('ui.baTreePickerIndexOk'),
    cancelText: appI18n.global.t('ui.baTreePickerIndexCancel'),
  })

  if (!res.confirm) return

  try {
    const res = await opportunityShiftApi(data.id, {
      to_uid: users[0].id,
      data: [data.id],
    })
    message.success(res.message)
    freshData()
  } catch (error) {
    message.error(error.message)
  }
}

const handleChangeTab = (item: any, index: number) => {
  data.currentId = item.id
  if (item.id == 3) {
    refreshDynamicRecord()
  } else if (item.id == 4) {
    refreshContractList()
  } else if (item.id == 5) {
    getSigningInfo()
  }
}

// 获取合同详情
const getSigningInfo = () => {
  let obj = {
    link_type: 'odds',
  }
  getContractDocEditApi(data.id, obj)
    .then((res) => {
      detail.value = res.data
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const customerMoreRef = ref(null)
const handleNavIconClick = () => {
  const forumMeus = [
    { name: '编辑', id: 1, icon: 'icon-gongzuohuibao-bianji' },
    { name: '移交同事', id: 2, icon: 'icon-danchuang-zhuanyi' },
    { name: '删除', id: 3, icon: 'icon-shanchu1' },
  ]
  customerMoreRef.value.popupOpen(forumMeus)
}

const dropDownItem = async (item: any) => {
  if (item.id == 1) {
    // 编辑
    clickNavigateTo(`/pages/customer/opportunity/add?id=${data.id}`)
  } else if (item.id == 2) {
    // 移交同事
    const event = Date.now() + ''
    uni.$once(event, handleShift)
    uni.navigateTo({
      url: '/pages/users/department/index?event=' + event,
    })
  } else if (item.id == 3) {
    uni.showModal({
      title: appI18n.global.t('ui.customerLeadDetailHint'),
      content: appI18n.global.t('ui.customerListCustomerMoreDeleteThisOpportunity'),
      success: (res) => {
        if (res.confirm) {
          opportunityDelApi(data.id)
            .then((res) => {
              message.success(res.message)
              delayedNavigateBack()
            })
            .catch((err) => {
              message.error(err.message)
            })
        }
      },
    })
  }
}

// 关注商机
const clickFollow = async (type: number) => {
  try {
    const res = await opportunityFollowApi(data.id, type)
    message.success(res.message)
    data.detailData.data.followed = type
    // freshData();
  } catch (error) {
    message.error(error.message)
  }
}

// 添加订单
const AddContract = () => {
  const key = `odds_${data.id}`
  uni.setStorageSync(key, {
    customer_name: data.detailData.customer_name,
    customer_id: data.eid,
    product: data.detailData.product,
  })
  // 添加订单
  clickNavigateTo(`/pages/customer/contract/addContract?odds_id=${data.id}&name=${data.detailData.customer_name}&eid=${data.eid}`)
}

// 生成合同
const AddSigning = () => {
  clickNavigateTo(`/pages/customer/signing/addForm?eid=${data.eid}&oid=${data.id}`)
}

// 打开客户聊天对话框
const openCustomerChat = async () => {
  if (!isWxWorkEnv) return message.error(appI18n.global.t('ui.customerListLiaisonListChatIsAvailableOnlyInWeCom'))
  if (!data.detailData.data.work_customer || !data.detailData.data.work_customer.external_userid) return message.error(appI18n.global.t('ui.customerOpportunityOpportunityListTheCustomerIsNotLinkedToWeCom'))
  try {
    const wxWork = await WxWork.getInstance()
    await new Promise((resolve, reject) => {
      wxWork.ww.openEnterpriseChat({
        userIds: '', // 外部联系人
        externalUserIds: [data.detailData.data.work_customer.external_userid],
        groupName: '',
        chatId: '',
        success: resolve,
        fail: reject,
      })
    })
    message.success(appI18n.global.t('ui.customerLeadLeadListChatOpened'))
  } catch (err) {
    message.error(`打开个人资料页失败: ${err.errMsg || err.message || '操作失败'}`)
  }
}

// 修改状态
const handleChangeStatus = async (item: any) => {
  await opportunityEditApi(data.id, {
    status: item.value,
    name: data.detailData.name,
    eid: data.eid,
  })
  freshData()
}

// 添加图片
const uploadAvatar = () => {
  const config = {
    eid: data.eid,
    relation_type: 'follow',
  }
  if (data.id > 0) {
    config.relation_id = data.id
  }
  uploadImage('attach/imgs', config)
    .then((res) => {
      data.imgs.push(res.data)
    })
    .catch((error) => {
      message.error(error)
    })
}

// 提交跟进记录
const handleConfirm = debounce(async () => {
  if (!formData.value.content) return message.error(appI18n.global.t('ui.customerLeadDetailFollowUpInformationCannotBeEmpty'))
  formData.value.attach_ids = data.imgs.map((item) => item.id)
  formData.value.eid = data.id

  try {
    const res = await followSaveApi(formData.value)
    Object.assign(formData.value, { content: '', eid: '', attach_ids: [] })
    data.imgs = []
    refreshDynamicRecord()
    message.success(res.message)
  } catch ({ message: msg }) {
    message.error(msg)
  }
})

// 附件删除
const deleteFile = (id) => {
  data.imgs = data.imgs.filter(function (item) {
    return item.id !== id
  })
}

const refreshDetails = () => {
  getOppoDetail()
}

// 获取商机详情
const getOppoDetail = async () => {
  try {
    const res = await opportunityEditFormApi(data.id)
    data.detailData = res.data
    data.eid = res.data.data.eid
  } catch (error) {
    message.error(error.message)
  }
}

const getContractList = async () => {
  if (data.contractLoaded) return false
  const page = contractWhere.value.page

  try {
    uni.showLoading({
      title: appI18n.global.t('ui.customerContractIndexLoading'),
    })
    const res = await clientContractListApi({
      oid: data.id,
      ...contractWhere.value,
    })
    uni.hideLoading()
    if (page === 1) {
      data.contractList = res.data.list
    } else {
      data.contractList.push(...res.data.list)
    }
    data.count = res.data.count
    data.contractLoaded = data.contractList.length >= res.data.count
  } catch (error) {
    message.error(error.message)
  }
}

const handleGetDynamicRecord = async () => {
  if (data.momentLoaded) return false
  try {
    const { page } = momentWhere.value
    uni.showLoading({
      title: appI18n.global.t('ui.customerContractIndexLoading'),
    })
    const { data: res } = await momentRecordApi({
      link_type: 'odds',
      eid: data.id,
      ...momentWhere.value,
    })
    uni.hideLoading()
    // 第一页直接覆盖，后续页追加
    data.momentList = page === 1 ? res.list : [...data.momentList, ...res.list]
    data.momentLoaded = data.momentList.length >= res.count
  } catch (err) {
    uni.hideLoading()
    message.error(err.message)
  }
}

function refreshContractList() {
  contractWhere.value.page = 1
  getContractList()
}

function refreshDynamicRecord() {
  momentWhere.value.page = 1
  data.momentLoaded = false
  handleGetDynamicRecord()
}

function freshData() {
  refreshContractList()
}

onLoad((options) => {
  const { id } = options
  if (id) {
    data.id = id
  }
})

onShow(() => {
  getOppoDetail()
  refreshDynamicRecord()
  refreshContractList()
})

import { onReachBottom } from '@dcloudio/uni-app'
// 下拉加载
onReachBottom(() => {
  if (data.currentId == 0) {
    momentWhere.value.page++

    handleGetDynamicRecord()
  } else if (data.currentId == 3) {
    contractWhere.value.page++
    getContractList()
  }
})
</script>

<style scoped lang="scss">
@import '../components/common.scss';

.customer-nav {
  border-top: 16rpx solid #f5f5f5;
  position: sticky;
  top: calc(var(--status-bar-height) + 44px - 16rpx);
  z-index: 1;
}

.sticky-navbar {
  background-color: #fff;
  padding-top: var(--status-bar-height);
  position: sticky;
  top: 0;
  z-index: 2;
}

.cr-position-header {
  position: initial;
}

.bag-fff {
  background-color: #fff;
}

::v-deep .empty {
  height: 1000rpx;
}

::v-deep .product-list-wrap {
  padding: 0 30rpx;
}

.detail-banner {
  background-color: #fff;
  padding: 24rpx 30rpx 30rpx 30rpx;

  .banner-content {
    display: flex;
    justify-content: space-between;
  }

  .customer-name {
    font-weight: 500;
    font-size: 30rpx;
    color: #303133;
    line-height: 32rpx;
    margin-bottom: 14px;
    .iconfont {
      cursor: pointer;
    }
  }

  .status-tag {
    height: 42rpx;
    border-radius: 8rpx;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24rpx;
    font-weight: 400;
    padding: 0 10rpx;
  }

  .customer-info {
    display: flex;
    align-items: center;
    font-weight: 400;
    font-size: 24rpx;
    color: #606266;
    line-height: 24rpx;

    &:last-child {
      margin-top: 22rpx;
    }

    .tel-icon {
      width: 36rpx;
      height: 36rpx;
      margin-left: 14rpx;
    }

    .divider {
      width: 2rpx;
      height: 26rpx;
      background: #ebeef5;
      margin-inline: 30rpx;
    }
  }
}

.bagf {
  background: #ffffff;
}

.m15 {
  padding: 15px;
  background-color: #fff;
}

.btn-box {
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;

  .addfujian {
    cursor: pointer;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 24rpx;
    color: #282828;
    .icon-fujian {
      font-size: 22rpx;
    }
  }

  .btn {
    cursor: pointer;
    width: 64px;
    height: 32px;
    background: #308bf8;
    border-radius: 6px 6px 6px 6px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #308bf8;
    border-radius: 12rpx;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 12px;
    color: #ffffff;
  }
}

.genjinBox {
  width: 100%;
  height: 194px;
  border-radius: 6px 6px 6px 6px;
  border: 1px solid #f5f5f5;
  padding: 24rpx;
  padding-top: 6rpx;
}

::v-deep .uni-easyinput__content-textarea {
  height: 120px !important;
}

.line {
  width: 100%;
  height: 10px;
  border-bottom: 1px solid #f0f1f5;
}

.icon-shequ-shoucang-yishoucang {
  color: #ff9900;
  cursor: pointer;
}

.main-body {
  flex: 1;
  display: flex;
  flex-flow: column;

  .product-list-box {
    padding-inline: 24rpx;
    background-color: #fff;
  }

  .moment-record,
  .examine-content,
  .follow-record {
    flex: 1;
  }
}

.add-record-title {
  color: #2b2c32;
  font-size: 30rpx;
  display: flex;
  align-items: center;
  font-weight: 500;
  padding-top: 40rpx;
  padding-bottom: 24rpx;
  padding-left: 30rpx;

  .add-btn-box {
    margin-left: auto;
  }

  .add-btn {
    font-weight: 400;
    color: #308bf8;
    font-size: 26rpx;
    display: flex;
    align-items: center;

    .iconfont {
      font-size: 20rpx;
      margin-right: 6rpx;
      transform: translateY(2rpx);
    }
  }
}
</style>
