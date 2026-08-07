<template>
  <uni-forms-item class="input-label">
    <template v-slot:label>
      <view class="uni-forms-item__label">
        <text v-if="configData.effect && configData.effect.required" class="iconfont"> * </text>
        <text class="label-item" v-if="type !== 'module'">
          {{ configData.title }}
        </text>
        <text class="label-item" v-else>
          {{ configData.field_name }}
        </text>
      </view>
    </template>
    <view class="input-right-conment" @click="addMember">
      <template v-if="type == 'module'">
        <view
          class="picker-input picker-input-placeholder"
          v-if="!configData.data_dict || (configData.data_dict && configData.data_dict.length <= 0)"
        >
          {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
          <view class="iconfont icon-fanhui"></view>
        </view>
        <view class="picker-input" v-else>
          <view class="user-work-item" v-for="(item, index) in configData.data_dict" :key="index">
            <avatar v-if="item" :src="item.avatar" :radius="4"></avatar>
            <view class="user-name">{{ item.name }}</view>
          </view>
        </view>
      </template>
      <template v-else>
        <view class="picker-input picker-input-placeholder" v-if="!configData.value || (configData.value && configData.value.length <= 0)">
          {{ $t('ui.examineFormCustomCheckboxPleaseSelect') }}
          <view class="iconfont icon-fanhui"></view>
        </view>
        <view class="picker-input" v-else>
          <view class="user-work-item" v-for="(item, index) in configData.value" :key="index">
            <!-- <avatar v-if="item" :src="item.avatar" :radius="4"></avatar> -->
            <view class="user-name">{{ item.name }}{{ configData.value.length - 1 === index ? '' : '、' }}</view>
          </view>
        </view>
      </template>
    </view>
  </uni-forms-item>
  <oa-member ref="memberRef" :onlyOne="onlyOne" @confirm="confirmMember"></oa-member>
</template>

<script setup>import appI18n from '@/locale';

import { reactive, toRefs, computed, watch } from 'vue'
import avatar from '@/components/avatar/index'
import message from '@/utils/message'
import oaMember from '@/components/oaMember/index.vue'
import { getIdsFromArray } from '@/utils/helper'
import { useStore } from 'vuex'
import { navigateToDepartment, resetSelectDepartment, resetExamineIndex } from '@/utils/autoload'

const store = useStore()
const memberRef = ref(null)
const onlyOne = ref(false)
const props = defineProps({
  configData: {
    type: Object,
    default: () => {
      return {}
    },
  },
  type: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  index: {
    type: Number,
    default: -1,
  },
})
const { configData, index, type, disabled } = toRefs(props)

const emit = defineEmits(['change'])
const data = reactive({
  showPerson: false,
  onlyOneself: false,
  mode: 'selector',
})

const confirmMember = (e) => {
  emit('change', e)
}

// 添加成员
const addMember = () => {
  if (disabled.value) {
    message.error(appI18n.global.t('ui.examineFormSelectMemberManualChangesAreNotAllowed'))
    return false
  }
  memberRef.value.popupOpen(configData.value.value || [])
  store.commit('setDepSelectIndex', index.value)
}
</script>

<style scoped lang="scss">
.input-label {
  align-items: center;

  ::v-deep .uni-easyinput__content-input {
    text-align: right;
    padding-right: 0 !important;
  }

  ::v-deep .uni-forms-item__label {
    width: 148rpx;
    display: flex;
    line-height: 1.2;

    .label-item {
      width: calc(100% - 16rpx);
    }

    .iconfont {
      width: 16rpx;
    }
  }

  .input-right-conment {
    min-height: 35px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    color: $uni-text-color;

    .picker-input {
      text-align: right;
      height: 100%;
      color: $uni-text-color;
      font-size: 30rpx;
      align-items: center;
      display: flex;
      justify-content: flex-end;
      flex-wrap: wrap;
      color: #c0c4cc;

      .iconfont {
        padding-right: 16rpx;
        margin-top: 7rpx;
        transform: rotate(180deg);
        font-size: 24rpx;
        color: #c0c4cc;
      }

      .user-work-item {
        // width: 60rpx;
        height: 60rpx;
        // margin: 0rpx 16rpx 0 0;
        line-height: 60rpx;

        .user-name {
          font-size: 28rpx !important;
          text-align: center;
          color: #303133;
        }
      }
    }
  }
}
</style>
