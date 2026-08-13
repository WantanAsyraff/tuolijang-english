import { $ } from '@/lang'
<template>
<div class="timeFrom">
  <!-- 请假表单 -->

  <el-form-item :error="errors.dateStart" :required="showRequired" :label="$('ui.programProgramTaskIndexStartTime')" :label-width="itemLabelWidth">
    <div class="el-form-item__content">
      <el-date-picker
        v-model="timeData.dateStart"
        :type="timeData.timeType === 'day' ? 'date' : 'datetime'"
        :placeholder="$('ui.userCalendarAddTodoSelectDate')"
        :format="timeData.timeType === 'day' ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm:ss'"
        :value-format="timeData.timeType === 'day' ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm:ss'"
        :clearable="false"
        @change="onchangeTimeStart"
      ></el-date-picker>

      <el-select
        v-if="timeData.timeType === 'day'"
        v-model="timeData.timeStart"
        class="ml10"
        :placeholder="$('ui.developConditionGroupPleaseSelect')"
        @blur="onChange"
        @change="onchangeSel(1)"
      >
        <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"></el-option>
      </el-select>
    </div>
  </el-form-item>
  <el-form-item :error="errors.dateEnd" :required="showRequired" :label="$('ui.programProgramTaskIndexEndTime')" :label-width="itemLabelWidth">
    <div class="el-form-item__content">
      <el-date-picker
        v-model="timeData.dateEnd"
        :type="timeData.timeType === 'day' ? 'date' : 'datetime'"
        :placeholder="$('ui.userCalendarAddTodoSelectDate')"
        :format="timeData.timeType === 'day' ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm:ss'"
        :value-format="timeData.timeType === 'day' ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm:ss'"
        :clearable="false"
        @change="onchangeTimeEnd"
      ></el-date-picker>
      <el-select
        v-if="timeData.timeType === 'day'"
        v-model="timeData.timeEnd"
        @blur="onChange"
        :placeholder="$('ui.developConditionGroupPleaseSelect')"
        class="ml10"
        @change="onchangeSel(2)"
      >
        <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"></el-option>
      </el-select>
    </div>
  </el-form-item>
  <el-form-item :error="errors.duration" :required="showRequired" :label="title" :label-width="itemLabelWidth">
    <div class="el-form-item__content">
      <el-input v-model="timeData.duration" style="width: 220px" @change="changeDuration" :placeholder="$('ui.hrApprovaTimePleaseEnterDuration')">
        <span slot="suffix" class="el-input__icon">{{ timeData.timeType === 'day' ? $('ui.hrApprovaTimeDay') : $('ui.hrApprovaTimeHours') }}</span>
      </el-input>
    </div>
  </el-form-item>
</div>
</template>

<script>
import { divTime } from '@/utils'
export default {
  name: 'Index',
  props: {
    formCreateInject: Object,
    value: {
      type: Object,
      default: () => ({})
    },
    disabled: Boolean,
    timeType: {
      type: String,
      default: () => 'day'
    },
    time: {
      type: String,
      default: () => ''
    },
    titleIpt: {
      type: String,
      default: () => '时长'
    }
  },
  data() {
    return {
      timeNum: this.time,
      options: [
        {
          value: '1',
          label: $('legacyScript.aM')
        },
        {
          value: '0',
          label: $('legacyScript.pM')
        }
      ],
      timeData: {
        dateStart: this.value.dateStart,
        timeStart: this.value.timeStart,
        dateEnd: this.value.dateEnd,
        timeEnd: this.value.timeEnd,
        duration: this.value.duration,
        timeType: this.timeType
      },
      errors: {
        dateStart: '',
        dateEnd: '',
        duration: ''
      },
      showErrors: false,
      itemLabelWidth: '84px',
      num: 0,
      title: this.titleIpt
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.syncOuterErrorVisibility()
    })
  },
  updated() {
    this.syncOuterErrorVisibility()
  },
  computed: {
    showRequired() {
      return !!(this.formCreateInject && this.formCreateInject.rule && this.formCreateInject.rule.effect && this.formCreateInject.rule.effect.required)
    },
    outerValidateState() {
      return this.$parent && this.$parent.validateState
    }
  },
  watch: {
    value(n) {
      this.timeData = n || {}
      if (this.isPristineValue(this.timeData) && this.outerValidateState !== 'error') {
        this.showErrors = false
      }
      this.updateErrors()
    },
    timeType(n) {
      this.timeData.timeType = n
      this.updateErrors()
    },
    time(n) {
      this.timeNum = n
    },
    titleIpt(n) {
      this.title = n
      this.updateErrors()
    },
    outerValidateState(n) {
      if (n === 'error') {
        this.showErrors = true
        this.updateErrors()
      }
    }
  },
  methods: {
    createEmptyErrors() {
      return {
        dateStart: '',
        dateEnd: '',
        duration: ''
      }
    },
    isEmpty(value) {
      return value === '' || value === undefined || value === null
    },
    isPristineValue(value = {}) {
      return this.isEmpty(value.dateStart) && this.isEmpty(value.dateEnd) && this.isEmpty(value.timeStart) && this.isEmpty(value.timeEnd) && this.isEmpty(value.duration)
    },
    isFieldComplete(value) {
      return !this.isEmpty(value.dateStart) && !this.isEmpty(value.dateEnd) && !this.isEmpty(value.timeStart) && !this.isEmpty(value.timeEnd) && !this.isEmpty(value.duration)
    },
    getErrors(value = this.timeData) {
      const errors = this.createEmptyErrors()

      if (this.isEmpty(value.dateStart) || this.isEmpty(value.timeStart)) {
        errors.dateStart = '请选择开始时间'
      }
      if (this.isEmpty(value.dateEnd) || this.isEmpty(value.timeEnd)) {
        errors.dateEnd = '请选择结束时间'
      }
      if (this.isEmpty(value.duration)) {
        errors.duration = `请输入${this.title}`
      }

      return errors
    },
    updateErrors() {
      this.errors = this.showErrors ? this.getErrors() : this.createEmptyErrors()
    },
    syncOuterErrorVisibility() {
      const outerError = this.$el && this.$el.nextElementSibling
      if (outerError && outerError.classList && outerError.classList.contains('el-form-item__error')) {
        outerError.style.display = 'none'
      }
    },
    syncFieldState() {
      const value = { ...this.timeData }
      this.$emit('input', value)
      this.$emit('change', value)
      this.errors = this.showErrors ? this.getErrors(value) : this.createEmptyErrors()

      if (!this.formCreateInject || !this.formCreateInject.api || !this.formCreateInject.rule) {
        return
      }

      this.$nextTick(() => {
        const field = this.formCreateInject.rule.field
        if (!this.isFieldComplete(value)) {
          this.syncOuterErrorVisibility()
          return
        }
        this.formCreateInject.api.clearValidateState(field)
        this.syncOuterErrorVisibility()
      })
    },
    onChange() {
      const time1 = Date.parse(new Date(this.timeData.dateStart))
      const time2 = Date.parse(new Date(this.timeData.dateEnd))
      if (time1 > time2) {
        return this.$message.error($('legacyScript.theEndTimeCannotBeEarlierThanTheStart'))
      }
      if (this.timeData.timeType === 'day') {
        if (time2 == time1) {
          setTimeout(() => {
            if (this.timeData.timeStart === '0' && this.timeData.timeEnd === '1') {
              return this.$message.error($('legacyScript.theEndTimeCannotBeEarlierThanTheStart'))
            }
          }, 200)
        }
        this.num = divTime(this.timeData.dateStart, this.timeData.dateEnd, 'day')
        if (time2 > time1) this.count()
        if (time2 === time1) this.count()
      } else {
        if (time2 > time1) this.timeData.duration = divTime(this.timeData.dateStart, this.timeData.dateEnd, 'time')
      }
    },
    count() {
      if (this.timeData.timeStart && this.timeData.timeEnd) {
        const len = parseFloat(this.timeData.timeStart) - parseFloat(this.timeData.timeEnd)
        if (len === 1) {
          this.timeData.duration = parseFloat(this.num) + 1
        } else if (len === 0) {
          this.timeData.duration = 0.5 + parseFloat(this.num)
        } else if (len === -1) {
          this.timeData.duration = parseFloat(this.num)
        }
        this.syncFieldState()
      }
    },
    onchangeTimeEnd(n) {
      this.timeData.dateEnd = n
      this.timeData.timeEnd = '1'
      this.onChange()
      this.syncFieldState()
    },
    onchangeTimeStart(n) {
      this.timeData.dateStart = n
      this.timeData.timeStart = '1'
      this.onChange()
      this.syncFieldState()
    },
    changeDuration() {
      this.syncFieldState()
    },
    onchangeSel(n) {
      this.count()
    }
  }
}
</script>

<style scoped lang="scss">
.el-form--label-top .el-form-item__label {
  float: none;
  display: inline-block;
  text-align: left;
  padding: 0 0 10px 0;
}
.timeFrom {
  padding-top: 10px;
}
.timeFrom {
  ::v-deep .el-form-item.is-error .el-input__inner,
  ::v-deep .el-form-item.is-error .el-textarea__inner {
    border-color: #f56c6c;
  }
  ::v-deep .el-form-item .el-input__inner,
  ::v-deep .el-form-item .el-textarea__inner {
    border-color: #dcdfe6;
  }
  ::v-deep .el-date-editor,
  ::v-deep .el-date-editor--date,
  ::v-deep .el-select {
    width: 220px;
  }
  ::v-deep > .el-form-item > .el-form-item__label {
    width: 84px;
  }
  ::v-deep > .el-form-item > .el-form-item__content {
    margin-left: 84px;
  }
}
</style>
