<template>
  <el-dialog
    :title="config.title"
    :visible.sync="dialogVisible"
    :width="config.width"
    :append-to-body="true"
    :before-close="handleClose"
    :close-on-click-modal="false"
  >
    <el-form ref="form" :model="formData" :rules="rules" :label-width="labelWidth">
      <div v-if="config.type === 1 || config.type === 3">
        <el-form-item class="mt20">
          <span slot="label">
            <span class="color-tab">*</span>
            {{ $("legacy.571d32700b46a308") }}
          </span>
          <el-col :span="11">
            <el-form-item prop="levelMin">
              <el-input-number
                v-model="formData.levelMin"
                :placeholder='$("legacy.eb2129f2fdcf13f2")'
                :min="0"
                :controls="false"
              ></el-input-number>
            </el-form-item>
          </el-col>
          <el-col class="text-center line" :span="2"><el-divider></el-divider></el-col>
          <el-col :span="11">
            <el-form-item prop="levelMax">
              <el-input-number
                v-model="formData.levelMax"
                :placeholder='$("legacy.45e19ed86bafd50d")'
                :min="formData.levelMin"
                :controls="false"
              ></el-input-number>
            </el-form-item>
          </el-col>
        </el-form-item>
        <el-form-item class="mt20">
          <span slot="label">
            <span class="color-tab">*</span>
            {{ $("legacy.3d64ed45eb24bca8") }}
          </span>
          <el-col :span="11">
            <el-form-item prop="salaryMin">
              <el-input-number
                v-model="formData.salaryMin"
                :placeholder='$("legacy.2e5c3b7083a71300")'
                :min="0"
                :controls="false"
              ></el-input-number>
            </el-form-item>
          </el-col>
          <el-col class="text-center line" :span="2"><el-divider></el-divider></el-col>
          <el-col :span="11">
            <el-form-item prop="salaryMax">
              <el-input-number
                v-model="formData.salaryMax"
                :placeholder='$("legacy.9eb17dad08fa9ba9")'
                :min="formData.salaryMin"
                :controls="false"
              ></el-input-number>
            </el-form-item>
          </el-col>
        </el-form-item>
      </div>
      <div v-if="config.type === 2">
        <el-form-item class="mt20" :label='$("legacy.c5952d5d6d7451e1")' prop="rank">
          <el-input-number :placeholder='$("legacyScript.pleaseEnterTheJobGradeSpan")' v-model="formData.rank" :controls="false"></el-input-number>
        </el-form-item>
        <el-form-item class="mt20" :label='$("legacy.6f7880f833915a73")' style="margin-bottom: 0">
          <div class="rank-explain">
            <p>{{ $("legacy.17ff579e3a5609e8") }}</p>
            <p>{{ $("legacy.d0a9a3c4e093ce0d") }}</p>
          </div>
        </el-form-item>
      </div>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button size="small" @click="handleClose">{{ $('public.cancel') }}</el-button>
      <el-button size="small" type="primary" :loading="loading" @click="handleConfirm">{{ $('public.ok') }}</el-button>
    </div>
  </el-dialog>
</template>
<script>
import { $ } from '@/lang'
import { rankLevelBatchApi, rankLevelEditApi, rankLevelSaveApi } from '@/api/setting'

export default {
  name: 'RankDialog',
  props: {
    config: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },
  data() {
    const checkLevelMin = (rule, value, callback) => {
      if (!value) {
        return callback(new Error($('请输入最低职等')))
      }
      setTimeout(() => {
        if (this.formData.levelMax && value > this.formData.levelMax) {
          callback(new Error($('最低职等不能大于最高职等')))
        } else {
          callback()
        }
      }, 150)
    }
    return {
      dialogVisible: false,
      labelWidth: '90px',
      formData: {
        levelMin: undefined,
        levelMax: undefined,
        salaryMin: undefined,
        salaryMax: undefined,
        rank: undefined
      },
      rules: {
        levelMin: [{ required: true, validator: checkLevelMin, trigger: 'blur' }],
        levelMax: [{ required: true, message: $('legacyScript.pleaseEnterTheHighestJobLevel'), trigger: 'blur' }],
        salaryMin: [{ required: true, message: $('legacyScript.pleaseEnterTheMinimumSalary'), trigger: 'blur' }],
        salaryMax: [{ required: true, message: $('legacyScript.pleaseEnterTheMaximumSalary'), trigger: 'blur' }],
        rank: [{ required: true, message: $('legacyScript.pleaseEnterTheJobGradeSpan'), trigger: 'blur' }]
      },
      loading: false
    }
  },
  watch: {
    config: {
      handler(nVal) {
        if (nVal.type === 2) {
          this.labelWidth = '110px'
        } else {
          this.labelWidth = '90px'
        }
        if (nVal.edit) {
          if (nVal.type === 1) {
            this.formData.levelMin = nVal.data.min_level
            this.formData.levelMax = nVal.data.max_level
            const arr = nVal.data.salary.split('-')
            this.formData.salaryMin = Number(arr[0])
            this.formData.salaryMax = Number(arr[1])
          }
        }
      },
      deep: true
    }
  },
  mounted() {},
  methods: {
    handleOpen() {
      this.dialogVisible = true
    },
    reset() {
      this.formData.levelMin = undefined
      this.formData.levelMax = undefined
      this.formData.salaryMin = undefined
      this.formData.salaryMax = undefined
      this.formData.rank = undefined
    },
    handleClose() {
      this.$refs.form.clearValidate()
      this.dialogVisible = false
    },
    handleConfirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          let data = {}
          if (this.config.type === 1) {
            data.salary = this.formData.salaryMin + '-' + this.formData.salaryMax
            data.min_level = this.formData.levelMin
            data.max_level = this.formData.levelMax
            if (this.config.edit === false) {
              this.rankLevelSave(data)
            } else {
              this.rankLevelEdit(this.config.data.id, data)
            }
          } else if (this.config.type === 2) {
            this.rankLevelBatch(this.formData.rank)
          }
        }
      })
    },
    // 职位等级添加
    rankLevelSave(data) {
      this.loading = true
      rankLevelSaveApi(data)
        .then((res) => {
          this.handleClose()
          this.loading = false
          this.$emit('isOk')
          this.reset()
        })
        .catch((error) => {
          this.loading = false
        })
    },
    // 职位等级编辑
    rankLevelEdit(id, data) {
      this.loading = true
      rankLevelEditApi(id, data)
        .then((res) => {
          this.handleClose()
          this.loading = false
          this.$emit('isOk')
          this.reset()
        })
        .catch((error) => {
          this.loading = false
        })
    },
    // 批量修改职位区间
    rankLevelBatch(id) {
      this.loading = true
      rankLevelBatchApi(id)
        .then((res) => {
          this.handleClose()
          this.loading = false
          this.$emit('isOk')
          this.reset()
        })
        .catch((error) => {
          this.loading = false
        })
    }
  }
}
</script>

<style scoped lang="scss">
.line {
  padding: 0 8px;
}
::v-deep .el-input-number--medium {
  width: 100%;
}
::v-deep .el-input__inner {
  text-align: left;
}
.rank-explain {
  p {
    margin: 0;
    font-size: 13px;
    color: rgba(0, 0, 0, 0.5);
  }
}
::v-deep .el-dialog__footer {
  padding: 0;
}
.dialog-footer {
  padding: 20px;
  border-top: 1px solid #e6ebf5;
}
</style>
