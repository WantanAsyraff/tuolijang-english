<template>
  <view class="content" v-if="!isWxWorkEnv || (isWxWorkEnv && data.id)">
    <view class="sticky-navbar">
      <default-nav-bar
        v-if="!isWxWorkEnv"
        jump-url="/pages/customer/list/index"
        :is-jump-bar="false"
        :is-right="true"
        :right-data="data.rightIcon"
        @handleNarItem="handleNarItem"
        :isLeft="!isWxWorkEnv"
      >
      </default-nav-bar>
    </view>
    <view class="cr-position-header">
      <view id="customer-header-body">
        <view class="details-content plr10">
          <view class="company-header mb12">
            <view class="company-name">
              <image
                v-if="data.clientInfo.work_customer && data.clientInfo.work_customer.avatar"
                class="customer-avatar"
                :src="data.clientInfo.work_customer.avatar"
              />

              <view class="name-content">
                <text>{{ data.clientInfo.customer_name || '--' }}</text>
                <text
                  class="work-icon line1"
                  v-if="data.clientInfo.work_customer && data.clientInfo.work_customer.name"
                  @click.stop="openCustomerChat(data.clientInfo.work_customer)"
                  :class="data.clientInfo.work_customer.type != 1 ? 'work-name' : ''"
                  >{{ data.clientInfo.work_customer.type == 1 ? $t('ui.customerLeadLeadListWeChat') : '@' + data.clientInfo.work_customer.corp_name || '--' }}</text
                >

                <text
                  class="iconfont icon-shequ-shoucang-yishoucang ml4"
                  @click="clickFollow(0)"
                  v-if="data.clientInfo.customer_followed == 1"
                ></text>
                <text class="iconfont icon-shequ-shoucang1 ml4" @click="clickFollow(1)" v-else></text>
              </view>
            </view>

            <view
              class="status-tag"
              v-if="data.clientInfo && data.clientInfo.customer_status"
              :style="{
                color: data.clientInfo.customer_status.color ? data.clientInfo.customer_status.color : '#1890ff',
                background: data.clientInfo.customer_status.color
                  ? getColor(data.clientInfo.customer_status.color, '0.1')
                  : getColor('#1890ff', '0.1'),
              }"
              >{{ $ts(data.clientInfo.customer_status.name || '--') }}
            </view>
            <view class="company-right" v-if="isWxWorkEnv">
              <view
                class="iconfont bar-return"
                @click="handleNarItem"
                :class="item.icon"
                v-for="item in data.rightIcon"
                :key="item.type"
                :title="item.text"
              ></view>
            </view>
          </view>

          <view class="info-content display-align mb12">
            <view class="info-user">{{ $t('ui.customerListCustomerMoreSalesperson') }}{{ data.clientInfo.salesman || '--' }} </view>
            <view class="info-time"
              >{{ $t('ui.customerLeadDetailAddTime') }}
              <uni-dateformat v-if="data.clientInfo.created_at" format="yyyy/MM/dd hh:mm" :date="data.clientInfo.created_at"> </uni-dateformat>
              <text v-else>--</text>
            </view>
          </view>
          <!--协作者-->
          <view v-if="data.member && data.member.length > 0" class="info-content display-align mb12">
            <view class="info-collaborator">{{ $t('ui.customerListDetailsCollaborators') }}</view>
            <view class="user-collaborator line1">
              <template v-if="data.member.length > 0">
                <view v-for="(item, index) in data.member.length > 4 ? data.member.slice(0, 4) : data.member" :key="index" class="collaborator-list">
                  <image :src="item.avatar || '/static/image/default-avatar.png'" class="avatar"></image>
                  <view class="collaborator-name line1">{{ item.name }}</view>
                </view>
                <text style="font-size: 30rpx" v-if="data.member.length > 4">...</text>
              </template>
              <text v-else>--</text>
            </view>
          </view>
          <view class="label-content" @click="handleShowTagPopup">
            <template v-if="data.clientInfo.customer_label?.length">
              <view v-for="(item, index) in data.clientInfo.customer_label">
                <text class="uni-tag">{{ item.name }} {{ index < data.clientInfo.customer_label.length - 1 ? ' 、' : '' }}</text>
              </view>
            </template>
            <view v-else>
              <view class="add-label">
                <text class="iconfont icon-xuanfuanniu-jia"></text>
                <text>{{ $t('ui.customerLeadDetailAddLabel') }}</text>
              </view>
              <!-- <uni-tag class="lext" text="+添加标签" type="warning" size="mini" /> -->
            </view>
            <view class="iconfont icon-jinru-copy"></view>
          </view>
        </view>
      </view>
    </view>

    <!-- 导航栏 -->
    <view class="customer-nav" :style="isWxWorkEnv ? 'top: 0;' : ''">
      <view class="customer-content">
        <view class="customer-tab">
          <view
            class="customer-tab-item"
            :data-index="index"
            v-for="(item, index) in data.tabData"
            :class="index === data.currentIndex ? 'active' : ''"
            :key="item.id"
            @click="changeTab(item, index)"
          >
            {{ item.name }}
          </view>
          <view class="underlineBox">
            <view class="underline bg-color-white"></view>
          </view>
        </view>
      </view>
    </view>

    <view class="customer-info">
      <!--商机/客户跟进-->
      <business-follow
        style="margin-bottom: 60px"
        v-if="data.currentId === 4"
        @initData="initData"
        :businessData="data.businessList"
        :businessRecord="data.businessRecord"
      ></business-follow>
      <!-- 基本信息 -->
      <customer-info
        class="bagf"
        v-if="data.currentId === 1"
        :eid="data.id"
        :type="`customer`"
        :customerData="data.customerData"
        @refreshDetails="refreshDetails"
      ></customer-info>
      <!--联系人-->
      <customer-contacts
        :class="data.contactList.length > 2 ? 'pb60' : ''"
        v-if="data.currentId === 5"
        :contactsData="data.contactList"
        :eid="data.id"
        :count="data.contactCount"
      ></customer-contacts>
      <!--合同-->
      <customer-contract
        v-if="data.currentId === 6"
        :class="data.contractList.length > 2 ? 'pb60' : ''"
        :contractData="data.contractList"
        :count="data.contractCount"
      ></customer-contract>
      <!--订单-->
      <customer-order
        v-if="data.currentId === 2"
        :class="data.orderList.length > 2 ? 'pb60' : ''"
        :orderData="data.orderList"
        :count="data.orderCount"
      ></customer-order>
      <!-- 客户跟进记录页面 -->
      <view v-if="data.currentId === 4 && data.businessList.length == 0" class="mb60">
        <view class="m15" style="padding-bottom: 0">
          <!-- <view class="follow-title">客户动态记录</view> -->
          <view class="genjinBox">
            <uni-easyinput
              :inputBorder="false"
              v-model="formData.content"
              type="textarea"
              :clearable="false"
              :maxlength="256"
              :placeholder="$t('ui.customerListDetailsEnterCustomerFollowUp')"
              class="mb10"
            >
            </uni-easyinput>
            <!-- 文件 -->
            <view class="btn-box">
              <view class="addfujian" @click="uploadAvatar"> <text class="iconfont icon-fujian"></text> {{ $t('ui.replyComponentIndexAddAttachment') }}</view>
              <view class="btn" @click="handleConfirm">{{ $t('ui.replyComponentIndexSubmit') }}</view>
            </view>
          </view>
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
        <follow-record
          v-if="data.currentId === 4"
          :followList="data.followList"
          :count="data.followCount"
          :showTitle="data.businessList.length > 0 ? false : true"
          :title="$t('ui.customerContractDetailsActivityRecords')"
          @getfollowList="getfollowList"
        >
        </follow-record>
      </view>

      <!-- 付款记录 -->
      <view v-if="data.currentId === 3">
        <view class="add-record-title payment">
          <view class="reord-header">
            <!-- 下拉选择 -->
            <picker
              class="picker-selector"
              mode="selector"
              @change="(e) => bindPickerChange(e)"
              :value="data.recordIndex"
              :range="data.recordStatusList"
              range-key="label"
            >
              <view class="search-default-label">
                {{ data.statusText }}
                <text class="iconfont icon-fanhui"></text>
              </view>
            </picker>
            <view class="add-btn" @click="handleOpenPaymentActionSheet">
              <i class="iconfont icon-xuanfuanniu-jia"></i>
              {{ $t('ui.examineFormApprovalBillAdd') }}
            </view>
          </view>
          <view v-if="data.census" class="record-account">
            <view
              >{{ $t('ui.customerListDetailsAmountReceivedCny') }}<text style="font-size: 24rpx; font-weight: 500">{{ data.census.income }}</text></view
            >
            <view
              >{{ $t('ui.customerListDetailsExpenseAmountYuan') }}<text style="color: #ff4949; font-size: 24rpx; font-weight: 500">{{ data.census.expend }}</text>
            </view>
          </view>
        </view>
        <account-record :listData="data.paymentList" :count="data.payCount"></account-record>
      </view>
    </view>

    <globalIndex />
    <uni-popup ref="popupRef" background-color="#fff" type="bottom" v-if="data.currentPickerData.length">
      <view class="uni-picker-header">
        <view class="uni-picker-action uni-picker-action-cancel" @click="cancelPopup()">{{ $t('ui.baTreePickerIndexCancel') }}</view>
        <view class="uni-picker-action uni-picker-action-confirm" @click="confirmPopup(data.currentType)">{{ $t('ui.examineFormTimeFromPickerCompleted') }} </view>
      </view>
      <picker-view :indicator-style="data.indicatorStyle" :value="data.newValue" class="picker-view" @change="changePicker($event, data.currentType)">
        <picker-view-column>
          <view class="item-value" v-for="(item, index) in data.currentPickerData" :key="index">
            {{ item.name }}
          </view>
        </picker-view-column>
      </picker-view>
    </uni-popup>

    <!-- 备注弹窗 -->
    <textarea-popup ref="textareaPopupRef" :config-data="data.configData" @change="changePop" />
    <!-- 操作弹窗 -->
    <more-popup ref="customerMoreRef" @handleItem="dropDownItem"></more-popup>
    <selected-label :title="$t('ui.customerListCustomerMoreCustomerLabels')" ref="selectedLabelRef" @changeItem="changeItem"> </selected-label>
    <!--底部应用组件-->
    <details-foot ref="detailsFootRef" @openPopup="handleShowAddPopup" :customerData="data.clientInfo"></details-foot>
    <!--添加弹窗-->
    <add-popup ref="addPopupRef" :dataList="data.listIcon" @handleClickItem="handleClickItem"></add-popup>
    <!--返回顶部-->
    <!-- <back-top v-if="showBackToTop" @scrollToTop="scrollToTop"></back-top>
    <view style="height: 80px;"></view> -->
  </view>
</template>

<script setup lang="ts">import appI18n from '@/locale';

import { lookPreview, fileSizeOne } from '@/utils/helper'
import defaultNavBar from '@/components/defaultNavBar/index'
import globalIndex from '@/components/globalIndex/index.vue'
import businessFollow from './components/businessFollow.vue'
import customerInfo from './components/customerInfo.vue'
import customerContacts from './components/customerContacts.vue'
import customerContract from './components/customerContract.vue'
import customerOrder from './components/customerOrder.vue'
import selectedLabel from './components/selectedLabel.vue'
import detailsFoot from './components/detailsFoot.vue'
import addPopup from './components/addPopup.vue'
import accountRecord from './components/accountRecord.vue'
import textareaPopup from '@/components/textareaPopup/index.vue'
import followRecord from './components/followRecord.vue'
import morePopup from '@/components/morePopup/index.vue'
import { getColor } from '@/utils/helper'
import { reactive, ref, getCurrentInstance, nextTick } from 'vue'
import { uploadImage, formatBytes } from '@/utils/file'
import { onLoad, onShow, onPageScroll } from '@dcloudio/uni-app'
import message from '@/utils/message'
import { useStore } from 'vuex'
import { filterPermissionButtons } from '@/utils/customerSwitch'
import {
  customerReturnApi,
  momentRecordApi,
  billListApi,
  configApproveApi,
  followSaveApi,
  followPutApi,
  clientContractLabelApi,
  clientEditInfoApi,
  clientLiaisonApi,
  clientContractListApi,
  opportunityRecordApi,
  opportunityListApi,
  clientDeleteApi,
  clientclaimApi,
  clientlostApi,
  clientCancelLostApi,
  clientStatusApi,
} from '@/api/customer'
import { getContractDocApi } from '@/api/signing'
import { isWxWorkEnv, WxWork } from '@/libs/wxwork'

const store = useStore()
const detailsFootRef = ref(null)
const formData = ref({
  content: '',
  attach_ids: [],
  types: 0,
  eid: 0,
  link_type: 'customer',
})
const data = reactive({
  rightIcon: [
    {
      type: 1,
      icon: 'icon-gengduo1',
      types: 'icon',
      text: appI18n.global.t('ui.moduleListMore'),
    },
  ],
  configData: {
    title: appI18n.global.t('ui.customerListDetailsRenameDocument'),
    placeholder: appI18n.global.t('ui.customerListDetailsEnterADocumentName'),
  },

  customer_switch: JSON.parse(uni.getStorageSync('storageUserData')).enterprise.customer_switch,
  imgs: [],
  followUpId: '', // 跟进记录id
  id: 0, // 客户id
  wxwork_userid: '', // 企业微信用户id
  currentIndex: 0,
  scrollTop: 0,
  statusBarHeight: 20,
  currentId: 4,
  clientInfo: {}, // 客户信息
  recordIndex: '',
  statusText: '全部',
  recordStatusList: [
    {
      label: appI18n.global.t('ui.attendanceDetailedUserCheckListAll'),
      value: '',
    },
    {
      label: appI18n.global.t('ui.customerContractPayRemindPaymentCollection'),
      value: 0,
    },
    {
      label: appI18n.global.t('ui.customerContractPayRemindRenewal'),
      value: 1,
    },
    {
      label: appI18n.global.t('ui.financePaymentPaymentExamineExpense'),
      value: 2,
    },
  ],
  followCount: 0,
  followWhere: {
    page: 1,
    limit: 10,
    eid: '',
    link_type: 'customer',
  },
  types: 1,
  followList: [], // 跟进记录
  payWhere: {
    page: 1,
    limit: 10,
    types: '',
    eid: '',
  },
  paymentList: [], // 付款记录
  census: {}, //账目数据
  contactCount: 0,
  contactWhere: {
    page: 1,
    limit: 10,
    types: '',
    eid: '',
  },
  contactList: [],
  contractCount: 0,
  contractWhere: {
    limit: 10,
    page: 1,
    types: '',
    view_search: 2,
    eid: '',
  },
  businessList: [],
  businessCount: 0,
  businessWhere: {
    limit: 20,
    page: 1,
  },
  businessRecord: [],
  contractList: [],
  tabData: [
    {
      name: '商机跟进',
      id: 4,
    },
    {
      name: '基本信息',
      id: 1,
    },
    {
      name: '账目记录',
      id: 3,
    },
    {
      name: '联系人',
      id: 5,
    },
    {
      name: '合同',
      id: 6,
    },
    {
      name: '订单',
      id: 2,
    },
  ],

  buildData: {},
  currentPickerData: [],
  currentType: 0,
  member: [],
  customerData: [],
  orderList: [],
  orderCount: 0,
  payCount: 0,
  orderWhere: {
    limit: 20,
    page: 1,
  },
  listIcon: [],
})
const textareaPopupRef = ref(null)
const topHeight = ref(0)
const popupRef = ref()
onShow(() => {
  let statusBarObj = getPhoneInfo()
  data.statusBarHeight = statusBarObj.statusBarHeight
  // #ifdef APP-PLUS
  topHeight.value = data.statusBarHeight + 'px'
  // #endif

  tabLoadingList()
})
onPageScroll((e) => {
  data.scrollTop = e.scrollTop
})
onLoad((e) => {
  // 客户权限判断
  getCustomerSwitch()
  getConfigApprove()
  data.id = e.id || 0
  data.wxwork_userid = e.userid
  const task = getClientInfo(data.id)
  if (e.types) {
    data.types = e.types
  } else {
    data.types = 'customer'
  }
  if (e.type == 2) {
    data.currentId = Number(e.type)
    data.currentIndex = 1
  }
  if (e.type == 3) {
    data.currentId = Number(e.type)
    data.currentIndex = 2
  }

  if (e.userid) {
    if (data.id) {
      tabLoadingList()
    } else {
      task.then(tabLoadingList)
    }

    task.then(() => {
      initData()
    })
  }

  if (e.tab) {
    data.currentIndex = Number(e.tab)
    data.currentId = data.tabData[data.currentIndex].id
    tabLoadingList()
  }
})
onShow(() => {
  initData()
})

onUnmounted(() => {
  if (scrollTimeout) {
    clearTimeout(scrollTimeout)
  }
})

const getCustomerSwitch = () => {
  if (!data.customer_switch.contract_module_switch) {
    data.tabData.splice(4, 1)
  }
}
const showBackToTop = ref(false)
// 添加一个状态记录是否已经显示过
const hasShown = ref(false)
// 记录当前滚动方向
const lastScrollY = ref(0)
const scrollingUp = ref(false)
// 使用节流函数优化性能
let scrollTimeout = null
const throttleDelay = 100 // 100ms节流
const handleScroll = () => {
  // 清除之前的定时器
  if (scrollTimeout) return
  scrollTimeout = setTimeout(() => {
    const currentScrollY = window.scrollY
    const viewportHeight = window.innerHeight
    const threshold = viewportHeight * 2
    // 判断滚动方向
    scrollingUp.value = currentScrollY < lastScrollY.value
    // 两种情况显示按钮：
    // 1. 当前滚动超过两屏高度，且正在向下滚动
    // 2. 或者之前已经显示过按钮（保持显示状态）
    if (currentScrollY > threshold) {
      if (!scrollingUp.value) {
        // 向下滚动且超过阈值，显示按钮
        showBackToTop.value = true
        hasShown.value = true
      } else {
        // 向上滚动，但之前已经显示过按钮，保持显示
        if (hasShown.value) {
          showBackToTop.value = true
        }
      }
    } else {
      // 滚动高度低于阈值，隐藏按钮
      showBackToTop.value = false
      hasShown.value = false
    }
    lastScrollY.value = currentScrollY
    scrollTimeout = null
  }, throttleDelay)
}
// 滚动到顶部
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })
  // 滚动到顶部后重置状态
  setTimeout(() => {
    showBackToTop.value = false
    hasShown.value = false
    lastScrollY.value = 0
  }, 300)
}
// 定义异步函数
const initData = async () => {
  try {
    await Promise.all([getOpportunityList(), getOpportunityRecord()])
  } catch (error) {
    console.error('加载数据失败:', error)
  }
}
// 选择账目记录状态
const bindPickerChange = (e) => {
  let index = e.detail.value
  data.recordIndex = data.recordStatusList[index].value
  data.statusText = data.recordStatusList[e.detail.value].label
  getbillList(true)
}

// 预览
const preview = (item: any) => {
  lookPreview(item.url, item.real_name, [item.url])
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
  uploadImage('attach/imgs', config, fileSizeOne)
    .then((res) => {
      data.imgs.push(res.data)
    })
    .catch((error) => {
      message.error(error)
    })
}
// 打开客户聊天对话框
const openCustomerChat = async (item) => {
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
    message.success(appI18n.global.t('ui.customerLeadLeadListChatOpened'))
  } catch (err) {
    message.error(`打开个人资料页失败: ${err.errMsg || err.message || '操作失败'}`)
  }
}
// 提交跟进记录
const handleConfirm = debounce(() => {
  if (!formData.value.content) {
    message.error(appI18n.global.t('ui.customerLeadDetailFollowUpInformationCannotBeEmpty'))
    return false
  }
  formData.value.attach_ids = data.imgs.map((item) => item.id)
  formData.value.eid = data.id
  const task = data.followUpId ? followPutApi(data.followUpId, formData.value) : followSaveApi(formData.value)
  task
    .then((res) => {
      data.followWhere.page = 1
      getfollowList(true)
      formData.value.content = ''
      data.imgs = []
      formData.value.eid = ''
      formData.value.attach_ids = []
      message.success(res.message)
    })
    .catch((err) => {
      message.error(err.message)
    })
})

// 附件删除
const deleteFile = (id) => {
  data.imgs = data.imgs.filter(function (item) {
    return item.id !== id
  })
}

// 标签选择回调
const changeItem = (e) => {
  let obj = {
    label: e,
  }
  clientContractLabelApi(data.id, obj).then((res) => {
    message.success(res.message)
    getClientInfo(data.id)
  })
}
const cHeight = ref(0)
const instance = getCurrentInstance()
const getCustomerHeight = () => {
  let query = uni.createSelectorQuery().in(instance)
  query.select('#customer-header-body').fields({
    size: true,
    rect: true,
  })
  query.exec((e) => {
    cHeight.value = -Math.ceil(e[0].height) + data.statusBarHeight + 20 + 'px'
  })
}

const customerMoreRef = ref(null)

const handleNarItem = (e) => {
  let forumMeus = []
  if (data.types == 'customer') {
    forumMeus = [
      { id: 1, icon: 'icon-danchuang-bianji', name: '编辑' },
      { id: 2, icon: 'icon-danchuang-bianji', name: '移交同事' },
      { id: 3, icon: 'icon-danchuang-bianji', name: '退回公海' },
      { id: 7, icon: 'icon-danchuang-shanchu', name: '删除客户' },
    ]
  } else if (data.types == 'customer_seas') {
    // 线索弹窗
    forumMeus = [
      { id: 1, icon: 'icon-danchuang-bianji', name: '编辑' },
      { id: 4, icon: 'icon-danchuang-lingqu', name: '领取' },
      { id: 5, icon: 'icon-danchuang-zhuanyi', name: '分配' },
      { id: 6, icon: 'icon-danchuang-liushi', name: data.clientInfo.customer_status.value == 2 ? '取消流失' : '标为流失' },
      { id: 7, icon: 'icon-danchuang-shanchu', name: '删除客户' },
    ]
  }
  customerMoreRef.value.popupOpen(forumMeus)
}

const dropDownItem = (e) => {
  if (e.id === 1) {
    //编辑
    clickNavigateTo(`/pages/customer/list/addCustomer?eid=${data.id}&Tabtype=${data.types}`)
  } else if (e.id === 2) {
    //移交同事
    clickNavigateTo(`/pages/customer/list/shift?type=1&eid=${data.id}`)
  } else if (e.id === 3) {
    //退回公海
    data.configData = {
      title: appI18n.global.t('ui.customerListCustomerMoreReturnCustomerToPool'),
      placeholder: appI18n.global.t('ui.customerLeadDetailReason'),
      type: '',
      text: '',
      refundType: 0, // 4 -> 客户公海 802 -> 线索池
    }
    data.configData.refundType = 4
    data.configData.title = appI18n.global.t('ui.customerListCustomerMoreReturnCustomerToPool')
    textareaPopupRef.value.popupOpen()
  } else if (e.id === 7) {
    //删除客户
    uni.showModal({
      title: appI18n.global.t('ui.customerLeadDetailHint'),
      content: appI18n.global.t('ui.customerListCustomerMoreDeleteThisCustomer'),
      success: (res) => {
        if (res.confirm) {
          clientDeleteApi(data.id)
            .then((res) => {
              if (res.status === 200) {
                message.success(res.message)
                delayedReLaunch('/pages/customer/list/index')
              }
            })
            .catch((error) => {
              message.error(error.message)
            })
        }
      },
    })
  } else if (e.id === 4) {
    //领取
    uni.showModal({
      title: appI18n.global.t('ui.customerLeadDetailHint'),
      content: appI18n.global.t('ui.customerListCustomerMoreClaimThisCustomer'),
      success: (res) => {
        if (res.confirm) {
          clientclaimApi(data.id)
            .then((res) => {
              if (res.status === 200) {
                message.success(res.message)
                delayedReLaunch('/pages/customer/list/index')
              }
            })
            .catch((error) => {
              message.error(error.message)
            })
        }
      },
    })
  } else if (e.id === 5) {
    //分配
    clickNavigateTo(`/pages/customer/list/shift?type=1&eid=${data.id}`)
  } else if (e.id === 6) {
    //流失
    if (data.clientInfo.customer_status.value == 2) {
      clientCancelLost(data.id)
    } else {
      clientLost(data.id)
    }
  }
}

// 退回公海池
const changePop = (e) => {
  customerReturnApi(data.id, { reason: e.value })
    .then((res) => {
      if (res.status === 200) {
        message.success(res.message)
        delayedReLaunch('/pages/customer/list/index')
      }
    })
    .catch((error) => {
      message.error(error.message)
    })
}
// 取消流失
const clientCancelLost = (id) => {
  uni.showModal({
    title: appI18n.global.t('ui.customerLeadDetailHint'),
    content: appI18n.global.t('ui.customerListCustomerMoreRemoveTheLostStatusFromThisCustomer'),
    success: (res) => {
      if (res.confirm) {
        clientCancelLostApi(id)
          .then((res) => {
            if (res.status === 200) {
              message.success(res.message)
              delayedReLaunch('/pages/customer/list/index')
            }
          })
          .catch((error) => {
            message.error(error.message)
          })
      }
    },
  })
}
// 标为流失
const clientLost = (id) => {
  uni.showModal({
    title: appI18n.global.t('ui.customerLeadDetailHint'),
    content: appI18n.global.t('ui.customerListCustomerMoreMarkThisCustomerAsLost'),
    success: (res) => {
      if (res.confirm) {
        clientlostApi(id)
          .then((res) => {
            if (res.status === 200) {
              message.success(res.message)
              getClientInfo(data.id)
            }
          })
          .catch((error) => {
            message.error(error.message)
          })
      }
    },
  })
}

const handleClickItem = (item) => {
  let type = addType.value
  switch (type) {
    case 'common':
      handleFootItem(item)
      break
    case 'accounts':
      handleActionChange(item)
      break
    default:
      router.push('/')
  }
}

const handleFootItem = (item) => {
  let type = item.type
  switch (type) {
    case 1:
      clickNavigateTo(`${item.url}?eid=${data.id}&types=${data.types}&type=2`)
      break
    case 2:
      clickNavigateTo(`${item.url}?eid=${data.id}`)
      break
    case 3:
      clickNavigateTo(`${item.url}?eid=${data.id}`)
      break
    case 4:
      clickNavigateTo(`${item.url}?eid=${data.id}&name=${data.clientInfo.customer_name}&types=${data.types}`)
      break
    case 5:
      clickNavigateTo(`${item.url}?eid=${data.id}&name=${data.clientInfo.customer_name}&types=${data.types}`)
      break
    case 6:
      clickNavigateTo(`${item.url}?id=${data.buildData.invoicing_switch}&eid=${data.id}&name=${data.clientInfo.customer_name}&types=${data.types}`)
      break
    default:
      router.push('/')
  }
}
const changeTab = (item, index) => {
  data.currentIndex = index
  data.currentId = item.id
  tabLoadingList()
  if (item.id == 3) {
    // 获取付款是否走审批
  }
}
const getConfigApprove = () => {
  configApproveApi().then((res) => {
    data.buildData = res.data
  })
}
const cancelPopup = () => {
  popupRef.value.close()
}

// tab切换获取列表
const tabLoadingList = () => {
  switch (data.currentId) {
    case 2: // 订单列表
      data.orderWhere.page = 1
      getOrderList(true)
      break
    case 3: // 付款记录
      data.payWhere.page = 1
      getbillList(true)
      break
    case 4: // 客户跟进记录
      data.followWhere.page = 1
      getfollowList(true)
      break
    case 5: // 联系人列表
      data.contactWhere.page = 1
      getContactList(true)
      break
    case 6: // 合同列表
      data.contractWhere.page = 1
      getContractList(true)
      break
    default:
      // 可以添加默认处理或错误提示
      console.warn(`未处理的 currentId: ${data.currentId}`)
      break
  }
}

const refreshDetails = () => {
  getClientInfo(data.id)
}
// 获取客户信息
const getClientInfo = (id) => {
  let obj = {}
  if (data.wxwork_userid) {
    obj = {
      userid: data.wxwork_userid,
    }
  }

  return clientEditInfoApi(id, obj)
    .then((res) => {
      data.clientInfo = res.data.data
      data.customerData = res.data.form
      data.id = res.data.data.id
      data.member = res.data.data.member
      nextTick(() => {
        getCustomerHeight()
      })
    })
    .catch((error) => {
      // if (isWxWorkEnv) {
      //   uni.redirectTo({
      //     url: "/pages/common/ww-default"
      //   });
      // } else {
      //   message.error(error.message);
      // }
    })
}

const listLoading = ref(false) // 是否正在加载
// 获取商机列表
const getOpportunityList = (tab = false) => {
  if (!data.customer_switch.opportunity_module_switch) {
    data.tabData[0].name = '客户跟进'
    return false
  }
  if (!data.id) return false
  data.businessWhere.eid = data.id
  opportunityListApi(data.businessWhere)
    .then((res) => {
      data.businessList = res.data.list
      addPropertyToAll()
      if (!res.data.list.length) data.tabData[0].name = '客户跟进'
    })
    .catch((error) => {
      message.error(error.message)
    })
}
// 给数组中的所有对象添加同一个新属性
const addPropertyToAll = () => {
  data.businessList = data.businessList.map((item) => ({
    ...item,
    showList: false,
    imgs: [],
    content: '',
  }))
}
// 获取商机记录
const getOpportunityRecord = () => {
  opportunityRecordApi({ link_type: 'customer', eid: data.id })
    .then((res) => {
      data.businessRecord = res.data
    })
    .catch((error) => {
      message.error(error.message)
    })
}
// 获取联系人列表
const getContactList = (tab = false) => {
  // 如果是tab刷新操作，则允许重新加载
  if (!tab && data.contactWhere.limit * data.contactWhere.page >= data.contactCount && data.contactCount != 0) {
    return false
  }
  data.contactWhere.eid = data.id
  uni.showLoading({
    title: appI18n.global.t('ui.customerContractIndexLoading'),
  })
  clientLiaisonApi(data.contactWhere)
    .then((res) => {
      uni.hideLoading()
      data.contactCount = res.data.count
      data.contactList = tab ? res.data.list : [...data.contactList, ...res.data.list]
      // 计算总页数
      const allPage = Math.ceil(data.contactCount / data.contactWhere.limit)
      // 更新加载状态
      listLoading.value = data.contactWhere.page < allPage ? true : false
    })
    .catch((error) => {
      message.error(error.message)
      uni.hideLoading()
    })
}
// 获取合同列表
const getContractList = (tab = false) => {
  // 如果是tab刷新操作，则允许重新加载
  if (!tab && data.contractWhere.limit * data.contractWhere.page >= data.contractCount && data.contractCount != 0) {
    return false
  }
  data.contractWhere.eid = data.id
  uni.showLoading({
    title: appI18n.global.t('ui.customerContractIndexLoading'),
  })
  getContractDocApi(data.contractWhere)
    .then((res) => {
      uni.hideLoading()
      data.contractCount = res.data.count
      if (tab) data.contractList = []
      data.contractList.push(...res.data.list)
      // 计算总页数
      const allPage = Math.ceil(data.contractCount / data.contractWhere.limit)
      // 更新加载状态
      listLoading.value = data.contractWhere.page < allPage ? true : false
    })
    .catch((error) => {
      message.error(error.message)
      uni.hideLoading()
    })
}
// 获取订单列表
const getOrderList = (tab = false) => {
  // 如果是tab刷新操作，则允许重新加载
  if (!tab && data.orderWhere.limit * data.orderWhere.page >= data.orderCount && data.orderCount != 0) {
    return false
  }
  data.orderWhere.eid = data.id
  uni.showLoading({
    title: appI18n.global.t('ui.customerContractIndexLoading'),
  })
  clientContractListApi(data.orderWhere)
    .then((res) => {
      uni.hideLoading()
      data.orderCount = res.data.count
      if (tab) data.orderList = []
      data.orderList.push(...res.data.list)
      // 计算总页数
      const allPage = Math.ceil(data.orderCount / data.orderWhere.limit)
      // 更新加载状态
      listLoading.value = data.orderWhere.page < allPage ? true : false
    })
    .catch((error) => {
      message.error(error.message)
      uni.hideLoading()
    })
}

// 获取动态记录
const getfollowList = (tab = false) => {
  if (!data.id) return false
  // 如果是tab刷新操作，则允许重新加载
  if (!tab && data.followWhere.limit * data.followWhere.page >= data.followCount && data.followCount != 0) {
    return false
  }

  uni.showLoading({
    title: appI18n.global.t('ui.customerContractIndexLoading'),
  })

  data.followWhere.eid = data.id
  momentRecordApi(data.followWhere)
    .then((res) => {
      data.followCount = res.data.count
      if (tab) data.followList = []
      res.data.list.forEach((item) => {
        item.card = item.creator
      })
      data.followList.push(...res.data.list)
      // 计算总页数
      const allPage = Math.ceil(data.followCount / data.followWhere.limit)
      // 更新加载状态
      listLoading.value = data.followWhere.page < allPage ? true : false
      uni.hideLoading()
    })
    .catch((error) => {
      message.error(error.message)
      uni.hideLoading()
    })
}
// 获取付款记录列表
const getbillList = (tab = false) => {
  // 如果是tab刷新操作，则允许重新加载
  if (!tab && data.payWhere.limit * data.payWhere.page >= data.payCount && data.payCount != 0) {
    return false
  }
  data.payWhere.eid = data.id
  data.payWhere.types = data.recordIndex
  uni.showLoading({
    title: appI18n.global.t('ui.customerContractIndexLoading'),
  })
  billListApi(data.payWhere)
    .then((res) => {
      uni.hideLoading()
      if (tab) data.paymentList = []
      data.payCount = res.data.count
      data.paymentList.push(...res.data.list)
      data.census = res.data.census

      const allPage = Math.ceil(res.data.count / data.payWhere.limit)
      // 更新加载状态
      listLoading.value = data.payWhere.page < allPage ? true : false
    })
    .catch((error) => {
      message.error(error.message)
      uni.hideLoading()
    })
}
const uid = computed(() => store.state.app.uid)
import { clickNavigateTo, getPhoneInfo, debounce, delayedReLaunch } from '@/utils/helper'
// 查看详情
const detailsItem = (item) => {
  let clientInfo = {
    name: data.clientInfo.name,
    eid: data.id,
  }
  uni.setStorageSync('clientInfo', JSON.stringify(clientInfo))
  if (!item.url) return false
  if (item.id === 2 || item === 3) {
    clickNavigateTo(`${item.url}?eid=${data.id}&name=${data.clientInfo.customer_name}&types=${data.types}`)
  } else if (item.id == 5) {
    // 商机
    // 是否是自己负责的客户
    let tabIndex = 0
    const isSelf = uid.value === data.clientInfo.uid
    if (!isSelf) {
      tabIndex = 1
      // 不是自己负责的客户，则为我查看的
    }

    const allData = data.customerData.map((item) => item.data).flat()
    const followData = allData.find((i) => i.key === 'last_follow_time')
    if (followData && followData.value === '关注') {
      tabIndex = 2
      // 关注
    }
    clickNavigateTo(`${item.url}?eid=${data.id}&tab_index=${tabIndex}&types=${data.types}`)
  } else {
    clickNavigateTo(`${item.url}?eid=${data.id}&name=${data.clientInfo.customer_name}&types=${data.types}`)
  }
}

// 添加付款和支出记录
const deanPopoverRef = ref(null)
const deanPopoverRefLeft = ref(null)
const addRecord = (type) => {
  deanPopoverRef.value?.close()
  deanPopoverRefLeft.value?.close()
  if (type <= 2) {
    clickNavigateTo(`/pages/customer/list/addFollow?eid=${data.id}&type=${type}`)
  } else if (type == 4) {
    if (data.buildData.contract_disburse_switch) {
      clickNavigateTo(`/pages/users/examine/default?id=${data.buildData.contract_disburse_switch}&eid=${data.id}&types=customer&nav_type=back`)
    } else {
      clickNavigateTo(`/pages/customer/list/addSpend?eid=${data.id}`)
    }
  } else if (type == 3) {
    // 添加回款 type：3
    clickNavigateTo(`/pages/users/examine/default?id=${data.buildData.contract_refund_switch}&eid=${data.id}&types=customer&nav_type=back`)
    // 添加续费
  } else if (type == 5) {
    clickNavigateTo(`/pages/users/examine/default?id=${data.buildData.contract_renew_switch}&eid=${data.id}&types=customer&nav_type=back`)
  }
}

const handleOpenPaymentActionSheet = () => {
  addType.value = 'accounts'
  data.listIcon = [
    {
      image: '/static/image/add-huikuan.png',
      name: '添加回款',
      type: 'add_payment',
      value: 3,
    },
    {
      image: '/static/image/add-xufei.png',
      name: '添加续费',
      type: 'add_payment',
      value: 5,
    },
    {
      image: '/static/image/add-zhichu.png',
      name: '添加支出',
      type: 'add_payment',
      value: 4,
    },
  ]
  addPopupRef.value.popupOpen()
}

const handleActionChange = (e) => {
  if (e.type === 'add_payment') {
    addRecord(e.value)
  }
}

// 关注
const clickFollow = (type) => {
  clientStatusApi(data.id, type)
    .then((res) => {
      message.error(res.message)
      data.clientInfo.customer_followed = type
    })
    .catch((error) => {
      message.error(error.message)
    })
}
const selectedLabelRef = ref(null)
const handleShowTagPopup = () => {
  let id = []
  if (data.clientInfo.customer_label && data.clientInfo.customer_label.length > 0) {
    data.clientInfo.customer_label.map((item) => {
      id.push(item.id)
    })
  }

  selectedLabelRef.value.popupOpen(id)
}

// 打开添加弹窗
const addPopupRef = ref(null)
const addType = ref(null)
// 底部添加弹窗
const handleShowAddPopup = () => {
  addType.value = 'common'
  const allButtons = [
    {
      image: '/static/image/add-tixing.png',
      name: '添加提醒',
      type: 1,
      url: '/pages/customer/list/addFollow',
    },
    {
      image: '/static/image/add-shangji.png',
      name: '添加商机',
      type: 2,
      url: '/pages/customer/opportunity/add',
    },
    {
      image: '/static/image/add-hetong.png',
      name: '添加合同',
      type: 3,
      url: '/pages/customer/signing/addForm',
    },
    {
      image: '/static/image/add-dingdan.png',
      name: '添加订单',
      type: 4,
      url: '/pages/customer/contract/addContract',
    },
    {
      image: '/static/image/add-lianxiren.png',
      name: '添加联系人',
      type: 5,
      url: '/pages/customer/list/addLiaison',
    },
    {
      image: '/static/image/add-fapiao.png',
      name: '申请发票',
      type: 6,
      url: '/pages/customer/invoice/checkPayment',
    },
  ]
  // 根据权限过滤按钮
  data.listIcon = filterPermissionButtons(allButtons)
  addPopupRef.value.popupOpen()
}

import { onReachBottom } from '@dcloudio/uni-app'
// 下拉加载
// 定义配置映射
const pageConfig = {
  2: {
    pageKey: 'contractWhere',
    method: getOrderList,
  },
  3: {
    pageKey: 'payWhere',
    method: getbillList,
  },
  4: {
    pageKey: 'followWhere',
    method: getfollowList,
  },
  5: {
    pageKey: 'contactWhere',
    method: getContactList,
  },
  6: {
    pageKey: 'contractWhere',
    method: getContractList,
  },
}

onReachBottom(() => {
  const config = pageConfig[data.currentId]
  if (config && listLoading.value) {
    data[config.pageKey].page++
    config.method()
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

.content {
  width: 100%;

  .cr-position-header {
    position: initial;
  }

  .details-content {
    position: relative;
    padding-top: 22rpx;
    padding-bottom: 22rpx;
    background-color: #fff;

    .item-list-status {
      position: absolute;
      top: 20rpx;
      right: 0;
      width: 160rpx;
      height: 188rpx;
    }

    .name-content {
      display: flex;
      align-items: center;
    }

    .work-icon {
      display: inline-block;
      max-width: 300rpx;
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      font-size: 13px;
      color: #1cbf6c;
      margin-left: 8rpx;
      // margin-left: 8rpx;
      // margin-right: 12rpx;
    }

    .work-name {
      color: #ff9900;
    }

    .company-header {
      // height: 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;

      .company-right {
        display: flex;
        justify-content: space-between;
      }

      .company-name {
        font-family:
          PingFang SC,
          PingFang SC;
        font-weight: 500;
        font-size: 30rpx;
        color: #303133;
        display: flex;
        align-items: center;
        white-space: nowrap;

        .customer-avatar {
          width: 64rpx;
          height: 64rpx;
          border-radius: 50%;
          margin-right: 12rpx;
        }

        .icon-shequ-shoucang-yishoucang {
          cursor: pointer;
          color: #ff9900;
        }
      }

      .status-tag {
        margin-left: 16rpx;
        height: 42rpx;
        white-space: nowrap;
        border-radius: 8rpx;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24rpx;
        font-weight: 400;
        padding: 0 10rpx;
      }

      .bar-return {
        cursor: pointer;
        font-size: 34rpx;
        font-weight: 400;

        .active-color {
          color: $nui-text-color-two;
        }
      }
    }

    .mb12 {
      margin-bottom: 20rpx;
    }

    .label-content {
      width: 100%;
      cursor: pointer;
      // padding-top: 16rpx;
      display: flex;
      flex-wrap: wrap;
      align-items: center;

      .add-label {
        display: flex;
        width: 144rpx;
        height: 42rpx;
        align-items: center;
        justify-content: center;
        color: #ff9900;
        font-size: 24rpx;
        border: 1px solid #ff9900;
        border-radius: 4px;
        cursor: pointer;

        .icon-xuanfuanniu-jia {
          font-size: 24rpx;
          margin-right: 4rpx;
        }
      }

      .icon-jinru-copy {
        font-size: 20rpx;
        margin-left: auto;
        color: #c0c4cc;
      }

      ::v-deep .uni-tag {
        display: inline-block;
        background-color: transparent;
        // border: 1px solid #FF9900;
        border: none;
        color: #ff9900;
        padding: 4rpx 0rpx !important;
        font-size: 24rpx;
        font-weight: 400;
        margin-right: 12rpx;
        border-radius: 8rpx;
        margin-bottom: 6rpx;

        &:last-of-type {
          margin-right: 0;
        }
      }
    }

    .info-content {
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      font-size: 24rpx;
      color: #606266;

      .info-collaborator {
        flex-shrink: 0;
      }

      .info-user {
        padding-right: 30rpx;
        position: relative;

        &::after {
          position: absolute;
          right: 0;
          top: 0;
          content: '';
          width: 1px;
          height: 100%;
          background-color: $uni-line-style-color-three;
        }
      }

      .info-time {
        padding-left: 30rpx;
      }

      .user-collaborator {
        display: flex;
        flex: 1;

        .collaborator-list {
          display: flex;
          align-items: center;
          justify-content: center;
          margin-right: 16rpx;

          .avatar {
            width: 32rpx;
            height: 32rpx;
            border-radius: 100%;
            margin-right: 8rpx;
          }

          .collaborator-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200rpx;
          }
        }
      }
    }

    .icon-content {
      padding: 16px 12rpx 18px 12rpx;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;

      .icon-content-item {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;

        .icon-box {
          width: 80rpx;
          height: 80rpx;
        }

        .iconfont {
          font-size: 42rpx;
          color: $uni-color-primary;
        }

        .name {
          margin-top: 4rpx;
          font-size: 24rpx;
          color: $uni-text-color;
        }
      }
    }
  }

  ::v-deep .uni-textarea-placeholder {
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 13px;
    color: #c0c4cc;
  }

  .icon-jinru-copy {
    cursor: pointer;
  }

  .customer-info {
    .bagf {
      background-color: #fff;
    }
  }
}

.popovers-container {
  display: flex;
  justify-content: space-around;
  /* Or 'flex-start' to have them at the beginning */
}

.name-content {
}

.bagf {
  min-height: 952rpx;
  background-color: #ffffff;
}

.reord-header {
  width: 100%;
  display: flex;
  justify-content: space-between;
}

.search-default-label {
  font-size: 26rpx;
  font-weight: 500;
}

.picker-selector .icon-fanhui {
  display: inline-block;
  transform: rotate(270deg);
  font-size: 20rpx;
  position: relative;
  top: -1px;
}

.m15 {
  padding: 15px;
  background-color: #fff;
}

.ml4 {
  display: inline-block;
  margin-left: 8rpx;
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

::v-deep .follow-main {
  margin-top: 0;
}

.genjinBox {
  width: 100%;
  height: 194px;
  border-radius: 6px 6px 6px 6px;
  border: 1px solid #eeeeee;
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

.modal-items {
  z-index: 99;
  color: #333333;
  text-align: left;
  font-size: 28rpx;
  display: flex;
  align-items: center;
  font-weight: normal;
  margin-bottom: 44rpx !important;

  &:last-of-type {
    margin-bottom: 0;
  }

  .iconfont {
    font-size: 30rpx;
    color: $nui-text-color-four;
    margin-right: 12rpx;
  }
}

.no-bottom-margin {
  margin-bottom: 0 !important;
}

.uni-picker-header {
  border-bottom: 1px solid #e5e5e5;
  width: 100%;
  height: 90rpx;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;

  .uni-picker-action {
    max-width: 50%;
    top: 0;
    height: 100%;
    box-sizing: border-box;
    padding: 0 14px;
    font-size: 30rpx;
    line-height: 90rpx;
    cursor: pointer;

    &.uni-picker-action-cancel {
      color: #888;
    }

    &.uni-picker-action-confirm {
      color: #007aff;
    }
  }
}

.box:last-child {
  margin-bottom: 0;
}

.picker-view {
  width: 750rpx;
  height: 480rpx;
  background-color: #fff;
}

.item-value {
  display: flex;
  justify-content: center;
  align-items: center;
}

::v-deep .examine-content-list .uni-list-item:first-child .uni-list-item__container {
  padding-top: 0;
}

.add-record-title {
  color: #2b2c32;
  font-size: 30rpx;
  padding: 30rpx;
  background: #fff;

  .add-btn {
    cursor: pointer;
    font-weight: 400;
    color: #308bf8;
    font-size: 26rpx;
    margin-left: auto;
    display: flex;
    align-items: center;

    .iconfont {
      font-size: 20rpx;
      margin-right: 6rpx;
      transform: translateY(2rpx);
    }
  }
}

// ::v-deep .follow-content  {
//   padding-top: 0 !important;
// }

.record-account {
  display: flex;
  align-items: center;
  margin-top: 20rpx;
  font-size: 22rpx;
  color: #606266;

  text {
    color: #303133;
    font-size: 500;
  }

  view {
    margin-right: 32rpx;
  }
}

::v-deep .uni-scroll-view-content {
  overflow-x: scroll;
  overflow-y: hidden;
}

.customer-tab {
  display: flex !important;
  justify-content: space-between !important;

  .customer-tab-item {
    margin-right: 0 !important;
  }
}
</style>
