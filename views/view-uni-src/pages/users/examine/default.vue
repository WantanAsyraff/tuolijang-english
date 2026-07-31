<template>
  <view class="content">
    <view class="cr-position-header">
      <view class="status_bar"></view>
      <default-nav-bar :default-title="data.defaultTitle" :is-right="true" :right-data="rightIcon" @handleNarItem="handleNarItem"></default-nav-bar>
    </view>

    <view class="examine-content">
      <examine-form
        ref="examineFormRef"
        :configData="data.listData"
        @change="changeExamineForm"
        :type="data.type"
        @searchData="searchData"
        @attendanceAbnormalRecord="attendanceAbnormalRecord"
      ></examine-form>

      <view class="process" v-if="data.examineData && data.examineData.list">
        <process :examine-data="data.examineData" @change="changeProcess"></process>
      </view>
    </view>

    <view class="examine-button">
      <button type="primary" :loading="loading" @click="handleConfirm">{{ $t('ui.replyComponentIndexSubmit') }}</button>
    </view>
    <global-index />
  </view>
</template>

<script setup>
import { ref, reactive } from 'vue'
import defaultNavBar from '@/components/defaultNavBar/index.vue'
import examineForm from '@/components/examineForm/index.vue'
import globalIndex from '@/components/globalIndex/index.vue'
import process from './components/process.vue'
import message from '@/utils/message'
import { clickNavigateTo, delayedReLaunch, clicKReLaunch, debounce } from '@/utils/helper'
import { attendanceAbnormalDateApi, attendanceAbnormalRecordApi, holidayTypeApi } from '@/api/business'
import { scheduleRecordApi } from '@/api/user'

const data = reactive({
  defaultTitle: '',
  dateId: 0,
  types: '',
  listData: [],
  examineData: {},
  approverDelete: false,
  copyerDelete: false,
  isEdit: false,
  aId: 0,
  examine: 0, // 判断是否走审批
  field: '',
  type: '',
  abnormal: '', // 异常日期
  record: '', // 异常记录
  holiday: '', // 假期类型
  abnormal_id: '', // 异常日期id
  record_id: 0, // 异常记录id
  holiday_id: '',
  abnormal_label: '',
  record_label: '',
  holiday_label: '',
  holiday_type: '',
  parameterData: {
    contract_id: '',
    customer_id: '',
    invoice_id: '',
    bill_id: '',
  },
  nav_type: '',
})
const loading = ref(false)
const examineFormRef = ref(null)
const rightIcon = reactive([
  {
    type: 1,
    icon: 'icon-dingbudaohang-lishijilu',
  },
])
const handleNarItem = () => {
  clickNavigateTo(`/pages/users/examine/mine?id=${data.id}&name=${data.defaultTitle}`)
}
let tryDateOptions = ref([])
let tryYiChangOptions = ref([])
let holidayTypeOptions = ref([])

const normalizeAbnormalRecordOptions = (list = []) => {
  return (Array.isArray(list) ? list : []).map((it) => ({
    ...it,
    value: it.value ?? it.shift_number,
    label: it.label ?? `${it.title || ''}${it.time ? ` : ${it.time}` : ''}`,
  }))
}

const normalizeAbnormalDateOptions = (list = []) => {
  return (Array.isArray(list) ? list : []).map((it) => ({
    ...it,
    value: it.value ?? it.id,
    label: it.label ?? it.date,
    record: normalizeAbnormalRecordOptions(it.record),
  }))
}

const syncAbnormalOptions = () => {
  const dateItem = data.listData.find((item) => item.symbol == 'attendanceExceptionDate')
  const recordItem = data.listData.find((item) => item.symbol == 'attendanceExceptionRecord')
  if (!dateItem) return

  dateItem.options = normalizeAbnormalDateOptions(dateItem.options?.length ? dateItem.options : tryDateOptions.value)

  if (data.dateId) {
    const selected = dateItem.options.find((item) => Number(item.value) === Number(data.dateId))
    if (selected) {
      dateItem.value = selected.value
      dateItem.text = selected.label
      if (recordItem) {
        recordItem.options = normalizeAbnormalRecordOptions(selected.record)
      }
    }
  }
}

import { onLoad } from '@dcloudio/uni-app'
onLoad((options) => {
  if (options.id) {
    data.id = options.id
    data.parameterData.customer_id = options.eid || ''
    data.parameterData.contract_id = options.cid || ''
    data.parameterData.invoice_id = options.invoice_id || ''
    data.parameterData.bill_id = options.bill_id || ''
    data.types = options.types
    if (options.isEdit && options.isEdit == 'true') {
      data.isEdit = true
      data.aId = options.aid
    }
  }

  if (options.nav_type) {
    data.nav_type = options.nav_type
  }

  if (options.dateId) {
    data.dateId = Number(options.dateId)
  }
  attendanceAbnormalDate()
  getHolidayType()
})

import { approveApplyFormApi, approveApplyListApi, approveApplySaveApi, approveApplyEditApi } from '@/api/business'
const approveApply = (id) => {
  approveApplyFormApi(id, data.parameterData)
    .then((res) => {
      data.defaultTitle = res.data.info.name
      data.examine = res.data.info.examine

      const arr = res.data.forms.filter((item) => item.children)
      if (arr.length > 0) {
        data.type = arr ? arr[0].type : ''
        if (arr[0].children) {
          arr[0].children.map((item) => {
            if (item.title == '假期类型') {
              data.holiday = item.field
              Reflect.set(item, 'options', holidayTypeOptions.value)
            }
          })
        }
      }

      res.data.forms.map((item) => {
        data.field = item.field
        if (item.children && item.type !== 'span' && item.type != 'approvalBill') {
          item.children.map((per) => {
            if (per.symbol == 'attendanceExceptionDate') {
              per.value = data.dateId
            }

            data.listData.push(per)
          })
        } else {
          data.listData.push(item)
        }
      })

      if (data.listData.length > 0) {
        data.listData.forEach((value) => {
          value.newvalue = ''
          if (value.type === 'approvalBill') {
            if (value.children && value.children.length > 0) {
              value.children.forEach((val) => {
                val.newvalue = ''
              })
            }
          }
        })
      }

      syncAbnormalOptions()

      // 编辑重新提交与再次提交
      if (data.isEdit) {
        approveApplyEdit(data.aId, {
          types: 0,
        })
      }

      // let result = handleFromObj(data.listData)
      if (data.examine !== 0) {
        approveApplyList(data.id, data.listData)
      }
    })
    .catch((error) => {
      message.error(error.message)
    })
}

// 选择人员
const changeProcess = (val, index) => {
  data.examineData.list[index].users = val
}
const searchData = (val) => {
  data.abnormal_id = val[0]
  data.abnormal_label = val[1]
  data.record_id = val[2]
  data.record_label = val[3]
  data.holiday_id = val[4]
  data.holiday_label = val[5]
  data.holiday_type = val[6] == 0 ? 'day' : 'time'
  data.listData.forEach((item) => {
    if (data.holiday_type && item.type == 'timeFrom') {
      item.props.timeType = data.holiday_type
    }
  })
}
// 异常日期
const attendanceAbnormalDate = () => {
  attendanceAbnormalDateApi().then((res) => {
    if (res.status == 200) {
      tryDateOptions.value = normalizeAbnormalDateOptions(res.data)
      approveApply(data.id)
    }
  })
}

// 异常记录
const attendanceAbnormalRecord = (id) => {
  attendanceAbnormalRecordApi(id).then((res) => {
    if (res.status == 200) {
      tryYiChangOptions.value = normalizeAbnormalRecordOptions(res.data)
      data.listData.map((item) => {
        if (item.title == '异常记录') {
          Reflect.set(item, 'options', tryYiChangOptions.value)
        }
      })
    }
  })
}

// 假期类型
const getHolidayType = () => {
  holidayTypeApi().then((res) => {
    holidayTypeOptions.value = res.data
  })
}

// 编辑
const approveApplyEdit = (id, datas) => {
  approveApplyEditApi(id, datas)
    .then((res) => {
      let option = res.data
      data.listData.map((value) => {
        if (option[value.field]) {
          value.newvalue = option[value.field]
          value.value = option[value.field]
        }
      })
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const changeExamineForm = (result) => {
  const datas = handleFromObj(result)
  if (data.examine !== 0) {
    approveApplyList(data.id, datas)
  }
}

// 获取人员配置表单
const approveApplyList = (id, datas) => {
  // 保存之前选择的审核人和抄送人数据
  const prevUsersMap = {}
  if (data.examineData && data.examineData.list) {
    data.examineData.list.forEach((item, index) => {
      if (item.users && item.users.length > 0) {
        prevUsersMap[index] = item.users
      }
    })
  }

  approveApplyListApi(id, datas)
    .then((res) => {
      // 合并之前选择的用户数据到新的列表中
      if (res.data.list && res.data.list.length > 0) {
        res.data.list.forEach((item, index) => {
          if (prevUsersMap[index]) {
            // 如果之前该位置有选择过用户，则保留
            item.users = prevUsersMap[index]
          }
        })
      }
      data.examineData = res.data
      var rules = res.data.rules
      let rulesEdit = res.data.rules.edit
      if (rulesEdit) {
        data.approverDelete = rulesEdit.includes('1')
        data.copyerDelete = rulesEdit.includes('2')
      }
      if (rules && rules.edit && rulesEdit.length > 0) {
        // 是否可删除审批人与抄送人
        if (data.examineData.list.length > 0) {
          data.examineData.list.forEach((value) => {
            if (value.types == 1) {
              // 判断审批人是否为指定审批人
              if (rules.abnormal > 0 && (value.settype === 2 || value.settype === 7) && value.users.length <= 0) {
                value.users.push(rules)
              }
              if (rulesEdit.includes(1)) {
                value.users.forEach((val) => {
                  val.isDelete = true
                })
              } else {
                data.editUser = true
              }
            }

            if (value.types == 2 && rulesEdit.includes(2)) {
              value.users.forEach((val) => {
                val.isDelete = true
              })
            }
          })
        }
      } else {
        data.editUser = true
      }
    })
    .catch((error) => {
      message.error(error.message)
    })
}

const handleFromObj = (datas) => {
  const obj = {}
  for (let i = 0; i < datas.length; i++) {
    const keys = datas[i]['field']
    obj[keys] = datas[i].newvalue ? datas[i].newvalue : datas[i].value ? datas[i].value : ''
  }
  return obj
}

// 表单内容提交
const handleConfirm = debounce(() => {
  examineFormRef.value.submit((fromData) => {
    const fromDatas = fromData
    const processInfo = data.examineData.list
    if (processInfo && processInfo.length && data.examine != 0) {
      let len = 0
      for (let i = 0; i < processInfo.length; i++) {
        const value = processInfo[i]
        if (!data.approverDelete && value.types == 1 && value.users.length <= 0) {
          message.error('自选节点不能为空')
          return
        }
        if (data.approverDelete && value.types == 1 && (value.settype == 4 || value.settype == 1) && value.users.length <= 0) {
          message.error('自选节点不能为空')
          return
        }
        if (value.users.length <= 0) {
          len++
        }
      }
      if (len === processInfo.length) {
        message.error('自选节点不能为空')
        return
      }
      processInfo.forEach((value, index) => {
        if (value.users.length <= 0) {
          processInfo.splice(index, 1)
        }
      })
    }
    const datas = {
      formInfo: fromDatas,
      processInfo: processInfo,
    }
    if (!loading.value) {
      approveSave(data.id, datas)
    }
  })
}, 500)

// 申请审批保存
const approveSave = (id, datas) => {
  loading.value = true
  approveApplySaveApi(id, datas)
    .then((res) => {
      loading.value = false
      message.success(res.message)
      if (data.types == 'customer') {
        if (data.nav_type == 'back') {
          setTimeout(() => {
            uni.navigateBack()
          }, 300)
        } else {
          clicKReLaunch(`/pages/customer/list/details?id=${data.parameterData.customer_id}&types=customer`)
        }
      } else if (data.types == 'contract') {
        clicKReLaunch(`/pages/customer/contract/details?id=${data.parameterData.contract_id}&tab=2`)
      } else if (data.types == 'invoice') {
        if (data.nav_type == 'back') {
          setTimeout(() => {
            uni.navigateBack()
          }, 300)
        } else {
          uni.redirectTo({
            url: `/pages/customer/invoice/index`,
          })
        }
      } else if (data.types == 'schedule') {
        let datas = JSON.parse(uni.getStorageSync('scheduleData'))

        scheduleRecord(datas.id, datas)
        clicKReLaunch('/pages/users/schedule/index')
      } else {
        delayedReLaunch('/pages/users/examine/center?id=0')
      }
    })
    .catch((error) => {
      loading.value = false
      message.error(error.message)
    })
}
// 修改日程状态
const scheduleRecord = (id, data) => {
  scheduleRecordApi(id, data).then((res) => {})
}
</script>

<style scoped lang="scss">
.content {
  width: 100%;
  position: relative;
  margin: 16rpx 0 16rpx 0;
  .cr-position-header {
    background-color: #fff;
  }

  .examine-content {
    padding-top: calc($uni-default-bar-height + var(--status-bar-height));
    padding-bottom: 126rpx;

    .process {
      background-color: #fff;
      // border-radius: 12rpx;
    }
  }

  .examine-button {
    height: 126rpx;
    line-height: 126rpx;
    width: 100%;
    padding: 0 20rpx;
    background-color: #fff;
    position: fixed;
    left: 0;
    bottom: 0;
    right: 0;
    display: flex;
    align-items: center;

    uni-button {
      width: 100%;
      height: 86rpx;
      line-height: 86rpx;
      font-size: $uni-font-size-default;
      border-radius: 12rpx;
    }
  }
}
</style>
