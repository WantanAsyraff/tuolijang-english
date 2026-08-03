<template>
  <div>
    <el-dialog :title="title" :visible.sync="dialogFormVisible" width="560px" v-bind="$attrs">
      <!-- 工作经历 -->
      <el-form :model="workForm" ref="workForm" :rules="workRules" label-width="auto" v-if="add === 'work'">
        <div class="form-box">
          <div class="form-item" v-for="(item, index) in workList" :key="index">
            <el-form-item :prop="item.value">
              <span slot="label">{{ item.label }}</span>
              <!-- date -->
              <el-date-picker v-if="item.type === 'date'" v-model="workForm[item.value]" type="date" size="small"
                style="width: 100%" :placeholder="item.placeholder" value-format="yyyy-MM-dd">
              </el-date-picker>
              <!-- input -->
              <el-input v-if="item.type === 'input'" v-model="workForm[item.value]" size="small" style="width: 100%"
                clearable :placeholder="item.placeholder" />
              <el-input v-if="item.type === 'textarea'" type="textarea" :rows="4" resize="none"
                v-model="workForm[item.value]"></el-input>
            </el-form-item>
          </div>
        </div>
        <div slot="footer" class="dialog-footer">
          <el-button size="small" @click="cancelFn">{{ $ts("取消") }}</el-button>
          <el-button type="primary" size="small" @click="submitFnn()">{{ $ts("确 定") }}</el-button>
        </div>
      </el-form>
      <!-- 教育经历 -->
      <el-form :model="educationForm" ref="educationForm" :rules="educationRules" label-width="90px"
        v-if="add === 'education'">
        <div class="form-box">
          <div class="form-item" v-for="(item, index) in educationList" :key="index">
            <el-form-item :prop="item.value">
              <span slot="label">{{ item.label }}</span>
              <!-- date -->
              <el-date-picker v-if="item.type === 'date'" v-model="educationForm[item.value]" type="date" size="small"
                style="width: 100%" :placeholder="item.placeholder" value-format="yyyy-MM-dd">
              </el-date-picker>
              <!-- input -->
              <el-input v-if="item.type === 'input'" v-model="educationForm[item.value]" size="small"
                style="width: 100%" clearable :placeholder="item.placeholder" />
              <el-input v-if="item.type === 'textarea'" type="textarea" :rows="4" resize="none"
                v-model="educationForm[item.value]"></el-input>
            </el-form-item>
          </div>
        </div>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button size="small" @click="cancelFn">{{ $ts("取消") }}</el-button>
        <el-button type="primary" size="small" @click="submitFnn()">{{ $ts("确定") }}</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import i18n from '@/lang'
export default {
  name: 'addWork',
  components: {},
  props: {
    add: {
      type: String,
      default: ''
    },
    edit: {
      type: String,
      default: ''
    }
  },
  data() {
    const startTime = (rules, value, callback) => {
      if (!value) {
        callback(new Error('请选择开始时间'))
      } else {
        if (this.workForm.end_time) {
          this.$refs.workForm.validateField('endTime')
        }
        callback()
      }
    }
    const endTime = (rules, value, callback) => {
      if (!value) {
        callback(new Error('请选择结束时间'))
      } else {
        if (!this.workForm.start_time) {
          callback(new Error('请选择开始时间！'))
        } else if (Date.parse(this.workForm.start_time) >= Date.parse(value)) {
          callback(new Error('结束时间必须大于开始时间！'))
        } else {
          callback()
        }
      }
    }
    const start_Time = (rules, value, callback) => {
      if (!value) {
        callback(new Error('请选择开始时间'))
      } else {
        if (this.educationForm.end_time) {
          this.$refs.educationForm.validateField('endTime')
        }
        callback()
      }
    }
    const end_Time = (rules, value, callback) => {
      if (!value) {
        callback(new Error('请选择结束时间'))
      } else {
        if (!this.educationForm.start_time) {
          callback(new Error('请选择开始时间！'))
        } else if (Date.parse(this.educationForm.start_time) >= Date.parse(value)) {
          callback(new Error('结束时间必须大于开始时间！'))
        } else {
          callback()
        }
      }
    }
    return {
      dialogFormVisible: false,
      title: i18n.t('legacyScript.addWorkExperience'),
      workForm: {
        start_time: '',
        end_time: '',
        company: '',
        position: '',
        describe: '',
        quit_reason: ''
      },

      workList: [
        {
          type: 'date',
          label: i18n.t('ui.userCalendarAddTodoStartTime'),
          value: 'start_time',
          placeholder: i18n.t('customer.placeholder29')
        },
        {
          type: 'date',
          label: i18n.t('ui.userCalendarAddTodoEndTime'),
          value: 'end_time',
          placeholder: i18n.t('customer.placeholder30')
        },
        {
          type: 'input',
          label: i18n.t('legacyScript.company'),
          value: 'company',
          placeholder: i18n.t('legacyScript.pleaseEnterYourCompanyName')
        },
        {
          type: 'input',
          label: i18n.t('ui.userTrainingPromotionPosition'),
          value: 'position',
          placeholder: i18n.t('legacyScript.pleaseEnterPosition')
        },
        {
          type: 'textarea',
          label: i18n.t('legacyScript.jobDescription'),
          value: 'describe',
          placeholder: i18n.t('legacyScript.pleaseEnterJobDescription')
        },
        {
          type: 'textarea',
          label: i18n.t('legacyScript.reasonsForLeaving'),
          value: 'quit_reason',
          placeholder: i18n.t('legacyScript.pleaseEnterTheReasonForResignation')
        }
      ],
      educationForm: {
        start_time: '',
        end_time: '',
        school_name: '',
        major: '',
        education: '',
        academic: '',
        remark: ''
      },
      educationList: [
        {
          type: 'date',
          label: i18n.t('legacyScript.admissionTime'),
          value: 'start_time',
          placeholder: i18n.t('customer.placeholder29')
        },
        {
          type: 'date',
          label: i18n.t('legacyScript.graduationDate'),
          value: 'end_time',
          placeholder: i18n.t('customer.placeholder30')
        },
        {
          type: 'input',
          label: i18n.t('legacyScript.schoolName'),
          value: 'school_name',
          placeholder: i18n.t('legacyScript.pleaseEnterSchoolName')
        },
        {
          type: 'input',
          label: i18n.t('legacyScript.major'),
          value: 'major',
          placeholder: i18n.t('legacyScript.pleaseEnterYourMajor')
        },
        {
          type: 'input',
          label: i18n.t('legacyScript.education'),
          value: 'education',
          placeholder: i18n.t('legacyScript.pleaseEnterEducation')
        },
        {
          type: 'input',
          label: i18n.t('legacyScript.gegree'),
          value: 'academic',
          placeholder: i18n.t('legacyScript.pleaseEnterGegree')
        },
        {
          type: 'textarea',
          label: i18n.t('ui.fdEnterpriseListViewDetailsRemarks'),
          value: 'remark',
          placeholder: i18n.t('legacyScript.pleaseEnterANote')
        }
      ],
      workRules: {
        start_time: [{ required: true, validator: startTime, trigger: 'blur' }],
        end_time: [{ required: true, validator: endTime, trigger: 'blur' }],
        company: [{ required: true, message: i18n.t('legacyScript.companyNameIsRequired'), trigger: 'blur' }],
        position: [{ required: true, message: i18n.t('legacyScript.positionIsRequired'), trigger: 'blur' }],
        describe: [{ required: true, message: i18n.t('legacyScript.jobDescriptionIsRequired'), trigger: 'blur' }]
      },
      educationRules: {
        start_time: [{ required: true, validator: start_Time, trigger: 'blur' }],
        end_time: [{ required: true, validator: end_Time, trigger: 'blur' }],
        school_name: [{ required: true, message: i18n.t('legacyScript.schoolNameIsRequired'), trigger: 'blur' }],
        major: [{ required: true, message: i18n.t('legacyScript.majorIsRequired'), trigger: 'blur' }],
        education: [{ required: true, message: i18n.t('legacyScript.educationIsRequired'), trigger: 'blur' }]
      }
    }
  },

  methods: {
    submitFn() {
      if (+this.workForm.end_time < +this.workForm.start_time) {
        this.$message.warning(i18n.t('legacyScript.endDateMustBeGreaterThanStartDate'))
        return
      }
      if (this.add == 'work') {
        this.$refs.workForm.validate((valid) => {
          if (valid) {
            setTimeout(() => {
              this.$emit('addWorkFn', this.workForm, 'work')
            }, 1000)
            this.dialogFormVisible = false
          }
        })
      } else {
        this.$refs.educationForm.validate((valid) => {
          if (valid) {
            setTimeout(() => {
              this.$emit('addWorkFn', this.educationForm, 'education')
            }, 1000)

            this.dialogFormVisible = false
          }
        })
      }
    },

    submitFnn() {
      this.debounce(this.submitFn())
    },

    debounce(func, wait = 1000) {
      let timer //计时器
      return function () {
        const args = arguments
        const that = this

        clearTimeout(timer)
        timer = setTimeout(() => {
          func.apply(that, args)
        }, wait)
      }
    },
    cancelFn() {

      if (this.add == 'work') {
        this.$refs.workForm.resetFields()
      } else {
        this.$refs.educationForm.resetFields()
      }
      this.dialogFormVisible = false
    },
    // 重置
    resetForm() {
      if (this.add == 'work') {
        this.$refs.workForm.resetFields()
      } else {
        this.$refs.educationForm.resetFields()
      }
    }
  }
}
</script>
<style scoped lang="scss"></style>
