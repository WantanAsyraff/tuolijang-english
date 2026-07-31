<!-- 订单发票-关联付款单表格组件 -->
<template>
<div class="paymentTable">
  <el-table :data="table" @selection-change="handleSelectionChange" :key="edit" style="width: 100%">
    <el-table-column type="selection" width="55" v-if="edit === ''"> </el-table-column>
    <el-table-column prop="bill_no" :label="$t('ui.invoiceInvoiceDetailsPaymentBillNo')" min-width="100"> </el-table-column>
    <el-table-column prop="treaty.contract_name" :label="$t('ui.invoiceInvoiceDetailsOrderName')" min-width="100"> </el-table-column>

    <el-table-column prop="types" :label="$t('ui.invoiceInvoiceDetailsBusinessType')" min-width="100">
      <template slot-scope="scope">
        <span v-if="scope.row.types === 0">{{ $t("ui.invoiceInvoiceDetailsPaymentRecord") }}</span>
        <span v-if="scope.row.types === 1">{{ $t("ui.invoiceInvoiceDetailsRenewalRecord") }} {{ scope.row.renew.title }}</span>
      </template>
    </el-table-column>
    <el-table-column prop="num" :label="$t('ui.invoiceInvoiceDetailsPaymentAmountYuan')" min-width="100"> </el-table-column>
    <el-table-column prop="status" :label="$t('ui.invoicePaymentTableInvoiceStatus')" min-width="100">
      <template slot-scope="scope">
        <div v-if="scope.row.status">
          <span v-if="scope.row.status === -1" class="table-btn blue"> {{ $t("ui.customerContractPaymentTableWithdrawInvoice") }} </span>
          <span v-if="scope.row.status === 0" class="table-btn gray"> {{ $t("ui.customerInvoiceIndexPendingInvoicing") }} </span>
          <span v-if="scope.row.status === 1" class="table-btn yellow"> {{ $t("ui.customerInvoiceIndexInvoiced") }} </span>
          <span v-if="scope.row.status === 2" class="table-btn red"> {{ $t("ui.userExamineExamineRejected") }} </span>
          <span v-if="scope.row.status === 3" class="table-btn green"> {{ $t("ui.customerInvoiceIndexApplyToVoid") }} </span>
          <span v-if="scope.row.status === 4" class="table-btn red"> {{ $t("ui.customerContractPaymentTableApproveInvalidation") }} </span>
          <span v-if="scope.row.status === 5" class="table-btn gray"> {{ $t("ui.customerContractPaymentTableRejectInvalidation") }} </span>
          <span v-if="scope.row.status === 6" class="table-btn yellow"> {{ $t("ui.customerInvoiceIndexWithdrawVoidRequest") }} </span>
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
      table: [],
      isImage: false,
      srcList: [],
      multipleSelection: [],
      total: 0
    }
  },

  watch: {
    tableData: {
      handler(nVal) {
        this.table = nVal
      }
    },
    ids: {
      handler(nVal) {
        this.multipleSelection = nVal
      }
    }
  },

  methods: {
    handleSelectionChange(val) {
      this.$emit('handleSelectionFn', val)
    },

    closeImageViewer() {
      this.isImage = false
      this.srcList = []
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
    // border: 1px solid #1890ff;
    color: #1890ff;
  }

  &.yellow {
    background: rgba(255, 153, 0, 0.05);
    // border: 1px solid #ff9900;
    color: #ff9900;
  }
  &.red {
    background: rgba(255, 153, 0, 0.05);
    // border: 1px solid #ed4014;
    color: #ed4014;
  }

  &.green {
    background: rgba(0, 192, 80, 0.05);
    // border: 1px solid #00c050;
    color: #00c050;
  }

  &.gray {
    background: rgba(153, 153, 153, 0.05);
    // border: 1px solid #999999;
    color: #999999;
  }
}
</style>
