<template>
  <view class="content" v-if="data.listData">
    <view class="cr-position-header" @click="changeInput">
      <view class="position nav-bar">
        <default-nav-bar background-color="transparent"></default-nav-bar>
      </view>
      <view class="position examine-info plr10">
        <uni-row class="examine-info-row">
          <uni-col :span="6" class="examine-info-left">
            <avatar :src="data.listData.card ? data.listData.card.avatar : ''" :radius="12"></avatar>
          </uni-col>
          <uni-col :span="18" class="examine-info-right">
            <view class="name">
              {{ data.listData.card ? data.listData.card.name : '--' }}{{ $t('ui.indexIndexS') }}{{ data.listData.approve ? data.listData.approve.name : '--' }}
              <text
                class="status-tag"
                :style="{
                  color: statusList[data.listData.status].color,
                  background: getColor(statusList[data.listData.status].color, '0.1'),
                }"
                >{{ $ts(statusList[data.listData.status].name) }}</text
              >
            </view>
            <view class="time"> {{ $t('ui.usersExamineDefaultsApplicationTime') }}{{ moment(data.listData.created_at).format('YYYY/MM/DD HH:mm') }} </view>
          </uni-col>
        </uni-row>
      </view>
    </view>

    <view class="examine-content" @click="changeInput">
      <view class="steps-content mb10">
        <check-examine-from :options="data.listData.content"></check-examine-from>
        <view v-if="data.listData.apply_id" class="revoke" @click="revokeFn(data.listData.apply_id)">
          {{ $t('ui.usersExamineDefaultsViewApplicationsToWithdraw') }} <text class="iconfont icon-jinru-copy"></text>
        </view>
      </view>

      <view class="steps-content" v-if="data.stepsData && data.stepsData.length > 0">
        <check-steps :options="data.stepsData" active-color="#007AFF"></check-steps>
      </view>

      <view class="replay-con">
        <uni-list :border="false">
          <view class="replay-title" :class="{ 'replay-pb': data.replayData.length == 0 }">{{ $t('ui.usersExamineDefaultsComment') }}{{ data.replayData.length }}）</view>
          <uni-list-item v-for="(item, index) in data.replayData" :key="item.id">
            <!-- 自定义 header -->
            <template v-slot:header>
              <view class="item-list-left">
                <avatar :src="item.card.avatar" :radius="8"></avatar>
              </view>
            </template>
            <!-- 自定义 body -->
            <template v-slot:body>
              <view class="item-list-right">
                <uni-row class="right-top">
                  <uni-col :span="12">{{ item.card.name }}</uni-col>
                  <uni-col :span="12" class="text-right">
                    <uni-dateformat format="MM/dd hh:mm" :date="item.created_at"></uni-dateformat>
                  </uni-col>
                </uni-row>
                <uni-row class="right-info">
                  <uni-col :span="20" :class="userInfo.uid !== item.card.uid ? 'width100' : ''">{{ item.content }} </uni-col>
                  <uni-col :span="4" class="text-right" v-if="userInfo.uid === item.card.uid">
                    <text class="iconfont icon-shanchu right-info-text" @click="handleDelete(item, index)"></text>
                  </uni-col>
                  <uni-col :span="24">
                    <view class="flie" v-if="item.files.length > 0">
                      <view class="box" v-for="(file, index2) in item.files" :key="index2" @click="preview(file)">
                        <view class="left">
                          <image v-if="isFileTypeIcon(file.name) == 'image.png'" class="slot-image" :src="file.src"> </image>
                          <image v-else class="slot-image" :src="`/static/image/cloudfile/${isFileTypeIcon(file.name)}`"> </image>

                          <view style="width: calc(100% - 40px)">
                            <view class="name">
                              {{ file.name }}
                            </view>
                            <view class="size"> {{ formatBytes(file.size) || '--' }} </view>
                          </view>
                        </view>
                      </view>
                    </view>
                  </uni-col>
                </uni-row>
              </view>
            </template>
          </uni-list-item>
        </uni-list>
      </view>
    </view>

    <view class="examine-bottom" v-if="data.typeIndex != -1">
      <view class="footer-box">
        <view class="examine-bottom-reply" @click="examineReply">
          <image class="reply-image" src="/static/image/attendance/liuyan.png" mode=""></image>
          <view class="name">{{ $t('ui.usersExamineDefaultsComment2') }}</view>
        </view>
        <view class="sign" v-if="data.typeIndex != -1 && data.examineData.verify_status === 0 && data.examineData.approve.types !== 11">
          <dean-popover ref="deanPopoverRef" model-direction="right" leftNum="46px" :btnList="data.btnList" @select="selectFn">
            <template #icon>
              <view class="examine-bottom-reply">
                <image class="reply-image" src="/static/image/attendance/gengduo.png" mode=""></image>
                <view class="name">{{ $t('ui.moduleListMore') }}</view>
              </view>
            </template>
          </dean-popover>
        </view>

        <template v-if="data.typeIndex != -1 && data.listData.status !== 1 && data.listData.verify_status == 0">
          <button type="warn" plain="true" @click="handleRefuse()"><text class="iconfont icon-shenpizhongxin-jujue"></text>{{ $t('ui.financePaymentDetailsRefuse') }}</button>
          <button type="primary" plain="true" @click="handleAgree()"><text class="iconfont icon-shenpizhongxin-tongyi"></text>{{ $t('ui.financePaymentDetailsAgree') }}</button>
        </template>
      </view>
    </view>

    <view class="details-fixed-btn" v-if="data.typeIndex == -1">
      <view @click="examineReply" class="box">
        <text class="iconfont icon-huifu"></text>
        {{ $t('ui.usersExamineDefaultsComment2') }}
      </view>
      <view class="btn-line" v-if="![2, -1].includes(data.listData.status)" />
      <view class="box" v-if="data.listData.status === 0 && data.listData.verify_status !== 0" @click="urgeFn()"
        ><text class="iconfont icon-cuiban"></text>
        <text>{{ $t('ui.usersExamineDefaultsSendReminder') }}</text>
      </view>
      <view class="btn-line" v-if="data.listData.status === 0 && data.listData.verify_status !== 0" />

      <view class="box" v-if="data.listData.approve.types < 6" @click="resubmit()"><text class="iconfont icon-zaicitijiao" />{{ $t('ui.usersExamineDefaultsResubmit') }}</view>
      <view class="btn-line" v-if="data.listData.approve.types < 6" />
      <template v-if="![2, -1].includes(data.listData.status)">
        <view class="box" @click="handleRevoke()"><text class="iconfont icon-chexiao" />{{ $t('ui.usersExamineExamineListDefaultRevoke') }}</view>
      </template>
    </view>
    <global-index></global-index>
    <replyComponent ref="replyComponentRef" :title="$t('ui.usersExamineDefaultsComment2')" @submit="submit"></replyComponent>
  </view>
</template>

<script setup>
import moment from 'moment'
import { reactive, computed } from 'vue'
import defaultNavBar from '@/components/defaultNavBar/index.vue'
import avatar from '@/components/avatar/index.vue'
import { formatBytes } from '@/utils/file'
import checkSteps from './components/checkProcess.vue'
import checkExamineFrom from './components/checkExamineFrom.vue'
import globalIndex from '@/components/globalIndex/index.vue'
import deanPopover from '@/components/deanPopover/index.vue'
import replyComponent from '@/components/replyComponent/index.vue'
import message from '@/utils/message'
import { clickNavigateTo, lookPreview, isFileTypeIcon, getColor } from '@/utils/helper'

import { useStore } from 'vuex'
const store = useStore()
const userInfo = computed(() => store.state.app.userInfo)
const data = reactive({
  id: 0,
  listData: {},
  stepsData: [],
  examineUser: {},
  examineData: {},
  replayData: [],
  btnList: [
    {
      icon: 'iconfont icon-jiaqiantubiao1',
      type: 1,
      name: '加签',
    },
    {
      icon: 'iconfont icon-zhuanshentubiao1',
      type: 2,
      name: '转审',
    },
  ],
  content: '',
  examineReplyBtn: false,
})

const statusList = ref({
  0: {
    name: '审核中',

    color: '#1890ff',
  },
  1: {
    name: '已通过',
    color: '#19BE6B',
  },
  2: {
    name: '已拒绝',
    color: '#ED4014',
  },
  '-1': {
    name: '已撤销',
    color: '#303133',
  },
})

import { onLoad } from '@dcloudio/uni-app'
onLoad((options) => {
  if (options.id) {
    data.id = options.id
    getApproveApply(options.id, { types: 1 })
  }
  if (options.typeIndex) {
    data.typeIndex = options.typeIndex
  }
})

import { showModal, delayedNavigateTo } from '@/utils/helper'

// 同意
const handleAgree = () => {
  showModal('确定要 同意 申请人的申请吗')
    .then(() => {
      getApproveVerify(data.id, 1)
    })
    .catch(() => {
      console.log('取消')
    })
}

// 重新提交
const resubmit = () => {
  clickNavigateTo(`/pages/users/examine/default?id=${data.examineData.approve.id}&name=${data.examineData.approve.name}&isEdit=true&aid=${data.id}`)
}

const revokeFn = (id) => {
  clickNavigateTo(`/pages/users/examine/defaults?id=${id}`)
}

// 撤销
const handleRevoke = () => {
  if (data.listData.status === 0) {
    showModal('确定要 撤销 申请吗')
      .then(() => {
        getApplyRevoke(data.id)
      })
      .catch(() => {
        console.log('取消')
      })
  } else {
    clickNavigateTo(`/pages/users/examine/components/addSignature?id=${data.id}&type=3`)
  }
}

const selectFn = (e) => {
  // 加签or转审
  if (e.type == 1) {
    clickNavigateTo(`/pages/users/examine/components/addSignature?id=${data.examineData.id}&type=1`)
  } else if (e.type == 2) {
    clickNavigateTo(`/pages/users/examine/components/addSignature?id=${data.examineData.id}&type=2`)
  } else if (e.type == 3) {
    handleRevoke()
  }
}

// 拒绝
const handleRefuse = () => {
  showModal('确定要 拒绝 申请人的申请吗')
    .then(() => {
      getApproveVerify(data.id, 0)
    })
    .catch(() => {
      console.log('取消')
    })
}

// 催办
const urgeFn = () => {
  approveUrgeApi(data.id)
    .then((res) => {
      message.success(res.message)
    })
    .catch((err) => {
      message.error(err.message)
    })
}

// 图片与文档预览
const preview = (item) => {
  lookPreview(item.src, item.name, [item.src])
}

// 申请审批处理
const getApproveVerify = (id, status) => {
  approveVerifyStatusApi(id, status)
    .then((res) => {
      message.success(res.message)
      delayedNavigateTo('/pages/users/examine/approve?id=1')
    })
    .catch((error) => {
      message.error(error.message)
    })
}
const replyComponentRef = ref(null)
const submit = (val) => {
  let ids = []
  if (val.files.length > 0) {
    val.files.map((item) => {
      ids.push(item.id)
    })
  }
  clickReplay(val.content, ids)
}

// 点击留言
const examineReply = () => {
  replyComponentRef.value.popupOpen()
  // data.examineReplyBtn = true;
}

const changeInput = () => {
  data.examineReplyBtn = false
}

// 提交留言
const clickReplay = (content, ids) => {
  // data.examineReplyBtn = false;
  const datas = {
    content: content,
    apply_id: data.listData.id,
    files: ids,
  }

  ;((data.btnList = [
    {
      icon: 'iconfont icon-jiaqiantubiao1',
      type: 1,
      name: '加签',
    },
    {
      icon: 'iconfont icon-zhuanshentubiao1',
      type: 2,
      name: '转审',
    },
  ]),
    getApproveReply(datas))
}

// 删除留言
const handleDelete = (item, index) => {
  showModal('确定要删除留言吗')
    .then(() => {
      approveReplyDelete(item.id, index)
    })
    .catch(() => {
      console.log('取消')
    })
}

import {
  approveApplyEditApi,
  approveVerifyStatusApi,
  approveReplyApi,
  approveReplyDelApi,
  approveApplyRevokeApi,
  approveUrgeApi,
} from '@/api/business'

// 申请审批撤销
const getApplyRevoke = (id) => {
  approveApplyRevokeApi(id)
    .then((res) => {
      message.success(res.message)
      delayedNavigateTo('/pages/users/examine/center')
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const getApproveApply = (id, datas, flage = false) => {
  if (!flage) {
    uni.showLoading({
      title: '加载中',
    })
  }
  approveApplyEditApi(id, datas)
    .then((res) => {
      if (!flage) {
        uni.hideLoading()
      }
      data.examineData = res.data
      if (data.examineData.rules && data.examineData.rules.is_sign !== 1) {
        data.btnList.splice(0, 1)
      }

      if (data.examineData.verify_status !== 0) {
        data.btnList.splice(1, 1)
      }
      data.stepsData = res.data.users
      data.replayData = res.data.reply

      data.listData = res.data
      let obj = {
        icon: 'iconfont icon-houtui-01',
        type: 3,
        name: '撤销',
      }

      if (
        ((data.listData.status == 1 && data.listData.rules && data.listData.rules.recall == 1) || data.listData.status === 0) &&
        !data.listData.recall
      ) {
        data.btnList.push(obj)
      }
      // getUnderApproval( res.data.users )
    })
    .catch((error) => {
      if (!flage) {
        uni.hideLoading()
      }
      message.error(error.message)
    })
}

// 发表留言
const getApproveReply = (datas) => {
  approveReplyApi(datas)
    .then((res) => {
      data.content = ''
      message.success(res.message)
      getApproveApply(data.id, { types: 1 }, true)
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// 删除留言
const approveReplyDelete = (id, index) => {
  approveReplyDelApi(id, index)
    .then((res) => {
      message.success(res.message)
      data.replayData.splice(index, 1)
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// 审批中信息判断
const getUnderApproval = (datas) => {
  let users = []
  if (datas.length > 0) {
    datas.forEach((value) => {
      if (value.types === 1) {
        users.push(...value.users)
      }
    })
  }
  if (users.length > 0) {
    for (let i = 0; i < users.length; i++) {
      if (users[i].status === 0) {
        data.examineUser = users[i]
        break
      }
    }
  }
}
</script>

<style scoped lang="scss">
.sign {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 30rpx;
  color: #606266;
}

.revoke {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 26rpx;
  color: #1890ff;
  margin-left: 20rpx;
  padding-bottom: 20rpx;

  .icon-jinru-copy {
    color: #c0c4cc !important;
    font-size: 12px;
  }
}

.content {
  width: 100%;
  position: relative;

  .cr-position-header {
    padding-top: var(--status-bar-height);
    position: sticky;
    top: 0;
    z-index: 1;
    width: 100%;
    background-color: #fff;
    background-image:
      linear-gradient(360deg, #ffffff 0%, rgba(255, 255, 255, 0) 100%),
      linear-gradient(70deg, rgba(175, 226, 253, 0.4) 2.86%, rgba(43, 131, 234, 0.4) 100%);
    .examine-info {
      font-family:
        PingFang SC,
        PingFang SC;
      margin-top: 20rpx;
      padding-bottom: 32rpx;

      .examine-info-row {
        display: flex;
        flex: 1;

        .examine-info-left {
          width: 84rpx;
          height: 80rpx;
          border-radius: 12rpx 12rpx 12rpx 12rpx;
        }

        .examine-info-right {
          width: calc(100% - 90rpx);
          padding-left: 26rpx !important;
          color: #2b2c32;
          display: flex;
          flex-direction: column;
          justify-content: space-around;

          .flex {
            display: flex;
            justify-content: space-between;
            align-items: center;

            .urge {
              font-size: 30rpx;
              font-family:
                PingFang SC-Regular,
                PingFang SC;
              font-weight: 400;
              color: #1890ff;
            }
          }

          .name {
            font-weight: 500;
            font-size: 30rpx;
            color: #2b2c32;
            margin-bottom: 4rpx;
            display: flex;
            align-items: center;
          }

          .time {
            font-weight: 400;
            font-size: 24rpx;
            color: #606266;
          }

          .examine-tag {
            font-size: 24rpx;
            padding: 0 2rpx;
            background-color: #ff8e32;
          }
        }
      }
    }

    .item-list-status {
      position: absolute;
      right: 0;
      bottom: 12rpx;
      // #ifndef APP-PLUS
      width: 140rpx;
      height: 164rpx;
      // #endif
      // #ifdef APP-PLUS
      width: 160rpx;
      height: 188rpx;
      // #endif
    }
  }

  .examine-content {
    // padding-top: 218rpx;
    padding-bottom: 158rpx;

    .steps-content {
      width: 100%;
      background-color: #fff;
      margin-top: 16rpx;
    }
  }

  .examine-bottom {
    position: fixed;
    left: 0;
    bottom: 0;
    height: 108rpx;
    width: 100%;
    box-shadow: 0px 0px 8px 0px rgba(215, 215, 215, 0.5);
    background-color: #fff;
    padding: 0 40rpx;

    .examine-bottom-reply {
      line-height: 1.2;
      text-align: center;
      margin-right: 36rpx;

      .reply-image {
        width: 40rpx;
        height: 40rpx;
      }

      .name {
        font-size: 24rpx;
        font-weight: 400;
        color: $nui-text-color-four;
      }
    }
  }

  // 上传附件
  .flie {
    width: 100%;
    padding: 24rpx 0rpx 0px 0;

    .box {
      width: 100%;
      height: 40px;
      background: #f6f7f9;
      border-radius: 4px 4px 4px 4px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12rpx;
      padding-right: 10px;
      padding-left: 10px;

      .icon-guanbi-yangshiyi1 {
        cursor: pointer;
        color: #999999;
        margin-top: 7px;
      }

      .left {
        width: 100%;
        display: flex;
        font-family:
          PingFang SC,
          PingFang SC;
        font-weight: 400;

        .slot-image {
          flex-shrink: 0; // flex布局下图片挤压变形
          width: 52rpx;
          height: 52rpx;
          margin-right: 10rpx;
        }

        .name {
          // width: calc(100% - 40px);
          overflow: hidden;
          white-space: nowrap;
          text-overflow: ellipsis;
          font-size: 24rpx;
          color: #303133;
        }

        .size {
          font-size: 20rpx;
          color: #909399;
          margin-top: 2rpx;
        }
      }
    }
  }

  .status-tag {
    min-width: 68rpx;
    height: 34rpx;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24rpx;
    font-weight: 400;
    padding: 0 8rpx;
    margin-left: 8rpx;
  }

  .replay-con {
    margin-top: 20rpx;
    // border-radius: 16rpx;
    .replay-title {
      padding: 28rpx 0 0 24rpx;
      font-size: 26rpx;
      font-weight: 500;
    }
    .replay-pb {
      padding-bottom: 28rpx;
    }

    ::v-deep .uni-list {
      // border-radius: 16rpx;

      .uni-list-item {
        background-color: rgba(0, 0, 0, 0);

        .uni-list-item__container {
          padding: 28rpx 24rpx 0 24rpx;
        }

        &:last-of-type {
          margin-bottom: 0;
        }
      }

      .uni-list--border {
        left: auto;
        top: auto;
      }
    }

    .item-list-left {
      width: 60rpx;
      height: 60rpx;
    }

    .item-list-right {
      width: calc(100% - 60rpx);
      padding-left: 14rpx;
      border-bottom: 1px solid #ebeef5;
      padding-bottom: 28rpx;

      .right-top {
        font-size: 24rpx;
        color: #687383;
      }

      .right-info {
        padding-top: 20rpx;
        font-size: 28rpx;
        color: #41485b;

        .right-info-text {
          font-size: 32rpx;
          color: #c0c4cc;
        }
      }
    }
  }

  .replay {
    box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.06);
    width: 100%;
    position: fixed;
    left: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    min-height: 108rpx;
    background-color: #fff;
    font-size: 28rpx;

    ::v-deep .uni-row {
      width: 100%;
      padding: 0 20rpx;

      .uni-input-placeholder {
        font-size: 28rpx;
        color: #c0c4cc;
      }
    }

    .replay-left {
      width: calc(100% - 60rpx);

      uni-textarea {
        width: 100%;
      }
    }

    .replay-right {
      width: 60rpx;

      .iconfont {
        color: #e4e7ed;
        font-size: 40rpx;
      }
    }
  }
}

.footer-box {
  height: 116rpx;
  display: flex;
  justify-content: space-between;
  align-items: center;

  uni-button {
    flex: 1;
    height: 74rpx;
    line-height: 1;
    padding: 0;
    font-size: 30rpx;
    margin-right: 16rpx;
    border-radius: 8rpx;
    display: flex;
    align-items: center;
    justify-content: center;

    &:last-of-type {
      margin-right: 0;
    }

    &::after {
      border-radius: 0;
    }

    .iconfont {
      padding-right: 14rpx;
    }
  }
}
</style>
