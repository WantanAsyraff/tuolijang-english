<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item, index) in listData" :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list">
            <view class="item-list-button">
              <uni-row class="item-bottom display-align">
                <uni-col :span="16" class="item-bottom-left">
                  <avatar v-if="item.work_customer" :src="item.work_customer.avatar" :radius="8"></avatar>
                  <avatar v-else src="/static/image/list/touxiang.png" :radius="8"></avatar>
                </uni-col>
                <uni-col :span="8" class="item-bottom-right">
                  <view class="item-bottom-right-top">
                    <uni-row>
                      <uni-col :span="18" class="display-align" style="cursor: pointer">
                        <text class="name">{{ item.liaison_name || '--' }}</text>
                        <!--  <image src="../../../../static/image/qiwei.png" class="qiwei"
                          @click.stop="openCustomerChat(item.work_customer)" v-if="item.work_customer"></image> -->

                        <text
                          v-if="item.e06d7153.value != 0"
                          class="iconfont"
                          :class="item.e06d7153.value == 1 ? 'icon-xingbie-nan' : 'icon-xingbie-nv'"
                          :style="{ color: item.e06d7153.value == 1 ? '#1890FF' : '#FF2529' }"
                        ></text>
                        <text v-else></text>
                      </uni-col>
                      <uni-col :span="6" class="text-right">
                        <view class="qiweiBox" @click.stop="openCustomerChat(item.work_customer)">
                          <image src="../../../../static/image/qiwei.png" class="qiwei" v-if="item.work_customer"> </image>
                          {{ $t('ui.customerListLiaisonListChat') }}
                        </view>
                        <dean-popover model-direction="right" :id="item.id" ref="deanPopoverRef" :index="index">
                          <template #icon>
                            <text class="iconfont icon-yunwenjian-gengduo"></text>
                          </template>
                          <view class="modal-item" @click="changePopover(item, index, 1)">
                            <text class="iconfont icon-gongzuohuibao-bianji"></text>{{ $t('ui.customerQuickReplyIndexEdit') }}
                          </view>
                          <view class="modal-item" @click="changePopover(item, index, 2)"> <text class="iconfont icon-shanchu1"></text>{{ $t('ui.examineFormApprovalBillDelete') }} </view>
                        </dean-popover>
                      </uni-col>
                    </uni-row>
                  </view>
                  <view class="item-bottom-time">
                    {{ item.liaison_job ? item.liaison_job.name : '--' }}
                  </view>
                </uni-col>
              </uni-row>
            </view>
            <uni-row class="item-list-content">
              <uni-col :span="4" class="left">{{ $t('ui.customerInvoiceAddInvoiceTelephone') }}</uni-col>
              <uni-col @click="call(item.liaison_tel)" class="phone" :span="20">{{ item.liaison_tel || '--' }}</uni-col>
            </uni-row>

            <uni-row class="item-list-content">
              <uni-col :span="4" class="left">{{ $t('ui.usersCenterIndexEmail') }}</uni-col>
              <uni-col :span="20">{{ item.liaison_email || '--' }}</uni-col>
            </uni-row>
            <uni-row class="item-list-content">
              <uni-col :span="4" class="left">{{ $t('ui.customerListLiaisonListWeChat') }}</uni-col>
              <uni-col :span="20">{{ item.liaison_wechat || '--' }}</uni-col>
            </uni-row>
            <uni-row class="item-list-content">
              <uni-col :span="4" class="left">{{ $t('ui.customerContractPayDetailRemarks') }}</uni-col>
              <uni-col :span="20">{{ item.l753bf282 || '--' }}</uni-col>
            </uni-row>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="8" :title="emptyTitle"></empty>
  </view>
</template>

<script setup>
import { WxWork, isWxWorkEnv } from '@/libs/wxwork'
import empty from '@/components/empty/index.vue'
import avatar from '@/components/avatar/index.vue'
import deanPopover from '@/components/deanPopover/index.vue'
import { toRefs, onMounted } from 'vue'
import { liaisonDeleteApi, salesmanCustomApi } from '@/api/customer'
import message from '@/utils/message'
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
  emptyTitle: {
    type: String,
    default: '',
  },
  eid: {
    type: Number,
    default: 0,
  },
})
const { listData, emptyTitle, eid } = toRefs(props)

import { clickNavigateTo } from '@/utils/helper'

const changePopover = (item, index, type) => {
  if (type === 1) {
    clickNavigateTo(`/pages/customer/list/addLiaison?eid=${eid.value}&id=${item.id}`)
  }

  if (type === 2) {
    let liaisonId = item.id
    uni.showModal({
      title: '提示',
      content: '您确定要删除该客户联系人吗?',
      success: (res) => {
        if (res.confirm) {
          liaisonDeleteApi(liaisonId)
            .then((res) => {
              message.success(res.message)
              listData.value.splice(index, 1)
            })
            .catch((error) => {
              message.error(error.message)
            })
        }
      },
    })
  }
}
// 打开客户聊天对话框
const openCustomerChat = async (item) => {
  if (!isWxWorkEnv) return message.error('只有在企业微信中可进行聊天')
  if (!item) {
    return false
  }
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
const call = (item) => {
  uni.makePhoneCall({
    phoneNumber: item, // 电话号码
    success: function () {
      console.log('拨打电话成功')
    },
    fail: function () {
      console.error('拨打电话失败')
    },
  })
}
</script>

<style scoped lang="scss">
.examine-content-list {
  ::v-deep .uni-list {
    background-color: $uni-default-bg;

    .uni-list--border {
      top: auto;
      left: auto;
    }

    .uni-list-item {
      margin-bottom: 20rpx;
      border-radius: 8rpx;

      .uni-list-item__container {
        padding: 24rpx 24rpx 40rpx 24rpx;
      }
    }
  }

  .item-list {
    width: 100%;
    position: relative;

    .item-list-top {
      font-size: 32rpx;
      font-weight: 600;
      padding-bottom: 24rpx;
      color: $uni-text-color;
    }

    .item-list-content {
      font-size: 28rpx;
      color: $uni-text-color;
      font-weight: 400;
      margin-top: 30rpx;
      display: flex;
      // align-items: flex-end;

      .left {
        color: $nui-text-color-four;
      }

      .phone {
        color: #6196d6;
      }
    }

    .item-list-button {
      display: flex;
      justify-content: space-between;
      border-bottom: 1px solid $uni-line-style-color-three;
      padding-bottom: 24rpx;
      width: 100%;

      .item-bottom {
        width: 100%;

        .item-bottom-left {
          width: 80rpx;
          height: 80rpx;
        }

        .item-bottom-right {
          width: calc(100% - 80rpx);
          padding-left: 20rpx !important;
          font-size: 28rpx;
          color: $nui-text-color-four;
          height: 80rpx;
          display: flex;
          justify-content: space-between;
          flex-direction: column;
          line-height: 1.2;

          .item-bottom-right-top {
            .name {
              font-size: $uni-font-size-default;
              font-weight: $uni-default-font-weight;
              color: $uni-text-color;
            }

            .iconfont {
              font-size: 30rpx;
              font-weight: normal;
              margin-left: 16rpx;
            }
          }

          .item-bottom-time {
            font-size: 24rpx;
          }
        }
      }
    }
  }
}

::v-deep .modal-content {
  width: 180rpx;
}

.text-right {
  display: flex;
  align-items: center;
}

.qiweiBox {
  width: 48px;
  height: 22px;
  background: rgba(24, 144, 255, 0.08);
  border-radius: 4px 4px 4px 4px;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 24rpx;
  color: #1890ff;
  display: flex;
  align-items: center;
  justify-content: center;

  .qiwei {
    width: 28rpx;
    height: 28rpx;
    margin-right: 4rpx;
  }
}
</style>
