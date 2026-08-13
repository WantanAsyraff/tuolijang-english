<template>
  <div>
    <el-dialog
      :visible.sync="dialogFormVisible"
      :width="formData.width"
      :modal="true"
      :close-on-click-modal="false"
      custom-class="person"
      :show-close="true"
      :before-close="handleClose"
    >
      <template slot="title">
        <div class="password-dialog-title">
          <span>{{ $('passwordDialog.title') }}</span>
          <div class="password-dialog-lang">
            <el-button size="mini" :type="language === 'zh-cn' ? 'primary' : 'default'" plain @click="changeLanguage('zh-cn')">
              {{ $('login.chinese') }}
            </el-button>
            <el-button size="mini" :type="language === 'en' ? 'primary' : 'default'" @click="changeLanguage('en')">
              English
            </el-button>
          </div>
        </div>
      </template>
      <div class="container">
        <el-form ref="form" :model="tableFrom" :rules="rules" label-width="100px" class="mt20">
          <el-form-item class="info">
            <el-alert class="cr-alert" title="" :closable="false" type="info" :show-icon="true">
              <template slot="title">
                <p>{{ $('passwordDialog.initialPasswordTip') }}</p>
              </template>
            </el-alert>
          </el-form-item>
          <el-form-item :label="$('passwordDialog.password')" prop="password">
            <el-input
              v-model="tableFrom.password"
              size="small"
              type="password"
              auto-complete="on"
              :placeholder="$('passwordDialog.passwordPlaceholder')"
            />
          </el-form-item>
          <el-form-item :label="$('passwordDialog.confirmPassword')" prop="password_confirm">
            <el-input
              v-model="tableFrom.password_confirm"
              size="small"
              type="password"
              auto-complete="on"
              :placeholder="$('passwordDialog.confirmPasswordPlaceholder')"
            />
          </el-form-item>
        </el-form>
        <div class="text-right">
          <el-button size="small" @click="handleClose()">{{ $('public.cancel') }}</el-button>
          <el-button type="primary" size="small" @click="handleConfirm()">{{ $('public.ok') }}</el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { userEntSavePasswordApi } from '@/api/user'

export default {
  name: 'DialogForm',
  props: {
    formData: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },
  data() {
    return {
      dialogFormVisible: false,
      loading: false,
      tableFrom: {
        password: '',
        password_confirm: ''
      }
    }
  },
  computed: {
    language() {
      return this.$store.getters.lang
    },
    rules() {
      return {
        password: [{ required: true, message: this.$('passwordDialog.passwordRequired'), trigger: 'blur' }],
        password_confirm: [
          { required: true, message: this.$('passwordDialog.confirmPasswordRequired'), trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    changeLanguage(lang) {
      if (lang === this.language) return
      this.$store.dispatch('app/setLanguage', lang)
      this.$nextTick(() => {
        if (this.$refs.form) {
          this.$refs.form.clearValidate()
        }
      })
    },
    handleClose() {
      this.dialogFormVisible = false
      let userInfo = this.$store.state.user.userInfo
      userInfo.is_init = 0
      this.$store.commit('user/SET_USERINFO', userInfo)
    },
    handleOpen() {
      this.dialogFormVisible = true
    },
    handleConfirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.tableFrom.phone = this.$store.state.user.userInfo.phone
          this.savePassword(this.tableFrom)
        }
      })
    },
    savePassword(data) {
      this.loading = true
      userEntSavePasswordApi(data)
        .then((res) => {
          if (res.status == 200) {
            this.loading = false
            this.handleClose()
          }
        })
        .catch(() => {
          this.loading = false
        })
    }
  }
}
</script>

<style lang="scss" scoped>
.password-dialog-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-right: 34px;
}
.password-dialog-lang {
  display: inline-flex;
  gap: 8px;
}
.info {
  ::v-deep .el-form-item__content {
    margin-left: 12px !important;
  }
  ::v-deep .el-alert {
    padding: 6px 20px;
  }
}
::v-deep .el-dialog__wrapper {
  background-color: rgba(0, 0, 0, 0.6);
}
.dialog-footer {
  padding-top: 20px;
  border-top: 1px solid #e6ebf5;
  text-align: right;
}
</style>
