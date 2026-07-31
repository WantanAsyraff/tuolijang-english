<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :index="1"></default-nav-bar>
      <view class="cr-notice-con plr10 display-align">
        <text class="iconfont icon-yemiantishi"></text>
        <text>{{ $t('ui.userUserIdAfterEnteringAValidMemberIdTheUserMust') }}</text>
      </view>
    </view>
    <view class="assessment m10">
      <view class="cr-center-list">
        <view class="center-list-item" v-for="(item,index) in data.listData" :key="index">
          <uni-row class="center-list-item-con">
            <uni-col :span="6"><text class="is-required">*</text>{{ $t('ui.userUserIdMemberId') }}</uni-col>
            <uni-col :span="18" class="display-align">
              <uni-easyinput v-model="item.uid" :inputBorder="false" type="text" :placeholder="$t('ui.userUserIdPleaseEnterMemberId')"></uni-easyinput>
            </uni-col>
          </uni-row>
        </view>

        <view class="center-list-item">
          <view class="add-botton display-align" @click="addUserItem">
            <text class="iconfont icon-liuyan-fasong"></text>{{ $t('ui.userUserIdAddMember') }}
          </view>
        </view>
      </view>
    </view>
    <view class="cr-bottom-button p10">
      <button type="primary">{{ $t('ui.userUserIdConfirmAdd') }}</button>
    </view>
  </view>
</template>

<script setup lang="ts">
import defaultNavBar from "@/components/defaultNavBar/index.vue";

const data = reactive({
  title: "",
  listData: [{ uid: "" }]
});

const addUserItem = (): void => {
  data.listData.push({ uid: "" });
};
</script>

<style scoped lang="scss">
  @import '@/static/css/form-item-list.scss';

  .content {
    width: 100%;

    .cr-notice-con {
      width: 100%;
      height: 68rpx;
      background-color: #F2F6FC;
      font-size: 24rpx;
      font-weight: 400;
      color: $uni-color-primary;

      .iconfont {
        padding-right: 10rpx;
      }
    }

    .assessment {
      padding-top: calc(44px + 68rpx + var(--status-bar-height));
      padding-bottom: $uni-bottom-default-height;
    }
  }
</style>
