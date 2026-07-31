<template>
  <view class="uni-steps">
    <view class="uni-steps-title">
      {{ $t('ui.usersExamineCheckProcessApprovalProcess') }}
      <view class="uni-steps-title-tip" v-if="data.approverDelete || data.copyerDelete">{{ $t('ui.usersExamineProcessCannot') }}{{ data.title }})</view>
    </view>
    <view class="uni-steps__column">
      <view class="uni-steps__column-text-container">
        <view class="uni-steps-container" v-for="(item, index) in examineData.list" :key="index">
          <view class="uni-steps__column-container">
            <view class="uni-steps__column-line-item">
              <view class="uni-steps__column-check">
                <text
                  :color="activeColor"
                  class="iconfont"
                  :class="item.types === 1 ? 'icon-shenpixiangqing-shenpi' : 'icon-shenpixiangqing-chaosong'"
                ></text>
              </view>
              <view
                class="uni-steps__column-line uni-steps__column-line--before"
                :style="{ backgroundColor: index <= active && index !== 0 ? activeColor : index === 0 ? 'transparent' : deactiveColor }"
              >
              </view>
              <view
                class="uni-steps__column-line uni-steps__column-line--after"
                :style="{
                  backgroundColor:
                    index < active && index !== examineData.list.length - 1
                      ? activeColor
                      : index === examineData.list.length - 1
                        ? 'transparent'
                        : deactiveColor,
                }"
              >
              </view>
            </view>
          </view>

          <view class="uni-steps__column-text">
            <view class="uni-steps__column-title">
              {{ item.title }}
              <uni-tag
                v-if="item.types == 1 && item.settype != 5 && item.examine_mode > 0 && item.users.length > 1"
                class="uni-steps-tag"
                :inverted="true"
                :text="getExamineText(item.examine_mode)"
                type="primary"
              />
            </view>
            <view class="uni-steps__column-user">
              <template v-for="(items, indexs) in item.users" :key="'user' + indexs">
                <view class="uni-steps__column-user-left">
                  <view class="image">
                    <avatar :src="items.card ? items.card.avatar : items.avatar" :radius="8"></avatar>
                  </view>
                  <view class="name line1">{{ items.card ? items.card.name : items.name }}</view>

                  <view v-if="item.types == 1 && item.examine_mode == 3 && indexs !== item.users.length - 1" class="iconfont icon-fanhui"></view>

                  <view class="icon-clear" v-if="item.types == 1 && !data.examineRules.edit.includes(1)" @click="deleteUsersItem(item.users, indexs)">
                    <uni-icons type="closeempty" size="13" color="#fff"></uni-icons>
                  </view>
                  <view class="icon-clear" v-if="item.types == 2 && !data.examineRules.edit.includes(2)" @click="deleteUsersItem(item.users, indexs)">
                    <uni-icons type="closeempty" size="13" color="#fff"></uni-icons>
                  </view>
                </view>
              </template>
              <!-- 审批人 -->
              <view class="uni-steps__column-user-left" v-if="isShowAddUser(item)" @click="clickDep(index, item)">
                <view class="iconfont-content">
                  <text class="iconfont icon-xuanfuanniu-jia"></text>
                </view>
              </view>
              <!-- 抄送人 -->
              <view class="uni-steps__column-user-left" v-if="item.types == 2 && item.self_select == 1" @click="clickDep(index, item)">
                <view class="iconfont-content">
                  <text class="iconfont icon-xuanfuanniu-jia"></text>
                </view>
              </view>
            </view>
          </view>
        </view>
      </view>
    </view>
    <!-- 选择人员组件 -->
    <oa-member ref="memberRef" :onlyOne="onlyOne" :specifyMember="specifyMember" @confirm="confirmMember"></oa-member>
  </view>
</template>

<script setup>
import { reactive, toRefs, watch, computed } from 'vue'
import { navigateToDepartment, resetExamineIndex, resetSelectDepartment } from '@/utils/autoload'
import avatar from '@/components/avatar/index'
import oaMember from '@/components/oaMember/index.vue'
import { useStore } from 'vuex'
const store = useStore()

const props = defineProps({
  activeColor: {
    // 激活状态颜色
    type: String,
    default: '#2979FF',
  },
  deactiveColor: {
    // 未激活状态颜色
    type: String,
    default: 'rgba(48, 139, 248, 0.3)',
  },
  active: {
    // 当前步骤
    type: Number,
    default: 0,
  },
  examineData: {
    type: Object,
    default() {
      return {}
    },
  },
})
const { activeColor, deactiveColor, active, examineData } = toRefs(props)
const data = reactive({
  examineList: [],
  examineRules: [],
  approverDelete: false,
  copyerDelete: false,
  title: '',
  index: -1,
})

const memberRef = ref(null)
const onlyOne = ref(false)
const specifyMember = ref([])

const getSelectPeople = computed(() => {
  return store.state.app.depSelectPeople
})

const getExamineText = (id) => {
  let str = ''
  if (id == 1) {
    str = '或签'
  } else if (id == 2) {
    str = '会签'
  } else if (id == 3) {
    str = '依次审批'
  }
  return str
}
const isShowAddUser = (item) => {
  return (item.select_mode == 1 && item.users.length == 0) || item.select_mode == 2
}

const deleteUsersItem = (row, index) => {
  row.splice(index, 1)
}

const clickDep = (index, row) => {
  store.commit('setDepUserIndex', index)
  data.index = index
  if (row.types == 1) {
    // 判断单选或多选成员
    onlyOne.value = row.select_mode == 1 ? true : false
  } else {
    onlyOne.value = false
  }
  if (row.types == 1 && row.settype == 4 && row.options.length > 0) {
    specifyMember.value = row.options
  } else {
    specifyMember.value = []
  }

  memberRef.value.popupOpen(row.users || row.options)
}

// 数据监听
watch(
  examineData,
  (newvalue) => {
    if (newvalue) {
      data.examineRules = newvalue.rules
      data.title = ''

      if (data.examineRules) {
        data.approverDelete = data.examineRules.edit.includes('1')
        data.copyerDelete = data.examineRules.edit.includes('2')
        data.title += data.approverDelete ? '和修改审批人' : ''
        data.title += data.copyerDelete ? '和删除抄送人' : ''
        if (data.title.length > 0) {
          data.title = data.title.substring(1)
        }
      }
    }
  },
  { immediate: true },
)

const emit = defineEmits(['change'])

const confirmMember = (e) => {
  emit('change', e, data.index)
}
</script>

<style scoped lang="scss">
$uni-primary: #2979ff !default;
$uni-border-color: #ededed;

.uni-steps {
  /* #ifndef APP-NVUE */
  display: flex;
  width: 100%;
  /* #endif */
  /* #ifdef APP-NVUE */
  flex: 1;
  /* #endif */
  flex-direction: column;
  padding: 15px;
}

.uni-steps-title {
  font-size: $uni-font-size-default;
  font-weight: 600;
  padding-bottom: 26rpx;
  display: flex;
  align-items: center;

  .uni-steps-title-tip {
    padding-left: 12rpx;
    font-size: 26rpx;
    color: $nui-text-color-four;
    font-weight: normal;
  }
}

.uni-steps__row {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: column;
}

.uni-steps__column {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: row-reverse;
}

.uni-steps__row-text-container {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: row;
  align-items: flex-end;
  margin-bottom: 8px;
}

.uni-steps__column-text-container {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: column;
  flex: 1;
}

.uni-steps__row-text {
  /* #ifndef APP-NVUE */
  display: inline-flex;
  /* #endif */
  flex: 1;
  flex-direction: column;
}

.uni-steps-container {
  display: flex;
}

.uni-steps__column-text {
  padding-bottom: 30rpx;
  width: calc(100% - 30px);
  display: flex;
  flex-direction: column;
}

.uni-steps__row-title {
  font-size: 14px;
  line-height: 16px;
  text-align: center;
}

.uni-steps__column-title {
  font-size: 28rpx;
  font-weight: 400;
  color: $nui-text-color-two;
  padding-left: 20rpx;
  padding-top: 10rpx;
  text-align: left;
  line-height: 28rpx;

  .uni-steps-tag {
    padding: 0 4rpx;
    font-size: 24rpx;
    font-weight: 400;
    margin-left: 10rpx;
  }
}

.uni-steps__column-user {
  display: flex;
  flex-wrap: wrap;
  margin-left: 20rpx;

  // ::v-deep .uni-row {
  //   margin-bottom: 16rpx;
  // }

  .uni-steps__column-user-left {
    padding-top: 40rpx;
    font-size: 24rpx;
    font-weight: 400;
    text-align: center;
    color: $nui-text-color-four;
    position: relative;
    // width: 110rpx;
    margin-right: 30rpx;

    &:last-of-type {
      margin-right: 0;
    }

    .image {
      width: 70rpx;
      height: 70rpx;
      display: inline-block;
    }

    .name {
      text-align: center;
    }

    .iconfont-content {
      width: 70rpx;
      height: 70rpx;
      border-radius: 8rpx;
      background: rgba(236, 237, 240, 0.5);
      border: 1px solid #f0f1f5;

      .iconfont {
        font-size: 30rpx;
        color: #bcbdc0;
        position: static;
      }
    }

    .iconfont {
      font-size: 26rpx;
      position: absolute;
      right: -32rpx;
      top: 38rpx;
      transform: rotate(180deg);
      line-height: 70rpx;
    }

    .icon-clear {
      position: absolute;
      right: -10rpx;
      top: 20rpx;
      width: 32rpx;
      height: 32rpx;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #c0c4cc;
      border-radius: 50%;
    }
  }

  .uni-steps__column-user-right {
    text-align: right;
    font-size: 24rpx;
    font-weight: 400;
    color: $nui-text-color-four;
  }
}

.uni-steps__row-desc {
  font-size: 12px;
  line-height: 14px;
  text-align: center;
}

.uni-steps__column-desc {
  font-size: 12px;
  text-align: left;
  line-height: 18px;
}

.uni-steps__row-container {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: row;
}

.uni-steps__column-container {
  /* #ifndef APP-NVUE */
  display: inline-flex;
  /* #endif */
  width: 30px;
  flex-direction: column;
}

.uni-steps__row-line-item {
  /* #ifndef APP-NVUE */
  display: inline-flex;
  /* #endif */
  flex-direction: row;
  flex: 1;
  height: 14px;
  line-height: 14px;
  align-items: center;
  justify-content: center;
}

.uni-steps__column-line-item {
  /* #ifndef APP-NVUE */
  display: flex;
  /* #endif */
  flex-direction: column;
  flex: 1;
  align-items: center;
  justify-content: center;
}

.uni-steps__row-line {
  flex: 1;
  height: 1px;
  background-color: #b7bdc6;
}

.uni-steps__column-line {
  width: 2px;
  background-color: #b7bdc6;
}

.uni-steps__row-line--after {
  transform: translateX(1px);
}

.uni-steps__column-line--after {
  flex: 1;
  transform: translate(0px, 1px);
}

.uni-steps__row-line--before {
  transform: translateX(-1px);
}

.uni-steps__column-line--before {
  transform: translate(0, -13px);
}

.uni-steps__row-circle {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background-color: #b7bdc6;
  margin: 0px 3px;
}

.uni-steps__column-circle {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background-color: #b7bdc6;
  margin: 4px 0px 5px 0px;
}

.uni-steps__row-check {
  margin: 0px 6px;
}

.uni-steps__column-check {
  height: 48rpx;
  width: 48rpx;
  line-height: 48rpx;
  background-color: rgba(48, 139, 248, 0.1);
  text-align: center;
  border-radius: 50%;

  .iconfont {
    font-size: 30rpx;
    color: $uni-color-primary;
  }
}
</style>
