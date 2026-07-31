<template>
  <view class="assessment">
    <uni-list :border="false" v-if="listData.length > 0">
      <uni-list-item v-for="(item,index) in listData" :key="'list'+item.id">
        <template v-slot:body>
          <template v-if="item.template_type.indexOf('assess') > -1">
            <template v-if="!Array.isArray(item.other)">
              <view class=" item-list">
                <view @click="itemListClick(1, item, index)">
                  <uni-row class="display-align item-list-top">
                    <uni-col :span="16" class="right-title">{{item.detail.name}}</uni-col>
                    <uni-col :span="8" class="text-right">
                      <uni-tag v-if="item.detail.status === 1" :inverted="true" :text="getStatusText(item.detail.status)" type="primary" />
                      <uni-tag v-if="item.detail.status === 2" :inverted="true" :text="getStatusText(item.detail.status)" type="warning" />
                      <uni-tag v-if="item.detail.status === 3" :inverted="true" :text="getStatusText(item.detail.status)" type="error" />
                      <uni-tag v-if="item.detail.status === 4" :inverted="true" :text="getStatusText(item.detail.status)" />
                      <uni-tag v-if="item.detail.status === 5" :inverted="true" :text="getStatusText(item.detail.status)" />
                    </uni-col>
                  </uni-row>
                  <view class="item-list-content">
                    <uni-row class="items display-align">
                      <uni-col :span="6" class="left">{{ $t('ui.noticeWorkListAppraisee') }}</uni-col>
                      <uni-col :span="18"><text class="default-color">{{item.detail.test.name}}</text></uni-col>
                    </uni-row>
                    <uni-row class="items display-align">
                      <uni-col :span="6" class="left">{{ $t('ui.timePopupIndexStartTime') }}</uni-col>
                      <uni-col :span="18">
                        <uni-dateformat format="yyyy/MM/dd" :date="item.detail.start_time"></uni-dateformat>
                      </uni-col>
                    </uni-row>
                    <uni-row class="items display-align">
                      <uni-col :span="6" class="left">{{ $t('ui.timePopupIndexEndTime') }}</uni-col>
                      <uni-col :span="18">
                        <uni-dateformat format="yyyy/MM/dd" :date="item.detail.end_time"></uni-dateformat>
                      </uni-col>
                    </uni-row>
                    <uni-row class="items display-align">
                      <uni-col :span="6" class="left">{{ $t('ui.noticeWorkListAssessmentCycle') }}</uni-col>
                      <uni-col :span="18">
                        <text>{{ getPeriodText(item.detail.period) }}</text>
                      </uni-col>
                    </uni-row>
                  </view>
                </view>
                <uni-row class="item-list-content item-list-button">
                  <button class="default-color" @click="itemListClick(1, item, index)">{{ $t('ui.noticeWorkListInProgress') }}{{getStatusText(item.detail.status)}}</button>
                </uni-row>
              </view>
            </template>
            <template v-else>
              <view class=" item-list">
                <view @click="clickHandle(item, index)">
                  <uni-row class="display-align item-list-top">
                    <uni-col :span="24" class="right-title">{{item.cate_name}}-{{item.title}}</uni-col>
                  </uni-row>
                  <view class="item-list-content">
                    <uni-row class="items display-align">
                      <uni-col :span="6" class="left">{{ $t('ui.customerListAddFollowReminderTime') }}</uni-col>
                      <uni-col :span="18">
                        <uni-dateformat format="yyyy/MM/dd hh:mm" :date="item.created_at"></uni-dateformat>
                      </uni-col>
                    </uni-row>
                  </view>
                </view>
                <uni-row class="item-list-content item-list-button">
                  <button v-if="item.button_template.no" @click="clickHandle(item, index)" class="default-error">{{item.button_template.no}}</button>
                  <button v-if="item.button_template.yes" @click="clickHandle(item, index)" class="default-color">{{item.button_template.yes}}</button>
                </uni-row>
              </view>
            </template>
          </template>

          <template v-if="item.template_type === 'business_approval'">
            <view class="item-list">
              <view @click="itemListClick(2, item, index)">
                <uni-row class="display-align item-list-top" v-if="item.detail.approve || item.detail.card">
                  <uni-col :span="16" class="right-title">
                    {{item.detail.card ? item.detail.card.name : '--'}}{{ $t('ui.noticeWorkListSubmitS') }}{{item.detail && item.detail.approve ? item.detail.approve.name : '--' }}
                  </uni-col>
                  <uni-col :span="8" class="text-right">
                    <uni-tag :inverted="true" :text="$t('ui.noticeWorkListPendingApproval')" type="primary" />
                  </uni-col>
                </uni-row>
                <view class="item-list-content">
                  <examine-list-item :content="item.detail.content"></examine-list-item>
                  <uni-row class="items display-align">
                    <uni-col :span="6" class="left">{{ $t('ui.customerInvoiceInvoiceDetailApplicationTime') }}</uni-col>
                    <uni-col :span="18">
                      <uni-dateformat format="yyyy/MM/dd hh:mm" :date="item.detail.created_at"></uni-dateformat>
                    </uni-col>
                  </uni-row>
                </view>
              </view>
              <uni-row class="item-list-content item-list-button">
                <button class="default-error" v-if="item.button_template.no" @click="getApproveVerify(item.other.id, 0, index)">{{item.button_template.no}}</button>
                <button class="default-color" v-if="item.button_template.yes" @click="getApproveVerify(item.other.id, 1, index)">{{item.button_template.yes}}</button>
              </uni-row>

              <!--<image class="item-list-status" src="/static/image/examine.png" mode=""></image>
              <image v-if="item.status === 1" class="item-list-status" src="/static/image/passed.png" mode=""></image>
              <image v-if="item.status === 2" class="item-list-status" src="/static/image/refuse.png" mode=""></image>
              <image v-if="item.status === -1" class="item-list-status" src="/static/image/revoke.png" mode=""></image>-->
            </view>
          </template>

          <template v-if="false">
            <view class="item-list">
              <uni-row class="display-align item-list-top">
                <uni-col :span="16" class="right-title">{{ $t('ui.noticeWorkListInvoiceRequestSubmittedByXiaobei') }}</uni-col>
                <uni-col :span="8" class="text-right">
                  <uni-tag :inverted="true" :text="$t('ui.noticeWorkListNotInvoiced')" type="error" />
                </uni-col>
              </uni-row>
              <view class="item-list-content">
                <uni-row class="items display-align">
                  <uni-col :span="6" class="left">{{ $t('ui.customerInvoiceAddInvoiceInvoiceType') }}</uni-col>
                  <uni-col :span="18"><text>{{ $t('ui.noticeWorkListAnnualLeave') }}</text></uni-col>
                </uni-row>
                <uni-row class="items display-align">
                  <uni-col :span="6" class="left line1">{{ $t('ui.noticeWorkListInvoiceAmount') }}</uni-col>
                  <uni-col :span="18">
                    <text>1234</text>
                  </uni-col>
                </uni-row>
                <uni-row class="items display-align">
                  <uni-col :span="6" class="left">{{ $t('ui.customerSigningPaymentTableLinkOrder') }}</uni-col>
                  <uni-col :span="18">
                    <text>{{ $t('ui.noticeWorkListXiaozhaiInternationalMallPhase1') }}</text>
                  </uni-col>
                </uni-row>
                <uni-row class="items display-align">
                  <uni-col :span="6" class="left">{{ $t('ui.customerInvoiceInvoiceDetailApplicationTime') }}</uni-col>
                  <uni-col :span="18">
                    <uni-dateformat format="yyyy/MM/dd" :date="new Date()"></uni-dateformat>
                  </uni-col>
                </uni-row>
              </view>
              <uni-row class="item-list-content item-list-button">
                <button class="default-error">{{ $t('ui.financePaymentDetailsRefuse') }}</button>
                <button class="default-color">{{ $t('ui.financePaymentDetailsAgree') }}</button>
              </uni-row>

              <image class="item-list-status" src="/static/image/examine.png" mode=""></image>
              <image v-if="item.status === 1" class="item-list-status" src="/static/image/passed.png" mode=""></image>
              <image v-if="item.status === 2" class="item-list-status" src="/static/image/refuse.png" mode=""></image>
              <image v-if="item.status === -1" class="item-list-status" src="/static/image/revoke.png" mode=""></image>
            </view>
          </template>

        </template>
      </uni-list-item>
    </uni-list>
    <empty v-else :index="4" :title="$t('ui.noticeWorkListNoTodoContent')"></empty>
  </view>

</template>

<script setup>
import empty from "@/components/empty/index";
import message from "@/utils/message";
import examineListItem from "@/pages/users/examine/components/examineListItem.vue";
import { getStatusText, getPeriodText } from "@/utils/assessment";
import { clickNavigateTo } from "@/utils/helper";
import { userMessageHandleApi } from "@/api/user";
import { approveVerifyStatusApi } from "@/api/business";
const props = defineProps({
  listData: {
    type: Array,
    default() {
      return [];
    }
  },
  emptyTitle: {
    type: String,
    default: ""
  },
});
const { listData, emptyTitle } = toRefs(props);

const data = reactive({
  assessInfo: ["assess_selt", "assess_publish"]
});

// 详情处理
const itemListClick = (type, row, index) => {
  if (Array.isArray(row.other)) {
    getMessageHandle(row.id, 1, index);
  } else {
    let str = `${row.uni_url}?id=${row.other.id}`;
    clickNavigateTo(str);
  }
};

const clickHandle = (item, index) => {
  getMessageHandle(item.id, 1, index);
};

// 处理消息状态
const getMessageHandle = (id, status, index) => {
  userMessageHandleApi(id, status).then((res) => {
    message.success("处理成功");
    listData.value.splice(index, 1);
  }).catch((error) => {
    message.error(error.message);
  });
};

// 申请审批处理
const getApproveVerify = (id, status, index) => {
  approveVerifyStatusApi(id, status).then((res) => {
    message.success(res.message);
    listData.value.splice(index, 1);
  }).catch((error) => {
    message.error(error.message);
  });
};
</script>

<style scoped lang="scss">
  @import '@/static/css/assessment.scss';

  .assessment {
    width: 100%;

    .items {
      uni-text {
        padding-left: 0 !important;
      }
    }
  }
</style>
