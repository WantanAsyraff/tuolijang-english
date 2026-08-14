<!-- 客户-添加跟进提醒弹窗组件 -->
<template>
  <el-dialog
    :title="`${isEdit ? $("public.edit") : $("public.add")} ${$("legacy.f0d805dd402c70de")}`"
    top="25vh"
    class="addBox"
    :append-to-body="true"
    :visible.sync="dialogVisible"
    :width="DIALOG_SIZE.SM"
  >
    <div class="line" />
    <el-form :model="form" ref="form" :rules="rules" class="from">
      <el-form-item :label='$("ui.userCalendarAddTodoReminderTime")' :label-width="formLabelWidth" prop="time">
        <el-date-picker
          class="picker-time"
          type="datetime"
          default-time="09:00:00"
          v-model="form.time"
          :placeholder='$("finance.accountselectdate")'
        >
        </el-date-picker>
      </el-form-item>
      <el-form-item :label='$("legacy.ec46dc51f876b854")' :label-width="formLabelWidth" prop="content">
        <el-input type="textarea" maxlength="200" v-model="form.content" :placeholder='$("customer.placeholder40")'></el-input>
      </el-form-item>
      <div class="dialog-footer">
        <el-button size="small" class="btn" @click="handleClose">{{ $("public.cancel") }}</el-button>
        <el-button size="small" type="primary" @click="handleConfirm" class="btn">{{ $("public.ok") }}</el-button>
      </div>
    </el-form>
  </el-dialog>
</template>

<script>
import { clientFollowEditApi, clientFollowSaveApi } from '@/api/client'
import { DIALOG_SIZE } from '@/constants/popupSize'
import { $ } from '@/lang'
export default {
  name: 'LiaisonDialog',
  props: {
    config: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },
  data() {
    return {
      DIALOG_SIZE,
      dialogVisible: false,
      form: {
        time: '',
        content: ''
      },
      rules: {
        time: [{ required: true, message: $('legacyScript.selectReminderTime'), trigger: 'change' }],
        content: [{ required: true, message: $('legacyScript.pleaseEnterReminderContent'), trigger: 'blur' }]
      },
      labelWidth: 110,
      loading: false,
      isEdit: true
    }
  },
  watch: {
    config: {
      handler(nVal) {
        if (nVal.isEdit) {
          this.form.content = nVal.data.content
          this.form.time = this.$moment(nVal.data.time).format('YYYY-MM-DD HH:mm:ss')
        }
      },
      deep: true
    }
  },
  computed: {
    formLabelWidth() {
      return this.$language === 'en' ? '130px' : '100px'
    }
  },
  methods: {
    handleOpen(val) {
      this.dialogVisible = true
      this.isEdit = val
    },
    handleClose() {
      this.reset()
      this.$refs.form.resetFields()
      this.dialogVisible = false
    },

    reset() {
      this.liaison = {
        time: '',
        content: ''
      }
    },

    handleConfirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          let data = {
            time: this.$moment(this.form.time).format('YYYY-MM-DD HH:mm:ss'),
            content: this.form.content,
            eid: this.config.eid,
            types: 1,
            link_type: this.config.link_type
          }

          if (this.config.isEdit) {
            this.followEdit(this.config.data.id, data)
          } else {
            this.clientFollow(data)
          }
        }
      })
    },

    // 客户跟进记录-保存
    async clientFollow(data) {
      this.loading = true
      const res = await clientFollowSaveApi(data)
      this.loading = false
      if (res.status === 200) {
        this.handleClose()
        this.$emit('change')
        this.reset()
      }
    },

    // 客户跟进记录-编辑
    async followEdit(id, data) {
      this.loading = true
      const res = await clientFollowEditApi(id, data)
      this.loading = false
      if (res.status === 200) {
        this.reset()
        this.handleClose()
        this.$emit('change')
      }
    }
  }
}
</script>

<style scoped lang="scss">
.from {
  padding: 0 24px;
  margin-top: 20px;
}

::v-deep .el-date-editor {
  width: 100%;
}
::v-deep .el-textarea__inner {
  resize: none;
  border: 1px solid #dcdfe6 !important;
}
::v-deep .el-input-number--medium {
  width: 100%;
}
::v-deep .el-select--medium {
  width: 100%;
}
::v-deep .el-form-item:last-of-type {
  margin-bottom: 0;
}
.dialog-footer {
  padding-top: 20px;
  text-align: right;
}
</style>
