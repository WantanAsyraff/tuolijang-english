<!-- 订单发票-关联付款单表格组件 -->
<template>
<div class="paymentTable">
  <div class="mb10" v-if="!type">
    <span class="select-label">{{ $t("ui.customerDetailsCustomerName") }}</span>
    <el-select v-model="eid" :placeholder="$t('ui.customerSigningPaymentTableSelectCustomer')" filterable size="small" style="width: 250px" @change="changeFn">
      <el-option v-for="item in optionsList" :key="item.id" :label="item.customer_name" :value="item.id" />
    </el-select>
    <span class="select-tips">{{ $t("ui.customerSigningPaymentTableYouCanSkipTheOrderAndSignTheContract") }}</span>
  </div>
  <el-table :data="table" ref="multipleTable" @selection-change="handleSelectionChange" style="width: 100%">
    <el-table-column v-if="selectionIsShow" type="selection" width="55"> </el-table-column>
    <!-- <el-table-column label="付款单号" min-width="100">
      <template slot-scope="scope">
        <template v-if="scope.row.bill_no && scope.row.bill_no.length > 0">
          <span v-for="(item, index) in scope.row.bill_no" :key="index">
            {{ item.bill_no }}<template v-if="index !== scope.row.bill_no.length - 1"> /</template>
          </span>
        </template>
        <span v-else>--</span>
      </template>
    </el-table-column> -->
    <el-table-column prop="contract_name" :label="$t('ui.invoiceInvoiceDetailsOrderName')" min-width="100"> </el-table-column>
    <el-table-column prop="contract_price" :label="$t('ui.customerSigningPaymentTableOrderAmountYuan')" min-width="100"> </el-table-column>
    <el-table-column prop="status" :label="$t('ui.customerListContractPaymentStatus')" min-width="100">
      <template slot-scope="scope">
        <span :class="{ success: scope.row.payment_status === 1, waiting: scope.row.payment_status === 0 }">{{
          scope.row.payment_status === 1 ? $t('ui.customerContractContractPaymentSettled') : $t('ui.customerContractContractPaymentUnsettled')
        }}</span>
      </template>
    </el-table-column>
    <el-table-column prop="contract_status.name" :label="$t('ui.customerListContractOrderStatus')" min-width="100">
      <template slot-scope="scope">
        <div
          class="dictionaries-tag over-text"
          :style="{
            color: scope.row.contract_status.color || '#1890ff',
            background: scope.row.contract_status.color
              ? getColorFn(scope.row.contract_status.color, '0.1')
              : getColorFn('#1890ff', '0.1')
          }"
        >
          {{ scope.row.contract_status.name }}
        </div>
      </template>
    </el-table-column>
    <el-table-column prop="salesman.name" :label="$t('ui.developModuleTreeOwner')" min-width="90">
      <template slot-scope="scope">
        {{ scope.row.salesman ? scope.row.salesman.name : '--' }}
      </template>
    </el-table-column>
    <el-table-column prop="created_at" :label="$t('ui.invoiceInvoiceDetailsCreatedTime')" min-width="150">
      <template slot-scope="scope">
        {{ scope.row.created_at || '--' }}
      </template>
    </el-table-column>

    <el-table-column fixed="right" :label="$t('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" width="150" v-if="!selectionIsShow">
      <template slot-scope="scope">
        <el-button @click="checkFn(scope.row)" type="text" size="small">{{ $t("ui.layoutNoticeNoticeListView") }}</el-button>
        <el-button @click="deleteFn(scope.row, scope.$index)" type="text" size="small">{{ $t("ui.chatIndexDelete") }}</el-button>
      </template>
    </el-table-column>
  </el-table>

  <edit-contract ref="editContract" :form-data="fromData"></edit-contract>
</div>
</template>
<script>
import i18n from '@/lang'
import { getColor } from '@/utils/format'
import { customerViewApi, clientContractListApi } from '@/api/enterprise'
export default {
  name: '',
  components: {
    editContract: () => import('@/views/customer/contract/components/editContract.vue')
  },
  props: {
    type: {
      //  type check 表示查看模式不展示下拉选择
      type: String,
      default: ''
    },
    selectionIsShow: {
      type: Boolean,
      default: true
    },
    cid: {
      type: Array,
      default: () => []
    },

    list: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      table: [],
      isImage: false,
      srcList: [],
      fromData: {},
      optionsList: [],
      multipleSelection: [],
      total: 0,
      eid: '',
      customerInfo: {}
    }
  },

  watch: {
    list: {
      handler(newVal, oldVal) {
        this.table = newVal
      },
      deep: true
    }
  },
  created() {
    if (!this.type) {
      this.getList()
    }
  },

  methods: {
    getList() {
      let obj = {
        limit: 0,
        page: 0,
        view_search: 1,
        is_select: 1,
        types: 'customer'
      }
      this.loadCustomerTask = customerViewApi(obj).then((res) => {
        this.optionsList = res.data.list
      })
    },
    getColorFn(color, opacity) {
      return getColor(color, opacity)
    },

    checkFn(item) {
      item.cid = item.id
      this.fromData = {
        title: i18n.t('legacyScript.viewOrder'),
        width: '1000px',
        data: item,
        isClient: false,
        name: item.client ? item.client.name : '',
        id: item.client ? item.client.id : '',
        edit: true,
        link_type: 'contract'
      }

      this.$refs.editContract.tabIndex = '1'
      this.$refs.editContract.tabNumber = 1
      this.$refs.editContract.openBox(item)
    },

    changeFn(e, cid) {
      const obj = {
        eid: e,
        page: 0,
        limit: 0,
        types: 'contract'
      }
      this.loadCustomerTask &&
        this.loadCustomerTask.then(() => {
          this.customerInfo = this.optionsList.find((item) => item.id == e)
          this.$emit('handleSelectionFn', this.multipleSelection, this.customerInfo)
        })
      clientContractListApi(obj).then((res) => {
        res.data.list = res.data.list.filter((item) => !item.is_sign)
        this.$set(this, 'table', res.data.list)
        this.total = res.data.count

        if (!cid && this.table.length > 0) {
          this.$nextTick(() => {
            this.table.forEach((item) => {
              this.$refs.multipleTable.toggleRowSelection(item, true)
            })
          })
          this.$emit('handleSelectionFn', this.table, this.customerInfo)
        }

        // 默认勾选 id 相同的行
        if (cid && cid.length > 0) {
          cid = cid.map(Number)
          this.$nextTick(() => {
            this.table.forEach((item) => {
              if (cid.includes(item.id) && this.$refs.multipleTable && this.$refs.multipleTable.toggleRowSelection) {
                this.$refs.multipleTable.toggleRowSelection(item, true)
              }
            })
          })
        }
      })
    },
    handleSelectionChange(val) {
      this.multipleSelection = val
      this.$emit('handleSelectionFn', val, this.customerInfo)
    },

    // 删除
    deleteFn(row, index) {
      this.$modalSure('确认移除此关联订单吗').then(() => {
        let ids = []
        this.table.splice(index, 1)

        this.table.map((item) => {
          ids.push(item.id)
        })
        this.$emit('deleteFn', ids)
      })
    }
  }
}
</script>
<style scoped lang="scss">
.dictionaries-tag {
  max-width: 100px;
  display: inline-block;
  margin: 0;
  box-sizing: border-box;
  height: 24px;
  padding: 0 8px;
  text-align: center;
  font-size: 12px;
  margin-top: 8px;
  border-radius: 3px;
}
.success {
  color: #19be6b;
}

.waiting {
  color: #ff9d00;
}

.select-label {
  font-size: 13px;
  color: #303133;
}
.select-tips {
  font-size: 12px;
  color: #909399;
  margin-left: 10px;
}
</style>
