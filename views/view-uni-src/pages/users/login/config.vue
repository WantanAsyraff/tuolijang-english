<template>
  <view class="content">
    <image class="login-image" src="../../../static/image/login-bg.png"></image>
    <view class="nav-bar">
      <view class="iconfont icon-fanhui bar-return" @click="back"> </view>
      <view>{{ $t('ui.usersLoginConfigAddServiceConfiguration') }}</view>
      <!-- #ifdef H5 -->
      <view></view>
      <!-- #endif -->
      <!-- #ifdef APP -->
      <view class="iconfont icon-saoma" @click="handleScan"></view>
      <!-- #endif -->
    </view>
    <view class="corporate">
      <view class="title">{{ $t('ui.usersLoginConfigEnterpriseBinding') }}</view>
      <uni-forms ref="forms" :modelValue="formData" label-position="top">
        <uni-forms-item :label="$t('ui.usersLoginConfigServiceDomain')">
          <uni-easyinput v-model="formData.address" :styles="{ borderColor: 'transparent' }" :clearable="false" :placeholder="$t('ui.usersLoginConfigEnterServiceDomain')" />
          <view class="tips">{{ $t('ui.usersLoginConfigExampleHttpsDemoTuoluojiangCom') }}</view>
        </uni-forms-item>
      </uni-forms>
      <button class="preserve" type="primary" :disabled="!formData.address" @click="handlePreserve">{{ $t('ui.usersLoginConfigSave') }}</button>
    </view>
  </view>
</template>

<script setup>import appI18n from '@/locale';

import message from '@/utils/message'
import { getServerConfigList, setActiveServerConfig } from '@/utils/serverConfig'
let formData = reactive({
  address: '',
  isDefault: 0,
})
// let chooseApiUrl
const data = reactive({
  serverConfig: [],
  address: '',
})
const serverConfigInfoData = getServerConfigList()
// 扫一扫
const handleScan = () => {
  uni.scanCode({
    success(res) {
      let containsIsITtem

      containsIsITtem = serverConfigInfoData.some((it) => it.address === res.result)
      if (containsIsITtem) {
        message.error(appI18n.global.t('ui.usersLoginConfigThisEnterpriseIsAlreadyConfigured'))
        return false
      }
      getUserScan(res.result)
    },
    fail(err) {
      console.error('扫码失败', err)
    },
  })
}
import { debounce } from '@/utils/helper'
// 页面跳转
const getUserScan = (result) => {
  let containsIsITtem
  let address
  address = result ? result : formData.address
  containsIsITtem = serverConfigInfoData.some((it) => it.address === address)
  if (containsIsITtem) {
    return message.error(appI18n.global.t('ui.usersLoginConfigThisEnterpriseIsAlreadyConfigured'))
  }
  uni.request({
    method: 'GET',
    url: address + '/api/uni/common/server_config',
    data: {},
    success: (res) => {
      const resStatus = res.data.status
      if (parseInt(resStatus) === 200) {
        const resData = res.data.data
        // #ifdef APP-PLUS
        // pc端版本号
        const version = resData.version.split('.')[0] + resData.version.split('.')[1]
        // App版本号
        const sysVersion = uni.getSystemInfoSync().appWgtVersion.split('.')[0] + uni.getSystemInfoSync().appWgtVersion.split('.')[1]
        if (sysVersion > version) {
          return message.error('管理端版本不适配，请升级至版本：v' + uni.getSystemInfoSync().appWgtVersion)
        }
        // #endif
        resData.address = resData.address || address
        data.address = resData.address
        resData.isDefault = true
        serverConfigInfoData.push(resData)
        uni.setStorageSync('serverConfigInfo', serverConfigInfoData)
        setActiveServerConfig(resData)
        message.success(appI18n.global.t('ui.customerListSuccessPopupAddedSuccessfully'))
        setTimeout(function () {
          uni.navigateTo({
            url: '/pages/users/login/index?type=1',
          })
        }, 500)
      } else {
        message.error(res.data.message)
      }
    },
    fail: () => {
      message.error(appI18n.global.t('ui.usersLoginConfigConfigurationFailedEnterTheCorrectServiceDomain'))
    },
  })
}
// 保存配置
const handlePreserve = debounce(() => {
  const regex = /^(https?:\/\/)?((([a-zA-Z0-9]|(?:[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9]))\.)+([a-zA-Z]{2,}))|((\d{1,3}\.){3}\d{1,3})(:\d+)?$/
  if (!regex.test(formData.address)) {
    message.error(appI18n.global.t('ui.usersLoginConfigCheckThatTheDomainIsCorrect'))
    return false
  }
  getUserScan()
}, 500)

const back = () => {
  uni.navigateTo({
    url: '/pages/users/login/index',
  })
}
</script>

<style scoped lang="scss">
.content {
  position: relative;
  height: 100vh;
  background-color: #fff;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;

  .login-image {
    width: 100%;
  }

  .tips {
    margin-top: 18rpx;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 22rpx;
    color: #606266;
  }

  .nav-bar {
    position: absolute;
    width: 100%;
    height: 88rpx;
    line-height: 88rpx;
    display: flex;
    justify-content: space-between;
    font-size: 36rpx;
    margin-bottom: 52rpx;
    padding: 0 30rpx;
    // #ifdef APP-PLUS
    top: 80rpx;
    // #endif

    .bar-return {
      // margin-left: 30rpx;
    }

    .icon-saoma {
      // margin-right: 32rpx;
      font-size: 40rpx;
    }
  }

  .corporate {
    position: absolute;
    padding: 0 30rpx;
    width: 100%;
    // #ifndef APP-PLUS
    top: 140rpx;
    // #endif
    // #ifdef APP-PLUS
    top: 220rpx;

    // #endif
    .title {
      font-size: 44rpx;
      margin-bottom: 60rpx;
    }

    ::v-deep .uni-forms-item__label {
      width: 140rpx !important;
      height: auto;
      font-size: 22rpx;
      line-height: 22rpx;
      color: $nui-text-color-two;
      padding: 0;
    }

    ::v-deep .uni-easyinput__content {
      border: none;
      background: none !important;

      .uni-easyinput__content-input {
        border-bottom: 1px solid #e4e7ed;
        height: 42px;
        padding: 0 !important;
      }

      .uni-easyinput__placeholder-class {
        font-size: $uni-font-size-default;
        color: $uni-text-color-five;
        font-weight: 400;
      }

      .uni-input-input {
        font-size: $uni-font-size-default;
        color: $uni-text-color;
      }
    }

    ::v-deep.uni-forms-item__label:after {
      content: '*';
      color: red;
      margin-left: 4px;
    }

    .agree-content {
      margin-top: 10rpx;
      font-size: 26rpx;

      ::v-deep .uni-label-pointer {
        display: flex;
        align-items: center;

        .uni-checkbox-input {
          border-radius: 50%;
        }
      }
    }

    .preserve {
      margin-top: 40rpx;
      font-size: 30rpx;
      height: 86rpx;
      line-height: 86rpx;
    }
  }
}
</style>
