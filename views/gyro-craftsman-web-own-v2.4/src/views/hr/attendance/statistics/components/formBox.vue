<template>
  <div>
    <oaFromBox
      v-if="search.length > 0"
      :btnText="$('ui.fdExamineIndexExport')"
      :dropdownList="dropdownList"
      :isAddBtn="false"
      :search="search"
      :timeVal="timeValue"
      :title="title"
      :total="total"
      :viewSearch="viewSearch"
      @addDataFn="exportExcelData"
      @confirmData="confirmData"
      @dropdownFn="dropdownFn"
    >
    </oaFromBox>
    <!-- 导出组件 -->
    <export-excel ref="exportExcel" :export-data="exportData" :save-name="saveName" :template="false" />
    <!-- 导入组件 -->
    <import-excel v-show="false" ref="importExcel" @importExcelData="importExcelData"></import-excel>
    <!-- 企微同步 -->
    <el-dialog :title='$("legacyScript.weComSync")' :visible.sync="dialogFormVisible" width="500px">
      <el-form>
        <el-form-item label-width="90px">
          <span slot="label"><span class="color-pdf">*</span> {{ $("ui.hrAttendanceStatisticsDetailsDrawerClockInTime") }}</span>
          <el-date-picker
            size="small"
            v-model="clockInTime"
            type="daterange"
            :range-separator="$('ui.commonFormListTo')"
            :start-placeholder="$('ui.customerSigningIndexStartDate')"
            :end-placeholder="$('ui.customerSigningIndexEndDate')"
            format="yyyy/MM/dd"
            value-format="yyyy/MM/dd"
            :picker-options="pickerOptions"
            @change="handleDateChange"
          >
          </el-date-picker>
          <div class="tips">{{ $("legacy.2d97d49614064b9e") }}</div>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button size="small" @click="dialogFormVisible = false">{{ $("ui.xmindEditorNodeHyperlinkCancel") }}</el-button>
        <el-button size="small" type="primary" @click="qiweiAsync">{{ $("ui.xmindEditorNodeHyperlinkOk") }}</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { $ } from '@/lang'
import oaFromBox from '@/components/common/oaFromBox'
import SettingMer from '@/libs/settingMer'
import helper from '@/libs/helper'
import { getToken } from '@/utils/auth'
import 'animate.css'
import importExcel from '@/components/common/importExcel'
import ExportExcel from '@/components/common/exportExcel'
import { attendanceGroupSelectApi } from '@/api/config'
import { attendanceImport, attendanceImportFile, attendanceWorkClockRecord } from '@/api/enterprise'
export default {
  name: 'CrmebOaEntFormBox',
  components: { importExcel, oaFromBox, ExportExcel },
  props: ['type', 'total'],
  data() {
    return {
      title: '',
      uploadData: {},
      dialogFormVisible: false,
      where: {},
      fileList: [],
      myHeaders: {
        authorization: 'Bearer ' + getToken()
      },
      dropdownList: [
        {
          label: $('finance.batchupload'),
          value: 1
        },
        {
          label: $('customer.export'),
          value: 2
        },
        {
          label: $('legacyScript.exportTemplate'),
          value: 3
        },
        {
          label: $('legacyScript.weComImport'),
          value: 4
        },
        {
          label: $('legacyScript.dingTalkImport'),
          value: 5
        }
      ],
      clockInTime: [],
      startDate: null,
      pickerOptions: {
        disabledDate: (time) => {
          if (this.startDate) {
            const start = this.$moment(this.startDate)
            const end = start.clone().add(30, 'days')
            return time.getTime() < start.valueOf() || time.getTime() > end.valueOf()
          }
        }
      },

      list: [],
      exportData: {
        data: [],
        cols: [{ wpx: 130 }, { wpx: 70 }, { wpx: 120 }, { wpx: 120 }, { wpx: 130 }, { wpx: 130 }]
      },
      salesmanList: [
        {
          value: 1,
          name: '正常'
        },
        {
          value: 2,
          name: '迟到'
        },
        {
          value: 3,
          name: '严重迟到'
        },
        {
          value: 4,
          name: '早退'
        },
        {
          value: 5,
          name: '缺卡'
        },
        {
          value: 6,
          name: '地点异常'
        }
      ],
      search: [],
      timeValue: [],
      viewSearch: [],
      // pickerOptions: this.$pickerOptionsTimeEle,
      saveName: '',
      importType: 1
    }
  },
  computed: {
    fileUrl() {
      return SettingMer.https + `/client/import`
    }
  },

  mounted() {
    if (this.type !== 'month') {
      this.timeValue = [this.$moment(new Date()).format('YYYY/MM/DD'), this.$moment(new Date()).format('YYYY/MM/DD')]
      this.where.time = this.timeValue[0] + '-' + this.timeValue[1]
    } else {
      this.timeValue = this.$moment(new Date()).format('YYYY-MM')
      this.where.time = this.timeValue
    }

    this.getList()
    this.$emit('confirmData', this.where)
  },

  methods: {
    // disabledDate(date) {
    //   if (date > dayjs().endOf('day')) return true
    //   if (this.startDate) {
    //     const startDay = dayjs(this.startDate)
    //     const maxEndDay = startDay.add(30, 'day')
    //     const minEndDay = startDay.subtract(0, 'day')
    //     return date < minEndDay.startOf('day') || date > maxEndDay.endOf('day')
    //   }

    //   return false
    // },
    handleDateChange(val) {
      if (!val || val.length < 2) return

      const [start, end] = val
      const startDay = this.$moment(start)
      const endDay = this.$moment(end)
      const diffDays = endDay.diff(startDay, 'days')

      if (diffDays > 30) {
        this.$message.error($('legacyScript.theDateRangeCannotExceed30DaysPleaseSelectAgain'))
        this.clockInTime = [start, startDay.clone().add(30, 'days').format('YYYY/MM/DD')]
      } else if (diffDays < 0) {
        this.$message.error($('legacyScript.endDateCannotBeEarlierThanStartDate'))
        this.clockInTime = []
      }
    },
    // 企微同步
    qiweiAsync() {
      if (this.clockInTime.length == 0) {
        return this.$message.error($('legacyScript.pleaseSelectATime'))
      }
      const [start, end] = this.clockInTime
      const startDay = this.$moment(start)
      const endDay = this.$moment(end)
      const diffDays = endDay.diff(startDay, 'days')
      if (diffDays > 30) {
        this.$message.error($('legacyScript.theDateRangeCannotExceed30DaysPleaseSelectAgain'))
        return false
      } else if (diffDays < 0) {
        this.$message.error($('legacyScript.endDateCannotBeEarlierThanStartDate'))
        return false
      }
      attendanceWorkClockRecord({ date: this.clockInTime }).then((res) => {
        this.$emit('confirmData', this.where)
        this.dialogFormVisible = false
      })
    },
    dropdownFn(data) {
      if (data.value === 1) {
        this.importType = 1
        this.$refs.importExcel.btnClick()
      } else if (data.value === 2) {
        this.exportExcelData()
      } else if (data.value === 3) {
        this.exportTemplate()
      } else if (data.value === 4) {
        this.importType = 4
        this.$refs.importExcel.btnClick()
      } else if (data.value === 5) {
        this.importType = 5
        this.$refs.importExcel.btnClick()
      } else if (data.value === 6) {
        // 企微同步
        this.dialogFormVisible = true
        this.clockInTime = []
      }
    },
    getSearch(type) {
      this.viewSearch = [
        {
          field: 'group_id',
          title: $('ui.hrAttendanceStatisticsClockAttendanceGroup'),
          type: 'select',
          options: this.list
        },
        {
          field: 'scope',
          title: $('legacyScript.data'),
          type: 'select',
          options: [
            {
              name: '包含离职人员',
              value: ''
            },
            {
              name: '不包含离职人员',
              value: '1'
            },
            {
              name: '仅展示离职人员',
              value: '2'
            }
          ]
        },
        {
          field: 'user_id',
          title: $('ui.hrAttendanceSettingAddConentPersonnel'),
          type: 'user_id',
          options: []
        }
      ]
      let searchList = [
        {
          field_name: '考勤时间',
          field_name_en: 'time',
          form_value: 'date_picker',
          data_dict: this.timeValue
        },
        {
          field_name: '打卡结果',
          field_name_en: 'status',
          form_value: 'select',
          data_dict: this.salesmanList
        },
        {
          field_name: '部门',
          field_name_en: 'frame_id',
          form_value: 'frame_id',
          data_dict: []
        }
      ]
      if (type == 'clock') {
        this.title = $('legacyScript.clockInRecords')
        let obj = {
          field_name_en: 'group_id',
          field_name: '考勤组',
          form_value: 'select',
          data_dict: this.list
        }
        this.dropdownList.push({
          label: $('legacyScript.weComSync'),
          value: 6
        })
        searchList.splice(1, 1)
        this.search = searchList
        this.search.push(obj)
        this.viewSearch.splice(0, 1)
      } else if (type == 'month') {
        this.title = $('legacyScript.monthlyStatistics')
        searchList[0].form_value = 'month'
        this.search = searchList
      } else {
        this.title = $('legacyScript.dailyStatistics')
        this.search = searchList
      }
    },
    // 获取考勤组数据
    async getList() {
      const result = await attendanceGroupSelectApi()
      this.list = result.data

      this.list.forEach((item) => {
        item.value = item.id
      })
      this.getSearch(this.type)
    },
    handleChange(file, fileList) {
      this.fileList = fileList
    },
    // 上传前
    handleUpload(file) {
      const types = helper.uploadCustomerTypes
      const fileTypeName = file.name.substr(file.name.lastIndexOf('.') + 1)
      const isImage = types.includes(fileTypeName)

      if (!isImage) {
        this.$message.error('不支持该' + fileTypeName + '格式')
        return false
      }
      return true
    },
    // 上传成功
    handleSuccess(response) {
      if (response.status === 200) {
        this.$message.success(response.message)
        this.$emit('confirmData', this.where)
      } else {
        this.$message.error(response.message)
        this.$emit('confirmData', this.where)
      }
    },

    confirmData(data) {
      if (data == 'reset') {
        this.reset()
      } else {
        this.where = { ...this.where, ...data }
        this.$emit('confirmData', this.where)
      }
    },
    reset() {
      this.where = {}

      if (this.type !== 'month') {
        this.timeValue = [this.$moment(new Date()).format('YYYY/MM/DD'), this.$moment(new Date()).format('YYYY/MM/DD')]
        this.where.time = this.timeValue[0] + '-' + this.timeValue[1]
      } else {
        this.timeValue = this.$moment(new Date()).format('YYYY-MM')
        this.where.time = this.timeValue
      }
      this.search[0].data_dict = this.timeValue

      this.departmentList = []
      this.$emit('confirmData', this.where)
    },
    // 导出报表
    exportExcelData() {
      this.$emit('confirmData', this.where, '导出')
    },
    //导入打卡记录
    async importExcelData(arrRes) {
      if (this.importType === 4) {
        arrRes.splice(0, 2)
        arrRes.splice(1, 1)
        await attendanceImportFile({ type: 2, data: this.formatData(arrRes) })
      } else if (this.importType === 5) {
        arrRes.splice(0, 2)
        await attendanceImportFile({ type: 1, data: this.formatData(arrRes) })
      } else {
        await attendanceImport(this.formatData(arrRes))
      }
      this.$emit('confirmData', this.where)
    },
    //格式化打卡记录
    formatData(data) {
      let thead = data[0]
      let result = []
      data.splice(0, 1)
      for (let i = 0; i < data.length; i++) {
        result.push({})
        for (let j = 0; j < data[i].length; j++) {
          if (thead[j]) result[i][thead[j]] = data[i][j] === '--' ? '' : data[i][j]
        }
      }
      return result
    },
    // 导出模版
    async exportTemplate() {
      this.saveName = '打卡记录模板(' + this.$moment(new Date()).format('MMDDHHmmss') + ').xlsx'
      this.exportData.data = [
        ['时间', '姓名', '第一次上班', '第一次下班', '第二次上班', '第二次下班'],
        ['2024/06/20 星期三', '张三', '2024/06/12 06:30', '2024/06/12 18:30', '2024/06/12 06:30', '2024/06/12 18:30']
      ]
      this.$nextTick(() => {
        this.$refs.exportExcel.exportExcel()
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.upload {
  display: inline-block;
  margin-left: 10px;
}
.tips {
  color: #909399;
  font-size: 12px;
}
</style>
