<template>
  <view class="content">
    <view class="sticky-navbar">
      <default-nav-bar :is-jump-bar="false" :is-right="true" :right-data="rightIcon" @handleNarItem="handleNarItem" />
    </view>
    <view class="cr-position-header">
      <view id="customer-header-body">
        <view class="details-content">
          <view class="company-name line1" v-if="detail.status"
            >{{ detail.doc_name }}
            <text
              class="status-tag"
              :style="{
                color: statusList[detail.status].color,
                background: getColor(statusList[detail.status].color, '0.1'),
              }"
              >{{ statusList[detail.status].name }}</text
            >
          </view>
          <view class="info-content"> 客户名称：{{ detail.customer && detail.customer.customer_name ? detail.customer.customer_name : '--' }} </view>
          <view class="info-content">
            业务员：{{ detail.admin && detail.admin.name ? detail.admin.name : '--' }}
            <text class="line" />
            起止时间：
            <template v-if="detail.start_date">
              <uni-dateformat format="yyyy/MM/dd" :date="detail.start_date"></uni-dateformat>
              ～
              <uni-dateformat format="yyyy/MM/dd" :date="detail.end_date"></uni-dateformat>
            </template>
            <template v-else>--</template>
          </view>
        </view>
      </view>
    </view>
    <!-- 导航栏 -->
    <view class="customer-nav">
      <view class="customer-content">
        <view class="customer-tab">
          <uni-row class="plr10 display-align">
            <uni-col :span="21" class="display-align">
              <view
                class="customer-tab-item"
                v-for="(item, index) in tabData"
                :class="item.id === data.currentId ? 'active' : ''"
                :key="item.id"
                @click="changeTab(item)"
              >
                {{ item.name }}
              </view>
            </uni-col>
          </uni-row>
        </view>
      </view>
    </view>

    <view class="customer">
      <view v-if="data.currentId === 1">
        <follow-record :followList="data.momentList" :showTitle="false" :title="'动态记录'"> </follow-record>
      </view>
      <view v-if="data.currentId === 2">
        <detailItem :detail="detail" @getDetails="getDetails" />
      </view>
      <view v-if="data.currentId === 3">
        <paymentTable :detail="detail" :listData="data.orderList" />
        <view class="footer-text" v-if="data.orderList.length > 0 && data.count <= data.orderList.length">-没有更多了-</view>
      </view>
    </view>
    <!-- 操作弹窗 -->
    <more-popup ref="customerMoreRef" :dataList="forumMeus[detail.status]" @handleItem="handleDropDownClick"></more-popup>
    <globalIndex />
  </view>
</template>

<script setup>
import defaultNavBar from '@/components/defaultNavBar/index'
import globalIndex from '@/components/globalIndex/index.vue'
import followRecord from '@/pages/customer/list/components/followRecord.vue'
import detailItem from './components/detailItem.vue'
import paymentTable from './components/paymentTable.vue'
import morePopup from '@/components/morePopup/index.vue'
import { reactive, ref } from 'vue'
import message from '@/utils/message'
import { useStore } from 'vuex'
import { getColor } from '@/utils/helper'
import { getContractDocEditApi, getContractDocOrdersApi, contractDocCancelApi, deleteContractDocApi, signFileUploadApi } from '@/api/signing'
import { momentRecordApi } from '@/api/customer'
const store = useStore()
const data = reactive({
  id: null, // 合同订单

  currentId: 2,
  momentLoaded: false,
  orderLoading: false,
  orderLoaded: false,
  momentList: [],
  orderList: [],
  count: 0,
})
const detail = ref({})
const statusList = ref({
  '-1': {
    name: '审批驳回',
    color: '#ED4014',
  },
  0: {
    name: '待处理',
    color: '#FFC107',
  },
  1: {
    name: '待审核',
    color: '#409EFF',
  },
  2: {
    name: '待签约',
    color: '#19BE6B',
  },
  3: {
    name: '已签约',
    color: '#409EFF',
  },
  4: {
    name: '已拒绝',
    color: '#909399',
  },
  5: {
    name: '已过期',
    color: '#909399',
  },
  6: {
    name: '已撤销',
    color: '#909399',
  },
})
const rightIcon = reactive([{ type: 1, icon: 'icon-gengduo1', types: 'icon' }])

const forumMeus = reactive({
  1: [
    // 待审核
    { name: '撤销申请', id: 1 },
    { name: '关联订单', id: 2 },
  ],
  2: [
    // 待签约
    { name: '签约变更', id: 3 },
    { name: '关联订单', id: 2 },
    { name: '撤销签约', id: 1 },
    { name: '删除', id: 5 },
  ],
  3: [
    // 已签约
    { name: '关联订单', id: 2 },
    { name: '删除', id: 5 },
  ],
  4: [
    // 已拒绝
    { name: '重新签约', id: 6 },
    { name: '签约变更', id: 3 },
    { name: '删除', id: 5 },
  ],
  5: [
    // 已过期
    { name: '重新签约', id: 6 },
    { name: '签约变更', id: 3 },
    { name: '删除', id: 5 },
  ],
  6: [
    // 已撤销
    { name: '重新签约', id: 6 },
    { name: '签约变更', id: 3 },
    { name: '删除', id: 5 },
  ],
  '-1': [
    // 已过期
    { name: '重新签约', id: 6 },
    { name: '签约变更', id: 3 },
    { name: '删除', id: 5 },
  ],
})
import { onLoad } from '@dcloudio/uni-app'

const momentWhere = ref({
  page: 1,
  limit: 10,
})
const orderWhere = ref({
  page: 1,
  limit: 10,
})
onLoad((e) => {
  if (e.tab) {
    data.currentId = Number(e.tab)
  }
  if (e.id) {
    data.id = Number(e.id)

    freshData()
  }
})

const dropDownRef = ref(null)
const customerMoreRef = ref(null)
const handleNarItem = (e) => {
  // 待签约 签约录入
  if (detail.value.status == 2 && detail.value.sign_type == 1) {
    customerMoreRef.value.popupOpen([{ name: '签约录入', id: 7 }, ...forumMeus[detail.value.status]])
  } else {
    customerMoreRef.value.popupOpen(forumMeus[detail.value.status])
  }
}

// tab切换
const tabData = reactive([
  { name: '基本信息', id: 2 },
  { name: '订单记录', id: 3 },
  { name: '动态记录', id: 1 },
])
const changeTab = (item) => {
  data.currentId = item.id
}
function freshData() {
  getDetails()
  refreshDynamicRecord()
  refreshOrderRecord()
}

// 刷新动态记录
const refreshDynamicRecord = () => {
  momentWhere.value.page = 1
  getMomentList()
}

// 刷新关联订单
const refreshOrderRecord = () => {
  orderWhere.value.page = 1
  getContractDocOrders()
}
// 获取关联订单
const getContractDocOrders = () => {
  if (data.orderLoaded) return
  getContractDocOrdersApi(data.id, orderWhere.value)
    .then((res) => {
      if (orderWhere.value.page === 1) {
        data.orderList = res.data.list
      } else {
        data.orderList.push(...res.data.list)
      }
      data.count = res.data.count
      data.orderLoaded = data.orderList.length >= res.data.count
    })
    .catch((error) => {
      message.error(error.message)
    })
}
// 获取动态记录
const getMomentList = () => {
  if (data.momentLoaded) return
  uni.showLoading({
    title: '加载中',
  })
  momentRecordApi({
    eid: data.id,
    link_type: 'contract_doc',
    ...momentWhere.value,
  })
    .then((res) => {
      uni.hideLoading()
      if (momentWhere.value.page === 1) {
        data.momentList = res.data.list
      } else {
        data.momentList.push(...res.data.list)
      }

      data.momentLoaded = data.momentList.length >= res.data.count
    })
    .catch((error) => {
      uni.hideLoading()
      message.error(error.message)
    })
}
// 合同签约的其操作
const handleDropDownClick = (item) => {
  if (item.id == 1) {
    // 撤销申请
    handleCancel()
  } else if (item.id == 5) {
    // 删除
    handleDelete()
  } else if (item.id == 3) {
    // 签约变更
    handleSign('edit')
  } else if (item.id == 6) {
    // 重新签约
    handleSign('add')
  } else if (item.id == 2) {
    // 关联订单
    let value = encodeURIComponent(JSON.stringify(detail.value))
    let type = 'order'
    clickNavigateTo(`/pages/customer/signing/orderList?detail=${value}&type=${type}`)
  } else if (item.id == 7) {
    // 签约录入
    handleAddFile()
  }
}

import { uploadFlie } from '@/utils/file'
// 签约录入
const handleAddFile = () => {
  uploadFlie('common/upload', {}, 100).then((res) => {
    if (res.status == 200) {
      signFileUploadApi(detail.value.id, {
        file_id: res.data.id,
      }).then((val) => {
        if (val.status == 200) {
          getDetails()
          uni.showToast({
            title: '签约文件上传成功',
            icon: 'success',
          })
        }
      })
    }
  })
}

// 跳转签约/变更页面
const handleSign = (type) => {
  const query = {
    id: data.id,
    type,
    eid: detail.value.eid,
  }
  clickNavigateTo(`/pages/customer/signing/addForm?id=${query.id}&type=${query.type}&eid=${query.eid}`)
}

// 撤销申请
const handleCancel = () => {
  uni.showModal({
    title: '提示',
    content: '您确定要撤销此合同申请吗?',
    success: (res) => {
      if (res.confirm) {
        contractDocCancelApi(data.id)
          .then((res) => {
            if (res.status === 200) {
              message.success(res.message)
              getDetails(data.id)
            }
          })
          .catch((error) => {
            message.error(error.message)
          })
      }
    },
  })
}
// 删除
const handleDelete = () => {
  uni.showModal({
    title: '提示',
    content: '您确定要删除此合同吗?',
    success: (res) => {
      if (res.confirm) {
        deleteContractDocApi(data.id)
          .then((res) => {
            if (res.status === 200) {
              message.success(res.message)
              clickNavigateTo('/pages/customer/signing/index')
            }
          })
          .catch((error) => {
            message.error(error.message)
          })
      }
    },
  })
}

// 菜单导航
import { clickNavigateTo, delayedReLaunch, showModal, getPhoneInfo } from '@/utils/helper'

// 获取合同详情
const getDetails = () => {
  getContractDocEditApi(data.id)
    .then((res) => {
      detail.value = res.data
      try {
        const userInfo = uni.getStorageSync('storageUserData')

        if (userInfo && detail.value && detail.value.admin && detail.value.admin.id) {
          let userId = JSON.parse(userInfo).userInfo.id
          if (detail.value.admin.id != userId) {
            rightIcon.splice(0, rightIcon.length)
          }
        }
      } catch (error) {
        console.error('获取用户信息失败:', error)
      }
    })
    .catch((error) => {
      message.error(error.message)
    })
}
const listLoading = ref(false) // 是否正在加载

import { onReachBottom } from '@dcloudio/uni-app'
// 下拉加载
onReachBottom(() => {
  if (data.currentId == 1) {
    momentWhere.value.page++
    getMomentList()
  } else if (data.currentId == 3) {
    orderWhere.value.page++
    getContractDocOrders()
  }
})
</script>

<style scoped lang="scss">
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

.content {
  width: 100%;
  .details-content {
    position: relative;
    padding: 20rpx 30rpx 30rpx 30rpx;
    font-family:
      PingFang SC,
      PingFang SC;
    background-color: #ffffff;

    .company-name {
      display: flex;
      justify-content: space-between;
      font-weight: 500;
      font-size: 30rpx;
      color: #303133;
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
  }

  .customer-nav {
    width: 100%;
    padding-top: 16rpx;
    background-color: $uni-default-bg;
    position: sticky;
    top: calc(var(--status-bar-height) + 44px - 16rpx);
    z-index: 1;

    .customer-content {
      background-color: #fff;
      width: 100%;
      font-family:
        PingFang SC,
        PingFang SC;

      .customer-tab {
        height: 84rpx;
        border-bottom: 2rpx solid #eeeeee;

        ::v-deep .uni-row {
          height: 100%;

          .uni-col {
            height: 100%;
          }
        }

        .customer-tab-item {
          font-weight: 400;
          font-size: 26rpx;
          color: #606266;
          margin-right: 38rpx;
          cursor: pointer;
          height: 100%;
          line-height: 40px;
          text-wrap: nowrap;

          &.active {
            color: $uni-text-color;
            /* 核心：设置渐变背景 */
            background-image: linear-gradient(90deg, #1890ff 0%, rgba(24, 144, 255, 0.45) 100%);
            /* 裁剪背景，只显示底部1px */
            background-size: 48rpx 5rpx;
            /* 第二个值是边框高度 */
            background-repeat: no-repeat;
            background-position: bottom;
            /* 避免背景和内容重叠 */
            background-clip: padding-box;
            border-bottom: none;
            /* 清空默认边框 */
            // border-radius: 20px;
          }

          &:last-of-type {
            margin-right: 0;
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
      }
    }
  }
}
::v-deep .follow-main {
  margin-top: 0;
}
</style>
