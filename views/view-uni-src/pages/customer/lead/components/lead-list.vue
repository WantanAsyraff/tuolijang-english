<template>
  <view class="examine-content-list">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item, index) in listData" :key="'list' + item.id">
        <template v-slot:body>
          <view class="item-list">
            <view @click="examineList(item)" >
              <view class="item-list-top">
                <text>{{ item.name }} <text class="iconfont icon-shequ-shoucang-yishoucang"
                    v-if="item.followed == 1"></text></text>
                <text class="status-tag" v-if="item.status.name" :style="{
                  color: item.status.color ? item.status.color : '#1890ff',
                  background: item.status.color
                    ? getColor(item.status.color, '0.1')
                    : getColor('#1890ff', '0.1')
                }" >{{ $ts(item.status.name) }}</text>
              </view>
              <uni-row class="item-list-content" v-if="item.work_customer
              ">
                <uni-col :span="5" class="left">{{ $t('ui.customerLeadLeadListWeComCustomer') }}</uni-col>
                <uni-col v-if="item.work_customer.name" :span="19" style="display: flex; align-items: center;"
                  @click.stop="openCustomerChat(item.work_customer)">
                  <image :src="item.work_customer.avatar" class="img"></image>

                  <text class="mr4"> {{ item.work_customer.name }}</text> <text class='work-icon over-text'
                    :class="item.work_customer.type != 1 ? 'work-name' : ''">{{ item.work_customer.type == 1 ? $t('ui.customerLeadLeadListWeChat') : item.work_customer.corp_name || '--' }}</text>
                </uni-col>

                <uni-col :span="19" v-else>--</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerSigningDetailItemContactPhone') }}</uni-col>
                <uni-col :span="19">{{ item.phone || '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerLeadLeadListLeadSource') }}</uni-col>
                <uni-col :span="19">{{ $ts(item.source.name || '--') }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerContractPayDetailSalesperson') }}</uni-col>
                <uni-col :span="19">{{ item.salesman && item.salesman.name ? item.salesman.name : '--' }}</uni-col>
              </uni-row>
              <uni-row class="item-list-content">
                <uni-col :span="5" class="left">{{ $t('ui.customerLeadLeadListAddTime') }}</uni-col>
                <uni-col :span="19">
                  <uni-dateformat v-if="item.created_at" format="yyyy/MM/dd hh:mm"
                    :date="item.created_at"></uni-dateformat>
                  <text v-else>--</text>
                </uni-col>
              </uni-row>
            </view>
          </view>
        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="7" :title="emptyTitle" class="bgf" style="height: calc(100vh - 300rpx);"></empty>

    <view class="add">
		  <text class="iconfont icon-xuanfuanniu-jia" @click="createLead"></text>
		</view>
  </view>
</template>

<script setup>import appI18n from '@/locale';

import { WxWork, isWxWorkEnv } from "@/libs/wxwork";
import empty from "@/components/empty/index.vue";
import avatar from "@/components/avatar/index.vue";
import { leadFollowApi } from "@/api/customer";
import { getColor } from "@/utils/helper"
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
  keyWord: {
    type: String,
    default: 'clue'
  }
});

const { listData, emptyTitle,keyWord } = toRefs(props);
import { clickNavigateTo } from "@/utils/helper";
const examineList = (item) => {
  clickNavigateTo(
    `/pages/customer/lead/detail?id=${item.id}&&types=${keyWord.value}`,
  );
};



const createLead = () => {
clickNavigateTo(`/pages/customer/lead/add?types=${keyWord.value}`);
}
// 打开客户聊天对话框
const openCustomerChat = async (item) => {
  try {
    const wxWork = await WxWork.getInstance();
    await new Promise((resolve, reject) => {
      wxWork.ww.openEnterpriseChat({
        userIds: '', // 外部联系人
        externalUserIds: [item.external_userid],
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
};
</script>

<style scoped lang="scss">
.icon-shequ-shoucang-yishoucang {
  color: #f90;
  
}

.p24 {
  padding-left: 24rpx;
  padding-right: 24rpx;
}

.label-text {
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.mr4 {
  white-space: nowrap;
  margin-right: 8rpx;
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


.status-tag {

  padding: 0 8rpx;
  height: 42rpx;
  background: rgba(24, 144, 255, 0.1);
  border-radius: 8rpx;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 24rpx !important;
  color: #1890FF;
  font-weight: 400;
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

      .uni-list-item__container {
        padding: 30rpx;
      }
    }
  }

  .item-list {
    width: 100%;
    position: relative;

    .item-list-top {
      width: 100%;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      padding-bottom: 20rpx;
      font-family: PingFang SC, PingFang SC;
      font-weight: 500;
      font-size: 28rpx;
      color: #303133;
      display: flex;
      justify-content: space-between;
    }

    .item-list-content {
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 24rpx;
      color: #303133;
      margin-bottom: 12rpx;
      display: flex;
      align-items: flex-end;

      &:last-of-type {
        margin-bottom: 0;
      }

      .left {
        font-weight: 400;
        font-size: 24rpx;
        color: #606266;
      }
    }

  }


}

.add {
  position: fixed;
  cursor: pointer;
  right: 20rpx;
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
</style>