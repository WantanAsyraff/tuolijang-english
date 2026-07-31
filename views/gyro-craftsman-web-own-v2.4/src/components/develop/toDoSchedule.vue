<!-- @FileDescription: 低代码-触发器更新规则-日程待办组件-->
<template>
<div>
  <el-form-item :label="$t('ui.developToDoScheduleToDoTitle')">
    <el-input class="textPosition" size="small" :placeholder="$t('ui.developToDoSchedulePleaseEnterPushTitle')" @input="onInput" v-model="form.title">
    </el-input>
    <span class="prompt"
      >{{ $t("ui.developToDoScheduleTheTitleSupportsFieldVariablesForExample") }} <span class="color-file">{createdOn} </span>{{ $t("ui.developToDoScheduleCreatedOnIsTheInternalFieldIdentifierOfThe") }}</span
    >
    <el-popover placement="left" width="100" trigger="hover">
      <div class="field-box">
        <div
          class="field-text over-text"
          v-for="(item, index) in options.string_fields"
          :key="index"
          @click="handleClick(item, 'title')"
        >
          {{ item.label }}
        </div>
      </div>
      <span class="el-icon-chat-dot-square icon" slot="reference"></span>
    </el-popover>
  </el-form-item>
  <el-form-item :label="$t('ui.developToDoScheduleParticipants')" class="mt14">
    <div class="flex-between">
      <el-select
        size="small"
        v-model="form.schedule_user.operator"
        :placeholder="$t('ui.developConditionGroupPleaseSelect')"
        style="width: 200px"
        filterable
      >
        <el-option
          v-for="item in options.update_type.slice(0, 2)"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        >
        </el-option>
      </el-select>
      <el-select
        v-if="form.schedule_user.operator != 'value'"
        size="small"
        class="ml14"
        v-model="form.schedule_user.form_field_uniqid"
        :placeholder="$t('ui.developConditionGroupPleaseSelect')"
        style="width: 100%"
        filterable
      >
        <el-option v-for="item in options.user_feilds" :key="item.value" :label="item.label" :value="item.value">
        </el-option>
      </el-select>
      <select-member
        v-if="form.schedule_user.operator == 'value'"
        :placeholder="$t('ui.developToDoScheduleSelectParticipants')"
        :value="form.schedule_user.useList || []"
        @getSelectList="getSelectList"
        style="width: 100%"
        class="ml14"
        :is-avatar="true"
      ></select-member>
    </div>
  </el-form-item>
  <el-form-item :label="$t('ui.developToDoScheduleScheduleCycle')" class="mt14">
    <el-checkbox v-model="form.schedule_cycle" true-label="1" false-label="0">{{ $t("ui.userCalendarAddTodoAllDay") }}</el-checkbox>
  </el-form-item>
  <el-form-item :label="$t('ui.programProgramTaskIndexStartTime')" class="mt14">
    <div class="flex-between">
      <el-select size="small" v-model="form.start_time.operator" :placeholder="$t('ui.developConditionGroupPleaseSelect')" style="width: 200px" filterable>
        <el-option
          v-for="item in options.update_type.slice(0, 2)"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        >
        </el-option>
      </el-select>
      <el-select
        v-if="form.start_time.operator != 'value'"
        size="small"
        class="ml14"
        v-model="form.start_time.form_field_uniqid"
        :placeholder="$t('ui.developConditionGroupPleaseSelect')"
        style="width: 100%"
        filterable
      >
        <el-option v-for="item in options.time_feilds" :key="item.value" :label="item.label" :value="item.value">
        </el-option>
      </el-select>
      <el-date-picker
        v-if="form.start_time.operator == 'value'"
        v-model="form.start_time.value"
        :type="form.schedule_cycle == 1 ? 'date' : 'datetime'"
        style="width: 100%"
        class="ml14"
        size="small"
        :format="form.schedule_cycle == 1 ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm:ss'"
        :value-format="form.schedule_cycle == 1 ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm:ss'"
        :placeholder="$t('ui.developToDoScheduleSelectStartTime')"
      >
      </el-date-picker>
    </div>
  </el-form-item>
  <el-form-item :label="$t('ui.programProgramTaskIndexEndTime')" class="mt14">
    <div class="flex-between">
      <el-select size="small" v-model="form.end_time.operator" :placeholder="$t('ui.developConditionGroupPleaseSelect')" style="width: 200px" filterable>
        <el-option
          v-for="item in options.update_type.slice(0, 2)"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        >
        </el-option>
      </el-select>
      <el-select
        v-if="form.end_time.operator != 'value'"
        size="small"
        class="ml14"
        v-model="form.end_time.form_field_uniqid"
        :placeholder="$t('ui.developConditionGroupPleaseSelect')"
        style="width: 100%"
        filterable
      >
        <el-option v-for="item in options.time_feilds" :key="item.value" :label="item.label" :value="item.value">
        </el-option>
      </el-select>
      <el-date-picker
        v-if="form.end_time.operator == 'value'"
        v-model="form.end_time.value"
        :type="form.schedule_cycle == 1 ? 'date' : 'datetime'"
        style="width: 100%"
        class="ml14"
        size="small"
        :format="form.schedule_cycle == 1 ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm:ss'"
        :value-format="form.schedule_cycle == 1 ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm:ss'"
        :placeholder="$t('ui.developToDoScheduleSelectEndTime')"
      >
      </el-date-picker>
    </div>
  </el-form-item>
  <el-form-item :label="$t('ui.developToDoScheduleReminderTime')" class="mt14">
    <el-select size="small" v-model="form.remind_time" :placeholder="$t('ui.developConditionGroupPleaseSelect')" style="width: 100%">
      <el-option v-for="item in remindOptions" :key="item.value" :label="item.label" :value="item.value"> </el-option>
    </el-select>
  </el-form-item>
  <el-form-item :label="$t('ui.developToDoScheduleScheduleType')" class="mt14"> {{ $t("ui.developToDoSchedulePersonalReminder") }}</el-form-item>
  <el-form-item :label="$t('ui.developToDoScheduleToDoDescription')" class="mt14">
    <el-input class="textPosition" type="textarea" :rows="3" :placeholder="$t('ui.developToDoSchedulePleaseEnterPushContent')" v-model="form.template">
    </el-input>
    <span class="prompt"
      >{{ $t("ui.developToDoScheduleContentSupportsFieldVariablesSuchAs") }} <span class="color-file">{createdOn} </span>{{ $t("ui.developToDoScheduleCreatedOnIsTheInternalFieldIdentifierOfThe") }}</span
    >

    <el-popover placement="left" width="100" trigger="hover">
      <div class="field-box">
        <div
          class="field-text over-text"
          v-for="(item, index) in options.string_fields"
          :key="index"
          @click="handleClick(item)"
        >
          {{ item.label }}
        </div>
      </div>
      <span class="el-icon-chat-dot-square icon" slot="reference"></span>
    </el-popover>
  </el-form-item>
</div>
</template>
<script>
export default {
  props: {
    field: {
      // 源字段
      type: Array,
      default: []
    },
    options: {
      // 更新方式
      type: Array,
      default: []
    },
    data: {
      type: Object,
      default: () => {}
    }
  },
  components: { selectMember: () => import('@/components/form-common/select-member') },
  data() {
    return {
      form: {
        title: '',
        schedule_user: { operator: 'field_value', form_field_uniqid: '', value: '' },
        start_time: { operator: 'field_value', form_field_uniqid: '', value: '' },
        end_time: { operator: 'field_value', form_field_uniqid: '', value: '' },
        remind_time: '',
        template: '',
        schedule_cycle: '1'
      },
      remindOptions: [
        {
          value: -1,
          label: '不提醒'
        },
        {
          value: 0,
          label: '任务开始时'
        },
        {
          value: 1,
          label: '提前5分钟'
        },
        {
          value: 2,
          label: '提前15分钟'
        },
        {
          value: 3,
          label: '提前30分钟'
        },
        {
          value: 4,
          label: '提前1小时'
        },
        {
          value: 5,
          label: '提前2小时'
        },
        {
          value: 6,
          label: '提前1天'
        },
        {
          value: 7,
          label: '提前2天'
        },
        {
          value: 8,
          label: '提前1周'
        }
      ]
    }
  },
  mounted() {
    if (this.data.title) {
      this.data.remind_time = Number(this.data.remind_time)
      this.form = this.data
    }
  },
  methods: {
    onInput() {
      this.$forceUpdate()
    },
    getSelectList(data) {
      let arr = []
      data.map((item) => {
        arr.push(item.value)
      })
      this.form.schedule_user.value = arr
      this.form.schedule_user.useList = data
    },

    handleClick(val, type) {
      if (type == 'title') {
        this.$set(this.form, 'title', this.form.title + '{' + val.value + '}')
        this.onInput()
      } else {
        this.form.template = this.form.template + '{' + val.value + '}'
      }
    }
  }
}
</script>
<style scoped lang="scss">
.icon {
  font-size: 16px;
  position: absolute;
  right: 5px;
  bottom: 42px;
}
.field-text {
  cursor: pointer;
  height: 32px;
  line-height: 32px;
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
}
.field-text:hover {
  background: #f7fbff;
  color: #1890ff;
}
.textPosition {
  position: relative;
}
::v-deep .el-textarea__inner {
  resize: none;
  font-size: 13px;
}
.prompt {
  font-size: 13px;
  color: #909399;
  line-height: 10px;
}
.field-box {
  height: 350px;
  overflow-y: auto;
  scrollbar-width: none; /* firefox */
  -ms-overflow-style: none; /* IE 10+ */
}
.field-box::-webkit-scrollbar {
  height: 0;
  width: 0;
}
</style>
