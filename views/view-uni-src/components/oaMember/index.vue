<template>
  <view>
    <!-- 选择成员弹窗 -->
    <uni-popup ref="popupRefTime" type="bottom" :is-mask-click="true">
      <view class="bag">
        <view class="header bgf">
          <view class="header-title">
            <text class="iconfont icon-fanhui" @click="reset"></text>
            <text style="text-align: center">{{ $t('ui.oaMemberIndexSelectMembers') }}</text>
          </view>
        </view>
        <!-- 搜索 -->
        <view class="search-box bgf">
          <uni-search-bar :placeholder="$t('ui.oaMemberIndexSearchMember')" cancelButton="none" bgColor="#F5F5F5" v-model="searchName" @clear="clearData" @input="searchData">
            <template v-slot:searchIcon>
              <text class="iconfont icon-sousuo1"></text>
            </template>
          </uni-search-bar>
        </view>

        <!-- 指定成员 -->
        <view v-if="specifyMember.length > 0" class="org-content">
          <Org :specifyMember="specifyMember" :onlyOne="onlyOne" @goBack="closeOrg" />
        </view>

        <template v-else>
          <!-- 组织架构 -->
          <view class="org-box bgf" v-if="!data.isOrgShow">
            <view>
              <text class="iconfont icon-zuzhijiagou1"></text>
              {{ $t('ui.usersDepartmentIndexOrganization') }}
            </view>
            <text class="iconfont icon-jinru-copy" @click="openOrg"></text>
          </view>
          <view v-if="!data.isOrgShow" class="org-tips"> {{ $t('ui.oaMemberIndexCompanyMembers') }}{{ data.count }}) </view>

          <!-- 组织架构 -->
          <view v-if="data.isOrgShow" class="org-content">
            <Org :searchName="searchName" :onlyOne="onlyOne" @goBack="closeOrg" />
          </view>

          <!-- 选人组件 -->
          <view class="personnel-box" v-if="!data.isOrgShow">
            <view class="personnel-count" v-if="searchName" :key="searchName">
              <text>{{ $t('ui.oaMemberIndexFound') }} {{ filteredList.length }} {{ $t('ui.attendanceSchedulePeople') }}</text>
            </view>
            <personnel-list ref="personnelListRef" :options="filteredList" :onlyOne="onlyOne" :show-select="true" />
          </view>
        </template>

        <!-- 底部确认按钮 -->
        <view class="bottom-bar bgf">
          <view class="selected-info" @click="openSelectedPopup">
            <text class="selected-text">{{ $t('ui.oaMemberIndexSelected') }}{{ selectedCount }}{{ $t('ui.attendanceSchedulePeople') }}</text>
            <text class="iconfont icon-jinru-copy"></text>
          </view>
          <view class="confirm-btn" :class="{ active: selectedCount > 0 }" @click="confirmSelect">
            <text>{{ $t('ui.baTreePickerIndexOk') }}</text>
          </view>
        </view>
      </view>
      <!-- 已选成员弹窗 -->
      <uni-popup ref="selectedPopupRef" type="bottom" :mask-click="true" @maskclick="closeSelectedPopup" style="height: 100vh">
        <view class="selected-popup bgf">
          <view class="selected-header bgf">
            <view>
              <view class="selected-title">{{ $t('ui.oaMemberIndexSelected') }}</view>

              <view class="selected-text">{{ $t('ui.oaMemberIndexSelected2') }}{{ selectedCount }} / {{ data.count }}）</view>
            </view>

            <text class="iconfont icon-guanbi" @click="closeSelectedPopup"></text>
          </view>
          <view class="selected-list">
            <view v-if="selectedList.length === 0" class="no-data">
              <text>{{ $t('ui.oaMemberIndexNoMembersSelected') }}</text>
            </view>
            <view v-else>
              <view v-for="item in selectedList" :key="item.id" class="selected-item bgf">
                <view class="item-left">
                  <avatar :src="item.avatar" :radius="50" :auto-size="false" :width="72" :height="72" />
                  <view class="item-info">
                    <text class="item-name">{{ item.name }}</text>
                    <text class="item-dept" v-if="item.job && item.job.name">{{ item.job.name }}</text>
                  </view>
                </view>
                <view class="item-remove" @click="removeSelected(item)">
                  <text class="iconfont icon-chahao"></text>
                  <!-- <uni-icons type="closeempty" color="#909399" size="16" /> -->
                </view>
              </view>
            </view>
          </view>
        </view>
      </uni-popup>
    </uni-popup>
  </view>
</template>
<script setup>import appI18n from '@/locale';

import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useStore } from 'vuex'
import { enterpriseUsersApi } from '@/api/user'
import personnelList from './components/personnelList.vue'
import avatar from '@/components/avatar/index.vue'
import message from '@/utils/message'
import Org from './components/org.vue'
import { getPinYinFirstLetters } from '@/utils/pinyin'
const props = defineProps({
  onlyOne: {
    type: Boolean,
    default: false,
  },
  // 指定成员
  specifyMember: {
    type: Array,
    default: () => {
      return []
    },
  },
})
const { onlyOne } = toRefs(props)
const popupRefTime = ref(null)
const selectedPopupRef = ref(null)
const personnelListRef = ref(null)

const emit = defineEmits(['change', 'confirm'])

// 已选成员列表
const selectedList = computed(() => {
  return store.state.app.depSelectPeople || []
})

const selectedCount = computed(() => {
  return selectedList.value.length
})

const store = useStore()

const data = reactive({
  isOrgShow: false,
  list: [],
  count: 0,
  name: '',
  treeData: computed(() => store.state.app.frameTree),
})

onMounted(() => {
  getEntUsers()
})

const openOrg = () => {
  searchName.value = ''
  data.isOrgShow = true
}

const searchName = ref('')

const filteredList = computed(() => {
  const keyword = searchName.value.trim().toLowerCase()
  if (!keyword) {
    return data.list
  }
  const list = []
  const letter = getPinYinFirstLetters(keyword)
  let obj = {
    letter: '',
    data: [],
  }
  data.list.map((item) => {
    item.data.map((el) => {
      if (el.name.includes(keyword)) {
        obj.data.push(el)
      }
    })
  })
  console.log(obj)
  if (obj.data.length === 0) {
    return []
  } else {
    return [obj]
  }
})

const getEntUsers = () => {
  enterpriseUsersApi()
    .then((res) => {
      data.list = res.data.list
      data.count = res.data.count
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// 防抖搜索
let searchTimer = null
const searchData = (e) => {
  // if (searchTimer) clearTimeout(searchTimer)
  // searchTimer = setTimeout(() => {
  //   searchName.value = e || ''
  // }, 300)
}

const clearData = () => {
  searchName.value = ''
}

// 打开已选成员弹窗
const openSelectedPopup = () => {
  selectedPopupRef.value.open()
}

// 关闭已选成员弹窗
const closeSelectedPopup = () => {
  selectedPopupRef.value.close()
}

// 移除已选成员
const removeSelected = (item) => {
  const ids = store.state.app.depSelectIds || []
  const people = store.state.app.depSelectPeople || []

  const index = people.findIndex((p) => p.id === item.id)
  if (index > -1) {
    ids.splice(index, 1)
    people.splice(index, 1)
    store.commit('setDepSelectIds', ids)
    store.commit('setDepSelectPeople', people)
  }
}

// 确认选择
const confirmSelect = () => {
  if (selectedCount.value === 0) {
    message.error(appI18n.global.t('ui.oaMemberIndexPleaseSelectAMember'))
    return
  }
  emit('confirm', [...selectedList.value])
  reset()
}

// 打开弹窗
const popupOpen = (val) => {
  if (val) {
    store.commit('setDepSelectPeople', val)
    let ids = []
    val.map((item) => {
      ids.push(item.id)
    })
    store.commit('setDepSelectIds', ids)
  }

  popupRefTime.value.open()
  setTimeout(() => {
    personnelListRef.value?.refreshLayout?.()
  }, 300)
}

// 关闭弹窗
const reset = () => {
  data.isOrgShow = false
  searchName.value = ''
  popupRefTime.value.close()
}

defineExpose({ popupOpen, reset })
</script>
<style lang="scss" scoped>
.bag {
  height: 100vh;
  background-color: #f0f1f5;
  display: flex;
  flex-direction: column;
}
.header {
  width: 100%;
  height: 88rpx;
  line-height: 88rpx;
  padding: 0 30rpx;
  text-align: center;
  .icon-fanhui {
    float: left;
  }
  .header-title {
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 500;
    font-size: 32rpx;
    color: #333333;
  }
}
.org-tips {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 26rpx;
  color: #666666;
  margin: 8px 0 8px 15px;
}
.search-box {
  padding: 6rpx 30rpx 16rpx 30rpx;
  ::v-deep .uni-searchbar {
    padding: 0;
  }
}

.icon-chahao {
  font-size: 24rpx;
  color: #909399;
}

.org-box {
  margin-top: 8px;
  width: 100%;
  padding: 0 30rpx;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100rpx;
  line-height: 100rpx;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 30rpx;
  color: #303133;
  .icon-zuzhijiagou1 {
    font-size: 32rpx;
    color: #1890ff;
    margin-right: 6rpx;
  }
  .icon-jinru-copy {
    color: #909399;
  }
}

.org-content {
  flex: 1;
  min-height: 0;
  overflow: hidden;
}

.personnel-box {
  position: relative;
  flex: 1;
  min-height: 0;
}

.personnel-count {
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 26rpx;
  color: #666666;
  padding: 16rpx 30rpx;
}

// 底部确认栏
.bottom-bar {
  flex-shrink: 0;
  height: 120rpx;
  padding: 0 30rpx;
  padding-bottom: constant(safe-area-inset-bottom);
  padding-bottom: env(safe-area-inset-bottom);
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 -2rpx 10rpx rgba(0, 0, 0, 0.05);

  .selected-info {
    display: flex;
    align-items: center;
    gap: 8rpx;

    .selected-text {
      font-size: 28rpx;
      color: #1890ff;
    }

    .icon-jinru-copy {
      font-size: 24rpx;
      color: #1890ff;
    }
  }

  .confirm-btn {
    width: 160rpx;
    height: 72rpx;
    line-height: 72rpx;
    text-align: center;
    background: #e6e6e6;
    border-radius: 36rpx;
    font-size: 28rpx;
    color: #999999;

    &.active {
      background: #1890ff;
      color: #ffffff;
    }
  }
}

// 已选成员弹窗
.selected-popup {
  height: 60vh;
  background: #f0f1f5;
  border-radius: 24rpx 24rpx 0 0;
}

.selected-header {
  height: 154rpx;
  position: relative;
  // padding: 0 30rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 30rpx;
  font-family:
    PingFang SC,
    PingFang SC;
  background-color: pink;

  .selected-title {
    text-align: center;
    font-weight: 500;
    font-size: 30rpx;
    color: #303133;
  }
  .selected-text {
    text-align: center;
    font-weight: 400;
    font-size: 26rpx;
    color: #909399;
    margin-top: 16rpx;
  }

  .icon-guanbi {
    position: absolute;
    top: 30rpx;
    right: 30rpx;
    font-size: 32rpx;
    color: #c0c4cc;
  }
}
::v-deep .uni-searchbar__box {
  height: 32px;
  line-height: 32px;
}

::v-deep .uni-searchbar__cancel {
  display: none;
}
::v-deep .uni-searchbar__text-placeholder {
  font-size: 24rpx;
  color: #909399;
}
::v-deep .uni-input-input {
  font-size: 24rpx;
  color: #303133;
}
::v-deep .uni-input-placeholder,
.uni-input-input {
  font-size: 24rpx !important;
  color: #909399;
}

.selected-list {
  height: calc(50vh - 50rpx);
  overflow-y: auto;
  padding-left: 30rpx;
}

.no-data {
  text-align: center;
  padding: 60rpx;
  color: #909399;
  font-size: 28rpx;
}

.selected-item {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 112rpx;
  padding-right: 30rpx;
  border-bottom: 1px solid #ebeef5;

  .item-left {
    display: flex;
    align-items: center;

    .item-info {
      margin-left: 20rpx;
      display: flex;
      flex-direction: column;

      .item-name {
        font-size: 28rpx;
        color: #2b2c32;
      }

      .item-dept {
        font-size: 24rpx;
        color: #909399;
        margin-top: 6rpx;
      }
    }
  }

  .item-remove {
    padding-right: 30rpx;
  }
}
.bgf {
  background-color: #ffffff;
}
</style>
