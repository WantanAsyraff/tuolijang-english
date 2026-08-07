<template>
  <view class="check-per">
    <!-- 面包屑导航 -->
    <view class="breadcrumb">
      <view v-if="!searchName" class="breadcrumb-list">
        <text
          v-for="(item, index) in breadcrumbList"
          :key="item.id"
          :class="{
            'breadcrumb-active': index === breadcrumbList.length - 1,
            'breadcrumb-highlight': data.selectedUserDeptId && item.id === data.selectedUserDeptId,
          }"
          @click="breadcrumbClick(item, index)"
        >
          {{ item.label }}
          <text v-if="index < breadcrumbList.length - 1" class="breadcrumb-sep">
            <text class="iconfont icon-jinru-copy" />
          </text>
        </text>
      </view>
      <text v-else class="breadcrumb-active">{{ $t('ui.oaMemberOrgSearchResults') }}</text>
    </view>
    <view class="scroll-per">
      <!-- 搜索结果视图 -->
      <template v-if="searchName && searchResult.length > 0">
        <view class="search-tip">
          <text>{{ $t('ui.oaMemberOrgFound') }} {{ searchResult.length }} {{ $t('ui.oaMemberOrgMatchingResults') }}</text>
        </view>
        <view class="uni-indexed-list__item-content pr10">
          <view class="display-align user-list" v-for="(item, index) in searchResult" :key="item.id || index" @click="userClick(item, index)">
            <view v-if="!onlyOne" style="margin-right: 24rpx">
              <uni-icons
                :type="checkedArr.includes(item.id) ? 'checkbox-filled' : 'circle'"
                :color="checkedArr.includes(item.id) ? '#1890FF' : '#C0C4CC'"
                size="18"
              />
            </view>
            <image
              class="avatar"
              :src="item.avatar ? item.avatar : '/static/image/default-avatar.png'"
              @error="avatarError(item)"
              mode="aspectFill"
            ></image>
            <view class="item-content-info">
              <text class="text" :class="{ 'text-active': checkedArr.includes(item.value) }">{{ item.name }}</text>
              <text class="caption">{{ item._deptName }}</text>
            </view>
          </view>
        </view>
      </template>
      <!-- 无搜索结果 -->
      <view v-else-if="searchName && searchResult.length === 0" class="empty-tip">
        <text>{{ $t('ui.oaMemberOrgNoMatchingPeopleFound') }}</text>
      </view>
      <!-- 组织架构视图 -->
      <template v-if="!searchName && specifyMember.length == 0">
        <!-- 当前层级的部门人员 -->
        <view class="uni-indexed-list__item-content" v-for="(item, index) in currentTreeData" :key="item.id">
          <!-- 选择用户 -->
          <template v-if="showPerson">
            <view class="display-align user-list" v-for="(items, indexs) in item.user" :key="'key' + indexs" @click="userClick(items, indexs, item)">
              <view v-if="!onlyOne" style="margin-right: 24rpx">
                <uni-icons
                  :type="checkedArr.includes(items.id) ? 'checkbox-filled' : 'circle'"
                  :color="checkedArr.includes(items.id) ? '#1890FF' : '#C0C4CC'"
                  size="18"
                />
              </view>
              <image
                class="avatar"
                :src="items.avatar ? items.avatar : '/static/image/default-avatar.png'"
                @error="avatarError(items)"
                mode="aspectFill"
              ></image>
              <view class="item-content-info">
                <text class="text" :class="{ 'text-active': checkedArr.includes(items.id) && onlyOne }">{{ items.name || '--' }}</text>
                <text class="caption" v-if="items.job">{{ items.job.name }}</text>
              </view>
            </view>
          </template>
          <!-- 选择部门 -->
          <template v-if="item.children?.length">
            <view
              class="user-list lh112"
              v-for="(items, indexs) in item.children"
              :key="'dep' + indexs"
              @click.stop="depClick(items, index)"
              v-if="item.children"
            >
              <template v-if="items.type == 0">
                <uni-row class="display-align">
                  <uni-col :span="20" class="display-align">
                    <view v-if="!onlyOne" style="margin-right: 24rpx" @click.stop="toggleDeptUsersSelect(items)">
                      <!-- 当前部门下面的人员被选中但是没有全部被选中 -->
                      <uni-icons v-if="getDeptUserSelectStatus(items) === 'partial'" type="minus-filled" size="18" color="#1890FF"></uni-icons>
                      <!-- 当前部门下面的人员全部被选中 -->
                      <uni-icons v-else-if="getDeptUserSelectStatus(items) === 'all'" type="checkbox-filled" size="18" color="#1890FF"></uni-icons>
                      <!-- 当前部门下面的人员没有被选中 -->
                      <uni-icons v-else type="circle" size="18" color="#C0C4CC"></uni-icons>
                    </view>
                    <view style="margin-right: 20rpx" @click.stop="changeDepartment(items, indexs)">
                      <uni-icons
                        :type="userList.checkedFrame.includes(items.id) ? 'checkbox-filled' : 'circle'"
                        :color="userList.checkedFrame.includes(items.id) ? '#1890FF' : '#C0C0C0'"
                        size="24"
                      />
                    </view>
                    <view class="avatar dep-icon" mode="">
                      <text class="iconfont icon-fenzu"></text>
                    </view>

                    <view class="item-content-info">
                      <text class="text" :class="{ 'text-active': getDeptUserSelectStatus(items) === 'partial' && onlyOne }">{{ items.label }}</text>
                    </view>
                  </uni-col>
                  <uni-col :span="4" class="text-right">
                    <template v-if="showPerson">
                      <uni-icons type="right dep-right-icon" v-if="items.children && items.children.length > 0"></uni-icons>
                    </template>
                    <template v-else>
                      <uni-icons v-if="items.children" type="right dep-right-icon"></uni-icons>
                    </template>
                  </uni-col>
                </uni-row>
              </template>
            </view>
          </template>
        </view>
        <!-- 空状态提示 -->
        <view v-if="currentTreeData.length === 0" class="empty-tip">
          <text>{{ $t('ui.customerListStatisticsNoData') }}</text>
        </view>
      </template>
      <!-- 自定义选择成员 -->
      <template v-else-if="!searchName">
        <view class="uni-indexed-list__item-content pr10">
          <view class="display-align user-list" v-for="(items, indexs) in specifyMember" :key="'key' + indexs" @click="userClick(items, indexs)">
            <view style="margin-right: 20rpx">
              <uni-icons
                :type="checkedArr.includes(items.id) ? 'checkbox-filled' : 'circle'"
                :color="checkedArr.includes(items.id) ? '#1890FF' : '#C0C0C0'"
                size="24"
              />
            </view>
            <image
              class="avatar"
              :src="items.avatar ? items.avatar : '/static/image/default-avatar.png'"
              @error="avatarError(items)"
              mode="aspectFill"
            ></image>
            <view class="item-content-info">
              <text class="text">{{ items.name }}</text>
              <text class="caption" v-if="items.job">{{ items.job.name }}</text>
            </view>
          </view>
        </view>
      </template>
    </view>
  </view>
</template>

<script setup>import appI18n from '@/locale';

import { ref, toRefs, reactive, computed, watch } from 'vue'
import message from '@/utils/message'
import { clickNavigateTo } from '@/utils/helper'
import { useStore } from 'vuex'
const store = useStore()
const props = defineProps({
  specifyMember: {
    // 指定成员
    type: Array,
    default: () => [],
  },
  // 是否显示成员
  showPerson: {
    type: Boolean,
    default: true,
  },
  onlyOne: {
    type: Boolean,
    default: false,
  },
  searchName: {
    type: String,
    default: '',
  },
})
const { onlyOne, showPerson, specifyMember, searchName } = toRefs(props)
const userInfo = computed(() => store.state.app.userInfo)
const customUsers = computed(() => store.state.app.selectCustomUsers)
let checkedArr = ref([])
let selectPeopleArr = ref([])
const searchResult = ref([]) // 搜索结果列表

let data = reactive({
  isUser: 1,
  user: [],
  dep: [],
  checkedFrame: [],
  treeData: computed(() => store.state.app.frameTree),
  selectedUserDeptId: '', // 当前选中人员所属部门ID
})

// 当前显示的树数据层级
const currentDepId = ref('')
const breadcrumbList = ref([
  {
    label: appI18n.global.t('ui.usersDepartmentIndexOrganization'),
    id: '',
  },
])

// 根据当前层级获取对应的数据
const currentTreeData = computed(() => {
  const treeData = data.treeData
  // 根层级：返回整个树数据
  if (!currentDepId.value || !treeData || treeData.length === 0) {
    return treeData || []
  }
  // 递归查找当前层级的部门节点
  const findNode = (nodes, targetId) => {
    for (const node of nodes) {
      if (node.id === targetId) {
        return node
      }
      if (node.children && node.children.length > 0) {
        const found = findNode(node.children, targetId)
        if (found) return found
      }
    }
    return null
  }
  const result = findNode(treeData, currentDepId.value)
  if (!result) return []
  // 返回节点数组，包含 user 和 children，这样点击部门后会显示该部门的直接成员和子部门
  return [result]
})

// 默认选中id
const getSelectIds = computed(() => {
  return store.state.app.depSelectIds
})

// 选中人的列表
const getSelectPeople = computed(() => {
  return store.state.app.depSelectPeople
})

// 数据监听
watch(
  [getSelectIds, getSelectPeople],
  (newvalue) => {
    checkedArr.value = newvalue[0]
    // 判断是否选择部门
    data.checkedFrame = showPerson.value ? [] : checkedArr.value
    selectPeopleArr.value = newvalue[1]
    // 判断数据加载问题
    data.user = newvalue[1]
  },
  { immediate: true, deep: true },
)

// 监听搜索关键词，递归搜索所有部门中匹配的人员
watch(
  () => searchName.value,
  (val) => {
    if (!val) {
      searchResult.value = []
      return
    }
    const keyword = val
    const results = []
    const seenIds = new Set() // 用于去重

    const searchAllUsers = (nodes) => {
      if (!nodes?.length) return
      nodes.forEach((node) => {
        if (node.user?.length) {
          node.user.forEach((user) => {
            if (user.name.includes(keyword) && !seenIds.has(user.id)) {
              seenIds.add(user.id)
              results.push({ ...user, _deptName: node.label })
            }
          })
        }
        if (node.children?.length) {
          searchAllUsers(node.children)
        }
      })
    }

    searchAllUsers(data.treeData)
    searchResult.value = results
  },
)

let emit = defineEmits(['handleDep'])

// 点击选中人员
const userClick = (item, index, parentItem) => {
  let len = checkedArr.value.indexOf(item.id)
  if (len > -1) {
    checkedArr.value.splice(len, 1)
    const index = data.user.findIndex((items) => items.id === item.id)
    if (index > -1) {
      data.user.splice(index, 1)
    }
    // 取消选中时，清空选中部门ID
    if (checkedArr.value.length === 0) {
      data.selectedUserDeptId = ''
    }
  } else {
    // 单选模式下，先清空之前的选择
    if (onlyOne.value) {
      checkedArr.value = []
      data.user = []
    }
    data.user.push(item)
    checkedArr.value.push(item.id)
    // 记录选中人员所属部门ID
    data.selectedUserDeptId = parentItem ? parentItem.id : ''
  }
  store.commit('setDepSelectIds', checkedArr.value)
  store.commit('setDepSelectPeople', Array.from(new Set(data.user)))
  data.isUser = 1
  emit('handleDep', data.user)
}

import defaultAvatar from '/static/image/default-avatar.png'
import { unInvoicedListApi } from '@/api/customer'
const avatarError = (item) => {
  item.avatar = defaultAvatar
}

// 选择部门
const changeDepartment = (item, index) => {
  let len = data.checkedFrame.indexOf(item.id)
  if (len > -1) {
    data.checkedFrame.splice(len, 1)
    const index = data.user.findIndex((items) => items.id == item.id)
    if (index > -1) {
      data.user.splice(index, 1)
    }
  } else {
    // 判断是否为单选
    if (onlyOne.value) {
      if (selectPeopleArr.value.length > 0) {
        message.error(appI18n.global.t('ui.oaMemberOrgOnlyOneMemberCanBeSelected'))
        return false
      }
    }
    const dep = {
      label: item.label,
      name: item.label,
      id: item.id,
      is_mastart: false,
    }
    data.user.push(dep)
    data.checkedFrame.push(item.id)
  }
  store.commit('setDepSelectIds', data.checkedFrame)
  store.commit('setDepSelectPeople', Array.from(new Set(data.user)))
  data.isUser = 1
  emit('handleDep', data.user)
}

// 点击选中部门 - 进入下一级
const depClick = (item, index) => {
  // 如果是选择模式且是部门选择
  if (!showPerson.value && item.childFrame == 0) return false
  if (!showPerson.value && !item.children) return false

  // 添加到面包屑导航
  breadcrumbList.value.push({
    label: item.label,
    id: item.id,
  })

  // 更新当前层级ID
  currentDepId.value = item.id

  data.isUser = 2
  data.index = index
  data.id = item.id
  data.dep = item
  emit('handleDep', data)
}

// 获取部门下人员的选中状态
const getDeptUserSelectStatus = (deptItem) => {
  if (!deptItem.user || deptItem.user.length === 0) return 'none'
  const selectedUsers = deptItem.user.filter((user) => checkedArr.value.includes(user.id))
  if (selectedUsers.length === 0) return 'none'
  if (selectedUsers.length === deptItem.user.length) return 'all'
  return 'partial'
}

// 切换部门下所有人员的选中状态
const toggleDeptUsersSelect = (deptItem) => {
  if (!deptItem.user || deptItem.user.length === 0) return
  const status = getDeptUserSelectStatus(deptItem)
  if (status === 'none' || status === 'partial') {
    // 全选该部门下所有人员
    deptItem.user.forEach((user) => {
      if (!checkedArr.value.includes(user.id)) {
        data.user.push(user)
        checkedArr.value.push(user.id)
      }
    })
  } else {
    // 取消全选该部门下所有人员
    deptItem.user.forEach((user) => {
      const idx = checkedArr.value.indexOf(user.id)
      if (idx > -1) {
        checkedArr.value.splice(idx, 1)
      }
      const userIdx = data.user.findIndex((u) => u.id === user.id)
      if (userIdx > -1) {
        data.user.splice(userIdx, 1)
      }
    })
  }
  store.commit('setDepSelectIds', checkedArr.value)
  store.commit('setDepSelectPeople', Array.from(new Set(data.user)))
  emit('handleDep', data.user)
}

// 面包屑导航点击 - 返回指定层级
const breadcrumbClick = (item, index) => {
  // 如果是最后一项，不处理
  if (index === breadcrumbList.value.length - 1) return

  // 如果是根目录，重置
  if (!item.id) {
    currentDepId.value = ''
    // 保留第一项，清除其他项
    breadcrumbList.value = [breadcrumbList.value[0]]
  } else {
    // 返回到指定层级
    currentDepId.value = item.id
    // 截断面包屑
    breadcrumbList.value = breadcrumbList.value.slice(0, index + 1)
  }
}
</script>

<style lang="scss" scoped>
.check-per {
  height: 100%;
  display: flex;
  flex-direction: column;
  min-height: 0;
  background-color: #fff;
  margin-top: 16rpx;

  .scroll-per {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding-left: 30rpx;
  }
  .text-right {
    margin-right: 30rpx;
    font-size: 32rpx;
  }

  .uni-indexed-list__item-content {
    .user-list {
      height: 112rpx;
      border-bottom: 1px solid #ebeef5;
    }

    .avatar {
      height: 72rpx;
      width: 72rpx;
      border-radius: 50%;
    }
    .lh112 {
      line-height: 112rpx;
      padding-right: 30rpx;
      .icon-fenzu {
        font-size: 38rpx;
      }
      .avatar {
        border-radius: 16rpx;
        line-height: 72rpx;
      }
    }

    .dep-icon {
      background: linear-gradient(203deg, rgba(66, 172, 249, 0.15) 0%, rgba(44, 132, 247, 0.15) 100%);
      text-align: center;
      line-height: 80rpx;

      uni-text {
        font-size: 60rpx;
        color: #318cf8;
      }
    }

    .item-content-info {
      padding-left: 24rpx;

      uni-text {
        display: block;
      }

      .text {
        font-family:
          PingFang SC,
          PingFang SC;
        font-weight: 400;
        font-size: 26rpx;
        color: #303133;
      }

      .caption {
        padding-top: 4rpx;
        font-size: 22rpx;
        color: #909399;
      }
    }

    .dep-right-icon {
      color: #ccc !important;
    }

    .item-button {
      background-color: #f0f1f5;
      color: #909399;

      &::after {
        border: none;
      }
    }
  }
}
.breadcrumb {
  flex-shrink: 0;
  padding-left: 30rpx;
  height: 82rpx;
  border-bottom: 1px solid #ebeef5;
  font-family:
    PingFang SC,
    PingFang SC;
  font-weight: 400;
  font-size: 26rpx;
  color: #666666;
  display: flex;
  align-items: center;
  overflow-x: auto;
  overflow-y: hidden;
  white-space: nowrap;
  -webkit-overflow-scrolling: touch;

  .breadcrumb-list {
    display: inline-flex;
    align-items: center;
    width: max-content;
    flex-shrink: 0;
    padding-right: 30rpx;
    white-space: nowrap;
  }

  text {
    color: #1890ff;
  }

  .breadcrumb-sep {
    margin: 0 12rpx;
    color: #909399;
    .icon-jinru-copy {
      font-size: 24rpx;
      color: #909399;
    }
  }

  .breadcrumb-active {
    color: #303133 !important;
  }

  .breadcrumb-highlight {
    color: #1890ff !important;
    font-weight: 500;
  }
}

.empty-tip {
  text-align: center;
  padding: 60rpx 0;
  color: #909399;
  font-size: 28rpx;
}

.search-tip {
  padding: 20rpx 0;
  font-size: 24rpx;
  color: #909399;
  // background-color: #f5f5f5;
}
.lh112 {
  line-height: 112rpx;
}

.text-active {
  color: #1890ff !important;
}
</style>
