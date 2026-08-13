<template>
  <div>
    <el-dialog
      :title="repeatData.title"
      :visible.sync="dialogVisible"
      :width="repeatData.width"
      :before-close="handleClose"
    >
      <div class="body mt15">
        <el-form ref="form" :model="rules" :rules="rule" label-width="80px">
          <el-form-item :label="$('calendar.repetitiontype') + ':'">
            <el-select
              v-model="rules.repeat"
              size="small"
              :placeholder="$('calendar.placeholder10')"
              @change="handleRepeat"
            >
              <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value" />
            </el-select>
          </el-form-item>
          <el-form-item prop="rate" :label="$('calendar.frequency') + ':'">
            <el-input-number
              v-model="rules.rate"
              size="small"
              :controls="false"
              :min="1"
              :placeholder="$('calendar.placeholder17')"
            />
            <span class="day-title">{{ dayTitle }}</span>
          </el-form-item>
          <el-form-item prop="weekDays" v-if="rules.repeat === 1" :label="$('calendar.weekly') + ':'">
            <el-checkbox-group v-model="rules.weekDays" size="small">
              <el-checkbox-button v-for="item in week" :key="item.value" :label="item.value">{{
                item.label
              }}</el-checkbox-button>
            </el-checkbox-group>
          </el-form-item>
          <el-form-item prop="monthDays" v-if="rules.repeat === 2" :label="$('calendar.monthly') + ':'" class="month">
            <el-checkbox-group v-model="rules.monthDays" size="small">
              <el-checkbox-button
                v-for="item in 31"
                :key="item"
                :label="item.toString()"
                >{{ item &lt; 10 ? '0'+ item : item }}</el-checkbox-button
              >
            </el-checkbox-group>
          </el-form-item>
          <el-form-item :label="$('calendar.enddate') + ':'">
            <el-date-picker
              v-model="rules.time"
              size="small"
              type="datetime"
              prefix-icon="el-icon-date"
              :clearable="true"
              :placeholder="$('calendar.neverend')"
            />
          </el-form-item>
        </el-form>
      </div>
      <div slot="footer" class="dialog-footer text-center">
        <el-button size="small" @click="handleClose">{{ $('public.cancel') }}</el-button>
        <el-button size="small" type="primary" @click="handleConfirm">{{ $('public.ok') }}</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'RepeatDialog',
  components: {},
  props: {
    repeatData: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },
  data() {
    const checkWeek = (rule, value, callback) => {
      if (this.rules.repeat === 1 && value.length <= 0) {
        return callback(new Error(this.$('calendar.placeholder15')))
      } else {
        return callback()
      }
    }
    const checkMonth = (rule, value, callback) => {
      if (this.rules.repeat === 2 && value.length <= 0) {
        return callback(new Error(this.$('calendar.placeholder16')))
      } else {
        return callback()
      }
    }
    return {
      dialogVisible: false,
      rules: {
        repeat: 0,
        rate: 1,
        end_time: '',
        weekDays: [],
        monthDays: [],
        close: false,
        time: ''
      },
      rule: {
        rate: [{ required: true, message: this.$('calendar.placeholder17'), trigger: 'blur' }],
        weekDays: [{ required: false, validator: checkWeek, trigger: 'change' }],
        monthDays: [{ required: false, validator: checkMonth, trigger: 'change' }]
      },
      options: [
        { value: 0, label: this.$('calendar.repeatbyday') },
        { value: 1, label: this.$('calendar.repeatweekly') },
        { value: 2, label: this.$('calendar.repeatmonthly') },
        { value: 3, label: this.$('calendar.repeatyear') }
      ],
      week: [
        { value: '1', label: this.$('hr.monday') },
        { value: '2', label: this.$('hr.tuesday') },
        { value: '3', label: this.$('hr.wednesday') },
        { value: '4', label: this.$('hr.thursday') },
        { value: '5', label: this.$('hr.friday') },
        { value: '6', label: this.$('hr.saturday') },
        { value: '7', label: this.$('hr.sunday') }
      ],
      dayTitle: this.$('access.day'),
      option: [
        { value: '', label: this.$('calendar.neverend') },
        { value: 1, label: this.$('hr.placeholder4') }
      ]
    }
  },
  computed: {
    lang: function () {
      return this.$store.getters.lang
    }
  },
  watch: {
    repeatData: {
      handler(nVal) {
        if (nVal.edit) {
          this.rules.repeat = nVal.data.period
          this.rules.rate = nVal.data.rate
          if (nVal.data.period === 1) {
            this.rules.weekDays = nVal.data.days
          } else if (nVal.data.period === 2) {
            this.rules.monthDays = nVal.data.days
          }
          this.handleRepeat(nVal.data.period)
          this.rules.time = nVal.data.end_time.indexOf('0000-00-00') > -1 ? '' : nVal.data.end_time
        }
      },
      deep: true
    },
    lang() {
      this.setOptions()
    }
  },
  methods: {
    setOptions() {
      this.options = [
        { value: 0, label: this.$('calendar.repeatbyday') },
        { value: 1, label: this.$('calendar.repeatweekly') },
        { value: 2, label: this.$('calendar.repeatmonthly') },
        { value: 3, label: this.$('calendar.repeatyear') }
      ]
      this.week = [
        { value: 1, label: this.$('hr.monday') },
        { value: 2, label: this.$('hr.tuesday') },
        { value: 3, label: this.$('hr.wednesday') },
        { value: 4, label: this.$('hr.thursday') },
        { value: 5, label: this.$('hr.friday') },
        { value: 6, label: this.$('hr.saturday') },
        { value: 7, label: this.$('hr.sunday') }
      ]
      this.dayTitle = this.$('access.day')
      this.option = [
        { value: '', label: this.$('calendar.neverend') },
        { value: 1, label: this.$('hr.placeholder4') }
      ]
    },
    handleClose() {
      this.dialogVisible = false
      this.rules.close = true
      this.confirmData()
    },
    confirmData() {
      this.$emit('handleRepeatData', this.rules)
    },
    handleRepeat(e) {
      if (e === 0) {
        this.dayTitle = this.$('access.day')
      } else if (e === 1) {
        this.dayTitle = this.$('user.work.week')
      } else if (e === 2) {
        this.dayTitle = this.$('user.work.month')
      } else if (e === 3) {
        this.dayTitle = this.$('calendar.year')
      } else {
        this.dayTitle = this.$('access.day')
      }
    },
    handleConfirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          if (this.rules.time) {
            this.rules.end_time = this.$moment(this.rules.time).format('YYYY-MM-DD HH:mm:ss')
          } else {
            this.rules.end_time = ''
          }
          this.rules.close = false
          this.dialogVisible = false
          this.confirmData()
        }
      })
    }
  }
}
</script>

<style scoped lang="scss">
.body {
  ::v-deep .el-select--medium,
  ::v-deep .el-input--medium {
    width: 100%;
  }
  ::v-deep .el-input__inner {
    text-align: left;
  }
  .day-title {
    padding-left: 10px;
  }
  .month {
    ::v-deep .el-checkbox-button__inner {
      padding: 9px 14px;
    }
    ::v-deep .el-checkbox-button:nth-of-type(9n + 1) .el-checkbox-button__inner {
      border-left: 1px solid #dcdfe6;
    }
  }
}
.dialog-footer {
  padding-top: 20px;
  border-top: 1px solid #e6ebf5;
}
</style>
