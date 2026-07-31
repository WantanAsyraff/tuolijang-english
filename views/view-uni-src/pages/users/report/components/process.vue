<template>
  <view class="uni-steps">
    <view>
      {{ $t('ui.usersReportProcessReportTo') }}
      <view class="uni-steps-title-tip" v-if="data.approverDelete || data.copyerDelete">{{ $t('ui.usersExamineProcessCannot') }}{{ data.title }}) </view>
    </view>
    <view class="uni-steps__column">
      <view class="uni-steps__column-text-container">
        <view class="uni-steps-container" v-for="(item, index) in examineData" :key="index">
          <view class="uni-steps__column-text">
            <view class="uni-steps__column-user">
              <template v-for="(items, indexs) in item" :key="'user' + indexs">
                <view class="uni-steps__column-user-left">
                  <view class="image">
                    <avatar :src="items.avatar" :radius="8"></avatar>
                  </view>
                  <view class="name line1">{{ items.name }}</view>

                  <view v-if="item.types == 1 && item.examine_mode == 3 && indexs !== item.users.length - 1" class="iconfont icon-fanhui"></view>
                  <view class="icon-clear" v-if="item.types == 1 && !data.approverDelete" @click="deleteUsersItem(item.users, indexs)">
                    <uni-icons type="closeempty" size="13" color="#fff"></uni-icons>
                  </view>
                  <view class="icon-clear" v-if="item.types == 2 && !data.copyerDelete" @click="deleteUsersItem(item.users, indexs)">
                    <uni-icons type="closeempty" size="13" color="#fff"></uni-icons>
                  </view>
                </view>
              </template>
              <view v-if="!isDefault" class="uni-steps__column-user-left" @click="clickDep(index, item)">
                <view class="iconfont-content">
                  <text class="iconfont icon-xuanfuanniu-jia"></text>
                </view>
              </view>
            </view>
          </view>
        </view>
        <view v-if="!isDefault && examineData.length === 0" class="uni-steps__column-user-left" @click="clickDep(0, [])">
          <view class="iconfont-content">
            <text class="iconfont icon-xuanfuanniu-jia"></text>
          </view>
        </view>
      </view>
    </view>
    <oa-member ref="memberRef" @confirm="confirmMember"></oa-member>
  </view>
</template>

<script setup>
import { reactive, toRefs, watch, computed } from 'vue'
import { navigateToDepartment, resetExamineIndex, resetSelectDepartment } from '@/utils/autoload'
import oaMember from '@/components/oaMember/index.vue'
import avatar from '@/components/avatar/index'
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
  isDefault: {
    type: Boolean,
    dafault: false,
  },
})
const { examineData } = toRefs(props)

const data = reactive({
  examineList: [],
  examineRules: [],
  approverDelete: false,
  copyerDelete: false,
  title: '',
  index: -1,
})

const deleteUsersItem = (row, index) => {
  row.splice(index, 1)
}
const memberRef = ref(null)

// 选择人员
const clickDep = (index, row) => {
  memberRef.value.popupOpen(row)
}

const confirmMember = (e) => {
  examineData.value[0] = e
}

// 数据监听
watch(
  examineData,
  (newvalue) => {
    if (newvalue) {
      data.examineRules = newvalue.rules
      data.title = ''
    }
  },
  {
    immediate: true,
  },
)
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
  padding: 30rpx 30rpx;
  border-radius: 16rpx;
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
  // padding-bottom: 30rpx;
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

.uni-steps__column-user-left {
  padding-top: 40rpx;
  font-size: 24rpx;
  font-weight: 400;
  text-align: center;
  color: $nui-text-color-four;
  position: relative;
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
    right: 14rpx;
    top: 18rpx;
    width: 32rpx;
    height: 32rpx;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #c0c4cc;
    border-radius: 50%;
  }
}

.uni-steps__column-user {
  display: flex;
  flex-wrap: wrap;

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
