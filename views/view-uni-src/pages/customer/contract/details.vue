<template>
  <view class="content">
    <!-- 顶部导航内容 -->
    <view class="sticky-navbar">
      <default-nav-bar :is-jump-bar="false" :is-right="true" :right-data="rightIcon" @handleNarItem="handleNarItem"> </default-nav-bar>
    </view>
    <view class="cr-position-header">
      <view id="customer-header-body">
        <view class="details-content plr10">
          <view class="company-name line1 mb12">
            <text>
              {{ data.detail.contract_no }}
              <text class="iconfont icon-shequ-shoucang-yishoucang" @click="clickFollow(0)" v-if="data.detail.contract_followed == 1"></text>
              <text class="iconfont icon-shequ-shoucang1" @click="clickFollow(1)" v-if="data.detail.contract_followed != 1"></text>
            </text>
            <text
              class="status-tag"
              v-if="data.detail.contract_status"
              :style="{
                color: data.detail.contract_status.color ? data.detail.contract_status.color : '#1890ff',
                background: data.detail.contract_status.color ? getColor(data.detail.contract_status.color, '0.1') : getColor('#1890ff', '0.1'),
              }"
              >{{ $ts(data.detail.contract_status.name) }}</text
            >
          </view>
          <view class="info-content display-align mb12"> {{ $t('ui.customerContractDetailsOrderNo') }}{{ data.detail.contract_no || '--' }} </view>
          <view class="info-content display-align mb12"> {{ $t('ui.customerContractDetailsCustomerName') }}{{ data.detail.customer_name || '--' }} </view>

          <view class="info-content">
            {{ $t('ui.customerContractDetailsOrderAmount') }}{{ data.detail.price || '--' }}
            <!-- <text class="line" />
            起止时间：
            <template v-if="data.detail.start_date">
              <uni-dateformat format="yyyy/MM/dd" :date="data.detail.start_date"></uni-dateformat>
              ～
              <uni-dateformat format="yyyy/MM/dd" :date="data.detail.end_date"></uni-dateformat>

            </template>
<template v-else>--</template> -->
          </view>
        </view>
      </view>
    </view>

    <!-- 导航栏 -->
    <view class="customer-nav">
      <view class="customer-content">
        <view class="customer-tab">
          <view
            class="customer-tab-item"
            :data-index="index"
            v-for="(item, index) in tabData"
            :class="item.id === data.currentId ? 'active' : ''"
            :key="item.id"
            @click="changeTab(index)"
          >
            {{ item.name }}
          </view>
          <view class="underlineBox">
            <view class="underlineBox"></view>
          </view>
        </view>
      </view>
    </view>

    <!-- 合同详情内容 -->
    <view class="customer">
      <!--动态记录 -->
      <view v-if="data.currentId === 6" :class="data.momentList.length > 2 ? 'pb60' : ''">
        <follow-record :followList="data.momentList" :showTitle="false" :title="$t('ui.customerContractDetailsActivityRecords')"> </follow-record>
      </view>

      <!-- 基本信息 -->
      <view v-if="data.currentId === 1" class="bgf">
        <customerInfo :customerData="data.detailValue" :id="data.id" :type="`contract`" @refreshDetails="refreshDetails"></customerInfo>
      </view>

      <!-- 合同信息 -->
      <view v-if="data.currentId === 7" class="bgf pb60">
        <detailItem v-if="detail.length > 0 || detail.id" :detail="detail" @getDetails="getSigningInfo" />
        <view v-else class="bgf">
          <view class="addSing" @click="addSign()"><text class="iconfont icon-biaodan-chengyuantianjia" />{{ $t('ui.examineFormApprovalBillAdd') }}</view>

          <empty :index="7" :title="$t('ui.customerContractContractListNoContractData')" class="bgf" style="height: calc(100vh - 300rpx)"></empty>
        </view>
      </view>

      <!-- 产品信息 -->

      <view v-if="data.currentId == 8">
        <ProductList :list-data="data.product" :moreShow="true" />
      </view>

      <!-- 账目记录 -->
      <view v-if="data.currentId === 3" :class="data.paymentList.length > 1 ? 'pb60' : ''">
        <view class="record-header bgf plr30">
          <view class="left">
            <text class="record-name">{{ $t('ui.customerContractDetailsOrderAmount') }}</text>
            <text class="record-number">{{ data.detail.price }}</text>
          </view>
          <view class="left">
            <text class="record-name">{{ $t('ui.customerContractDetailsAmountReceived') }}</text>
            <text class="record-number">{{ data.paymentPrice }}</text>
          </view>
          <view class="left">
            <text class="record-name">{{ $t('ui.customerContractDetailsOutstandingAmount') }}</text>
            <text class="record-number" style="color: #e93323">{{ data.auditPrice }}</text>
          </view>
          <view class="left">
            <text class="record-name">{{ $t('ui.customerContractDetailsExpenseAmount') }}</text>
            <text class="record-number">{{ data.expensePrice }}</text>
          </view>
        </view>
        <view class="search-box">
          <picker class="picker-selector" mode="selector" @change="pickerChange($event, data.currentId)" :range="data.payTypes" range-key="name">
            <view class="search-default-label">{{ data.payTypesText }} <text class="iconfont icon-jinru"></text></view>
          </picker>
          <view class="add" @click="bottomBtnList(2)"><text class="iconfont icon-biaodan-chengyuantianjia" />{{ $t('ui.examineFormApprovalBillAdd') }}</view>
        </view>
        <payment-record class="bgf plr30" :list-data="data.paymentList" :count="data.paymentCount" :cid="data.id"></payment-record>
        <view class="footer-text" v-if="data.paymentList.length > 0 && data.paymentCount <= data.paymentList.length"> {{ $t('ui.customerListFollowRecordNoMore') }} </view>
      </view>

      <!-- 付款提醒 -->
      <view v-if="data.currentId === 2" :class="data.remindList.length > 1 ? 'pb60' : ''">
        <view class="search-box bgf">
          <picker class="picker-selector" mode="selector" @change="pickerChange($event, data.currentId)" :range="data.remindTypes" range-key="name">
            <view class="search-default-label">{{ data.payTypesText }} <text class="iconfont icon-jinru"></text></view>
          </picker>
          <view class="add" @click="bottomBtnList(1)"><text class="iconfont icon-biaodan-chengyuantianjia" />{{ $t('ui.examineFormApprovalBillAdd') }}</view>
        </view>
        <pay-remind class="bgf plr30" :list-data="data.remindList" :buildData="data.buildData"></pay-remind>
        <view class="footer-text" v-if="data.remindList.length > 0 && data.remindCount <= data.remindList.length"> {{ $t('ui.customerListFollowRecordNoMore') }} </view>
      </view>

      <!-- 发票 -->
      <view v-if="data.currentId === 4" :class="data.invoiceList.length > 1 ? 'pb60' : ''">
        <view class="search-box">
          <picker class="picker-selector" mode="selector" @change="pickerChange($event, data.currentId)" :range="data.invoiceTypes" range-key="name">
            <view class="search-default-label">{{ data.payTypesText }} <text class="iconfont icon-jinru"></text></view>
          </picker>
        </view>
        <invoice-list
          class="bgf plr30"
          :list-data="data.invoiceList"
          :type="1"
          :eid="data.detail.eid"
          :cid="data.id"
          :name="data.detail.client ? data.detail.client.name : ''"
          :empty-title="$t('ui.customerContractDetailsNoInvoiceApplicationsYetApplyNow')"
        >
        </invoice-list>
        <view class="footer-text" v-if="data.invoiceList.length > 0 && data.invoiceCount <= data.invoiceList.length"> {{ $t('ui.customerListFollowRecordNoMore') }} </view>
      </view>

      <!-- 附件 -->
      <view v-if="data.currentId === 5">
        <view class="search-box">
          <view />
          <view class="add" @click="bottomBtnList(4)"><text class="iconfont icon-biaodan-chengyuantianjia" />{{ $t('ui.examineFormApprovalBillAdd') }}</view>
        </view>
        <file-record
          class="bgf plr30"
          style="padding-left: 10rpx"
          direction="column"
          :active="0"
          :list-data="data.fileList"
          :empty-title="$t('ui.customerContractDetailsNoUploadedDocuments')"
        ></file-record>
      </view>
    </view>

    <!-- 底部操作 -->
    <view class="details-fixed-btn">
      <view @click="bottomBtnList(1)" class="box">
        <text class="iconfont icon-tixing"></text>
        {{ $t('ui.customerContractDetailsReminder') }}
      </view>
      <view class="btn-line" />
      <view @click="bottomBtnList(2)" class="box"><text class="iconfont icon-tianjiahuikuan"></text>{{ $t('ui.customerContractDetailsAddAccount') }}</view>
      <template v-if="data.customer_switch.invoice_module_switch">
        <view class="btn-line" />
        <view @click="bottomBtnList(3)" class="box"><text class="iconfont icon-shenqingfapiao" />{{ $t('ui.customerContractDetailsApplyForInvoice') }}</view>
      </template>
    </view>
    <!--添加弹窗-->
    <add-popup ref="addPopupRef" :dataList="data.listIcon" @handleClickItem="addRecord"></add-popup>
    <!-- 操作弹窗 -->
    <more-popup ref="customerMoreRef" @handleItem="dropDownItem"></more-popup>

    <globalIndex />
  </view>
</template>

<script setup>import appI18n from '@/locale';

import defaultNavBar from '@/components/defaultNavBar/index'
import empty from '@/components/empty/index.vue'
import ProductList from '@/pages/customer/opportunity/components/product-list.vue'
import detailItem from '@/pages/customer/signing/components/detailItem.vue'
import globalIndex from '@/components/globalIndex/index.vue'
import customerInfo from '@/pages/customer/list/components/customerInfo.vue'
import followRecord from '@/pages/customer/list/components/followRecord.vue'
import addPopup from '@/pages/customer/list/components/addPopup.vue'
import payRemind from './components/payRemind.vue'
import { getContractDocEditApi } from '@/api/signing'
import morePopup from '@/components/morePopup/index.vue'
import paymentRecord from '@/pages/customer/list/components/paymentRecord.vue'
import fileRecord from './components/fileRecord.vue'
import invoiceList from '@/pages/customer/invoice/components/invoiceList.vue'
import { reactive, ref } from 'vue'
import message from '@/utils/message'
import { useStore } from 'vuex'
import { getColor } from '@/utils/helper'
import { configApproveApi, contractEditInfoApi, momentRecordApi, clientContractStatusApi } from '@/api/customer'
import { isModuleSwitchEnabled, CUSTOMER_MODULE_KEYS } from '@/utils/customerSwitch'
const store = useStore()
const data = reactive({
  id: 0, // 订单id
  eid: 0, // 客户id
  currentId: 1,
  customer_switch: JSON.parse(uni.getStorageSync('storageUserData')).enterprise.customer_switch,
  detail: {},
  listIcon: [],

  payTypes: [
    {
      id: '',
      name: '全部',
    },
    {
      id: '0',
      name: '回款',
    },
    {
      id: '1',
      name: '续费',
    },
    {
      id: '2',
      name: '支出',
    },
  ],
  remindTypes: [
    {
      id: '',
      name: '全部',
    },
    {
      id: '0',
      name: '回款提醒',
    },
    {
      id: '1',
      name: '续费提醒',
    },
  ],
  invoiceTypes: [
    {
      id: '',
      name: '全部',
    },
    {
      id: '1',
      name: '个人普通发票',
    },
    {
      id: '2',
      name: '企业普通发票',
    },
    {
      id: '3',
      name: '企业专用发票',
    },
  ],
  payTypesText: '全部',
  paymentPrice: 0,
  auditPrice: 0,
  expensePrice: 0,
  paymentList: [],

  remindLoaded: false,
  remindWhere: {
    page: 1,
    limit: 10,
    types: '',
    cid: '',
  },
  billLoaded: false,
  billWhere: {
    page: 1,
    limit: 5,
    types: '',
    cid: '',
  },
  paymentCount: 0,
  invoiceLoaded: false,
  invoiceWhere: {
    page: 1,
    limit: 10,
    types: '',
    cid: '',
  },
  invoiceCount: 0,

  momentLoaded: false,
  momentList: [],
  where: {
    page: 1,
    limit: 10,
  },

  detailValue: [],
  remindList: [],
  remindCount: 0,
  invoiceList: [],
  invoiceCount: 0,
  fileLoaded: false,
  fileWhere: {
    page: 1,
    limit: 10,
    cid: '',
  },
  product: [],
  fileList: [],
  jumpUrl: '',

  buildData: {},
})

const detail = ref({})

const rightIcon = reactive([{ type: 1, icon: 'icon-gengduo1', types: 'icon' }])

const forumMeus = reactive([
  { name: '编辑', id: 1, icon: 'icon-gongzuohuibao-bianji' },
  { name: '转移', id: 2, icon: 'icon-danchuang-zhuanyi' },
  { name: '删除', id: 3, icon: 'icon-shanchu1' },
])

// tab切换
const tabData = reactive([
  { name: '基本信息', id: 1 },
  { name: '合同信息', id: 7 },
  { name: '产品清单', id: 8 },
  { name: '账目记录', id: 3 },
  { name: '付款提醒', id: 2 },
  { name: '发票', id: 4 },
  { name: '记录', id: 5 },
  { name: '动态记录', id: 6 },
])

import { onLoad, onShow } from '@dcloudio/uni-app'
onShow(() => {
  data.jumpUrl = toJumpUrl()
  getDetails(data.id)
  if (data.currentId == 7) {
    getSigningInfo()
  }
})

onLoad((e) => {
  data.id = e.id
  getDetails(data.id)
  getMomentList()
  getPrice()
  getConfigApprove()

  if (e.tab) {
    data.currentId = tabData[Number(e.tab)].id
    tabLoadingList()
  }
  data.jumpUrl = toJumpUrl()

  // 客户权限控制
  if (!data.customer_switch.contract_module_switch) {
    tabData.splice(1, 1)
  }
  if (!data.customer_switch.invoice_module_switch) {
    let index = tabData.findIndex((item) => item.id === 4)
    tabData.splice(index, 1)
  }
})
const getConfigApprove = () => {
  configApproveApi().then((res) => {
    data.buildData = res.data
  })
}

const toJumpUrl = () => {
  // 判断跳转地址
  const formType = store.state.app.customerFormType
  let url = ''
  if (formType.type && formType.type === 'list') {
    url = `/pages/customer/list/contract?eid=${formType.eid}`
  } else {
    url = '/pages/customer/contract/index'
  }
  return url
}
const addSign = () => {
  clickNavigateTo(`/pages/customer/signing/addForm?eid=${data.eid}&cid=${data.id}`)
}

// 筛选
const pickerChange = (e, id) => {
  const { value } = e.detail
  const map = {
    2: { types: data.remindTypes, where: data.remindWhere, api: getRemindList },
    3: { types: data.payTypes, where: data.billWhere, api: getBillList },
    4: { types: data.invoiceTypes, where: data.invoiceWhere, api: getInvoiceList },
  }
  const cfg = map[id]
  if (!cfg) return
  data.payTypesText = cfg.types[value].name
  cfg.where.types = cfg.types[value].id
  data.billLoaded = false
  data.remindLoaded = false
  data.invoiceLoaded = false
  cfg.api(true)
}

// 关注/取消关注
const clickFollow = (status) => {
  clientContractStatusApi(data.id, status, {
    status: status,
  }).then((res) => {
    message.error(res.message)
    data.detail.contract_followed = status
  })
}

const refreshDetails = () => {
  // getSigningInfo()
  getDetails(data.id)
}

// 获取合同详情
const getSigningInfo = () => {
  let obj = {
    link_type: 'contract',
  }
  getContractDocEditApi(data.id, obj)
    .then((res) => {
      detail.value = res.data
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const addPopupRef = ref(null)

// 底部操作
const bottomBtnList = (e) => {
  if (e === 1) {
    // 添加提醒
    clickNavigateTo(`/pages/customer/contract/addRemind?cid=${data.id}&eid=${data.eid}&name=${data.detail.customer_name}`)
  } else if (e === 2) {
    // 添加账目
    data.listIcon = [
      {
        image: '/static/image/add-huikuan.png',
        name: '添加回款',
        type: 'add_payment',
        value: 1,
      },
      {
        image: '/static/image/add-xufei.png',
        name: '添加续费',
        type: 'add_payment',
        value: 2,
      },
      {
        image: '/static/image/add-zhichu.png',
        name: '添加支出',
        type: 'add_payment',
        value: 3,
      },
    ]
    addPopupRef.value.popupOpen(data.listIcon)
  } else if (e === 3) {
    // 添加发票
    let dataInfo = { eid: data.eid, cid: data.id }
    unInvoicedListApi(dataInfo).then((res) => {
      if (res.data.length > 0) {
        clickNavigateTo(`/pages/customer/invoice/checkPayment?id=${data.buildData.invoicing_switch}&eid=${data.eid}&cid=${data.id}`)
      } else {
        clickNavigateTo(`/pages/users/examine/default?id=${data.buildData.invoicing_switch}&eid=${data.eid}&cid=${data.id}&types=contract`)
      }
    })
  } else if (e === 4) {
    // 添加记录
    clickNavigateTo(`/pages/customer/contract/addFile?cid=${data.id}`)
  }
}

// 顶部操作
const customerMoreRef = ref(null)
const handleNarItem = (e) => {
  customerMoreRef.value.popupOpen(forumMeus)
}

const dropDownRef = ref(null)

const dropDownItem = (e) => {
  if (e.id === 1) {
    clickNavigateTo(`/pages/customer/contract/addContract?cid=${data.id}&eid=${data.eid}&name=${data.detail.customer_name}`)
  }
  if (e.id === 2) {
    clickNavigateTo(`/pages/customer/list/shift?type=2&cid=${data.id}`)
  }
  if (e.id === 3) {
    let cid = data.id
    showModal(appI18n.global.t('ui.customerContractDetailsDeleteThisOrder'))
      .then(() => {
        contractDeleteApi(cid)
          .then((res) => {
            message.success(res.message)
            const url = toJumpUrl()
            delayedReLaunch(url)
          })
          .catch((error) => {
            message.error(error.message)
          })
      })
      .catch(() => {
        console.log('取消了')
      })
  }
}

// 添加账目
const addRecord = (val) => {
  let type = val.value
  if (type == 3) {
    // 添加支出
    clickNavigateTo(`/pages/users/examine/default?id=${data.buildData.contract_disburse_switch}&cid=${data.id}&types=contract`)
  } else if (type == 1) {
    // 添加回款
    clickNavigateTo(`/pages/users/examine/default?id=${data.buildData.contract_refund_switch}&cid=${data.id}&types=contract`)
    // 添加续费
  } else if (type == 2) {
    clickNavigateTo(`/pages/users/examine/default?id=${data.buildData.contract_renew_switch}&cid=${data.id}&types=contract`)
  }
}

const changeTab = (index) => {
  if (index === 3) {
    getPrice()
  }
  data.currentId = tabData[index].id
  if (data.currentId == 7) {
    getSigningInfo()
  }
  tabLoadingList()
}

const tabLoadingList = () => {
  if (data.currentId === 2) {
    // 付款提醒
    data.remindWhere.page = 1
    getRemindList(true)
  } else if (data.currentId === 3) {
    // 付款记录
    data.billWhere.page = 1
    getBillList(true)
  } else if (data.currentId === 4) {
    // 发票
    data.invoiceWhere.page = 1
    getInvoiceList(true)
  } else if (data.currentId === 5) {
    // 发票
    data.fileWhere.page = 1
    getFlieList(true)
  }
}

// 菜单导航
import { clickNavigateTo, delayedReLaunch, showModal } from '@/utils/helper'
import { uploadFlie } from '@/utils/file'

const getPrice = () => {
  clientcontractStatisticsApi(data.id).then((res) => {
    data.auditPrice = res.data.unpaid_price
    data.paymentPrice = res.data.payment_price
    data.expensePrice = res.data.expense_price
  })
}
const scrollTo = () => {
  if (data.currentId == 3) {
    clickNavigateTo(`/pages/customer/contract/addPayment?cid=${data.id}&eid=${data.eid}`)
  }
  if (data.currentId == 2) {
    clickNavigateTo(`/pages/customer/contract/addRemind?cid=${data.id}&eid=${data.eid}`)
  }
  if (data.currentId == 4) {
    let dataInfo = { eid: data.detail.eid }
    unInvoicedListApi(dataInfo).then((res) => {
      if (res.data.length > 0) {
        clickNavigateTo(`/pages/customer/invoice/checkPayment?eid=${data.eid}&name=${data.detail.client.name}&cid=${data.id}`)
      } else {
        clickNavigateTo(`/pages/customer/invoice/addInvoice?eid=${data.eid}&name=${data.detail.client.name}&cid=${data.id}`)
      }
    })
  }
  if (data.currentId == 5) {
    const datas = { cid: data.id, eid: data.eid }
    uploadFlie('client/file/upload', datas)
      .then((res) => {
        if (res.status) {
          changeTab(4)
        }
      })
      .catch((error) => {
        message.error(error.message)
      })
  }
}

import {
  billListApi,
  contractRemindListApi,
  clientInvoiceApi,
  clientContractResourceListApi,
  contractDeleteApi,
  unInvoicedListApi,
  clientcontractStatisticsApi,
} from '@/api/customer'

// 获取客户详情
const getDetails = (id) => {
  contractEditInfoApi(id)
    .then((res) => {
      data.detail = res.data.data
      data.detailValue = res.data.form
      data.eid = res.data.data.eid
      data.product = res.data.product || []
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const listLoading = ref(false) // 是否正在加载
// 获取付款记录列表
const getBillList = (tab = false) => {
  if (data.billLoaded) return
  data.billWhere.cid = data.id
  billListApi(data.billWhere)
    .then((res) => {
      if (data.billWhere.page === 1) {
        data.paymentList = res.data.list
      } else {
        data.paymentList.push(...res.data.list)
      }
      data.paymentCount = res.data.count
      data.billLoaded = data.paymentList.length >= res.data.count
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// 获取动态记录
const getMomentList = () => {
  if (data.momentLoaded) return
  momentRecordApi({
    eid: data.id,
    link_type: 'contract',
    page: data.where.page,
    limit: data.where.limit,
  })
    .then((res) => {
      if (data.where.page === 1) {
        data.momentList = res.data.list
      } else {
        data.momentList.push(...res.data.list)
      }
      data.momentLoaded = data.momentList.length >= res.data.count
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// 获取付款提醒列表
const getRemindList = (tab = false) => {
  if (data.remindLoaded) return
  data.remindWhere.cid = data.id
  contractRemindListApi(data.remindWhere)
    .then((res) => {
      if (tab) data.remindList = []
      if (data.remindWhere.page === 1) {
        data.remindList = res.data.list
      } else {
        data.remindList.push(...res.data.list)
      }
      data.remindCount = res.data.count
      data.remindLoaded = data.remindList.length >= res.data.count
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// 获取发票列表
const getInvoiceList = (tab = false) => {
  if (data.invoiceLoaded) return
  data.invoiceWhere.eid = data.eid
  clientInvoiceApi(data.invoiceWhere)
    .then((res) => {
      if (data.invoiceWhere.page === 1) {
        data.invoiceList = res.data.list
      } else {
        data.invoiceList.push(...res.data.list)
      }
      data.invoiceCount = res.data.count
      data.invoiceLoaded = data.invoiceList.length >= res.data.count
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// 获取附件列表
const getFlieList = (tab = false) => {
  if (data.fileLoaded) return
  data.fileWhere.cid = data.id
  clientContractResourceListApi(data.fileWhere)
    .then((res) => {
      if (data.fileWhere.page === 1) {
        data.fileList = res.data.list
      } else {
        data.fileList.push(...res.data.list)
      }
      data.fileLoaded = data.fileList.length >= res.data.count
    })
    .catch((error) => {
      message.error(error.message)
    })
}

import { onReachBottom } from '@dcloudio/uni-app'
// 下拉加载
onReachBottom(() => {
  if (data.currentId === 2) {
    data.remindWhere.page++
    getRemindList()
  } else if (data.currentId === 3) {
    data.billWhere.page++
    getBillList()
  } else if (data.currentId === 4) {
    data.invoiceWhere.page++
    getInvoiceList()
  } else if (data.currentId === 5) {
    data.fileWhere.page++
    getFlieList()
  } else if (data.currentId === 6) {
    data.where.page++
    getMomentList()
  }
})
</script>

<style>
/* page {
  background-color: #fff;
} */
</style>

<style scoped lang="scss">
@import '../components/common.scss';

.sticky-navbar {
  background-color: #fff;
  padding-top: var(--status-bar-height);
  position: sticky;
  top: 0;
  z-index: 1;
}

.customer-nav {
  border-top: 16rpx solid #f5f5f5;
  position: sticky;
  top: calc(var(--status-bar-height) + 88rpx - 16rpx);
  z-index: 40;
}

.customer-tab-scroll {
  white-space: nowrap;
  height: 84rpx;
  width: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;

  &::-webkit-scrollbar {
    display: none;
  }
}

.customer-right {
  display: flex;
  align-items: center;
  justify-content: flex-end;

  .icon-add {
    font-size: 42rpx;
    color: $uni-text-color;
  }
}

.icon-shequ-shoucang-yishoucang {
  cursor: pointer;
  color: #f90;
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

.content {
  width: 100%;
  // background-color: #fff;

  .icon-xuanfuanniu-jia {
    color: red;
    margin-left: 20rpx;
    margin-bottom: 2rpx;
  }

  .cr-position-header {
    position: initial;
  }

  .details-content {
    position: relative;
    padding-top: 22rpx;
    padding-bottom: 30rpx;

    .mb12 {
      margin-bottom: 24rpx;
    }

    .company-name {
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 500;
      font-size: 30rpx;
      color: #303133;
      display: flex;
      justify-content: space-between;
    }

    .info-content {
      font-weight: 400;
      font-size: 24rpx;
      color: #606266;
      margin-top: 20rpx;
      height: 34rpx;
      line-height: 34rpx;
    }

    .line {
      display: inline-block;
      width: 2rpx;
      height: 22rpx;
      background: #eeeeee;
      margin: 0 14rpx;
    }

    .icon-content {
      padding: 36rpx 12rpx;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;

      .icon-content-item {
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

  .customer {
    width: 100%;

    ::v-deep .follow-main {
      margin-top: 0;
    }

    .record-header {
      display: flex;
      flex-wrap: wrap;
      gap: 18rpx;

      padding-top: 28rpx;

      font-family:
        PingFang SC,
        PingFang SC;

      .left {
        width: 336rpx;
        height: 64rpx;
        padding: 0 20rpx;
        background: #f7f7f7;
        border-radius: 8rpx;
        line-height: 64rpx;
      }

      .record-name {
        font-weight: 400;
        font-size: 24rpx;
        color: #606266;
      }

      .record-number {
        font-weight: 500;
        font-size: 26rpx;
        color: #303133;
      }
    }
  }
}

.plr30 {
  padding: 0 30rpx;
}

::v-deep .product-list-wrap {
  padding: 0 30rpx;
}

.search-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20rpx 30rpx;

  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 500;
  font-size: 13px;
  color: #303133;
  background-color: #fff;

  .icon-jinru {
    color: #606266;
    font-size: 11px;
  }

  .add {
    color: #308bf8;
    font-weight: normal;
    font-size: 26rpx;

    .icon-biaodan-chengyuantianjia {
      cursor: pointer;
      font-size: 26rpx;
      margin-right: 4rpx;
    }
  }
}

.addSing {
  cursor: pointer;
  padding-right: 30rpx;
  height: 86rpx;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  color: #308bf8;
  font-weight: normal;
  font-size: 26rpx;

  .icon-biaodan-chengyuantianjia {
    cursor: pointer;
    font-size: 26rpx;
    margin-right: 4rpx;
  }
}

#customer-header-body {
  background-color: #fff;
}

.customer-tab {
  width: 100%;
  overflow-x: auto;
  display: flex !important;
  gap: 30rpx;
  padding-right: 30rpx;
  box-sizing: border-box;
  -webkit-overflow-scrolling: touch;

  /* #ifndef H5 */
  scrollbar-width: none;

  &::-webkit-scrollbar {
    display: none;
  }

  /* #endif */

  .customer-tab-item {
    flex-shrink: 0;
    white-space: nowrap;
    margin-right: 0 !important;
  }
}
</style>
