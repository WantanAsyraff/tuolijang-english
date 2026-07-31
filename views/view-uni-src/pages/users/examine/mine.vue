<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :default-title="data.defaultTitle"></default-nav-bar>
    </view>

    <view class="examine-content">
      <uni-list :border="false" v-if="data.listData.length > 0">
        <uni-list-item v-for="(item) in data.listData" :key="'list'+item.id">
          <template v-slot:body>
            <view class="item-list">
              <view class="item-list-top" @tap="examineList(item)" v-if="item.card">
                
            <text>{{item.card.name}}{{ $t('ui.indexIndexS') }}{{item.approve.name}}</text>
              <text class="status-tag" :style="{
                color: statusList[item.status].color,
                background: getColor(statusList[item.status].color, '0.1')
              }">{{ $ts(statusList[item.status].name) }}</text>
              
              </view>
              <view class="" @tap="examineList(item)">


                <examine-list-item :content="item.content"></examine-list-item>
                <uni-row class="item-list-content">
                  <uni-col :span="5" class="left">{{ $t('ui.usersExamineExamineListDefaultSubmissionTime') }}</uni-col>
                  <uni-col :span="19" class="examine-from-right">
                   
                    <uni-dateformat  format="yyyy/MM/dd hh:mm" :date="item.created_at"></uni-dateformat>
                  </uni-col>
                </uni-row>
              </view>

            </view>
          </template>
        </uni-list-item>
      </uni-list>
      <empty v-else :index="4" :title="$t('ui.usersExamineMineNoSubmitContent')"></empty>
    </view>
    <global-index></global-index>
  </view>
</template>

<script setup>
import { getColor } from "@/utils/helper"
import defaultNavBar from "@/components/defaultNavBar/index.vue";
import empty from "@/components/empty/index.vue";
import globalIndex from "@/components/globalIndex/index.vue";
import examineListItem from "./components/examineListItem.vue";
import { ref, reactive } from "vue";
import message from "@/utils/message";
const data = reactive({
  defaultTitle: "",
  customStyle: { border: "none", lineHeight: "20px", background: "#ED4014" },
  listData: [],
  where: {
    limit: 10,
    page: 1,
    types: 0,
    approve_id: ""
  }
});
import { approveApplyApi } from "@/api/business";
import { onLoad } from "@dcloudio/uni-app";
onLoad((options) => {
  if (options.id) {
    data.id = options.id;
    data.where.approve_id = options.id;
    data.defaultTitle = options.name ? options.name : "申请审批";
    getConfigList();
  }
});

import { clickNavigateTo } from "@/utils/helper";
const examineList = (item) => {
  clickNavigateTo(`/pages/users/examine/defaults?id=${item.id}`);
};

const statusList = ref({
  '0': {
    name: '审核中',

    color: '#1890ff'
  },
  '1': {
    name: '已通过',
    color: '#19BE6B'
  },
  '2': {
    name: '已拒绝',
    color: '#ED4014'
  },
  '-1': {
    name: '已撤销',
    color: '#303133'
  }

})

const listLoading = ref(false);
// 列表加载
const getConfigList = () => {
  approveApplyApi(data.where).then((res) => {
    data.listData.push(...res.data.list);
    const allPage = Math.ceil(res.data.count / data.where.limit);
    if (data.listData.length <= 0 || data.where.page >= allPage) {
      listLoading.value = false;
    } else {
      listLoading.value = true;
    }
    uni.stopPullDownRefresh(); // 停止刷新
  }).catch((error) => {
    message.error(error.message);
  });
};

import { onReachBottom } from "@dcloudio/uni-app";
// 下拉加载
onReachBottom(() => {
  if (listLoading.value) {
    data.where.page++;
    getConfigList();
  }
});
</script>

<style scoped lang="scss">
  .content {
    width: 100%;
    position: relative;

    .cr-position-header {
      background-color: #fff;
    }

    .examine-content {
      padding-top: calc($uni-default-bar-height + var(--status-bar-height));

      ::v-deep .uni-list {
        background-color: $uni-default-bg;

        .uni-list-item {
          margin-top: 8rpx;
          // border-radius: 8rpx;

          .uni-list-item__container {
            padding: 32rpx 24rpx 36rpx 24rpx;
          }
        }
      }

      .item-list {
        width: 100%;
        position: relative;

        .item-list-top {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding-bottom: 24rpx;
          font-size: 32rpx;
          font-weight: 600;
          color: $uni-text-color;
        }

        .item-list-content {
          height: 24rpx;
          line-height: 24rpx;
          font-size: 24rpx;
          color: $uni-text-color;
          font-weight: 400;

          .left {
            max-width: 120rpx;
            color: #606266;
          }

          uni-button {
            width: 100%;
            height: 74rpx;
            line-height: 74rpx;
            font-size: 30rpx;
            background-color: #F0F1F5;
            margin-right: 32rpx;

            &:last-of-type {
              margin-right: 0;
            }

            &::after {
              border-radius: 8rpx;
              border: none;
            }
          }
        }

        .item-list-button {
          display: flex;
          justify-content: space-between;
        }

        .item-list-status {
          position: absolute;
          top: -32rpx;
          right: -24rpx;
          width: 160rpx;
          height: 188rpx;
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
}
</style>
