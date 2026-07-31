<template>
  <view class="content">
    <view class="todo-title" @click="openTypePopup"> {{ data.title }}·{{ data.num }} <text class="iconfont icon-jinru"></text> </view>
    <scroll-view class="todo-list" scroll-y @scrolltolower="loadMore" v-if="data.list.length || data.completedList.length || data.loading">
      <view class="todo-item" v-for="(item, index) in data.list" :key="index" @click="scheduleItem(item)">
        <view class="item-content">
          <view class="item-title over-text2">{{ item.title || '--' }}</view>
          <view class="item-time mt10" v-if="item.source_created_at">创建时间：{{ item.source_created_at }}</view>
        </view>
      </view>
      <!-- 已完成 -->
      <view v-if="data.completedList.length > 0">
        <uni-collapse>
          <uni-collapse-item :show-animation="true">
            <template v-slot:title>
              <view class="completed-collapse-title">
                <text>隐藏已完成事项</text>
              </view>
            </template>
            <view class="todo-item" v-for="(item, index) in data.completedList" :key="index" @click="scheduleItem(item)">
              <view class="item-content">
                <view class="item-title over-text2">{{ item.title || '--' }}</view>
                <view class="item-time mt10" v-if="item.source_created_at">创建时间：{{ item.source_created_at }}</view>
              </view>
            </view>
          </uni-collapse-item>
        </uni-collapse>
      </view>
      <view class="loading-more" v-if="data.loading">
        <text>加载中...</text>
      </view>
    </scroll-view>

    <!-- 空状态 -->
    <view class="empty-state" v-if="!data.list.length && !data.completedList.length && !data.loading && data.noMore && data.completedNoMore">
      <text>暂无待办</text>
    </view>
    <!-- 待办类型弹窗 -->
    <todoTypePopup ref="todoTypePopupRef" :typeList="data.typeList" @change="onTypeChange"></todoTypePopup>
  </view>
</template>
<script setup>
import { todoListApi, todoViewApi } from '@/api/user'
import { ref, reactive, onMounted } from 'vue'
import todoTypePopup from './todoTypePopup.vue'
import { clickNavigateTo } from '@/utils/helper'
const todoTypePopupRef = ref(null)
import message from '@/utils/message'

const data = reactive({
  title: '全部待办',
  num: 0,
  typeList: [],
  where: {
    page: 1,
    limit: 15,
    status: 1,
    type: '',
  },
  completedWhere: {
    page: 1,
    limit: 15,
    status: 2,
    type: '',
  },
  list: [],
  completedList: [],
  loading: false,
  noMore: false,
  completedNoMore: false,
})

const resetTodoListState = () => {
  data.where.page = 1
  data.noMore = false
  data.completedWhere.page = 1
  data.completedWhere.type = data.where.type
  data.completedNoMore = false
  data.list = []
  data.completedList = []
}

onMounted(() => {
  getTodoView()
  getTodoList(true)
})

const getTodoList = (isReset = false) => {
  if (data.loading) return
  if (isReset) {
    resetTodoListState()
  }

  data.loading = true
  let shouldLoadCompleted = false
  todoListApi(data.where)
    .then((res) => {
      const list = res.data.list || []
      if (data.where.page === 1) {
        data.list = list
      } else {
        data.list = [...data.list, ...list]
      }
      if (list.length < data.where.limit) {
        data.noMore = true
        shouldLoadCompleted = true
      }
    })
    .catch((error) => {
      message.error(error.message)
    })
    .finally(() => {
      data.loading = false
      if (shouldLoadCompleted) {
        getCompletedList(true)
      }
    })
}

// 加载已完成数据
const getCompletedList = (isReset = false) => {
  if (data.loading) return
  if (!isReset && data.completedNoMore) return
  if (isReset) {
    data.completedWhere.page = 1
    data.completedNoMore = false
    data.completedList = []
  }

  data.completedWhere.type = data.where.type
  data.loading = true
  todoListApi(data.completedWhere)
    .then((res) => {
      const list = res.data.list || []
      if (data.completedWhere.page === 1) {
        data.completedList = list
      } else {
        data.completedList = [...data.completedList, ...list]
      }
      if (list.length < data.completedWhere.limit) {
        data.completedNoMore = true
      }
    })
    .catch((error) => {
      message.error(error.message)
    })
    .finally(() => {
      data.loading = false
    })
}

const scheduleItem = (item) => {
  if (item.type === 'schedule') {
    clickNavigateTo(`/pages/users/schedule/detail?id=${item.source_id}&start=${item.extra.start_time}&end=${item.extra.end_time}`)
  } else if (['approve_pending', 'approve_submit'].includes(item.type)) {
    clickNavigateTo(`/pages/users/examine/defaults?id=${item.source_id}`)
  } else if (item.type === 'invoice') {
    clickNavigateTo(`/pages/customer/invoice/details?id=${item.source_id}`)
  } else if (item.type === 'contract') {
    clickNavigateTo(`/pages/customer/contract/details?id=${item.source_id}`)
  } else if (item.type === 'customer') {
    clickNavigateTo(`/pages/customer/list/details?id=${item.source_id}&types=customer`)
  } else if (item.type === 'notice') {
    clickNavigateTo(`/pages/users/noticeDefault/index?id=${item.source_id}`)
  } else if (['assess_appeal', 'assess_check', 'assess_self'].includes(item.type)) {
    clickNavigateTo(`/pages/users/assessment/default?id=${item.source_id}`)
  } else {
    message.error('移动端暂不支持此类待办')
  }
}

const loadMore = () => {
  if (data.loading) return
  if (!data.noMore) {
    data.where.page++
    getTodoList()
    return
  }
  if (!data.completedNoMore) {
    data.completedWhere.page++
    getCompletedList()
  }
}

const getTodoView = () => {
  todoViewApi().then((res) => {
    let totalCount = 0
    const arr = []
    for (let key in res.data) {
      totalCount += res.data[key].count
      arr.push({
        id: key,
        name: res.data[key].label,
        count: res.data[key].count,
      })
    }

    arr.unshift({
      id: '',
      name: '全部待办',
      count: totalCount,
    })
    data.num = totalCount

    data.typeList = arr
  })
}

const openTypePopup = () => {
  todoTypePopupRef.value.popupOpen()
}

const onTypeChange = (item) => {
  data.title = item.name
  data.num = item.count
  data.where.type = item.id
  getTodoList(true)
}
</script>
<style lang="scss" scoped>
.content {
  width: 100%;
  padding: 24rpx 0rpx 0 24rpx;
  .todo-title {
    cursor: pointer;
    height: 36rpx;
    line-height: 36rpx;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 500;
    font-size: 26rpx;
    color: #303133;
    margin: 30rpx 0 0 0;
    .icon-jinru {
      color: #909399;
      font-size: 24rpx;
      margin-left: 14rpx;
    }
  }
  .todo-list {
    height: calc(100vh - 300rpx);
    .todo-item {
      display: flex;
      padding-bottom: 28rpx;
      // height: 138rpx;
      padding-top: 28rpx;

      border-bottom: 1px solid #eeeeee;

      .item-content {
        flex: 1;

        .item-title {
          font-family:
            PingFang SC,
            PingFang SC;
          font-weight: 400;
          font-size: 26rpx;
          color: #303133;
        }
        .item-time {
          font-family:
            PingFang SC,
            PingFang SC;
          font-weight: 400;
          font-size: 24rpx;
          color: #909399;
          margin-top: 6rpx;
        }
      }
    }
    .loading-more {
      text-align: center;
      padding: 24rpx 0;
      font-size: 24rpx;
      color: #909399;
    }
  }
  .empty-state {
    text-align: center;
    padding: 100rpx 0;
    color: #909399;
    font-size: 26rpx;
  }
}
::v-deep .uni-collapse {
  .uni-collapse-item__title {
    padding-top: 28rpx;
    padding-bottom: 20rpx;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;
    font-size: 24rpx;
    color: #909399;
    display: flex;
    justify-content: center;
    align-items: center;
    .uni-collapse-item__title-wrap {
      width: auto;
      flex: none;
      white-space: nowrap;
    }
    .completed-collapse-title {
      display: flex;
      align-items: center;
      justify-content: center;
      white-space: nowrap;
    }
    .uni-icons {
      font-size: 12px !important;
      flex: none;
    }
  }
  .uni-collapse-item__title.uni-collapse-item-border {
    border-bottom: none;
  }
}
</style>
