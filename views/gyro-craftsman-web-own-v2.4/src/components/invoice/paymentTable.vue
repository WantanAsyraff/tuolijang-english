<!-- 订单发票-关联付款单表格组件 -->
<template>
<div class="paymentTable">
  <el-table
    ref="paymentTable"
    :data="table"
    :key="edit"
    row-key="id"
    style="width: 100%"
    @selection-change="handleSelectionChange"
  >
    <el-table-column type="selection" width="55" v-if="edit === ''"> </el-table-column>
    <el-table-column prop="bill_no" :label="$t('ui.invoiceInvoiceDetailsPaymentBillNo')" min-width="100"> </el-table-column>
    <el-table-column prop="treaty.contract_name" :label="$t('ui.invoiceInvoiceDetailsOrderName')" min-width="100"> </el-table-column>

    <el-table-column prop="types" :label="$t('ui.invoiceInvoiceDetailsBusinessType')" min-width="100">
      <template slot-scope="scope">
        <span v-if="scope.row.types === 0">{{ $t("ui.invoiceInvoiceDetailsPaymentRecord") }}</span>
        <span v-if="scope.row.types === 1"
          >{{ $t("ui.invoicePaymentTableRenewalRecord") }} <span v-if="scope.row.renew.title">-</span>{{ scope.row.renew.title }}</span
        >
      </template>
    </el-table-column>
    <el-table-column prop="num" :label="$t('ui.invoiceInvoiceDetailsPaymentAmountYuan')" min-width="100"> </el-table-column>
    <el-table-column prop="status" :label="$t('ui.invoicePaymentTableInvoiceStatus')" min-width="100">
      <template slot-scope="scope">
      
        <div v-if="scope.row.status != undefined">
          <span 
            :class="`table-btn ${getStatusClass(scope.row.status)}`"
          >
            {{ getStatusText(scope.row.status) }}
          </span>
        </div>
        <div v-else>--</div>
      </template>
    </el-table-column>

    <el-table-column prop="card.name" :label="$t('ui.hrAssessCheckIndexCreator')" min-width="90"> </el-table-column>
    <el-table-column prop="created_at" :label="$t('ui.invoiceInvoiceDetailsCreatedTime')" min-width="150"> </el-table-column>

    <el-table-column fixed="right" :label="$t('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" width="80" v-if="edit !== ''">
      <template slot-scope="scope">
        <el-button @click="deleteFn(scope.row)" type="text" size="small">{{ $t("ui.invoicePaymentTableRemove") }}</el-button>
      </template>
    </el-table-column>
  </el-table>
  <el-image-viewer v-if="isImage" :on-close="closeImageViewer" :url-list="srcList" />
</div>
</template>
<script>
import ElImageViewer from 'element-ui/packages/image/src/image-viewer'
export default {
  name: '',
  components: { ElImageViewer },
  props: ['tableData', 'edit', 'ids'],
  data() {
    return {
      table: this.tableData,
      isImage: false,
      srcList: [],
      multipleSelection: [],
      total: 0,
      isRestoringSelection: false
    }
  },

  watch: {
    tableData: {
      handler(nVal) {
        this.table = nVal || []
        this.restoreSelection()
      }
    },
    ids: {
      handler(nVal) {
        this.multipleSelection = Array.isArray(nVal) ? nVal : []
        this.restoreSelection()
      },
      deep: true
    }
  },
  mounted() {
    this.restoreSelection()
  },

  methods: {
    handleSelectionChange(val) {
      if (this.isRestoringSelection) return
      this.$emit('handleSelectionFn', val)
    },
    restoreSelection() {
      const selectedIds = Array.isArray(this.ids) ? this.ids : []

      this.$nextTick(() => {
        const tableRef = this.$refs.paymentTable
        if (!tableRef || this.edit !== '') return

        this.isRestoringSelection = true
        tableRef.clearSelection()

        if (selectedIds.length) {
          const selectedIdSet = new Set(selectedIds.map((id) => String(id)))
          this.table.forEach((row) => {
            if (selectedIdSet.has(String(row.id))) {
              tableRef.toggleRowSelection(row, true)
            }
          })
        }

        this.$nextTick(() => {
          this.isRestoringSelection = false
        })
      })
    },

    closeImageViewer() {
      this.isImage = false
      this.srcList = []
    },
    getStatusClass(status) {
      const statusMap = {
        '-1': 'blue',
        '0': 'gray',
        '1': 'yellow',
        '2': 'red',
        '3': 'green',
        '4': 'red',
        '5': 'gray',
        '6': 'yellow'
      }
      return statusMap[status] || ''
    },
    getStatusText(status) {
      const textMap = {
        '-1': '开票撤回',
        '0': '待开票',
        '1': '已开票',
        '2': '已拒绝',
        '3': '申请作废',
        '4': '同意作废',
        '5': '拒绝作废',
        '6': '作废撤回'
      }
      return textMap[status] || ''
    },
    handlePictureCardPreview(val) {
      this.srcList.push(val)
      this.isImage = true
    },
    deleteFn(row) {
      this.$modalSure('确认移除此付款订单吗').then(() => {
        this.table = this.table.filter((item) => {
          return item.id != row.id
        })

        this.$emit('totalFn', this.total, this.table)
      })
    }
  }
}
</script>
<style scoped lang="scss">
.img {
  width: 40px;
  height: 40px;
}
.table-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 22px;
  border-radius: 3px;
  font-size: 13px;

  &.blue {
    background: rgba(24, 144, 255, 0.05);
    color: #1890ff;
  }

  &.yellow {
    background: rgba(255, 153, 0, 0.05);
    color: #ff9900;
  }
  &.red {
    background: rgba(255, 153, 0, 0.05);
    color: #ed4014;
  }

  &.green {
    background: rgba(0, 192, 80, 0.05);
    color: #00c050;
  }

  &.gray {
    background: rgba(153, 153, 153, 0.05);
    color: #999999;
  }
}
</style>
