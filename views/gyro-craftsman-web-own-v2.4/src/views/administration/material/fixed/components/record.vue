<!-- 物资记录详情弹窗 -->
<template>
<div class="station">
  <el-drawer
    :title="formData.title"
    :visible.sync="drawer"
    :direction="direction"
    :modal="true"
    :wrapper-closable="true"
    :before-close="handleClose"
    :append-to-body="true"
    :size="formData.width"
  >
    <div slot="title" class="invoice-title">
      <el-row class="invoice-header">
        <el-col class="invoice-left">
          <div class="invoice-logo"><i class="icon iconfont iconhetong"></i></div>
        </el-col>
        <el-col v-if="drawer" class="invoice-right">
          <div class="txt1 over-text">
            {{ formData.title }}
          </div>
          <div class="txt2">
            <span class="title">{{ $("ui.administrationMaterialFixedMaterialDialogMaterialName") }}</span>
            <span>{{ formData.data.name || '-' }}</span>

            <span class="title">{{ $("ui.administrationMaterialFixedRecordSpecificationModel") }}</span> <span>{{ formData.data.units || '--' }}</span>
            <span class="title">{{ $("ui.administrationMaterialFixedMaterialDialogMaterialCategory") }}</span><span>{{ $(formData.data.cate.cate_name) || '--' }}</span>
            <span class="title">{{ $("ui.administrationMaterialFixedRecordUnitOfMeasure") }}</span><span>{{ formData.data.specs || '--' }}</span>
          </div>
        </el-col>
      </el-row>
    </div>

    <div class="invoice v-height-flag">
      <el-form ref="form" class="mt14" label-width="80px">
        <el-row :gutter="14">
          <el-col :span="9">
            <el-form-item :label="$('ui.administrationMaterialFixedRecordBusinessType')">
              <el-select v-model="where.types" @change="getSearch" size="small" clearable :placeholder="$('ui.invoiceInvoiceDetailsBusinessType')">
                <el-option v-for="(item, index) in option" :key="index" :label="item.label" :value="item.value" />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="9">
            <el-form-item :label="$('ui.administrationMaterialFixedRecordDepartmentEmployee')">
              <el-select
                v-model="index"
                filterable
                size="small"
                clearable
                :placeholder="$('ui.administrationMaterialFixedRecordPleaseSelectDepartmentEmployee')"
                @change="getSearch"
              >
                <el-option
                  v-for="(item, index) in userOptions"
                  :key="item.value"
                  :label="item.name"
                  :value="index"
                ></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-tooltip effect="dark" :content="$('ui.administrationMaterialFixedRecordResetSearchConditions')" placement="top">
              <div class="reset" @click="reset"><i class="iconfont iconqingchu"></i></div>
            </el-tooltip>
          </el-col>
        </el-row>
      </el-form>
      <div class="table-box v-height-flag">
        <el-table :data="tableData" style="width: 100%" row-key="id" default-expand-all>
          <el-table-column prop="id" :label="$('ui.formCommonOaTableSerialNumber')" min-width="45">
            <template slot-scope="scope">{{ scope.$index + 1 }}</template>
          </el-table-column>
          <el-table-column prop="types" :label="$('ui.invoiceInvoiceDetailsBusinessType')" min-width="80">
            <template slot-scope="scope">
              <span v-if="scope.row.types === 0">{{ $("ui.administrationMaterialFixedMaterialDialogStockIn") }}</span>
              <span v-if="scope.row.types === 1">{{ $("ui.administrationMaterialFixedConsumeIssue") }}</span>
              <span v-if="scope.row.types === 2">{{ $("ui.administrationMaterialFixedFixedReturn") }}</span>
              <span v-if="scope.row.types === 3">{{ $("ui.administrationMaterialFixedFixedRepair") }}</span>
              <span v-if="scope.row.types === 4">{{ $("ui.administrationMaterialFixedFixedDisposal") }}</span>
              <span v-if="scope.row.types === 5">{{ $("ui.administrationMaterialFixedRecordRepairHandling") }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="info" :label="$('ui.administrationMaterialFixedFixedImportantInformation')" min-width="130"></el-table-column>
          <el-table-column prop="num" :label="$('ui.administrationMaterialFixedRecordMaterialQuantity')" min-width="80" />
          <el-table-column prop="creater.name" :label="$('ui.administrationMaterialChartIndexOperator')" min-width="80" />
          <el-table-column prop="created_at" :label="$('ui.administrationMaterialFixedLogOperationTime')" min-width="130" />
          <el-table-column prop="mark" :label="$('ui.xmindEditorToolbarNodeBtnListRemarks')" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row.mark || '--' }}</span>
            </template>
          </el-table-column>
        </el-table>
        <el-pagination
          :page-size="where.limit"
          :current-page="where.page"
          :page-sizes="[10, 15, 20]"
          layout="total, prev, pager, next, jumper"
          :total="total"
          @size-change="handleSizeChange"
          @current-change="pageChange"
        />
      </div>
    </div>
  </el-drawer>
</div>
</template>
<script>
import { $ } from '@/lang'
import { storageRecordApi, storageRecordUsersApi } from '@/api/administration'

export default {
  name: 'Record',
  components: {},
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
      drawer: false,
      direction: 'rtl',
      loading: false,
      where: {
        page: 1,
        limit: 15,
        name: '',
        types: '',
        storage_id: null,
        frame_id: '',
        card_id: ''
      },
      usersWhere: {
        types: '',
        storage_id: null
      },
      index: 0,
      total: 0,
      tableData: [],
      onlyPerson: false,
      openStatus: false,
      activeDepartment: {},
      option: [],
      userOptions: []
    }
  },
  watch: {
    formData: {
      handler(nVal) {
        if (nVal.type === 0) {
          this.option = [
            { value: '', label: $('finance.all') },
            { value: 0, label: $('ui.administrationMaterialFixedMaterialDialogStockIn') },
            { value: 1, label: $('ui.administrationMaterialFixedConsumeIssue') }
          ]
        } else {
          this.option = [
            { value: '', label: $('finance.all') },
            { value: 0, label: $('ui.administrationMaterialFixedMaterialDialogStockIn') },
            { value: 1, label: $('ui.administrationMaterialFixedConsumeIssue') },
            { value: 2, label: $('ui.administrationMaterialFixedFixedReturn') },
            { value: 3, label: $('ui.administrationMaterialFixedFixedRepair') },
            { value: 4, label: $('ui.administrationMaterialFixedFixedDisposal') },
            { value: 5, label: $('ui.administrationMaterialFixedRecordRepairHandling') }
          ]
        }
      },
      deep: true
    }
  },
  methods: {
    handleClose() {
      this.drawer = false
      this.where.types = ''
      this.where.name = ''
      this.where.card_id = ''
      this.where.frame_id = ''
      this.index = 0
      this.where.page = 1
    },
    openBox() {
      this.drawer = true
      this.getTableData()
      this.getOptionData()
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.getTableData()
    },
    getOptionData() {
      storageRecordUsersApi(this.usersWhere).then((res) => {
        this.userOptions = res.data || []
        this.userOptions.unshift({ id: '', name: '全部', types: -1 })
      })
    },
    pageChange(page) {
      this.where.page = page
      this.getTableData()
    },
    getSearch() {
      this.where.page = 1
      this.getTableData()
    },
    reset() {
      this.where.types = ''
      this.where.name = ''
      this.where.card_id = ''
      this.where.frame_id = ''
      this.index = 0
      this.where.page = 1
      this.getSearch()
    },
    // 记录
    getTableData() {
      if (this.index) {
        const data = this.userOptions[this.index]
        if (data.types === 0) {
          this.where.card_id = data.id
          this.where.frame_id = ''
        } else {
          this.where.card_id = ''
          this.where.frame_id = data.id
        }
      } else {
        this.where.frame_id = ''
        this.where.card_id = ''
      }
      storageRecordApi(this.where).then((res) => {
        this.tableData = res.data.list || []
        this.total = res.data.count
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.station ::v-deep .el-drawer__body {
  padding: 20px 20px 50px 20px;
}
.btn {
  width: 54px;
  height: 32px;
  font-size: 13px;
  border-radius: 4px;
  border: 1px solid #dcdfe6;
  font-weight: 500;
  color: #606266;
  background-color: #fff;
}

.invoice {
  margin: 20px;
  height: calc(100% - 40px);
  .caption {
    margin: 0 -20px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(216, 216, 216, 0.3);
    ::v-deep .el-row {
      padding: 0 20px;
      font-size: 13px;
      font-weight: 600;
    }
  }
}
.table-box {
  height: calc(100% - 130px);
}
::v-deep .el-select,
::v-deep .el-input-number {
  width: 100%;
}
::v-deep .el-form-item {
  margin-bottom: 14px;
}
::v-deep .el-drawer__header {
  border-bottom: none;
  padding-bottom: 10px;
  padding-top: 10px;
  border-bottom: 1px solid #dcdfe6;
}
::v-deep .el-drawer__body {
  padding-bottom: 20px;
}
::v-deep .el-pagination {
  display: flex;
  flex-pack: end;
  justify-content: flex-end;
  margin-top: 20px;
}
.invoice-title {
  .invoice-header {
    display: flex;
    align-items: center;
    .invoice-left {
      width: 48px;
      margin-right: 10px;
      .invoice-logo {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #1890ff;
        border-radius: 4px;
        i {
          color: #ffffff;
          font-size: 30px;
        }
      }
    }
    .invoice-right {
      width: calc(100% - 55px);
    }
    .txt1 {
      font-size: 16px;
      font-weight: bold;
      color: rgba(0, 0, 0, 0.85);
    }
    .txt3 {
      font-size: 14px;
    }
    .txt2 {
      margin-top: 10px;
      font-size: 13px;
      color: #000;
      .title {
        color: #999999;
        padding-left: 20px;
      }
      .title:first-of-type {
        padding-left: 0;
      }
      .info1 {
        color: #19be6b;
      }
      .info2 {
        color: rgba(245, 34, 45, 1);
      }
      .info3 {
        color: #1890ff;
      }
    }
  }
}
::v-deep .el-drawer__header {
  height: 80px !important;
  line-height: none !important;
}
</style>
