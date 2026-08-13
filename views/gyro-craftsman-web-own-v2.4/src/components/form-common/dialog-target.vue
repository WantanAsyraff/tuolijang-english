import { $ } from '@/lang'
<!-- @FileDescription: 指标模板库弹窗页面 -->
<template>
  <div>
    <el-dialog
      :modal="true"
      :show-close="true"
      :title="title"
      :visible.sync="dialogFormVisible"
      top="8vh"
      width="1200px"
    >
      <div slot="title" class="dialog-title">
        <div class="dialog-title-title">{{ title }}</div>
      </div>
      <el-row>
        <el-col :span="4">
          <!--部门-->
          <departmentNot ref="department" :type="type" @eventOptionData="eventOptionData" />
        </el-col>
        <el-col :span="20">
          <div class="dialog-box">
            <span class="info"
              >{{ $("file.total") }}{{ total }}{{ $("legacy.0d56e4c3a3c98d9b") }}<span v-if="multipleSelection.length !== 0"
                >{{ $("legacy.4689290d0e7f5d77") }} {{ multipleSelection.length }} {{ $("legacy.0d56e4c3a3c98d9b") }}</span
              ></span
            >
            <div class="dialog-title-search">
              <el-input
                v-model="where.name"
                clearable
                :placeholder='$("legacy.5a6962f8d4a1708a")'
                prefix-icon="el-icon-search"
                size="small"
                style="width: 300px"
                @change="handleSearch"
              />
            </div>
          </div>
          <div class="ml10">
            <div class="table-box">
              <el-table
                ref="table"
                :data="tableData"
                :header-cell-style="{ background: '#f0fafe' }"
                :height="420"
                :row-key="getRowKeys"
                :tree-props="{ children: 'children' }"
                default-expand-all
                style="width: 100%"
                @selection-change="handleSelectionChange"
              >
                <el-table-column type="selection" width="55" />
                <el-table-column :label='$("access.targetname")' prop="name" />
                <el-table-column :label='$("ui.hrAssessQuotaIndexIndicatorContent")' prop="content" />
                <el-table-column :label='$("ui.userAssessmentExecutionScoringCriteria")' prop="info" />
              </el-table>
            </div>
          </div>
          <div class="block">
            <el-pagination
              :current-page="where.page"
              :page-size="where.limit"
              :total="total"
              layout="total, prev, pager, next"
              @size-change="handleSizeChange"
              @current-change="pageChange"
            />
          </div>
        </el-col>
      </el-row>
      <div slot="footer" class="dialog-footer">
        <el-button size="small" @click="closeDialog">{{ $("public.cancel") }}</el-button>
        <el-button size="small" type="primary" @click="handleConfirm">{{ $("public.ok") }}</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { assessTargetListApi } from '@/api/enterprise'
export default {
  name: 'SelectTarget',
  components: {
    departmentNot: () => import('@/components/user/department')
  },
  props: {
    title: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      dialogFormVisible: false,
      type: 0,
      where: {
        page: 1,
        limit: 15,
        name: ''
      },
      tableData: [],
      total: 0,
      selectDada: [],
      multipleSelection: []
    }
  },
  watch: {},
  mounted() {},
  methods: {
    openDialog() {
      this.dialogFormVisible = true
    },
    closeDialog() {
      if (this.multipleSelection.length > 0) {
        this.$refs.table.clearSelection()
      }
      this.dialogFormVisible = false
    },
    handleConfirm() {
      if (this.multipleSelection.length <= 0) {
        this.$message.error($('legacyScript.noSelectionMade'))
      } else {
        this.$emit('dialogChangeDada', this.multipleSelection)
        this.closeDialog()
      }
    },
    handleSearch() {
      this.where.page = 1
      this.getTableData()
    },
    getTableData() {
      const data = {
        page: this.where.page,
        limit: this.where.limit,
        name: this.where.name,
        cate_id: this.cateId
      }
      assessTargetListApi(data).then((res) => {
        this.tableData = res.data.list
        this.total = res.data.count
      })
    },
    pageChange(page) {
      this.where.page = page
      this.getTableData()
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.getTableData()
    },
    eventOptionData(data) {
      this.cateId = data.id === 'template' ? '' : data.id
      this.where.page = 1
      this.getTableData()
    },
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    getRowKeys(row) {
      return row.id
    }
  }
}
</script>

<style lang="scss" scoped>
.dialog-title {
  font-size: 14px;
  display: flex;
  align-items: center;

  .dialog-title-title {
    width: 30%;
    font-size: 16px;
    font-family: PingFang SC;
    font-weight: 600;
    line-height: 22px;
    color: rgba(0, 0, 0, 0.85);
  }
  ::v-deep .el-pagination {
    padding-bottom: 0;
  }
  .dialog-title-search {
    display: inline-block;
    // display: flex;
    // align-items: center;
    // width: 70%;
    ::v-deep button {
      margin-left: 15px;
    }
  }
}

.info {
  margin-left: 12px;
  line-height: 32px;
}
.table-box {
  margin-left: 12px;
}
::v-deep .el-table th.is-leaf {
  background: #f0fafe !important;
  color: #000;
}
.dialog-box {
  display: flex;
  justify-content: space-between;
  margin: 0 12px 20px;
}
</style>
