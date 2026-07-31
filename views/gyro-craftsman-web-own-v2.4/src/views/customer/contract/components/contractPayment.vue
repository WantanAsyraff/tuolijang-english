<!-- 订单-账目记录页面组件 -->
<template>
<div class="station">
  <div class="mb10 btn-box1">
    <div class="title-16">{{ $t("ui.customerContractContractPaymentAccountRecordsList") }}</div>
    <div>
      <el-button size="small" @click="handleBuild(0, buildData.contract_renew_switch, 'contract_renew_switch')">
        {{ $t("ui.customerContractContractPaymentAddRenewal") }}
      </el-button>
      <el-button size="small" @click="handleBuild(0, buildData.contract_disburse_switch, 'contract_disburse_switch')">
        {{ $t("ui.customerContractContractPaymentAddExpense") }}
      </el-button>
      <el-button
        type="primary"
        size="small"
        @click="handleBuild(0, buildData.contract_refund_switch, 'contract_refund_switch')"
      >
        {{ $t("ui.customerContractContractPaymentAddPayment") }}
      </el-button>
    </div>
  </div>
  <el-row class="flex">
    <el-col :span="20" class="amount">
      <div class="status-span">
        <span class="amount-label">{{ $t('customer.paymentstatus') }}：</span>

        <span v-if="parseFloat(formInfo.data.surplus) === 0" class="color-success">{{ $t("ui.customerContractContractPaymentSettled") }}</span>
        <span v-else class="color-danger"> {{ $t("ui.customerContractContractPaymentUnsettled") }}</span>
      </div>
      <div>
        <span class="amount-label ml36">{{ $t("ui.customerContractContractPaymentAmountPaidYuan") }}</span
        ><span class="amount-val">{{ paymentPrice.payment_price }}</span>
      </div>
      <div>
        <span class="amount-label ml36">{{ $t("ui.customerContractContractPaymentAmountDueYuan") }}</span
        ><span class="amount-val">{{ paymentPrice.unpaid_price }} </span>
      </div>
      <div>
        <span class="amount-label ml36">{{ $t("ui.customerContractContractPaymentTotalExpenseAmountYuan") }}</span
        ><span class="amount-val">{{ paymentPrice.expense_price }} </span>
      </div>
    </el-col>
  </el-row>
  <el-table :data="debtData" style="width: 100%">
    <el-table-column prop="date" :label="$t('ui.customerContractContractPaymentPaymentTime')" min-width="180"> </el-table-column>
    <el-table-column prop="bill_types" :label="$t('ui.customerContractContractPaymentRecordType')" min-width="110">
      <template slot-scope="scope">
        <el-tag :type="scope.row.types == 2 ? 'warning' : 'success'">{{
          scope.row.types == 2 ? $t('ui.customerContractContractPaymentExpense') : $t('ui.customerContractContractPaymentIncome')
        }}</el-tag>
      </template>
    </el-table-column>
    <el-table-column prop="num" :label="$t('ui.invoiceInvoiceDetailsPaymentAmountYuan')" min-width="130"> </el-table-column>
    <el-table-column prop="pay_type" :label="$t('ui.customerContractContractPaymentPaymentMethod')" min-width="120">
      <template slot-scope="scope">
        <span>{{ scope.row.pay_type !== '' ? scope.row.pay_type : '--' }}</span>
      </template>
    </el-table-column>
    <el-table-column prop="bill_no" :label="$t('ui.invoiceInvoiceDetailsPaymentBillNo')" min-width="120"> </el-table-column>
    <el-table-column prop="status" :label="$t('ui.customerContractContractPaymentPaymentReviewStatus')" min-width="100">
      <template slot-scope="scope">
        <el-tag v-if="scope.row.status === 0" type="warning" size="mini"> {{ $t("ui.customerListApplyForPaymentPendingReview") }}</el-tag>
        <el-tag v-if="scope.row.status === 1 && !scope.row.recall" type="info" size="mini"> {{ $t("ui.customerListApplyForPaymentApproved") }}</el-tag>
        <el-tag v-if="scope.row.status === 1 && scope.row.recall" type="info" size="mini"> {{ $t("ui.userExamineExamineWithdrawing") }}</el-tag>
        <el-tag v-if="scope.row.status === -1" type="info" size="mini"> {{ $t("ui.customerListApplyForPaymentRevoked") }}</el-tag>
        <el-popover v-if="scope.row.status === 2" trigger="hover" placement="top">
          <p>{{ $t('customer.reason') }}:</p>
          <p>{{ scope.row.fail_msg }}</p>
          <div slot="reference">
            <el-tag type="danger" size="mini"> {{ $t('customer.fail') }}</el-tag>
          </div>
        </el-popover>
      </template>
    </el-table-column>

    <el-table-column prop="address" fixed="right" min-width="180" :label="$t('public.operation')">
      <template slot-scope="scope">
        <el-button type="text" @click="handleCheck(scope.row)">{{ $t("ui.layoutNoticeNoticeListView") }}</el-button>

        <template v-if="userId == scope.row.card.id">
          <el-button
            type="text"
            v-if="
              (scope.row.status == 1 &&
                scope.row.approve_rule &&
                scope.row.approve_rule.recall == 1 &&
                !scope.row.recall) ||
              scope.row.status === 0 ||
              (!scope.row.approve_rule && scope.row.status != -1)
            "
            @click="withdraw(scope.row)"
            >{{ $t("ui.formDesignerToolbarPanelIndexRevoke") }}</el-button
          >
          <el-button @click="handleDelete(scope.row)" v-if="scope.row.status === -1" type="text">{{ $t("ui.chatIndexDelete") }} </el-button>
        </template>
      </template>
    </el-table-column>
  </el-table>
  <div class="block mt10 text-right">
    <el-pagination
      :page-size="where.limit"
      :current-page="where.page"
      layout="total, prev, pager, next, jumper"
      :total="debtTotal"
      @current-change="debtChange"
    />
  </div>
  <applyForPayment ref="applyForPayment" :form-data="fromData"></applyForPayment>
  <edit-examine
    ref="editExamine"
    :parameterData="parameterData"
    :ids="formInfo.data.id"
    @isOk="isOk()"
  ></edit-examine>
  <detail-examine ref="detailExamine" @getList="getTableData" />
  <!-- 撤销 -->
  <oa-dialog
    ref="oaDialog"
    :fromData="oaFromData"
    :formConfig="formConfig"
    :formRules="formRules"
    :formDataInit="formDataInit"
    @submit="getApplyRevoke"
  ></oa-dialog>
</div>
</template>
<script>
import { mapGetters } from 'vuex'
import { clientBillDeleteApi, clientBillListApi, getContractStatisticsApi } from '@/api/enterprise'
import { approveApplyRevokeApi } from '@/api/business'
import { configRuleApproveApi } from '@/api/config'

export default {
  name: 'ContractRecord',
  props: {
    formInfo: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },
  components: {
    oaDialog: () => import('@/components/form-common/dialog-form'),
    applyForPayment: () => import('@/views/customer/list/components/applyForPayment'),
    editExamine: () => import('@/views/user/examine/components/editExamine'),
    detailExamine: () => import('@/views/user/examine/components/detailExamine')
  },
  computed: {
    ...mapGetters(['userInfo'])
  },
  data() {
    return {
      fromData: {},
      paymentPrice: {},
      userId: this.$store.state.user.userInfo.id,
      debtData: [],
      debtTotal: 0,
      where: {
        page: 1,
        limit: 15,
        types: ''
      },
      formDataInit: {
        info: ''
      },
      formConfig: [
        {
          type: 'textarea',
          label: '撤销理由：',
          placeholder: '请输入撤销理由',
          key: 'info'
        }
      ],
      formRules: {
        // info: [{ required: true, message: '请输入撤销理由', trigger: 'blur' }]
      },
      oaFromData: {
        width: '600px',
        title: '撤销',
        btnText: '确定',
        labelWidth: 'auto',
        type: ''
      },
      renewCensusData: [],
      rowData: {},
      buildData: [],
      parameterData: {
        contract_id: '',
        customer_id: '',
        invoice_id: '',
        bill_id: ''
      }
    }
  },
  watch: {
    formInfo: {
      handler(nVal) {
        this.parameterData.contract_id = nVal.data.cid
        nVal.isClient === false ? (this.where.cid = nVal.data.cid) : (this.where.eid = nVal.data.eid)
      },
      immediate: true,
      deep: true
    }
  },
  methods: {
    handleBuild(command, val, type) {
      this.parameterData.contract_id = this.formInfo.data.cid ? this.formInfo.data.cid : this.where.cid
      this.$refs.editExamine.openBox(val, this.parameterData.contract_id, type)
    },
    // 撤回
    withdraw(row) {
      this.rowData = row
      if (row.status === 0) {
        this.$modalSure(this.$ts('你确定要撤销申请吗')).then(() => {
          this.getApplyRevoke()
        })
      } else {
        this.$refs.oaDialog.openBox()
      }
    },
    async getApplyRevoke(data) {
      await approveApplyRevokeApi(this.rowData.apply_id, data)
      if (data) {
        this.$refs.oaDialog.handleClose()
      }
      this.getTableData()
      this.$emit('refresh-detail')
    },

    async getConfigApprove() {
      const result = await configRuleApproveApi(0)
      this.buildData = result.data
    },

    getTableData(cid) {
      this.getStatistics()
      this.where.cid = cid ? cid : this.parameterData.contract_id
      clientBillListApi(this.where).then((res) => {
        this.debtData = res.data.list
        this.debtTotal = res.data.count
        this.renewCensusData = res.data.renew_census
      })
    },

    getStatistics() {
      getContractStatisticsApi(this.formInfo.data.id).then((res) => {
        this.paymentPrice = res.data
      })
    },

    // 查看
    async handleCheck(item) {
      this.fromData = {
        title: this.$t('customer.viewcustomer'),
        width: '500px',
        data: item,
        isClient: false,
        name: this.formInfo.data.name,
        id: item.eid,
        edit: true,
        type: 1
      }
      if (item.apply_id) {
        item.id = item.apply_id
        this.$refs.detailExamine.openBox(item)
      } else {
        this.$refs.applyForPayment.openBox()
      }
    },
    debtChange(val) {
      this.where.page = val
      this.getTableData()
    },

    isOk() {
      this.where.types = ''
      setTimeout(() => {
        this.getTableData()
        this.$emit('refresh-detail')
      }, 300)
    },

    // 删除
    handleDelete(item) {
      this.$modalSure('您确定要删除这条付款记录吗').then(() => {
        clientBillDeleteApi(item.id).then((res) => {
          if (this.where.page > 1 && this.debtData.length <= 1) {
            this.where.page--
          }

          this.getTableData()
          this.$emit('refresh-detail')
        })
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.station {
  .status-span {
    font-size: 13px;
  }
}
.el-button {
  font-size: 13px;
}
.btn-box1 {
  height: 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.ml36 {
  margin-left: 36px;
}

.invoice-info {
  color: #909399;
}
.invoice-info > div > span {
  color: #606266;
}
.img {
  width: 40px;
  height: 40px;
}
.flex {
  display: flex;
  align-items: center;
}
.amount {
  display: flex;
  margin-bottom: 10px;
  font-size: 13px;
  .amount-label {
    color: #909399;
  }
  .amount-val {
    color: #303133;
  }
}

::v-deep .el-input__inner {
  text-align: left;
}

.renewal-content {
  width: 100%;
  margin-bottom: 10px;

  p {
    margin: 12px 20px 0 0;
    padding: 0;
    font-size: 13px;
    display: inline-block;

    &:last-of-type {
      margin-right: 0;
    }
  }
}
.el-tabs--border-card > .el-tabs__content {
  padding: 0;
}

.from-item-title {
  margin-top: 8px;
  border-left: 5px solid #1890ff;

  span {
    padding-left: 10px;
    font-weight: bold;
    font-size: 14px;
  }
}
.table-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 22px;
  border-radius: 3px;
  font-size: 13px;
  cursor: pointer;

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
