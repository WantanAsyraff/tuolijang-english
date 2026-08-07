<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item, index) in listData" :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list">
            <view @click="examineList(item)" class="p30">
              <view class="item-list-top">
                <view>
                  {{ item.customer_name || '--' }}
                  <text class="iconfont icon-shequ-shoucang-yishoucang" v-if="item.customer_followed == 1"></text>
                </view>
                <text
                  class="status-tag"
                  :style="{
                    color: item.customer_status.color ? item.customer_status.color : '#1890ff',
                    background: item.customer_status.color ? getColor(item.customer_status.color, '0.1') : getColor('#1890ff', '0.1'),
                  }"
                  v-if="item.customer_status"
                  >{{ $ts(item.customer_status.name) }}</text
                >
              </view>
              <uni-row class="item-list-content" v-if="item.work_customer">
                <uni-col :span="5" class="left">{{ $t('ui.customerLeadLeadListWeComCustomer') }}</uni-col>
                <uni-col
                  :span="19"
                  style="display: flex; align-items: center"
                  @click.stop="openCustomerChat(item.work_customer)"
                  v-if="item.work_customer.name"
                >
                  <image :src="item.work_customer.avatar" class="img"></image>
                  <text class="mr4"> {{ item.work_customer.name }}</text>
                  <text class="work-icon over-text" :class="item.work_customer.type != 1 ? 'work-name' : ''">{{
                    item.work_customer.type == 1 ? $t('ui.customerLeadLeadListWeChat') : '@' + item.work_customer.corp_name || '--'
                  }}</text>
                </uni-col>

                <uni-col :span="19" v-else>--</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerSigningDetailItemContactPhone') }}</uni-col>
                <uni-col :span="19">{{ item.customer_tel || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content tag-list">
                <uni-col :span="5" class="left">{{ $t('ui.customerListCustomerMoreCustomerLabels') }}</uni-col>
                <uni-col :span="19">
                  <view class="label-text" v-if="item.customer_label && item.customer_label.length > 0">
                    <text v-for="(val, index) in item.customer_label" :key="index">
                      {{ val.name }} <text v-if="index < item.customer_label.length - 1">、</text>
                    </text>
                  </view>
                  <view class="label-text" v-else> -- </view>
                </uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerListCustomerListDefaultOwner') }}</uni-col>
                <uni-col :span="19">{{ item.salesman.name || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerListBusinessFollowFollowUpTime') }}</uni-col>
                <uni-col :span="19">
                  <uni-dateformat v-if="item.last_follow_time" format="yyyy/MM/dd hh:mm" :date="item.last_follow_time"></uni-dateformat>
                  <text v-else>--</text>
                </uni-col>
              </uni-row>
            </view>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="7" :title="emptyTitle" class="bgf" style="height: calc(100vh - 300rpx)"></empty>
    <!-- 新增 -->
    <view class="add">
      <text class="iconfont icon-xuanfuanniu-jia" @click="createCustomer"></text>
    </view>
  </view>
</template>

<script setup>import appI18n from '@/locale';

import empty from '@/components/empty/index.vue'
import avatar from '@/components/avatar/index.vue'
import { getColor } from '@/utils/helper'
import message from '@/utils/message'
import { WxWork } from '@/libs/wxwork'
const props = defineProps({
  listData: {
    type: Array,
    default() {
      return []
    },
  },
  typeIndex: {
    type: Number,
    default: 0,
  },
  types: {
    type: Number,
    default: 0,
  },
  emptyTitle: {
    type: String,
    default: '',
  },
})
const { listData, emptyTitle, types } = toRefs(props)
import { clickNavigateTo } from '@/utils/helper'
const examineList = (item) => {
  clickNavigateTo(`/pages/customer/list/details?id=${item.id}&&types=${types.value}`)
}

const editFormRef = ref(null)

import { clientStatusApi } from '@/api/customer'
const clickFollow = (index, item) => {
  clientStatusApi(item.id, item.customer_followed == 0 ? 1 : 0)
    .then((res) => {
      message.success(res.message)
      item.customer_followed = item.customer_followed == 0 ? 1 : 0
    })
    .catch((error) => {
      message.error(error.message)
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

const getAbnormalText = (row) => {
  let str = ''
  if (row.customer_status == 0) {
    str = '未成交'
  } else if (row.customer_status == 1) {
    str = '已成交'
  } else if (row.customer_status == 2) {
    str = '已流失'
  }
  return str
}
const createCustomer = (e) => {
  clickNavigateTo(`/pages/customer/list/addCustomer?types=${types.value}`)
}
</script>

<style scoped lang="scss">
.icon-shequ-shoucang-yishoucang {
  color: #f90;
}

.p24 {
  padding-left: 24rpx;
  padding-right: 24rpx;
}

.mr4 {
  white-space: nowrap;
  margin-right: 8rpx;
}

.img {
  width: 40rpx;
  height: 40rpx;
  border-radius: 50%;
  margin-right: 12rpx;
}

.label-text {
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.examine-content-list {
  ::v-deep .uni-list {
    background-color: $uni-default-bg;

    .uni-list--border {
      top: auto;
      left: auto;
    }

    .uni-list-item {
      margin-top: 8rpx;
      border-radius: 8rpx;

      .uni-list-item__container {
        padding: 36rpx 0rpx;
      }
    }
  }

  .status-tag {
    margin-left: 16rpx;
    min-width: 68rpx;
    height: 42rpx;
    border-radius: 8rpx;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24rpx;
    font-weight: 400;
    padding: 0 10rpx;
  }

  .item-list {
    width: 100%;
    position: relative;
    .p30 {
      padding: 0 30rpx;
    }
    .item-list-top {
      width: 100%;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      font-size: 32rpx;
      padding-bottom: 24rpx;
      color: $uni-text-color;
      font-family:
        PingFang SC-中黑体,
        PingFang SC;
      font-weight: 500;
      display: flex;
      justify-content: space-between;
    }

    .work-icon {
      font-family:
        PingFang SC,
        PingFang SC;
      font-weight: 400;
      font-size: 12px;
      color: #1cbf6c;
    }

    .work-name {
      color: #ff9900;
      font-size: 12px;
    }

    .item-list-content {
      font-size: 24rpx;
      color: $uni-text-color;
      font-weight: 400;
      margin-bottom: 12rpx;
      display: flex;
      align-items: flex-end;

      &.tag-list {
        align-items: flex-start;
      }

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

    ::v-deep .avatar-image {
      border-radius: 50%;
    }

    .item-list-button {
      margin-top: 24rpx;
      display: flex;
      justify-content: space-between;
      border-top: 1px solid $uni-line-style-color-three;
      padding: 24rpx 0;
      width: 100%;

      .item-bottom {
        height: 52rpx;
        width: 100%;
        display: flex;
        align-items: center;

        .item-bottom-text {
          // margin-bottom: 15rpx;
          padding-left: 16rpx;
          color: $uni-text-color;
          font-size: 28rpx;
        }

        .item-bottom-right {
          height: 52rpx;
          font-size: 28rpx;
          color: $nui-text-color-four;
          // margin-bottom: 15rpx;

          .iconfont {
            font-size: 32rpx;
            margin-right: 4rpx;
          }
        }
      }
    }

    .item-list-status {
      position: absolute;
      top: -36rpx;
      right: -24rpx;
      width: 160rpx;
      height: 188rpx;
    }
  }
}

.align {
  display: flex;
  line-height: 52rpx;
}
.add {
  cursor: pointer;
  position: fixed;
  right: 20rpx;
  bottom: 140rpx;
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #47b5ff 0%, #0f86f5 100%);
  box-shadow: 0px 4px 4px 0px rgba(28, 146, 248, 0.1145);
  border-radius: 50%;
  text-align: center;
  line-height: 42px;
  color: #fff;

  .icon-xuanfuanniu-jia {
    font-size: 15px;
  }
}
</style>
