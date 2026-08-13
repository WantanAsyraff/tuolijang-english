import { $ } from '@/lang'
<template>
  <div class="table-box">
    <el-dialog
      :show-close="status == 'add' ? false : true"
      :visible.sync="dialogVisible"
      :width="title == '调薪弹窗' ? '680px' : '470px'"
      append-to-body
    >
      <div slot="title" class="header">
        <span>{{ title }}</span>
        <el-link v-if="status == 'add'" :underline="false" icon="el-icon-edit-outline" @click="editFn"
          >{{ $("ui.businessFormSettingFormCreateDesignerFcDesignerEditField") }}</el-link
        >
      </div>
      <div class="line" />

      <div v-if="status == 'add'" class="from">
        <el-form
          v-if="title !== '调薪弹窗'"
          ref="formName"
          class="demo-dynamic"
          label-position="right"
          label-width="100px"
        >
          <el-form-item
            :rules="[{ type: 'date', required: true, message: '请选择日期', trigger: 'change' }]"
            :label='$("legacy.ea0b7ecf44e2e5a1")'
          >
            <el-date-picker v-model="take_date" :placeholder='$("hr.placeholder4")' type="date" value-format="yyyy-MM-dd">
            </el-date-picker>
          </el-form-item>

          <div v-for="(item, index) in fromItem" :key="index">
            <el-form-item :label="item.label">
              <el-input
                v-model="item.value"
                :placeholder="item.placeholder"
                maxLength="9"
                type="number"
                @blur="blurVal(item.value, index)"
                @input="handleInput(item.value, index)"
              ></el-input>
            </el-form-item>
          </div>
          <el-form-item :label='$("legacy.ccad6c9545bc387e")' prop="desc">
            <el-input
              v-model="mark"
              maxlength="200"
              :placeholder='$("legacy.bc96c1245d6ff8e5")'
              style="width: 260px"
              type="textarea"
            ></el-input>
          </el-form-item>
          <el-form-item class="footer">
            <el-button class="btn" @click="restFn">{{ $("public.cancel") }}</el-button>
            <el-button :loading="loading" class="btn" type="primary" @click="okFn('formName')">{{ $("public.ok") }}</el-button>
          </el-form-item>
        </el-form>

        <el-form
          v-if="title == '调薪弹窗'"
          ref="formName"
          class="demo-dynamic"
          label-position="right"
          label-width="120px"
        >
          <el-form-item
            :rules="[{ type: 'date', required: true, message: '请选择日期', trigger: 'change' }]"
            :label='$("legacy.ea0b7ecf44e2e5a1")'
          >
            <div class="change-item">
              <el-date-picker v-model="take_date" :placeholder='$("hr.placeholder4")' type="date" value-format="yyyy-MM-dd">
              </el-date-picker>
            </div>
          </el-form-item>

          <div v-for="(item, index) in fromItem" :key="index">
            <el-form-item :label="item.label">
              <div class="change-item">
                <el-input v-model="item.num" :disabled="true" class="disabled-item"> </el-input>
                <i class="el-icon-d-arrow-right"></i>
                <el-input
                  v-model="item.value"
                  :placeholder="item.placeholder"
                  maxLength="9"
                  type="number"
                  @input="handleInput(item.value, index)"
                ></el-input>
              </div>
            </el-form-item>
          </div>
          <el-form-item :label='$("legacy.ccad6c9545bc387e")' prop="desc">
            <el-input
              v-model="mark"
              maxlength="200"
              :placeholder='$("legacy.1acac51e0e616f3e")'
              type="textarea"
            ></el-input>
          </el-form-item>
          <el-form-item class="footer">
            <el-button class="btn" @click="restFn">{{ $("public.cancel") }}</el-button>
            <el-button class="btn" type="primary" @click="okFn('formName')">{{ $("public.ok") }}</el-button>
          </el-form-item>
        </el-form>
      </div>
      <!-- 添加表单 -->

      <div v-if="status === 'edit'" class="addForm">
        <div class="assess-right v-height-flag">
          <div>
            <draggable
              v-model="fromItem"
              animation="1000"
              chosen-class="chosen"
              force-fallback="true"
              group="people"
              @end="onEnd"
              @start="onStart"
            >
              <transition-group>
                <div v-for="(item, index) in fromItem" :key="item.id" class="item-list">
                  <i class="icon iconfont icontuodong item-drag"></i>
                  <el-input v-model="item.label" clearable :placeholder='$("legacy.4d01e840dd9ffb59")' show-word-limit />
                  <i
                    v-if="item.sort !== 1 && item.sort !== 2"
                    class="el-icon-remove item-remove"
                    @click="handleDelete(index)"
                  ></i>
                </div>
              </transition-group>
            </draggable>
            <el-button class="add-type mt14" type="text" @click="handleAddType()">
              <i class="el-icon-plus"></i> {{ $("public.add") }}</el-button
            >

            <div class="footer">
              <el-button class="btn" @click="handleResetFn">{{ $("public.cancel") }}</el-button>
              <el-button class="btn" type="primary" @click="handleConfirm">{{ $("public.ok") }}</el-button>
            </div>
          </div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import draggable from 'vuedraggable'
import { getSalary, getSalaryContent, putSalaryContent, latelySalaryContent } from '@/api/enterprise'

export default {
  name: '',
  components: {
    draggable
  },
  props: {
    id: {
      type: [String, Number, Boolean],
      default: () => false
    },
    editForm: {
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {
      unmodifiedFormItem: [],
      dialogVisible: false,
      mark: '',
      title: $('legacyScript.setSalary'),
      status: 'add',
      loading: false,
      take_date: '',
      fromItem: [
        {
          label: $('legacyScript.baseSalaryCNY'),
          value: '0',
          placeholder: $('legacyScript.pleaseEnterBaseSalary'),
          sort: 1
        },
        {
          label: $('legacyScript.performancePayCNY'),
          value: 0,
          placeholder: $('legacyScript.pleaseEnterPerformancePay'),
          sort: 2
        },
        {
          label: $('legacyScript.positionSalaryCNY'),
          value: 0,
          placeholder: $('legacyScript.pleaseEnterPositionSalary'),
          sort: 3
        },

        {
          label: $('legacyScript.allowanceCNY'),
          value: 0,
          placeholder: $('legacyScript.pleaseEnterManagementAllowance'),
          sort: 4
        },
        {
          label: $('legacyScript.skillSubsidyCNY'),
          value: 0,
          placeholder: $('legacyScript.pleaseEnterBaseSalary'),
          sort: 5
        },

        {
          label: $('legacyScript.otherSubsidiesYuan'),
          value: 0,
          placeholder: $('legacyScript.pleaseEnterOtherSubsidiesSuchAsDiningTransportationPhoneBills'),
          sort: 6
        }
      ],
      time: '',
      rules: [],
      total: 0,
      salaryId: '',
      type: '',
      nowtype: ''
    }
  },
  created() {
    const nowDate = new Date()
    const date = {
      year: nowDate.getFullYear(),
      month: nowDate.getMonth() + 1,
      day: nowDate.getDate()
    }
    const dayDate = date.year + '-' + (date.month >= 10 ? date.month : '0' + date.month) + '-' + date.day
    this.take_date = dayDate
  },

  methods: {
    show() {
      this.dialogVisible = true
    },
    handleResetFn() {
      this.fromItem = JSON.parse(JSON.stringify(this.unmodifiedFormItem))
      if (this.nowtype == '调薪弹窗') {
        this.title = $('legacyScript.salaryAdjustmentPopup')
        this.status = 'add'
        this.type = '调薪弹窗'
      } else {
        // this.title = '编辑薪资'
        this.status = 'add'
      }
    },

    restFn() {
      this.dialogVisible = false
      this.fromItem.forEach((item) => {
        item.value = 0
      })
      this.nowtype = ''
    },

    blurVal(data, index) {
      this.fromItem[index].value = data.replace(/^\D*([0-9]\d*\.?\d{0,2})?.*$/, '$1')
    },
    handleInput(data, index) {
      this.fromItem[index].value = data.replace(/\D/g, '').replace(/^0{1,}/g, '')
    },

    // 单独编辑
    async editId(val) {
      this.status = 'add'
      this.title = val.title
      this.salaryId = val.id
      const result = await getSalaryContent(val.id)
      this.mark = result.data.mark
      this.take_date = result.data.take_date
      this.fromItem = result.data.content
      this.dialogVisible = true
    },

    // 调薪弹窗
    async changeSalary(val) {
      this.title = val.title
      this.status = 'add'
      this.type = '调薪弹窗'
      this.nowtype = '调薪弹窗'
      const result = await latelySalaryContent(this.id)
      this.mark = result.data.mark
      this.take_date = result.data.take_date
      this.fromItem = result.data.content
      // 添加新属性
      for (let i = 0; i < this.fromItem.length; i++) {
        this.fromItem[i].num = this.fromItem[i].value
      }
      //  绑定值置空
      this.fromItem.forEach((item) => {
        item.value = ''
      })
      this.dialogVisible = true
    },

    // 编辑
    editFn() {
      this.unmodifiedFormItem = JSON.parse(JSON.stringify(this.fromItem))
      this.title = $('legacyScript.editFormFields')
      this.status = 'edit'
    },

    // 拖动
    onStart() {
      this.drag = true
    },
    onEnd() {
      this.drag = false
    },

    // 删除动态表单
    handleDelete(index) {
      this.fromItem.splice(index, 1)
    },

    // 添加动态表单
    handleAddType() {
      if (this.fromItem.length > 0) {
        const status = this.fromItem.some((el, index) => {
          return el.label === ''
        })
        if (status) {
          this.$message.warning(this.$('customer.message05'))
        } else {
          this.fromItem.push({
            sort: '',
            value: 0,
            label: '',
            placeholder: $('finance.pleaseinput')
          })
        }
      } else {
        this.fromItem.push({
          sort: '',
          value: 0,
          label: '',
          placeholder: $('finance.pleaseinput')
        })
      }
    },

    // 提交表单
    async okFn() {
      let totalArr = []
      this.fromItem.forEach((item) => {
        totalArr.push(item.value)
      })
      let newtol = totalArr.map(Number)
      this.total = newtol.reduce((x, y) => x + y)

      let data = {
        take_date: this.take_date,
        card_id: this.id,
        total: this.total,
        content: this.fromItem,
        mark: this.mark
      }
      this.loading = true
      if (this.title == '编辑薪资') {
        await putSalaryContent(this.salaryId, data)
        await this.$emit('getSalaryList')
        this.dialogVisible = false
        this.loading = false
      } else {
        await getSalary(data)
        this.dialogVisible = false
        await this.$emit('getSalaryList')
        this.loading = false
        this.fromItem.forEach((item) => {
          item.value = '0'
        })
      }
    },

    // 确定动态表单
    handleConfirm() {
      const data = []
      if (this.fromItem.length <= 0) {
        this.$message.warning(this.$('customer.message05'))
      } else {
        const status = this.fromItem.some((el, index) => {
          return el.label === ''
        })
        if (status) {
          this.$message.warning(this.$('customer.message05'))
        } else {
          const len = this.fromItem.length
          this.fromItem.map((value, index) => {
            if (this.type == '调薪弹窗') {
              data.push({
                label: value.label,
                value: '',
                num: 0,
                sort: len - index + 1,
                placeholder: value.placeholder + value.label
              })
            } else {
              data.push({
                label: value.label,
                value: value.value,
                sort: len - index + 1,
                placeholder: value.placeholder + value.label
              })
            }
          })
          this.status = 'add'
          if (this.type == '调薪弹窗') {
            this.title = $('legacyScript.salaryAdjustmentPopup')
          }
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.mt15 {
  margin-top: 15px;
}
::v-deep .el-dialog {
  height: 600px;
  overflow: auto;
}
::v-deep .el-dialog::-webkit-scrollbar {
  height: 0;
  width: 0;
}

.line {
  width: 100%;
  height: 4px;
  border-bottom: 1px solid #ccc;
  margin-bottom: 20px;
}
.el-icon-remove {
  margin-top: 10px;
  margin-left: 5px;
  color: red;
}
.change-item {
  display: flex;
  align-items: center;
  .disabled-item {
    width: 50%;
  }
  ::v-deep .el-date-editor {
    width: 242px;
  }
  .el-icon-d-arrow-right {
    margin: 0 10px;
    font-size: 16px;
    color: #ccc;
  }
}
.add-text {
  color: #1890ff;
  font-weight: 500;
}
.header {
  display: flex;
  justify-content: space-between;
  span {
    font-size: 13px;
    font-weight: 700;
  }
}
::v-deep .el-scrollbar__wrap {
  margin-right: -17px !important;
  margin-bottom: -17px !important;
  margin-left: 20px;
}
.item-list {
  margin-top: 14px;
}
.item {
  display: flex;
}
::v-deep .el-dialog__body {
  padding: 0px 20px 10px 20px;
}
::v-deep .el-input {
  width: 260px;
}
::v-deep .el-form-item__label {
  font-size: 12px !important;
  font-weight: 500;
}
::v-deep .el-input__inner {
  height: 28px;
  font-size: 13px;
}
::v-deep .el-form-item {
  margin-bottom: 10px;
}
.footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 80px;
  margin-right: 10px;
  .btn {
    margin-bottom: 28px;
  }
}
</style>
<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none !important;
  margin: 0;
}
</style>
