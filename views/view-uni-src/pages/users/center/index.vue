<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar></default-nav-bar>
    </view>

    <view class="assessment m10">
      <view class="cr-center-list">
        <view class="center-list-item">
          <uni-row class="center-list-item-con">
            <uni-col :span="8">{{ $t('ui.usersCenterIndexHeadPortrait') }}</uni-col>
            <uni-col :span="16" class="display-align right">
              <image @click="uploadAvatar" class="avatar" @error="avatarError" :src="data.userInfo.avatar"
                mode="aspectFill"></image>
              <view class="iconfont icon-fanhui"></view>
            </uni-col>
          </uni-row>
          <uni-row class="center-list-item-con">
            <uni-col :span="8">{{ $t('ui.usersCenterIndexNickname') }}</uni-col>
            <uni-col :span="16" class="display-align right" @click="clickEdit(1)">
              <view class="title">{{ data.userInfo.name }}</view>
              <view class="iconfont icon-fanhui"></view>
            </uni-col>
          </uni-row>
          <uni-row class="center-list-item-con">
            <uni-col :span="8">{{ $t('ui.usersCenterIndexUserId') }}</uni-col>
            <uni-col :span="16" class="display-align right" @click="copyUid">
              <view class="title">{{ data.userInfo.uid ? data.userInfo.uid.substring(0, 20) + '...' : '' }}</view>
            </uni-col>
          </uni-row>
        </view>

        <view class="center-list-item">
          <uni-row class="center-list-item-con">
            <uni-col :span="8">{{ $t('ui.usersCenterIndexPhone') }}</uni-col>
            <uni-col :span="16" class="display-align right" @click="clickNavigateTo('/pages/users/center/phone')">
              <view class="title">{{ data.userInfo.phone }}</view>
              <view class="iconfont icon-fanhui"></view>
            </uni-col>
          </uni-row>
          <uni-row class="center-list-item-con">
            <uni-col :span="8">{{ $t('ui.usersCenterIndexEmail') }}</uni-col>
            <uni-col :span="16" class="display-align right" @click="clickEdit(2)">
              <view class="title">{{ data.userInfo.email }}</view>
              <view class="iconfont icon-fanhui"></view>
            </uni-col>
          </uni-row>
          <uni-row class="center-list-item-con">
            <uni-col :span="8">{{ $t('ui.usersCenterIndexPassword') }}</uni-col>
            <uni-col :span="16" class="display-align right" @click="clickNavigateTo('/pages/users/center/password')">
              <view class="title">********</view>
              <view class="iconfont icon-fanhui"></view>
            </uni-col>
          </uni-row>
        </view>
      </view>
      <button class="botton" type="primary" @click="logout">{{ $t('ui.userAvatarSideslipLogOut') }}</button>
    </view>
    <edit-content ref="editContentRef" :title="data.title" :edit-data="data.configData"
      @handleOk="handleOk"></edit-content>
  </view>
</template>

<script setup>
import defaultNavBar from "@/components/defaultNavBar/index";
import editContent from "./components/editContent";
import { ref, reactive, onMounted, computed } from "vue";
import message from "@/utils/message";
import { clickNavigateTo, showModal } from "@/utils/helper";
import { uploadImage } from "@/utils/file";
import { userUserInfoApi, userUserInfoEditApi } from "@/api/user";
import defaultAvatar from "/static/image/default-avatar.png";
import { logoutApi } from "@/api/public";
import { useStore } from "vuex";
import { socketService } from "@/app/services/socket";
import { useI18n } from "vue-i18n";
import { getLanguage, setLanguage, type SupportedLocale } from "@/locale";
const store = useStore();
const { t } = useI18n();
const currentLanguage = ref<SupportedLocale>(getLanguage());
const languageOptions = computed(() => [
  { label: t("common.chinese"), value: "zh-cn" as SupportedLocale },
  { label: t("common.english"), value: "en" as SupportedLocale },
]);
const languageIndex = computed(() => languageOptions.value.findIndex((item) => item.value === currentLanguage.value));
const currentLanguageLabel = computed(() => languageOptions.value.find((item) => item.value === currentLanguage.value)?.label || "");
const refreshForLanguage = () => {
  uni.reLaunch({ url: "/pages/index/index" });
};
const changeLanguage = (event: any) => {
  const selected = languageOptions.value[Number(event.detail.value)]?.value || "zh-cn";
  currentLanguage.value = setLanguage(selected);
  refreshForLanguage();
};
const translateToEnglish = () => {
  if (currentLanguage.value === "en") return;
  currentLanguage.value = setLanguage("en");
  refreshForLanguage();
};

onMounted(() => {
  getCenterUser();
});

const data = reactive({
  title: "",
  configData: {},
  userInfo: {},
  fromData: {
    avatar: "",
    email: "",
    phone: "",
    real_name: "",
    uid: "",
  },
});
const loading = ref(false);

// 获取用户信息
const getCenterUser = () => {
  userUserInfoApi()
    .then((res) => {
      data.userInfo = res.data || {};
      updateInit(res.data);
    })
    .catch((error) => {
      message.error(error.message);
    });
};
// 保存用户信息
const saveCenterUser = (data) => {
  userUserInfoEditApi(data)
    .then((res) => {
      message.success(res.message);
      reset();
      getCenterUser();
    })
    .catch((error) => {
      message.error(error.message);
    });
};

const editContentRef = ref(null);
const clickEdit = (type) => {
  if (type === 1) {
    data.title = String(t("user.nickname"));
    data.configData = {
      value: data.userInfo.name,
      placeholder: t("user.enterNickname"),
      type: type,
    };
  } else if (type === 2) {
    data.title = String(t("user.email"));
    data.configData = {
      value: data.userInfo.email,
      placeholder: t("user.enterEmail"),
      type: type,
      types: "email",
    };
  }
  editContentRef.value.popupOpen();
};

const avatarError = () => {
  data.userInfo.avatar = defaultAvatar;
};

// 复制链接
const copyUid = () => {
  uni.setClipboardData({
    data: data.userInfo.uid,
    showToast: false,
    success: () => {
      message.success(t("common.copied"));
    },
  });
};

// 上传头像
const uploadAvatar = () => {
  uploadImage("common/upload")
    .then((res) => {
      data.fromData.avatar = res.data.url;
      saveCenterUser(data.fromData);
    })
    .catch((error) => {
      message.error(error);
    });
};

const updateInit = (userInfo) => {
  const value = uni.getStorageSync("storageUserData");
  if (value) {
    const data = JSON.parse(value);
    data.userInfo.avatar = userInfo.avatar;
    data.userInfo.email = userInfo.email;
    data.userInfo.phone = userInfo.phone;
    data.userInfo.real_name = userInfo.real_name;

    store.state.app.userInfo.avatar = userInfo.avatar;
    uni.setStorageSync("storageUserData", JSON.stringify(data));
  }
};

// 编辑
const handleOk = (e) => {
  if (e.type === 1) {
    if (data.userInfo.real_name !== e.value) {
      data.fromData.real_name = e.value;
      saveCenterUser(data.fromData);
    }
  } else if (e.type === 2) {
    if (data.userInfo.email !== e.value) {
      data.fromData.email = e.value;
      saveCenterUser(data.fromData);
    }
  }
};
// 重置
const reset = () => {
  data.fromData = {
    avatar: "",
    email: "",
    phone: "",
    real_name: "",
    uid: "",
  };
};

// 退出登录
const logout = () => {
  showModal(t("common.confirmLogout"))
    .then(() => {
      loading.value = true;
      logoutApi()
        .then((res) => {
          if (res.status === 200) {
            socketService.disconnect();
            store.commit("logout");
            uni.reLaunch({
              url: "/pages/users/login/index",
            });
          }
          loading.value = false;
        })
        .catch((error) => {
          message.error(error.message);
          loading.value = false;
        });
    })
    .catch(() => {});
};
</script>

<style scoped lang="scss">
  @import '@/static/css/form-item-list.scss';

  .content {
    width: 100%;

    .assessment {

      padding-top: calc($uni-default-bar-height + var(--status-bar-height));
      .botton {
        margin-top: 40rpx;
        height: 86rpx;
        line-height: 86rpx;
        font-size: $uni-font-size-default;
      }
      .translate-en-button {
        margin: 18rpx 24rpx 20rpx;
        height: 72rpx;
        line-height: 72rpx;
        font-size: 28rpx;
      }
    }
  }
</style>
