<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar
        :index="1"
        :default-title="data.repeatTitle"
        @goBackChange="goBackChange"
        :is-right="true"
        :right-data="[]"
        :right-text="$t('ui.attendanceShiftAddSave')"
        @handleClickRight="clickSubmi"
      ></default-nav-bar>
    </view>
    <view class="cr-forms m10">
      <uni-forms :border="false" label-width="80px">
        <view class="form-item-list">
          <uni-forms-item class="date-time">
            <template v-slot:label>
              <view class="uni-forms-item__label">
                <text class="label-item">{{ $t('ui.usersScheduleCreateToDoTitle') }}</text>
                <text class="is-required">*</text>
              </view>
            </template>
            <uni-easyinput
              :inputBorder="false"
              :clearable="false"
              v-model="formData.title"
              type="text"
              :styles="styles"
              :placeholder-style="placeholderStyle"
              :maxlength="255"
              :autoHeight="true"
              :placeholder="$t('ui.usersScheduleCreateEnterAToDoTitle')"
            >
            </uni-easyinput>
          </uni-forms-item>
          <uni-forms-item class="date-time ptb0">
            <template v-slot:label>
              <view class="uni-forms-item__label">
                <text class="label-item" style="margin-top: 18rpx">{{ $t('ui.usersScheduleCreateParticipants') }}</text>
                <text class="is-required" style="padding-top: 26rpx">*</text>
              </view>
            </template>
            <uni-row class="display-align">
              <uni-col :span="23" class="players" @click="clickAdd">
                <template v-if="data.infoUser.length > 0">
                  <view class="players-list-item" v-for="item in data.infoUser" :key="item.id">
                    <avatar :src="item.avatar" :radius="4"></avatar>
                  </view>
                </template>
                <template v-else>
                  <view class="players-list-item"></view>
                </template>
              </uni-col>
              <uni-col :span="1" class="date-time-right display-align">
                <view style="margin-top: 18rpx" class="iconfont icon-jinru-copy"></view>
              </uni-col>
            </uni-row>
          </uni-forms-item>
          <uni-forms-item class="date-time">
            <template v-slot:label>
              <view class="uni-forms-item__label">
                <text class="label-item">{{ $t('ui.usersScheduleDayAllDay') }}</text>
              </view>
            </template>
            <uni-row class="display-align">
              <uni-col :span="21"> </uni-col>

              <uni-col :span="3" class="date-time-right display-align">
                <switch style="transform: scale(0.7)" :checked="formData.all_day" @change="switch1Change" />
              </uni-col>
            </uni-row>
          </uni-forms-item>

          <uni-forms-item class="date-time">
            <template v-slot:label>
              <view class="uni-forms-item__label">
                <text class="label-item">{{ $t('ui.timePopupIndexStartTime') }}</text>
                <text class="is-required">*</text>
              </view>
            </template>
            <uni-row class="display-align">
              <uni-col :span="23">
                <xp-picker
                  v-if="formData.all_day == 0"
                  :disabled="false"
                  v-model="formData.start_time"
                  mode="ymdhis"
                  actionPosition="top"
                  :placeholder="$t('ui.usersScheduleCreateSelectStartTime')"
                  :yearRange="[2014, 2050]"
                />
                <xp-picker
                  v-else
                  :disabled="false"
                  v-model="formData.start_time"
                  mode="ymd"
                  actionPosition="top"
                  :placeholder="$t('ui.usersScheduleCreateSelectStartTime')"
                  :yearRange="[2014, 2050]"
                />
              </uni-col>
              <uni-col :span="1" class="date-time-right display-align">
                <view class="iconfont icon-jinru-copy"></view>
              </uni-col>
            </uni-row>
          </uni-forms-item>
          <uni-forms-item class="date-time">
            <template v-slot:label>
              <view class="uni-forms-item__label">
                <text class="label-item">{{ $t('ui.timePopupIndexEndTime') }}</text>
                <text class="is-required">*</text>
              </view>
            </template>
            <uni-row class="display-align">
              <uni-col :span="23">
                <xp-picker
                  v-if="formData.all_day == 0"
                  :disabled="false"
                  v-model="formData.end_time"
                  mode="ymdhis"
                  actionPosition="top"
                  :placeholder="$t('ui.usersScheduleCreateSelectEndTime')"
                  :yearRange="[2014, 2050]"
                />
                <xp-picker
                  v-else
                  :disabled="false"
                  v-model="formData.end_time"
                  mode="ymd"
                  actionPosition="top"
                  :placeholder="$t('ui.usersScheduleCreateSelectEndTime')"
                  :yearRange="[2014, 2050]"
                />
              </uni-col>
              <uni-col :span="1" class="date-time-right display-align">
                <view class="iconfont icon-jinru-copy"></view>
              </uni-col>
            </uni-row>
          </uni-forms-item>
          <uni-forms-item class="date-time">
            <template v-slot:label>
              <view class="uni-forms-item__label">
                <text class="label-item">{{ $t('ui.customerListAddFollowReminderTime') }}</text>
                <text class="is-required">*</text>
              </view>
            </template>
            <uni-row class="display-align">
              <uni-col :span="23">
                <picker class="picker-selector" mode="selector" @change="changeRemind" :value="data.index" :range="repeatOption" range-key="text">
                  <view class="search-default-label">{{ data.remindText }}</view>
                </picker>
              </uni-col>
              <uni-col :span="1" class="date-time-right display-align">
                <view class="iconfont icon-jinru-copy"></view>
              </uni-col>
            </uni-row>
          </uni-forms-item>
          <uni-forms-item class="date-time">
            <template v-slot:label>
              <view class="uni-forms-item__label">
                <text class="label-item">{{ $t('ui.usersScheduleCreateDuplicate') }}</text>
                <text class="is-required">*</text>
              </view>
            </template>
            <uni-row class="display-align">
              <uni-col :span="23">
                <picker class="picker-selector" mode="selector" @change="changeRepeat" :value="data.repeatIndex" :range="option" range-key="text">
                  <view class="search-default-label">{{ data.periodText }}</view>
                </picker>
              </uni-col>
              <uni-col :span="1" class="date-time-right display-align">
                <view class="iconfont icon-jinru-copy"></view>
              </uni-col>
            </uni-row>
          </uni-forms-item>
          <template v-if="formData.period > 0">
            <uni-forms-item class="date-time" v-if="formData.period === 1 || formData.period === 4">
              <template v-slot:label>
                <view class="uni-forms-item__label">
                  <text class="label-item">{{ $t('ui.usersScheduleCustomCreateFrequency') }}{{ handleRepeat(formData.period) }})</text>
                  <text class="is-required">*</text>
                </view>
              </template>
              <uni-easyinput
                v-model="formData.rate"
                :inputBorder="false"
                :styles="styles"
                type="number"
                :clearable="false"
                :maxlength="200"
                :placeholder="$t('ui.usersScheduleCustomCreateEnterFrequency')"
              >
              </uni-easyinput>
            </uni-forms-item>
            <!-- 按周重复 -->
            <view class="center-list-item weekDays" v-if="formData.period === 2">
              <view class="weekDays-list">
                <view
                  class="weekDays-list-item"
                  v-for="(item, index) in week"
                  :key="index"
                  :class="data.weekDays.includes(item.value) ? 'active' : ''"
                  @click="weekDaysItem(item)"
                >
                  {{ item.text }}
                </view>
              </view>
            </view>
            <!--按月重复 -->
            <view class="center-list-item monthDays" v-if="formData.period === 3">
              <view class="monthDays-list">
                <view
                  class="monthDays-list-item"
                  v-for="(item, index) in 31"
                  :key="index"
                  :class="data.monthDays.includes(item) ? 'active' : ''"
                  @click="monthDaysItem(item)"
                >
                  {{ item }}
                </view>
              </view>
            </view>
            <uni-forms-item class="date-time">
              <template v-slot:label>
                <view class="uni-forms-item__label">
                  <text class="label-item">{{ $t('ui.usersScheduleCreateRepeatEndDate') }}</text>
                </view>
              </template>
              <uni-row class="display-align">
                <uni-col :span="23">
                  <xp-picker
                    :disabled="false"
                    v-model="formData.fail_time"
                    mode="ymd"
                    actionPosition="top"
                    :placeholder="$t('ui.usersScheduleCustomCreateNeverEnd')"
                    :yearRange="[2014, 2050]"
                  />
                </uni-col>
                <uni-col :span="1" class="date-time-right display-align">
                  <view class="iconfont icon-jinru-copy"></view>
                </uni-col>
              </uni-row>
            </uni-forms-item>
          </template>
          <uni-forms-item class="date-time">
            <template v-slot:label>
              <view class="uni-forms-item__label">
                <text class="label-item">{{ $t('ui.usersScheduleCreateScheduleType') }}</text>
                <text class="is-required">*</text>
              </view>
            </template>
            <uni-row class="display-align">
              <uni-col :span="23">
                <picker class="picker-selector" mode="selector" @change="changeType" :value="data.typeIndex" :range="data.typeData" range-key="name">
                  <view class="search-default-label" v-if="data.typeData.length > 0">
                    {{ data.typeData[data.typeIndex].name }}
                  </view>
                </picker>
              </uni-col>
              <uni-col :span="1" class="date-time-right display-align">
                <view class="iconfont icon-jinru-copy"></view>
              </uni-col>
            </uni-row>
          </uni-forms-item>
        </view>
        <uni-forms-item class="form-item-list is-direction-top">
          <template v-slot:label>
            <view class="uni-forms-item__label">
              <text class="label-item">{{ $t('ui.usersScheduleCreateTodoContent') }}</text>
              <!-- <text class="is-required">*</text> -->
            </view>
          </template>
          <c-editor :content="formData.content" :placeholder="$t('ui.usersScheduleCreatePleaseEnterTodoContent')" @saveContent="saveContent"></c-editor>
        </uni-forms-item>
      </uni-forms>
    </view>
    <!-- 选择人员组件 -->
    <oa-member ref="memberRef" :onlyOne="onlyOne" @confirm="confirmMember"></oa-member>

    <global-index></global-index>
  </view>
</template>

<script setup lang="ts">import appI18n from '@/locale';

import defaultNavBar from '@/components/defaultNavBar/index.vue'
import oaMember from '@/components/oaMember/index.vue'
import cEditor from '@/components/editor-common/editor.vue'
import globalIndex from '@/components/globalIndex/index.vue'
import { reactive, ref, type Ref, computed, watch } from 'vue'
import moment from 'moment'
import message from '@/utils/message'
import { delayedReLaunch, clickNavigateTo, getFindIndex } from '@/utils/helper'
import type { PickerType, Res } from '@/utils/typeHelper'
import { onLoad } from '@dcloudio/uni-app'
import { scheduleSaveApi, scheduleDetailApi, scheduleEditApi, scheduleTypesApi } from '@/api/user'
import avatar from '@/components/avatar/index.vue'
import { resetSelectDepartment } from '@/utils/autoload'
import { useStore } from 'vuex'
import { decryptQuery } from '@/utils/cryptoCode'
import { navigateToDepartment } from '@/utils/autoload'

const store = useStore()
const placeholderStyle = ref('#C0C4CC')
const loading: Ref<boolean> = ref(false)
const styles = reactive({
  color: '#303133',
  disableColor: '#ffffff',
})
// 获取
const userInfo = computed(() => store.state.app.userInfo)
const option = reactive([
  { value: 0, text: appI18n.global.t('ui.usersScheduleCreateNoRepeat') },
  { value: 1, text: appI18n.global.t('ui.usersScheduleCustomCreateRepeatDaily') },
  { value: 2, text: appI18n.global.t('ui.usersScheduleCustomCreateRepeatWeekly') },
  { value: 3, text: appI18n.global.t('ui.usersScheduleCustomCreateRepeatMonthly') },
  { value: 4, text: appI18n.global.t('ui.usersScheduleCustomCreateRepeatYearly') },
])

const repeatOption = reactive([
  { value: -1, text: appI18n.global.t('ui.usersScheduleCreateNoReminder') },
  { value: 0, text: appI18n.global.t('ui.usersScheduleCreateAtTaskStart') },
  { value: 1, text: appI18n.global.t('ui.usersScheduleCreate5MinutesBefore') },
  { value: 2, text: appI18n.global.t('ui.usersScheduleCreate15MinutesBefore') },
  { value: 3, text: appI18n.global.t('ui.usersScheduleCreate30MinutesBefore') },
  { value: 4, text: appI18n.global.t('ui.usersScheduleCreate1HourBefore') },
  { value: 5, text: appI18n.global.t('ui.usersScheduleCreate2HoursBefore') },
  { value: 6, text: appI18n.global.t('ui.usersScheduleCreate1DayBefore') },
  { value: 7, text: appI18n.global.t('ui.usersScheduleCreate2DaysBefore') },
  { value: 8, text: appI18n.global.t('ui.usersScheduleCreate1WeekBefore') },
])

const week = reactive([
  { value: 1, text: appI18n.global.t('ui.usersScheduleCustomCreateMonday') },
  { value: 2, text: appI18n.global.t('ui.usersScheduleCustomCreateTuesday') },
  { value: 3, text: appI18n.global.t('ui.usersScheduleCustomCreateWednesday') },
  { value: 4, text: appI18n.global.t('ui.usersScheduleCustomCreateThursday') },
  { value: 5, text: appI18n.global.t('ui.usersScheduleCustomCreateFriday') },
  { value: 6, text: appI18n.global.t('ui.usersScheduleCustomCreateSaturday') },
  { value: 7, text: appI18n.global.t('ui.usersScheduleCustomCreateSunday') },
])

onLoad((e) => {
  getScheduleTypes()
  if (e.id) {
    const options = decryptQuery(e.id)
    data.id = options.id
    data.eType = Number(options.type)
    data.start = options.start
    data.end = options.end
    getScheduleDetail(data.id, {
      start_time: options.start,
      end_time: options.end,
    })
    data.repeatTitle = '编辑日程'
    uni.setNavigationBarTitle({ title: appI18n.global.t('ui.usersScheduleCreateEditSchedule') })
  } else if (e.time) {
    let timeObj = JSON.parse(e.time)
    formData.start_time = moment(timeObj.start_time).format('yyyy-MM-DD HH:mm:ss')
    formData.end_time = moment(timeObj.end_time).format('yyyy-MM-DD HH:mm:ss')
  } else {
    data.id = 0
    // 新建日程时默认添加自己为参与人之一
    setUserSelect()
  }
})
const memberRef = ref(null)
// 返回的回掉函数清楚选中人员
const goBackChange = () => {
  resetSelectDepartment()
}

const switch1Change = (e) => {
  formData.all_day = e.detail.value ? 1 : 0
  if (formData.all_day == 1) {
    formData.start_time = moment(formData.start_time).format('YYYY-MM-DD')
    formData.end_time = moment(formData.end_time).format('YYYY-MM-DD')
  } else {
    formData.start_time = moment().format('yyyy-MM-DD HH:mm:ss')
    formData.end_time = moment().add(1, 'hours').format('yyyy-MM-DD HH:mm:ss')
  }
}

const setUserSelect = (): void => {
  // store.commit('setDepSelectPeople', data.infoUser)
  // store.commit('setDepSelectIds', [userInfo.value.id])
}

const formData: any = reactive({
  title: '',
  content: '',
  remind: 1,
  member: [],
  start_time: moment().format('yyyy-MM-DD HH:mm:ss'),
  end_time: moment().add(1, 'hours').format('yyyy-MM-DD HH:mm:ss'),
  period: 0,
  rate: 1,
  cid: 0,
  days: [],
  fail_time: '',
  all_day: 0,
})

const data = reactive({
  periodText: '不重复',
  remindText: '提前5分钟',
  repeatTitle: '新建日程',
  isEdit: false,
  id: 0,
  repeatIndex: 0,
  typeIndex: 0,
  infoUser: [{ id: userInfo.value.id, name: userInfo.value.name, avatar: userInfo.value.avatar }],
  index: 2,
  weekDays: [],
  monthDays: [],
  typeData: [],
  eType: -1,
  contents: '',
  detail: {},
  start: '',
  end: '',
})

enum Repeat {
  天 = 1,
  周,
  月,
  年,
}
const handleRepeat = (e: number): string => {
  return Repeat[e]
}

// 判断开始时间与结束时间的大小 返回boolean值
const isDateGreater = (dateTime1: string, dateTime2: string): boolean => {
  const dateTime1Timestamp = new Date(dateTime1.replace(/-/g, '/')).getTime()
  const dateTime2Timestamp = new Date(dateTime2.replace(/-/g, '/')).getTime()
  return dateTime1Timestamp > dateTime2Timestamp
}

// 监听内容变化
const saveContent = (e: string) => {
  data.contents = e
}

const clickSubmi = (): boolean => {
  if (!formData.title) {
    message.error(appI18n.global.t('ui.usersScheduleCreatePleaseEnterToDoTitle'))
    return false
  }
  if (!formData.start_time) {
    message.error(appI18n.global.t('ui.usersScheduleCreateSelectStartTime'))
    return false
  }
  if (!formData.end_time) {
    message.error(appI18n.global.t('ui.usersScheduleCreateSelectEndTime'))
    return false
  }
  if (data.infoUser.length <= 0) {
    message.error(appI18n.global.t('ui.usersScheduleCreateSelectParticipants'))
    return false
  }
  if (formData.period === 2 && data.weekDays.length <= 0) {
    message.error(appI18n.global.t('ui.usersScheduleCreatePleaseSelectARepeatingWeek'))
    return false
  }
  if (formData.period === 3 && data.monthDays.length <= 0) {
    message.error(appI18n.global.t('ui.usersScheduleCreateSelectRepeatDate'))
    return false
  }
  // if (!data.contents) {
  //   message.error('请填写待办内容')
  //   return false
  // }
  if (isDateGreater(formData.start_time, formData.end_time)) {
    message.error(appI18n.global.t('ui.examineFormTimeFromTheEndTimeCannotBeEarlierThanTheStart'))
    return false
  }
  if (formData.fail_time && isDateGreater(formData.start_time, `${formData.fail_time} 23:59:59`)) {
    message.error(appI18n.global.t('ui.usersScheduleCreateRepeatEndTimeCannotBeEarlierThanTheStart'))
    return false
  }

  // 判断重复类型
  if (formData.period === 2) {
    formData.days = data.weekDays
  } else if (formData.period === 3) {
    formData.days = data.monthDays
  } else {
    formData.days = []
  }

  formData.member = []
  data.infoUser.forEach((val) => {
    formData.member.push(val.id)
  })

  formData.cid = data.typeData[data.typeIndex] ? data.typeData[data.typeIndex].id : ''

  formData.content = data.contents

  if (!loading.value) {
    if (data.id > 0) {
      formData.type = data.eType
      formData.start = data.start
      formData.end = data.end
      scheduleEdit(data.id, formData)
    } else {
      scheduleSave(formData)
    }
  }
}

// 获取类型
const getScheduleTypes = () => {
  scheduleTypesApi()
    .then((res: Res) => {
      if (res.data.length > 0) {
        res.data.map((item: any) => {
          if (item.is_public !== 0) {
            data.typeData.push(item)
          }
        })
      }
    })
    .catch((error: Res) => {
      message.error(error.message)
    })
}

// 添加日程
const scheduleSave = (data: object) => {
  loading.value = true
  scheduleSaveApi(data)
    .then((res: Res) => {
      loading.value = true
      message.success(res.message, 'none')
      delayedReLaunch('/pages/users/schedule/index')
      resetSelectDepartment()
    })
    .catch((error: Res) => {
      loading.value = false
      message.error(error.message)
    })
}

// 编辑日程
const scheduleEdit = (id: number, data: object) => {
  loading.value = true
  scheduleEditApi(id, data)
    .then((res: Res) => {
      loading.value = false
      message.success(res.message, 'none')
      delayedReLaunch('/pages/users/schedule/index')
      resetSelectDepartment()
    })
    .catch((error: Res) => {
      loading.value = false
      message.error(error.message)
    })
}

const confirmMember = (e) => {
  data.infoUser = e
}

// 添加成员
const clickAdd = (): void => {
  memberRef.value.popupOpen(data.infoUser)
  // let ids: Array<number> = []
  // data.infoUser.forEach((value: any) => {
  //   ids.push(value.id)
  // })
  // store.commit('setDepSelectPeople', data.infoUser)
  // store.commit('setDepSelectIds', ids)
  // const str = `isShow=true&isFirst=1&mode=multiSelector&isSelect=1`
  // navigateToDepartment(str, 'pages/users/schedule/create')
}

// 获取详情
const getScheduleDetail = (id: number, datas: object): void => {
  scheduleDetailApi(id, datas)
    .then((res: Res) => {
      const config = res.data
      data.detail = res.data
      formData.title = config.title
      if (data.eType <= 1) {
        formData.start_time = data.start
        formData.end_time = data.end
      } else {
        formData.start_time = config.start_time
        formData.end_time = config.end_time
      }
      formData.all_day = config.all_day
      if (formData.all_day == 1) {
        formData.start_time = moment(formData.start_time).format('YYYY-MM-DD')
        formData.end_time = moment(formData.end_time).format('YYYY-MM-DD')
      }

      formData.remind = config.remindInfo.ident
      data.index = getFindIndex(repeatOption, formData.remind, 'value')
      data.remindText = repeatOption[data.index].text
      formData.period = config.period
      data.repeatIndex = getFindIndex(option, config.period, 'value')
      data.periodText = option[data.repeatIndex].text

      if (formData.period === 2) {
        data.weekDays = config.days
      } else if (formData.period === 3) {
        data.monthDays = config.days
      }
      formData.fail_time = config.fail_time
      formData.rate = config.rate
      data.contents = config.content
      setTimeout(() => {
        formData.content = config.content
      }, 300)
      formData.cid = config.type.id

      data.typeIndex = getFindIndex(data.typeData, formData.cid)

      if (config.user.length > 0) {
        data.infoUser = config.user

        // let ids: Array<number> = []
        // config.user.forEach((value: any) => {
        //   ids.push(value.id)
        //   value.card = {
        //     name: value.name,
        //     avatar: value.avatar,
        //     id: value.id,
        //   }
        // })

        // store.commit('setDepSelectPeople', config.user)
        // store.commit('setDepSelectIds', ids)
      }
    })
    .catch((error: Res) => {
      message.error(error.message)
    })
}

// 重复
const changeRepeat = (e: any) => {
  let len = e.detail.value
  formData.period = option[len].value
  data.periodText = option[len].text
}
// 选择提醒
const changeRemind = (e: any) => {
  let len = e.detail.value
  data.index = len
  formData.remind = repeatOption[len].value
  data.remindText = repeatOption[len].text
}
// 选择日程类型
const changeType = (e: any) => {
  let len = e.detail.value
  data.typeIndex = len
  formData.cid = data.typeData[len].id
}

// 周选择
const weekDaysItem = (item: PickerType) => {
  let len = data.weekDays.indexOf(item.value)
  if (len === -1) {
    data.weekDays.push(item.value)
  } else {
    data.weekDays.splice(len, 1)
  }
}
// 月选择
const monthDaysItem = (item: PickerType) => {
  let len = data.monthDays.indexOf(item)
  if (len === -1) {
    data.monthDays.push(item)
  } else {
    data.monthDays.splice(len, 1)
  }
}
</script>

<style scoped lang="scss">
::v-deep .ql-container {
  height: auto;
  min-height: 560rpx;
}

.content {
  width: 100%;
  position: relative;

  .cr-position-header {
    position: sticky;
  }

  .cr-forms {
    padding-bottom: 56rpx;

    .form-item-list {
      border-radius: 12rpx;
      background-color: #fff;
      margin-bottom: 20rpx;
    }

    .is-direction-top {
      background-color: #fff;
    }

    ::v-deep .uni-forms-item {
      padding: 36rpx 24rpx 18rpx 24rpx;
      margin-bottom: 20rpx;

      .uni-forms-item__label {
        height: auto;
        padding: 0;
        font-size: $uni-font-size-default;
        color: $uni-text-color;
        line-height: 1;
        display: flex;
        align-items: center;
      }

      .uni-easyinput__placeholder-class {
        font-size: $uni-font-size-default;
        color: $uni-text-color-five;
        font-weight: 400;
      }

      .is-disabled {
        color: $uni-text-color;
      }

      .colorBox {
        width: 32rpx;
        height: 32rpx;
        border-radius: 50%;
        margin: 20rpx 0;
      }

      .uni-textarea-textarea {
        font-size: $uni-font-size-default;
      }

      .is-required {
        color: #dd524d;
        margin-top: 6rpx;
        margin-left: 4rpx;
        font-weight: bold;
      }

      .uni-easyinput__content-textarea {
        min-height: 320rpx;
      }

      .mask {
        .uni-easyinput__content-textarea {
          min-height: 180rpx;
        }
      }

      .uni-date-x {
        padding: 0;

        .uniui-calendar {
          display: none;
        }
      }
    }

    .search-default-label {
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      font-size: $uni-font-size-default;
      color: $uni-text-color;
    }

    .date-time {
      margin-bottom: 0 !important;
      margin-left: 24rpx;
      padding: 18rpx 24rpx 18rpx 0 !important;
      border-bottom: 1px solid $uni-line-style-color-three;

      &.ptb0 {
        padding-top: 0 !important;
      }

      &:last-of-type {
        border-bottom: none;
      }

      ::v-deep .uni-input-wrapper {
        .uni-input-placeholder {
          text-align: right;
        }

        .uni-input-input {
          text-align: right;
        }
      }

      ::v-deep .xp-h-full {
        height: 35px;

        .picker-label {
          justify-content: flex-end;
          font-size: $uni-font-size-default;

          &.is-placeholder {
            color: $uni-text-color-five;
          }
        }
      }

      .date-time-right {
        .iconfont {
          font-size: 28rpx;
          color: #c0c4cc;
        }
      }

      .players {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;

        .players-list-item {
          width: 56rpx;
          height: 56rpx;
          position: relative;
          border-radius: 4rpx;
          margin: 14rpx 16rpx 0 0;
        }
      }
    }

    ::v-deep .checklist-box.is--tag {
      margin-right: 0;
    }
  }

  ::v-deep .editor-content {
    padding: 14rpx 0;
  }

  .weekDays {
    padding: 44rpx 40rpx 24rpx 40rpx;

    .weekDays-list {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      font-size: $uni-font-size-default;
      color: $uni-text-color;

      .weekDays-list-item {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20rpx;
        width: 132rpx;
        height: 60rpx;
        margin-right: 34rpx;

        &:nth-last-of-type(4n) {
          margin-right: 0;
        }

        &.active {
          background-color: $uni-color-primary;
          color: #ffffff;
          border-radius: 8rpx;
        }
      }
    }
  }

  .monthDays {
    padding: 44rpx 26rpx 0 26rpx;

    .monthDays-list {
      display: flex;
      flex-wrap: wrap;
      font-size: $uni-font-size-default;
      color: $uni-text-color;

      .monthDays-list-item {
        width: 56rpx;
        height: 56rpx;
        border-radius: 50%;
        margin-right: 44rpx;
        margin-bottom: 34rpx;
        display: flex;
        align-items: center;
        justify-content: center;

        &:nth-of-type(7n) {
          margin-right: 0;
        }

        &.active {
          background-color: $uni-color-primary;
          color: #ffffff;
        }
      }
    }
  }

  .report-button {
    margin-top: 34rpx;

    uni-button {
      font-size: 30rpx;
      height: 80rpx;
      line-height: 80rpx;
    }
  }
}
</style>
