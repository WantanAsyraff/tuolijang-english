<template>
  <view class="content" v-if="data.listData.id">
    <view class="cr-position-header">
      <!-- #ifdef APP-PLUS -->
      <view class="status_bar"></view>
      <view class="position bar">
        <default-nav-bar></default-nav-bar>
      </view>
      <image class="personal-bg" src="/static/image/personal-bg.png" mode=""></image>
      <!-- #endif -->

      <!-- #ifndef APP-PLUS -->
            <default-nav-bar></default-nav-bar>
      <image class="personal-bg" src="/static/image/personal-bg-h5.png" mode=""></image>
      <!-- #endif -->

      <view class="position personal-header plr10">
        <uni-row class="display-align">
          <uni-col :span="6" class="personal-header-left">
            <avatar :src="data.listData.avatar ? data.listData.avatar : '/static/image/default-avatar.png'" :radius="8">
            </avatar>
          </uni-col>
          <uni-col :span="18" class="personal-header-right">
            <view class="name line1">{{ data.listData.name ? data.listData.name : '' }}</view>
            <view class="job line1">{{ data.listData.job ? data.listData.job.name : '--' }}</view>
          </uni-col>
        </uni-row>
     
      </view>

    </view>
    <view class="assessment mt8">
      <view class="cr-center-list">
        <view class="center-list-item">
          <uni-row class="center-list-item-con">
            <uni-col :span="6">{{ $t('ui.userUserPhonePhoneNumber') }}</uni-col>
            <uni-col :span="18" class="text-right line1">{{ data.listData.phone }}

              <!-- <view class="dianhua"></view> -->
               <view class="dianhua" @click="callPhone(data.listData.phone)">
<text class="iconfont icon-phone-fill"></text>
               </view>
              
            </uni-col>
          </uni-row>
          <uni-row class="center-list-item-con">
            <uni-col :span="6">{{ $t('ui.usersCenterIndexEmail') }}</uni-col>
            <uni-col :span="18" class="text-right line1">{{ data.listData.info ? data.listData.info.email :
              '--'}}</uni-col>
          </uni-row>
          <template v-if="data.listData.frames">
            <uni-row class="center-list-item-con" v-for="(item, index) in data.listData.frames" :key="index">
              <uni-col :span="6">{{ index === 0 ? $t('ui.usersDepartmentSelectBottomBarDepartment') : '' }}</uni-col>
              <uni-col :span="18" class="display-align right">
                <view class="title">{{ item.name }}</view>
              </uni-col>
            </uni-row>
          </template>
          <template v-else>
            <uni-row class="center-list-item-con">
              <uni-col :span="6">{{ $t('ui.usersDepartmentSelectBottomBarDepartment') }}</uni-col>
              <uni-col :span="18" class="display-align right">
                <view class="title">--</view>
              </uni-col>
            </uni-row>
          </template>

        </view>
      </view>
    </view>
    <global-index></global-index>
  </view>
</template>

<script setup lang="ts">import appI18n from '@/locale';

import defaultNavBar from "@/components/defaultNavBar/index.vue";
import globalIndex from "@/components/globalIndex/index.vue";
import avatar from "@/components/avatar/index.vue";
import message from "@/utils/message";
import { enterpriseUserInfoApi } from "@/api/user";
import type { GetType, Res } from "@/utils/typeHelper";
import { useStore } from "vuex";
const store = useStore();

const enterprise = computed(() => store.state.app.enterprise);

onLoad((e: GetType) => {
  if (e.id) {
    getUserInfo(e.id);
  }
});

const data = reactive({
  title: "",
  listData: <any>{}
});

// 获取个人信息
const getUserInfo = (id: number): void => {
  enterpriseUserInfoApi(id).then((res: Res) => {
    data.listData = res.data;
    if (data.listData.frames && data.listData.frames.length > 1) {
      data.listData.frames.sort((a: any, b: any) => {
        return b.is_mastart - a.is_mastart;
      });
    }
  }).catch((error: Res) => {
    message.error(error.message);
  });

};

// 拨打电话
const callPhone = (phone: string): void => {
  uni.makePhoneCall({
    phoneNumber: phone,
    success: (res: any) => {
      message.success(appI18n.global.t('ui.userPersonalPhoneCallStartedSuccessfully'));
    },
    fail: (error: any) => {
      message.error(error.message);
    }
  });
};
</script>

<style scoped lang="scss">
@import '@/static/css/form-item-list.scss';

.content {
  width: 100%;

  .cr-position-header {
     padding-top: var(--status-bar-height);
    background-color: #fff;
    position: sticky;
    height:242rpx;

    .position {
      width: 100%;
      position: absolute;
      left: 0;
    }

    .personal-bg {
      position: absolute;
      right: 0;
      top: var(--status-bar-height - 40rpx);
      bottom: 0;
      width: 161px;
      height: 170px;
     
    
    }

    .bar {
      top: 44px;
    }

    .personal-header {
         position: absolute;
     top: var(--status-bar-height + 56rpx );
     padding-top: 24rpx;
      .personal-header-left {
        width: 100rpx;
        height: 100rpx;
      }

      .personal-header-right {
        padding-left: 26rpx !important;
        width: calc(100% - 100rpx);

        .name {
          font-size: 32rpx;
          font-weight: $uni-default-font-weight;
          color: #2B2C32;
        }

        .job {
          padding-top: 8rpx;
          font-size: 26rpx;
          color: #888888;
        }
      }

      .company-name {
        padding-top: 30rpx;
        font-size: 24rpx;
        font-weight: 400;
      }
    }
  }

  .mt8 {
    margin-top: 16rpx;
  }
}
.dianhua {
  cursor: pointer;
  display: inline-block;
  width: 48rpx;
height: 48rpx;
background:rgba(24, 144, 255, 0.06);
border-radius: 50%;
text-align: center;
line-height: 48rpx;
margin-left: 20rpx;
.icon-phone-fill {
  font-size: 28rpx;
  color: #1890FF;
}
}
</style>
