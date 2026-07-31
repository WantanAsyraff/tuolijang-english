<template>
  <!-- <BaseContainer> -->
  <view class="content">
    <view class="cr-position-header">
      <view id="customer-header-body">
        <NavBar v-if="!isWxWorkEnv" :is-right="true" :is-jump-bar="false" :right-data="rightIcon" @handleNarItem="handleNavIconClick"> </NavBar>

        <view class="detail-banner" v-if="leadInfo">
          <view class="company-header">
            <view @click.stop="openCustomerChat(leadInfo.work_customer)" class="display-align">
              <image :src="leadInfo.work_customer?.avatar" class="img" v-if="leadInfo.work_customer?.avatar"></image>
              <text class="name">{{ leadInfo.name }}</text>
              <text :class="leadInfo.work_customer?.type != 1 ? 'work-name1' : 'work-name'" v-if="leadInfo.work_customer?.type">
                {{ leadInfo.work_customer.type == 1 ? '@微信' : '@' + leadInfo.work_customer.corp_name || '--' }}
              </text>

              <text class="iconfont icon-shequ-shoucang-yishoucang" @click.stop="clickFollow(0)" v-if="leadInfo.followed == 1"></text>
              <text class="iconfont icon-shequ-shoucang1" @click.stop="clickFollow(1)" v-if="leadInfo.followed != 1"></text>
            </view>
            <view
              class="status-tag"
              v-if="leadInfo?.status.name"
              :style="{
                color: leadInfo.status.color ? leadInfo.status.color : '#1890ff',
                background: leadInfo.status.color ? getColor(leadInfo.status.color, '0.1') : getColor('#1890ff', '0.1'),
              }"
              >{{ leadInfo.status.name }}
            </view>
          </view>

          <view class="customer-info">
            业务员：{{ salesInfo || '--' }}
            <text class="divider"></text>
            添加时间： {{ moment(leadInfo?.created_at).format('YYYY/MM/DD HH:mm:ss') }}
          </view>
          <view class="label-content mb12" @click="handleShowTagPopup">
            <template v-if="customerLabelLength">
              <view v-for="(item, index) in leadInfo.customer_label" :key="index">
                <text class="uni-tag">{{ item.name }} {{ (index as number) < customerLabelLength - 1 ? ' 、' : '' }}</text>
              </view>
              <!-- <uni-tag class="lext" :text="item.name" type="warning" size="mini"
                v-for="(item, index) in leadInfo.customer_label" :key="index" /> -->
            </template>
            <view v-else>
              <view class="add-label">
                <text class="iconfont icon-xuanfuanniu-jia"></text>
                <text>添加标签</text>
              </view>
            </view>
            <view class="iconfont icon-jinru-copy"></view>
          </view>
        </view>
      </view>
      <!-- 导航栏 -->
      <view class="customer-nav" :style="{ borderTop: data.scrollTop >= 100 ? 'none' : '16rpx solid #f5f5f5' }">
        <view class="customer-content">
          <view class="customer-tab">
            <scroll-view scroll-x="true" style="white-space: nowrap; display: flex; height: 60rpx" scroll-with-animation show-scrollbar="true">
              <view
                class="customer-tab-item"
                v-for="(item, index) in data.tabData"
                :class="index === data.currentIndex ? 'active' : ''"
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
    </view>

    <!-- 基本信息 -->
    <view class="basic-info">
      <view v-if="data.currentIndex === 1" style="background-color: #fff; padding-right: 15px" class="pb60">
        <customerInfo class="bag" :customerData="fieldConfig" :id="leadId" :type="`clue`" @refreshDetails="refreshDetails" />
      </view>
      <!-- 跟进记录 -->
      <view v-else-if="data.currentIndex === 0" class="pb60">
        <view class="m15">
          <view class="genjinBox">
            <uni-easyinput
              :inputBorder="false"
              v-model="formData.content"
              type="textarea"
              :clearable="false"
              :maxlength="256"
              :autoHeight="true"
              placeholder="填写跟进记录"
              class="mb10"
            >
            </uni-easyinput>

            <view class="btn-box">
              <view class="addfujian" @click="uploadAvatar"> <text class="iconfont icon-fujian"></text> 添加附件</view>
              <view class="btn" @click="handleConfirm">确定</view>
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
        <follow-record :followList="data.momentList" :showTitle="false" :title="'动态记录'" />
      </view>
    </view>
    <!-- 底部操作 -->
    <view class="details-fixed-btn" style="width: 361rpx">
      <view @click="bottomBtnListFn(1)" class="box"><text class="iconfont icon-zhuankehu"></text>转客户</view>
      <view class="btn-line" />
      <view @click="bottomBtnListFn(2)" class="box"><text class="iconfont icon-dadianhua" />打电话</view>
    </view>
    <!-- 操作弹窗 -->
    <more-popup ref="customerMoreRef" @handleItem="dropDownItem"></more-popup>
    <selected-label title="客户标签" ref="selectedLabelRef" @changeItem="changeItem" />
    <!-- 备注弹窗 -->
    <textarea-popup ref="textareaPopupRef" :config-data="data.configData" @change="changePop" />
    <!-- </BaseContainer> -->
  </view>
</template>

<script setup lang="ts">
import { uploadImage, formatBytes } from '@/utils/file'
import { reactive, ref, getCurrentInstance, nextTick } from 'vue'
import { getColor, getPhoneInfo } from '@/utils/helper'
import { isWxWorkEnv, WxWork } from '@/libs/wxwork'

import NavBar from '@/components/defaultNavBar/index.vue'
import morePopup from '@/components/morePopup/index.vue'
import textareaPopup from '@/components/textareaPopup/index.vue'
import customerInfo from '@/pages/customer/list/components/customerInfo.vue'
import selectedLabel from '@/pages/customer/list/components/selectedLabel.vue'
import followRecord from '@/pages/customer/list/components/followRecord.vue'
import {
  leadFollowApi,
  leadShiftApi,
  leadClaimApi,
  leadRefundApi,
  leadDeleteApi,
  momentRecordApi,
  followSaveApi,
  leadEditFormApi,
  clientToCustomerApi,
  clientCluesLabelApi,
} from '@/api/customer'
import message from '@/utils/message'
import moment from 'moment'
const rightIcon = ref([
  {
    type: 1,
    icon: 'icon-gengduo1',
    types: 'icon',
    text: '更多',
  },
])
const formData = ref({
  content: '',
  attach_ids: [],
  types: 0,
  eid: 0,
  link_type: 'clue',
})

const clientInfo = ref({})
const customerMoreRef = ref(null)
const data = reactive({
  wxwork_userid: '',
  tabData: [
    {
      name: '动态记录',
      id: 2,
    },
    {
      name: '基本信息',
      id: 1,
    },
  ],
  scrollTop: 0,
  statusBarHeight: 20,
  configData: {
    title: '退回线索池',
    placeholder: '说明原因',
    type: '',
    text: '',
    refundType: 0, // 4 -> 客户公海 802 -> 线索池
  },
  types: 'clue',
  imgs: [],
  currentIndex: 0,

  momentList: [],
  momentLoaded: false,
})

const customerLabelLength = computed(() => leadInfo.value?.customer_label?.length || 0)

const momentWhere = ref({
  page: 1,
  limit: 10,
})
import { fileSizeOne, delayedReLaunch, debounce, clickNavigateTo } from '@/utils/helper'
const salesInfo = ref(null)
const leadInfo = ref(null)
const fieldConfig = ref([])
const leadId = ref(null)
const wwUserId = ref(null)

// 固定顶部nav
const cHeight = ref(0)
const topHeight = ref(0)
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

const handleChangeTab = (item: any, index: number) => {
  data.currentIndex = index
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
    message.success('打开会话框')
  } catch (err) {
    message.error(`打开个人资料页失败: ${err.errMsg || err.message || '操作失败'}`)
  }
}

const bottomBtnListFn = (status) => {
  if (status === 1) {
    shiftCustomerFn()
  } else if (status === 2) {
    // 打电话
    callPhoneFn()
  }
}

// 关注/取消关注
const clickFollow = (status) => {
  leadFollowApi(leadId.value, status, {
    status: status,
  }).then((res) => {
    message.success(res.message)
    leadInfo.value.followed = status
  })
}

// 打开标签
const selectedLabelRef = ref(null)
const handleShowTagPopup = () => {
  const id = leadInfo.value.customer_label?.map((item) => item.id) ?? []
  selectedLabelRef.value.popupOpen(id)
}

// 标签选择回调
const changeItem = (labels) => {
  clientCluesLabelApi(leadId.value, { label: labels }).then((res) => {
    message.success(res.message)
    getLeadDetail()
  })
}

const getOptionId = (item) => {
  if (item && typeof item === 'object') return item.id ?? item.value
  return item
}

const normalizeLabelIds = (value) => {
  if (Array.isArray(value)) return value.map(getOptionId).filter((item) => item !== undefined && item !== null && item !== '')
  if (typeof value === 'string') return value.split(',').map((item) => item.trim()).filter(Boolean)
  return value ? [getOptionId(value)] : []
}

const normalizeLabels = (value) => {
  if (Array.isArray(value)) return value
  if (typeof value === 'string') return value.split(',').map((item) => item.trim()).filter(Boolean)
  return value ? [value] : []
}

const getLeadCustomerLabels = () => {
  const rawHeaderLabels = leadInfo.value?.customer_label || []
  const headerLabels = normalizeLabelIds(rawHeaderLabels)
  if (headerLabels.length > 0) return headerLabels

  const labelField = fieldConfig.value
    .map((item) => item.data || [])
    .flat()
    .find((item) => item.key === 'customer_label' || item.type === 'customer_label')

  const fieldLabels = normalizeLabelIds(labelField?.value)
  if (fieldLabels.length > 0) return fieldLabels
  const textLabels = normalizeLabels(labelField?.text)
  if (textLabels.length > 0) return textLabels
  return rawHeaderLabels
}

// 转客户
const shiftCustomerFn = () => {
  if (clientInfo.value.customer) {
    uni.showModal({
      title: '提示',
      content: '存在关联的企微客户,是否转客户?',
      success: (res) => {
        if (res.confirm) {
          clientToCustomerApi(clientInfo.value.id)
            .then((res) => {
              if (res.status === 200) {
                message.success(res.message)
                if (isWxWorkEnv) {
                  uni.redirectTo({
                    url: '/pages/customer/list/details?id=' + res.data.id,
                  })
                } else {
                  delayedReLaunch('/pages/customer/list/index')
                }
              }
            })
            .catch((error) => {
              message.error(error.message)
            })
        }
      },
    })
  } else {
    const randomId = 'lead_' + Math.random().toString(36).substring(2, 15)
    const data = {
      link_id: clientInfo.value.id,
      customer_name: clientInfo.value.customer_name,
      customer_label: getLeadCustomerLabels(),
      customer_tel: clientInfo.value.phone || clientInfo.value.customer_tel || '',
      liaison_name: clientInfo.value.customer_name || '',
      liaison_tel: clientInfo.value.phone || clientInfo.value.customer_tel || '',
      b37a3f16: clientInfo.value.phone,
      area_cascade: clientInfo.value.area_cascade,
      '9bfe77e4': clientInfo.value.address,
      customer_followed: clientInfo.value.followed,
    }
    uni.setStorageSync(randomId, data)
    clickNavigateTo(`/pages/customer/list/addCustomer?lead_data_id=${randomId}`)
  }
}

// 打电话
const callPhoneFn = () => {
  uni.makePhoneCall({
    phoneNumber: clientInfo.value.phone,
    success: (res) => {
      message.success(res.message)
    },
    fail: (error) => {
      message.error(error.message)
    },
  })
}

// 打开操作
const handleNavIconClick = () => {
  const isClue = data.types === 'clue'
  const menuMap = {
    edit: { name: '编辑', id: 1, icon: 'icon-gongzuohuibao-bianji' },
    shift: { name: '移交同事', id: 2, icon: 'icon-danchuang-zhuanyi' },
    refund: { name: '退回线索池', id: 3, icon: 'icon-danchuang-zhuanyi' },
    claim: { name: '领取', id: 5, icon: 'icon-danchuang-zhuanyi' },
    assign: { name: '分配', id: 6, icon: 'icon-shanchu1' },
    del: { name: '删除', id: 4, icon: 'icon-shanchu1' },
  }

  const menuList = isClue ? [menuMap.edit, menuMap.shift, menuMap.refund, menuMap.del] : [menuMap.edit, menuMap.claim, menuMap.assign, menuMap.del]

  customerMoreRef.value.popupOpen(menuList)
}

const textareaPopupRef = ref(null)
const dropDownItem = (item) => {
  const actions = {
    1: () => clickNavigateTo(`/pages/customer/lead/add?id=${leadId.value}&types=${data.types}`), // 编辑
    2: () => handleMoreItem(801), // 移交同事
    3: () => textareaPopupRef.value.popupOpen(), // 退回线索池
    4: () => handleDelete(), // 删除
    5: () => handleClaim(), // 领取
    6: () => handleMoreItem(805), // 分配
  }
  actions[item.id]?.()
}

// 删除线索
const handleDelete = async () => {
  const { confirm } = await uni.showModal({
    title: '提示',
    content: '确定删除线索吗？',
  })
  if (!confirm) return

  try {
    const { message: msg } = await leadDeleteApi(leadId.value)
    message.success(msg)
    delayedReLaunch('/pages/customer/lead/index')
  } catch ({ message: errMsg }) {
    message.error(errMsg)
  }
}

// 退回线索池
const changePop = ({ value }) =>
  leadRefundApi({ reason: value, data: [leadId.value] })
    .then(({ message: msg }) => (message.success(msg), freshData()))
    .catch(({ message: msg }) => message.error(msg))

// 领取线索
const handleClaim = () =>
  uni.showModal({
    title: '提示',
    content: '您确定要领取此线索吗?',
    success: ({ confirm }) =>
      confirm &&
      leadClaimApi({ data: [leadId.value] })
        .then(({ message: msg }) => message.success(msg))
        .catch(({ message: msg }) => message.error(msg)),
  })

// 区分移交类型，分配 or 移交
const switchType = ref<801 | 805 | null>(null)

const handleShift = async (users: any[]) => {
  if (!users.length) return

  const isAssign = switchType.value === 805
  const { confirm } = await uni.showModal({
    title: '提示',
    content: `确定将线索${isAssign ? '分配' : '移交给'}${users[0].name}吗？`,
    confirmText: '确定',
    cancelText: '取消',
  })

  if (!confirm) return

  try {
    await leadShiftApi({ to_uid: users[0].id, data: [leadId.value] })
    message.success('操作成功')
    setTimeout(freshData, 300)
  } catch ({ message: msg }) {
    message.error(msg)
  }
}

const handleMoreItem = (type: number) => {
  // 仅处理“移交同事”或“分配”
  if (type !== 801 && type !== 805) return
  switchType.value = type
  const event = Date.now().toString()
  uni.$once(event, handleShift)
  uni.navigateTo({ url: `/pages/users/department/index?event=${event}` })
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

// 提交跟进记录
const handleConfirm = debounce(() => {
  if (!formData.value.content) {
    message.error('跟进信息不能为空')
    return false
  }
  formData.value.attach_ids = data.imgs.map((item) => item.id)
  formData.value.eid = leadId.value
  const task = followSaveApi(formData.value)
  task
    .then((res) => {
      refreshDynamicRecord()
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
const deleteFile = (val, id) => {
  data.imgs = data.imgs.filter(function (item) {
    return item.id !== id
  })
}

const refreshDetails = () => {
  getLeadDetail()
}

// 获取线索详情
const getLeadDetail = async () => {
  uni.showLoading({ mask: true })
  try {
    let obj = {}
    if (data.wxwork_userid) {
      obj = {
        userid: data.wxwork_userid,
      }
    }

    const res = await leadEditFormApi(leadId.value, obj)

    leadId.value = res.data.data.id
    salesInfo.value = res.data.data.salesman
    leadInfo.value = res.data.data

    fieldConfig.value = res.data.form
    clientInfo.value = res.data.form
      .map((item: any) => item.data)
      .flat()
      .reduce(
        (acc: any, item: any) => {
          acc[item.key] = item.value
          return acc
        },
        {
          id: leadId.value,
          customer_name: res.data.name,
          ...res.data,
        },
      )

    nextTick(() => {
      getCustomerHeight()
    })
  } catch (error) {
    console.log(error)
  } finally {
    uni.hideLoading()
  }
}

const handleGetDynamicRecord = async () => {
  if (data.momentLoaded) return false
  try {
    const res = await momentRecordApi({
      link_type: 'clue',
      eid: leadId.value,
      ...momentWhere.value,
    })
    if (momentWhere.value.page === 1) {
      data.momentList = res.data.list
    } else {
      data.momentList.push(...res.data.list)
    }
    data.momentLoaded = data.momentList.length >= res.data.count
  } catch (error) {
    message.error(error.message)
  }
}

function refreshDynamicRecord() {
  momentWhere.value.page = 1
  data.momentLoaded = false
  handleGetDynamicRecord()
}

function freshData() {
  const task = getLeadDetail()
  const run = () => {
    refreshDynamicRecord()
  }

  if (leadId.value) {
    run()
  } else {
    task.then(run)
  }
}

onLoad(async (options) => {
  if (options.userid) {
    data.wxwork_userid = options.userid
  }
  if (options.id) {
    leadId.value = options.id
  } else {
    leadId.value = 0
  }
  if (options.types) {
    data.types = options.types
  }
})

onShow(() => {
  let statusBarObj = getPhoneInfo()
  data.statusBarHeight = statusBarObj.statusBarHeight
  // #ifdef APP-PLUS
  topHeight.value = data.statusBarHeight + 'px'
  // #endif
  freshData()
})

import { onReachBottom, onPageScroll } from '@dcloudio/uni-app'
onPageScroll((e) => {
  data.scrollTop = e.scrollTop
})

// 下拉加载
onReachBottom(() => {
  if (data.currentIndex == 0) {
    momentWhere.value.page++
    handleGetDynamicRecord()
  }
})
</script>

<style scoped lang="scss">
@import '../components/common.scss';

.cr-position-header {
  position: sticky;
  top: 0px;
}

#customer-header-body {
  padding-top: var(--status-bar-height);
  background-color: #fff;
}

.status-tag {
  height: 42rpx;
  border-radius: 8rpx;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 24rpx;
  font-weight: 400;
  padding: 0 8rpx;
}

.icon-shequ-shoucang-yishoucang {
  cursor: pointer;
  color: #f90;
}

.detail-banner {
  background-color: #fff;
  padding: 24rpx 30rpx 34rpx 30rpx;

  .company-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;

    .iconfont {
      cursor: pointer;
    }

    .name {
      font-weight: 500;
      font-size: 30rpx;
      color: #303133;
      margin-right: 12rpx;
    }

    .work-name {
      font-size: 26rpx;
      color: #1cbf6c;
      margin-right: 12rpx;
    }

    .img {
      width: 64rpx;
      height: 64rpx;
      border-radius: 50%;
      margin-right: 12rpx;
    }

    .work-name1 {
      color: #ff9900;
      font-size: 26rpx;
      margin-right: 12rpx;
    }
  }

  .right-flex {
    display: flex;

    .bar-return {
      cursor: pointer;
      font-size: 34rpx;
      font-weight: 400;
      margin-right: 36rpx;

      &:last-of-type {
        margin-right: 0;
      }

      .active-color {
        color: $nui-text-color-two;
      }
    }
  }

  .customer-info {
    display: flex;
    align-items: center;
    font-weight: 400;
    font-size: 24rpx;
    color: #606266;
    margin-top: 16rpx;

    &:last-child {
      margin-top: 20rpx;
    }

    .divider {
      width: 2rpx;
      height: 26rpx;
      background: #ebeef5;
      margin-inline: 30rpx;
    }
  }
}
::v-deep .flie {
  padding-right: 0;
}
.customer-tab-info {
  padding-right: 0;
}

.m15 {
  padding: 15px;
  background-color: #fff;
}
.pb60 {
  padding-bottom: 60px;
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
    cursor: pointer;
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
    cursor: pointer;
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

.label-content {
  cursor: pointer;
  margin-top: 20rpx;
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
</style>
