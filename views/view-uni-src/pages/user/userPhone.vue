<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :index="1"></default-nav-bar>
    </view>
    <view class="assessment m10">
      <view class="cr-center-list">
        <view class="center-list-item" v-for="(item, index) in data.listData" :key="index">
          <uni-row class="center-list-item-con">
            <uni-col :span="6">{{ $t('ui.attendanceScheduleTableHeaderName') }}<text class="is-required">*</text></uni-col>
            <uni-col :span="18" class="display-align">
              <uni-easyinput v-model="item.name" :inputBorder="false" type="text" :placeholder="$t('ui.userUserPhoneEnterName')"></uni-easyinput>
            </uni-col>
          </uni-row>
          <uni-row class="center-list-item-con">
            <uni-col :span="6">{{ $t('ui.userUserPhonePhoneNumber') }}<text class="is-required">*</text></uni-col>
            <uni-col :span="18" class="display-align">
              <uni-easyinput v-model="item.phone" :inputBorder="false" type="number"
                :placeholder="$t('ui.userUserPhoneEnterMobileNumber')"></uni-easyinput>
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
  listData: [{ name: "", phone: "" }]
});

const addUserItem = (): void => {
  data.listData.push({ name: "", phone: "" });
};
</script>

<style lang="scss">
page {
  background-color: $uni-default-bg;
}
</style>

<style scoped lang="scss">
@import '@/static/css/form-item-list.scss';

.content {
  width: 100%;

  .assessment {
    padding-top: calc(44px + var(--status-bar-height));
    padding-bottom: $uni-bottom-default-height;
  }
}
</style>
