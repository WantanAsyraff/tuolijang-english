<template>
  <view>
    <view v-if="(loaded || list.itemIndex < 15) && list.items && list.items.length > 0" class="uni-indexed-list__list">
      <view v-for="(item, index) in list.items" :key="index" class="uni-indexed-list__item" hover-class="uni-indexed-list__item--hover">
        <view class="uni-indexed-list__item-container" @click="onClick(item)">
          <view class="uni-indexed-list__item-border" :class="{ 'uni-indexed-list__item-border--last': index === list.items.length - 1 }">
            <view v-if="showSelect" style="margin-right: 24rpx">
              <uni-icons
                v-if="!onlyOne"
                :type="checkedArr.includes(item.id) ? 'checkbox-filled' : 'circle'"
                :color="checkedArr.includes(item.id) ? '#007aff' : '#C0C0C0'"
                size="18"
              />
            </view>

            <view class="uni-indexed-list__item-content">
              <avatar :src="item.avatar" :radius="37" :auto-size="false" :width="72" :height="72" style="margin-top: 10rpx"> </avatar>
              <view class="item-content-info">
                <text class="text">{{ item.name }}</text>
                <text class="caption" v-if="item.job">{{ item.job.name }}</text>
              </view>
            </view>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup>
import avatar from '@/components/avatar/index.vue'
import { toRefs, ref, computed, watch, reactive } from 'vue'
import { useStore } from 'vuex'
import message from '@/utils/message'
const store = useStore()

const props = defineProps({
  loaded: {
    type: Boolean,
    default: false,
  },
  idx: {
    type: Number,
    default: 0,
  },
  list: {
    type: Object,
    default() {
      return {}
    },
  },
  showSelect: {
    type: Boolean,
    default: false,
  },
  // 单选
  onlyOne: {
    type: Boolean,
    default: false,
  },
  isChecked: {
    type: Number,
    default: 0,
  },
})
const { loaded, idx, list, showSelect, onlyOne, isChecked } = toRefs(props)
let checkedArr = ref([])
let selectPeopleArr = ref([])
const userList = reactive({
  user: [],
})
const emit = defineEmits(['itemClick'])

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
  (newvalue, oldvalue) => {
    checkedArr.value = newvalue[0]
    selectPeopleArr.value = newvalue[1]
    // 判断数据加载问题
    userList.user = newvalue[1]
  },
  {
    immediate: true,
    deep: true,
  },
)

// 人员选中
const onClick = (item) => {
  if (showSelect.value) {
    // 判断禁止选择已有成员
    if (isChecked.value === 1) {
      const checkIds = store.state.app.depCheckIds
      if (checkIds.includes(item.id)) {
        return false
      }
    }
    let len = checkedArr.value.indexOf(item.id)
    if (len > -1) {
      checkedArr.value.splice(len, 1)
      userList.user = userList.user.filter((val) => val.id !== item.id)
    } else {
      // 判断是否为单选
      if (onlyOne.value) {
        if (selectPeopleArr.value.length > 0) {
          message.error('只能选择一个人员')
          return false
        }
      }
      userList.user.push(item)
      checkedArr.value.push(item.id)
    }
    let plainArr = [...checkedArr.value]
    let selectArr = [...userList.user]

    let matchingItems = selectArr.filter((item) => plainArr.includes(item.id)) // userList.user中的元素以匹配这些ID
    store.commit('setDepSelectIds', checkedArr.value)

    // store.commit('setDepSelectPeople', Array.from(new Set(matchingItems)))
    store.commit('setDepSelectPeople', selectArr)
  }
}
</script>

<style lang="scss" scoped>
.uni-indexed-list__list {
  background-color: $uni-bg-color;
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: column;
  padding-left: 30rpx;
}

.uni-indexed-list__item {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex: 1;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.uni-indexed-list__item-container {
  flex: 1;
  position: relative;
  /* #ifndef APP-NVUE */
  display: flex;
  box-sizing: border-box;
  /* #endif */
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  /* #ifdef H5 */
  cursor: pointer;
  /* #endif */

  border-bottom: 1px solid #ebeef5;
}

.uni-indexed-list__item-border {
  flex: 1;
  height: 112rpx;
  position: relative;
  /* #ifndef APP-NVUE */
  display: flex;
  box-sizing: border-box;
  /* #endif */
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding-left: 0;
}

.uni-indexed-list__item-border--last {
  border-bottom-width: 0px;
}

.uni-indexed-list__item-content {
  flex: 1;
  font-size: 14px;
  display: flex;
  align-items: center;

  .item-content-info {
    padding-left: 24rpx;
    font-family:
      PingFang SC,
      PingFang SC;
    font-weight: 400;

    uni-text {
      display: block;
    }

    .text {
      font-size: 26rpx;
      color: #303133;
    }

    .caption {
      padding-top: 4rpx;
      font-size: 22rpx !important;
      color: #909399;
    }
  }
}

.uni-indexed-list {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: row;
}

.uni-indexed-list__title-wrapper {
  /* #ifndef APP-NVUE */
  display: flex;
  width: 100%;
  /* #endif */
  padding-left: 10rpx;
}

.uni-indexed-list__title {
  padding: 6px 12px;
  line-height: 24px;
  font-size: 32rpx;
  font-weight: 500;
  color: #909399;
}
</style>
