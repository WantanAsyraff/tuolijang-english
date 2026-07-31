<!-- 客户-选择客户标签弹窗组件 -->
<template>
<el-dialog
  :title="$t('ui.customerListLabelDialogSelectLabel')"
  :append-to-body="true"
  :visible.sync="dialogVisible"
  width="50%"
  :before-close="handleClose"
>
  <div class="label-content">
    <div>
      <el-input v-model="searchName"  :placeholder="$t('ui.customerListLabelDialogPleaseEnterCustomerLabels')" size="small" clearable style="width: 250px" @change="searchFn" />
    </div>
    <el-scrollbar style="height: calc(100% - 47px)">
      <el-checkbox-group v-model="checkGroup" size="small">
        <template v-for="child in tableData">
          <div class="box" v-if="child.children">
            <div class="label-title" v-if="child.children && child.children.length > 0">
              <span class="line" /> {{ child.name }}
              <el-button
                type="text"
                v-if="config.label && config.label.length > 0"
                @click="checkAll(child, child.child ? 'child' : 'children')"
                >{{ child.checked ? $t('ui.formCommonSelectLabelDeselectAll') : $t('ui.formCommonSelectLabelAll') }}</el-button
              >
            </div>
            <template v-if="child.children">
              <el-checkbox-button
                v-for="item in child.children"
                fill="#1890FF"
                :key="item.id"
                :class="item.pid === 0 ? 'label-title' : ''"
                border
                size="small"
                :label="item.id"
                @change="(val) => onChange(val, child, 'children')"
                >{{ item.name }}</el-checkbox-button
              >
            </template>
          </div>
        </template>
      </el-checkbox-group>
    </el-scrollbar>
  </div>

  <div slot="footer" class="dialog-footer">
    <el-button size="small" @click="handleClose">{{ $t('public.cancel') }}</el-button>
    <el-button size="small" type="primary" @click="handleConfirm">{{ $t('public.ok') }}</el-button>
  </div>
</el-dialog>
</template>
<script>
import { clientConfigLabelApi } from '@/api/enterprise'

export default {
  name: 'LabelDialog',
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
      dialogVisible: false,
      isIndeterminate: true,
      labelWidth: 90,
      tableData: [],
      labelData: [],
      arr: [],
      allData: [],
      where: {
        page: 0,
        limit: 0
      },
      searchName:'',

      pageArr: [1],
      total: 0,
      checkGroup: [],
      configDate: {},
      checkList: []
    }
  },
  watch: {
    config: {
      handler(nVal) {
        if (nVal.list && nVal.list.length > 0) {
          this.tableData = []
          this.tableData = nVal.list
          if (this.tableData && this.tableData.length > 0) {
            this.tableData.map((value) => {
              if (value.children && value.children.length > 0) {
                this.labelData.push(value)
                value.children.map((val) => {
                  this.labelData.push(val)
                })
              }
            })
          }
        }

        if (nVal.label && nVal.label.length > 0) {
          this.checkGroup = []
          nVal.label.map((value) => {
            this.checkGroup.push(value.id)
          })
          const checkSet = new Set(nVal.label.map((value) => value.id))

          this.tableData.forEach((item) => {
            item.checked = item.children.every((v) => checkSet.has(v.id))
          })
        } else {
          this.checkGroup = []
        }
        this.checkGroup = this.checkGroup.map(Number)
      },
      deep: true
    }
  },
  mounted() {
    this.getTableData()
  },
  methods: {
    searchFn() {
      this.tableData = []
      if (this.searchName) {
        // 使用 Set 来存储已添加的父项 ID，避免重复
        const addedParentIds = new Set()
        
        this.labelData.forEach((value) => {
          if(value.children && value.children.length > 0) {
            // 检查当前父项是否有子项匹配搜索条件
            const hasMatchingChild = value.children.some((val) => {
              return val.name.indexOf(this.searchName) > -1
            })
            
            // 如果有匹配的子项且父项尚未添加，则添加到结果中
            if (hasMatchingChild && !addedParentIds.has(value.id)) {
              this.tableData.push(value)
              addedParentIds.add(value.id)
            }
          }
        })
      } else {
        this.tableData = this.labelData
      }
    },
    onChange(val, parent, child) {
      if (val) {
        parent.checked = parent[child].every((v) => {
          return this.checkGroup.indexOf(v.id) > -1
        })
      } else {
        parent.checked = false
      }
    },
    checkAll(item, child) {
      if (item.checked) {
        item[child].forEach((v) => {
          if (this.checkGroup.indexOf(v.id) > -1) {
            this.checkGroup.splice(this.checkGroup.indexOf(v.id), 1)
          }
        })
        item.checked = false
        return
      }
      item[child].forEach((v) => {
        if (this.checkGroup.indexOf(v.id) === -1) {
          this.checkGroup.push(v.id)
        }
      })
      item.checked = true
    },

    // 列表
    getTableData() {
      if (this.tableData.length > 0) return false
      const data = {
        page: this.where.page,
        limit: this.where.limit
      }

      clientConfigLabelApi(data).then((res) => {
        this.tableData = res.data.list
        // this.tableData = res.data.list && res.data.list.length == 0 ? [] : res.data.list
        this.total = res.data.count
        this.labelData = []
        if (this.tableData && this.tableData.length > 0) {
          this.tableData.map((value) => {
            if (value.children.length > 0) {
              this.labelData.push(value)
              value.children.map((val) => {
                this.labelData.push(val)
              })
            }
          })
        }
      })
    },
    handleOpen() {
      this.dialogVisible = true
      this.tableData.forEach((v) => {
        v.checked = false
      })
    },
    handleClose() {
      this.dialogVisible = false
    },
    handleConfirm() {
      this.arr = []
      if (this.checkGroup.length > 0) {
        this.labelData.map((value, index) => {
          this.checkGroup.map((val) => {
            if (value.id === val) {
              this.arr.push(value)
            }
          })
        })
      }
      this.dialogVisible = false
      this.arr = this.arr.filter((obj, index) => this.arr.findIndex((item) => item.id === obj.id) === index)
      this.configDate = {
        data: this.arr,
        type: this.config.edit
      }

      this.$emit('handleLabelConf', this.configDate)
    }
  }
}
</script>

<style scoped lang="scss">
::v-deep .el-dialog__header {
  border-bottom: 1px solid #d8d8d8;
}
::v-deep .el-scrollbar__wrap {
  overflow-x: hidden;
}
::v-deep .el-dialog {
  border-radius: 6px;
}
.label-content {
  height: 420px;
  overflow: hidden;
}
.label-title {
 
  width: 100%;
  height: 36px;
  font-size: 14px;
  display: flex;
 align-items: center;
  font-family: PingFangSC-Semibold, PingFang SC;
  font-weight: 600;
  color: #333333;
  // margin-bottom: 6px;

  position: relative;
}


.box {
  margin-bottom: 30px;
}

.line {
  display: inline-block;
  width: 4px;
  height: 14px;
  // margin-top: 5px;
  border-left: 2px solid #1890ff;
  margin-right: 6px;
}

::v-deep .el-checkbox-button.is-checked .el-checkbox-button__inner {
  box-sizing: border-box !important;
  background: rgba(24, 144, 255, 0.08);
  border-radius: 4px;
  border: 1px solid #1890ff !important;
  box-shadow: none;
  font-weight: 400;
  color: #1890ff;
  margin: 10px 10px 0px 0px;
}

::v-deep .el-checkbox-button--small .el-checkbox-button__inner {
  box-sizing: border-box !important;
  background: #f2f6fc;
  border-radius: 4px;
  margin: 10px 10px 0px 0px;
  border: 1px solid transparent;
}
::v-deep .el-checkbox-button--small .el-checkbox-button__inner:hover {
  background-color: rgba(24, 144, 255, 0.08) !important;
}

::v-deep .el-checkbox__label {
  padding-left: 6px;
}

::v-deep .el-button--text {
  font-size: 13px;
  margin-left: 4px;
}
.left {
 font-family: PingFang SC, PingFang SC;
font-weight: 400;
font-size: 12px;
color: #909399;
margin-right: 10px;
}
</style>
