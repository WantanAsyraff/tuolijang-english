<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item, index) in listData" :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list">
            <view @click="examineList(item)" class="p24">
              <view class="item-list-top">
                <text> {{ item.name }} <text v-if="item.followed == 1"
                    class="iconfont icon-shequ-shoucang-yishoucang"></text></text>
                <text class="status-tag" :style="{
                  color: item.status ? item.status.color : '#1890ff',
                  background: item.status.color
                    ? getColor(item.status.color, '0.1')
                    : getColor('#1890ff', '0.1')
                }" v-if="item.status">{{ $ts(item.status.name) }}</text>
              </view>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerListBusinessFollowOpportunityNo') }}</uni-col>
                <uni-col :span="19">{{ item.odds_no || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerLeadLeadListWeComCustomer') }}</uni-col>
                <uni-col :span="19">
                  <view v-if="item.work_customer&&item.work_customer.name" style="display: flex; align-items: center;">
                    <image :src="item.work_customer.avatar" class="img"></image>
                    {{ item.work_customer.name }}
                    <text class='work-icon over-text'
                      :class="item.work_customer.type != 1 ? 'work-name' : ''">{{ item.work_customer.type == 1 ? $t('ui.customerLeadLeadListWeChat') : '@' + item.work_customer.corp_name || '--' }}</text>
                  </view>
                  <view v-else>
                    --
                  </view>
                </uni-col>
              </uni-row>

              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailCustomerName') }}</uni-col>
                <uni-col :span="19">{{ item.odds_customer || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerListBusinessFollowOpportunityQuote') }}</uni-col>
                <uni-col :span="19">
                  {{ item.total_amount }}{{ $t('ui.customerContractPayDetailYuan') }}
                </uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailSalesperson') }}</uni-col>
                <uni-col :span="19">
                  {{ item.salesman.name }}
                </uni-col>
              </uni-row>

              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerListBusinessFollowFollowUpTime') }}</uni-col>
                <uni-col :span="19">
                  <uni-dateformat v-if="item.last_follow_time" format="yyyy/MM/dd hh:mm"
                    :date="item.last_follow_time"></uni-dateformat>
                  <text v-else>--</text>

                  <view class="chart" @click.stop="openCustomerChat(item.work_customer)" v-if="isWxWorkEnv && item.work_customer&&item.work_customer.name">{{ $t('ui.customerOpportunityOpportunityListStartChat') }}</view>
                </uni-col>
              </uni-row>
            </view>
          </view>
        </template>
      </uni-list-item>
    </uni-list>

    <empty v-else :index="7" :title="emptyTitle" class="bgf" style="height: calc(100vh - 300rpx);"></empty>
    	<!-- <view class="add">
		  <text class="iconfont icon-xuanfuanniu-jia" @click="createSigning"></text>
		</view> -->
  </view>
</template>

<script setup>import appI18n from '@/locale';

import empty from "@/components/empty/index.vue";
import avatar from "@/components/avatar/index.vue";
import { getColor } from "@/utils/helper"
import { WxWork, isWxWorkEnv } from "@/libs/wxwork";
import message from "@/utils/message";
const props = defineProps({
  listData: {
    type: Array,
    default() {
      return [];
    },
  },
  emptyTitle: {
    type: String,
    default: "",
  },
});
const { listData, emptyTitle } = toRefs(props);
import { clickNavigateTo } from "@/utils/helper";
const examineList = (item) => {
  clickNavigateTo(
    `/pages/customer/opportunity/detail?id=${item.id}`,
  );
};

const createSigning = () => {
  clickNavigateTo(`/pages/customer/opportunity/add`);
};

// 打开客户聊天对话框
const openCustomerChat = async (work_customer) => {
  if(!isWxWorkEnv) return message.error(appI18n.global.t('ui.customerListLiaisonListChatIsAvailableOnlyInWeCom'));
  if (!work_customer || !work_customer.external_userid) return message.error(appI18n.global.t('ui.customerOpportunityOpportunityListTheCustomerIsNotLinkedToWeCom'));
  try {
    const wxWork = await WxWork.getInstance();
    await new Promise((resolve, reject) => {
      wxWork.ww.openEnterpriseChat({
        userIds: '', // 外部联系人
        externalUserIds: [work_customer.external_userid],
        groupName: '',
        chatId: '',
        success: resolve,
        fail: reject
      });
    });
    message.success(appI18n.global.t('ui.customerLeadLeadListChatOpened'));
  } catch (err) {
    message.error(`打开个人资料页失败: ${err.errMsg || err.message || '操作失败'}`);
  }
}

import { opportunityFollowApi } from "@/api/customer";
const clickFollow = (index, item) => {
  opportunityFollowApi(item.id, item.followed == 0 ? 1 : 0)
    .then((res) => {
      message.error(res.message);
      item.followed = item.followed == 0 ? 1 : 0;
    })
    .catch((error) => {
      message.error(error.message);
    });
};
</script>

<style scoped lang="scss">
::v-deep .uni-list-item__container {
  padding: 0;
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
    font-weight: 400;
    font-size: 24rpx;
    padding: 0 10rpx;
  }

  .item-list {
    width: 100%;
    position: relative;
    padding: 30rpx;
    font-family: PingFang SC, PingFang SC;

    .item-list-top {
      width: 100%;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      padding-bottom: 20rpx;
      font-weight: 500;
      font-size: 28rpx;
      color: #303133;
      display: flex;
      justify-content: space-between;
    }



    .item-list-content {
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
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 24rpx;
        color: #606266;
      }
    }
  }
}


.add {
  position: fixed;
  right: 20rpx;
  cursor: pointer;
   bottom: 140rpx;
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #47B5FF 0%, #0F86F5 100%);
  box-shadow: 0px 4px 4px 0px rgba(28, 146, 248, 0.1145);
  border-radius: 50%;
  text-align: center;
  line-height: 42px;
  color: #fff;

  .icon-xuanfuanniu-jia {
    font-size: 15px;
  }
}

.icon-shequ-shoucang-yishoucang {
  color: #FF9900;
}

.img {
  width: 40rpx;
  height: 40rpx;
  border-radius: 50%;
  margin-right: 8rpx;
}

.work-icon {
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #1CBF6C;
}

.work-name {
  color: #FF9900;
}

.chart-box {
  display: flex;
  justify-content: space-between;
}

.chart {
  width: 112rpx;
  height: 46rpx;
  border-radius: 8rpx 8rpx 8rpx 8rpx;
  border: 1rpx solid #1890FF;
  text-align: center;
  line-height: 44rpx;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 22rpx !important;
  color: #1890FF;
  position: absolute;
  right: 0rpx;
  bottom: 0rpx;
  cursor: pointer;
}
</style>
