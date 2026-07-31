<!-- 客户-订单收支 -->
<template>
  <div class="divBox">
    <el-card :body-style="{ padding: '20px 20px 20px 20px' }" class="normal-page el-card-flex whole-card">
      <oaFromBox
        ref="oaFromBox"
        :btnIcon="false"
        :isViewSearch="false"
        :search="search"
        :sortSearch="false"
        :timeVal="timeValue"
        :title="$t('customer.orderIncomeExpenses')"
        :total="total"
        :btnText="$t('customer.export')"
        @addDataFn="getExportData"
        @confirmData="confirmData"
      ></oaFromBox>

      <div class="flex-layout-table">
        <div class="mt10 table-box">
          <div class="table-wrapper">
            <div class="table-content">
              <el-table
                v-loading="loading"
                :data="tableData"
                height="100%"
                default-expand-all
                row-key="id"
                style="width: 100%"
                @sort-change="sortChange"
              >
                <el-table-column :label="$t('customer.paymentTime')" min-width="150" prop="date" sortable />
                <el-table-column :label="$t('customer.recordType')" min-width="90" prop="bill_types">
                  <template v-slot:default="scope">
                    <el-tag :type="scope.row.types === 2 ? 'warning' : 'success'" class="bill-types-tag">
                      {{ scope.row.types === 2 ? $t('customer.expense') : $t('customer.income') }}
                    </el-tag>
                  </template>
                </el-table-column>

                <el-table-column :label="$t('customer.paymentAmountYuan')" min-width="100" prop="num" />
                <el-table-column :label="$t('customer.paymentMethod')" min-width="100" prop="pay_type">
                  <template v-slot:default="scope">
                    <span>{{ scope.row.pay_type !== '' ? scope.row.pay_type : '--' }}</span>
                  </template>
                </el-table-column>
                <el-table-column :label="$t('customer.customerName')" min-width="120" prop="client.name">
                  <template v-slot:default="scope">
                    <span class="pointer default-color" @click="clientCheck(scope.row)">
                      {{ scope.row.client ? scope.row.client.customer_name : '--' }}
                    </span>
                  </template>
                </el-table-column>
                <el-table-column :label="$t('customer.orderNumber')" min-width="120" prop="contract.contract_no">
                  <template v-slot:default="scope">
                    <span class="pointer default-color" @click="treatyCheck(scope.row)">
                      {{ scope.row.contract ? scope.row.contract.contract_no : '--' }}
                    </span>
                  </template>
                </el-table-column>

                <el-table-column :label="$t('customer.approvalStatus')" min-width="140" prop="status">
                  <template v-slot:default="scope">
                    <el-tag v-if="scope.row.status === 1" class="status-tag" type="success">{{ $t('customer.approved') }}</el-tag>
                    <el-tag v-else-if="scope.row.status === -1" class="status-tag" type="info">{{ $t('customer.revoked') }}</el-tag>
                    <el-tag v-else-if="scope.row.status === 0" class="status-tag" type="warning">{{ $t('customer.pendingApproval') }}</el-tag>
                    <el-tag v-else class="status-tag" type="danger">{{ $t('customer.rejected') }}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column :label="$t('customer.salesperson')" min-width="140">
                  <template slot-scope="scope">
                    <div class="flex items-center">
                      <img
                        :src="scope.row.card.avatar"
                        alt=""
                        style="width: 24px; height: 24px; border-radius: 50%; margin-right: 7px; vertical-align: bottom"
                      />
                      {{ scope.row.card.name }}
                    </div>
                  </template>
                </el-table-column>
                <el-table-column :label="$t('customer.applicationTime')" min-width="140" prop="created_at"> </el-table-column>

                <el-table-column :label="$t('public.operation')" fixed="right" prop="address" width="100">
                  <template v-slot:default="scope">
                    <el-button type="text" @click="handleCheck(scope.row)">{{ $t('customer.view') }}</el-button>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </div>
          <div class="page-fixed">
            <el-pagination
              :current-page="where.page"
              :page-size="where.limit"
              :page-sizes="[15, 20, 30]"
              :total="total"
              layout="total, sizes,prev, pager, next, jumper"
              @size-change="handleSizeChange"
              @current-change="pageChange"
            />
          </div>
        </div>
      </div>

      <div class="footer-expend">
        <div class="expend">
          <span class="mr14"
            >{{ $t('customer.totalReceivedAmountYuan') }}: <span class="income">{{ census.income || '0' }} </span>
          </span>

          <span class="mr14">
            {{ $t('customer.totalExpenseAmountYuan') }}: <span class="expend-color">{{ census.expend || '0' }} </span>
          </span>

          <span>
            {{ $t('customer.amountUnderReviewYuan') }}: <span class="income">{{ census.review_income || '0' }} ({{ $t('customer.income') }})</span>
            <span class="expend-color">{{ census.review_expend || '0' }} ({{ $t('customer.expense') }})</span></span
          >
        </div>
      </div>
    </el-card>
    <!-- 查看客户详情侧滑 -->
    <edit-customer ref="editCustomer" :form-data="clientFromData"></edit-customer>
    <!-- 查看订单详情侧滑 -->
    <edit-contract ref="editContract" :form-data="contractFromData"></edit-contract>
    <!-- 查看详情 -->
    <detail-examine ref="detailExamine" @getList="getTableData" />
    <!-- 导出组件 -->
    <export-excel ref="exportExcel" :export-data="exportData" :save-name="saveName" :template="false" />
  </div>
</template>
<script>
import { clientBillListApi, getbillCate } from '@/api/enterprise'

export default {
  name: 'Index',
  components: {
    detailExamine: () => import('@/views/user/examine/components/detailExamine'),
    editCustomer: () => import('@/views/customer/list/components/editCustomer'),
    editContract: () => import('@/views/customer/contract/components/editContract'),
    exportExcel: () => import('@/components/common/exportExcel'),
    applyForPayment: () => import('@/views/customer/list/components/applyForPayment'),
    expendDialog: () => import('@/views/customer/list/components/expendDialog'),
    oaFromBox: () => import('@/components/common/oaFromBox')
  },
  props: {
    activeName: {
      type: String,
      default: '1'
    },
    types: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      loading: false,
      tableData: [],
      saveName: '',
      exportData: {
        data: [],
        cols: [
          { wpx: 130 },
          { wpx: 70 },
          { wpx: 120 },
          { wpx: 120 },
          { wpx: 130 },
          { wpx: 130 },
          { wpx: 110 },
          { wpx: 110 }
        ]
      },
      census: {},
      catePath: [],
      timeValue: [
        this.$moment().startOf('month').format('YYYY/MM/DD'),
        this.$moment().endOf('month').format('YYYY/MM/DD')
      ],
      where: {
        types: '',
        page: 1,
        status: '',
        time: '',
        name: '',
        time_field: 'date',
        limit: 15,
        no_withdraw: 1,
        sort: 'created_at desc',
        scope_frame: 'all'
      },
      total: 0,
      contractFromData: {},
      clientFromData: {},
      search: [
        {
          field_name: this.$t('customer.businesstype'),
          field_name_en: 'types',
          form_value: 'select',
          data_dict: [
            { name: this.$t('toptable.all'), id: '' },
            { name: this.$t('customer.orderPayment'), id: 0 },
            { name: this.$t('customer.orderRenewal'), id: 1 },
            { name: this.$t('customer.orderExpense'), id: 2 }
          ]
        },
        {
          form_value: 'manage'
        },
        {
          field_name: this.$t('customer.approvalStatus'),
          field_name_en: 'status',
          form_value: 'select',
          data_dict: [
            {
              id: '',
              name: this.$t('toptable.all')
            },
            {
              id: 0,
              name: this.$t('customer.audit')
            },
            {
              id: 1,
              name: this.$t('customer.passed')
            },
            {
              id: 2,
              name: this.$t('customer.fail')
            }
          ]
        },
        {
          field_name: this.$t('customer.timeType'),
          field_name_en: 'time_field',
          form_value: 'select',
          data_dict: [
            {
              value: 'date',
              name: this.$t('customer.paymentDate')
            },
            {
              value: 'time',
              name: this.$t('customer.applicationDate')
            }
          ]
        },
        {
          field_name: this.$t('customer.startTime'),
          field_name_end: this.$t('customer.endTime'),
          field_name_en: 'time',
          form_value: 'date_picker',
          data_dict: [
            this.$moment().startOf('month').format('YYYY/MM/DD'),
            this.$moment().endOf('month').format('YYYY/MM/DD')
          ]
        },
        {
          field_name: this.$t('customer.customerNameOrOrderNumber'),
          field_name_en: 'name',
          form_value: 'input'
        }
      ],
      dropdownList: [
        {
          value: 1,
          label: this.$t('customer.export')
        }
      ]
    }
  },
  created() {
    this.where.time = this.timeValue[0] + '-' + this.timeValue[1]
    this.getTableData()
  },
  methods: {
    pageChange(page) {
      this.where.page = page
      this.getTableData()
    },
    // 查看客户详情
    async clientCheck(val) {
      val.eid = val.eid
      val.cid = val.cid
      if (val) {
        this.clientFromData = {
          title: this.$t('customer.editcustomer'),
          width: '1100px',
          data: val,
          types: this.types
        }

        this.$refs.editCustomer.tabIndex = '1'
        this.$refs.editCustomer.tabNumber = 1
        this.$refs.editCustomer.openBox(val.eid, this.types)
      }
    },
    // 查看订单详情
    async treatyCheck(item) {
      item.cid = item.contract.id
      item.contract_name = item.contract.contract_name
      item.eid = item.eid
      this.contractFromData = {
        title: '查看订单',
        width: '1000px',
        data: item,
        isClient: false,
        name: item ? item.contract_name : '',
        id: item ? item.id : '',
        edit: true
      }

      this.$refs.editContract.tabIndex = '1'
      this.$refs.editContract.tabNumber = 1
      this.$refs.editContract.openBox(item)
    },

    sortChange(column) {
      if (column.order === 'ascending') {
        this.where.sort = column.prop + ' asc'
      } else if (column.order === 'descending') {
        this.where.sort = column.prop + ' desc'
      } else {
        this.where.sort = ''
      }

      this.getTableData()
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.getTableData()
    },

    // 查看
    async handleCheck(item) {
      item.id = item.apply_id
      this.$refs.detailExamine.openBox(item)
    },

    // 导出
    getExportData() {
      this.$refs.oaFromBox.loading = true
      this.saveName = this.$t('customer.exportPaymentReview') + '_' + this.$moment(new Date()).format('MM_DD_HH_mm_ss') + '.xlsx'

      let where = {
        types: this.where.types,
        page: 0,
        status: this.where.status,
        time: this.where.time,
        name: this.where.name,
        time_field: this.where.time_field,
        limit: 0,
        no_withdraw: this.where.no_withdraw
      }
      clientBillListApi(where).then((res) => {
        let data = res.data.list
        let aoaData = [
          [this.$t('customer.paymentTime'), this.$t('customer.paymentAmountYuan'), this.$t('customer.paymentMethod'), this.$t('customer.remark'), this.$t('customer.businesstype'), this.$t('customer.customerName'), this.$t('customer.orderNumber'), this.$t('customer.orderNumber'), this.$t('customer.salesperson')]
        ]
        if (data.length > 0) {
          data.forEach((value) => {
            if (value.types == 0) {
              value.types = this.$t('customer.paymentEntry')
            } else if (value.types == 1) {
              if (value.renew && value.renew.title) {
                value.types = this.$t('customer.renewalEntry') + '-' + value.renew.title
              } else {
                value.types = this.$t('customer.renewalEntry')
              }
            }else {
              value.types = this.$t('customer.expenseEntry')
            }

            aoaData.push([
              value.date,
              value.num,
              value.pay_type,
              value.mark,
              value.types,
              value.client ? value.client.customer_name : '',
              value.contract ? value.contract.contract_no : '',
              value.treaty ? value.treaty.contract_no : '',
              value.card ? value.card.name : ''
            ])
          })
          this.exportData.data = aoaData
          this.$refs.exportExcel.exportExcel()
          this.$refs.oaFromBox.loading = false
        }
      })

      // this.getTableData()
    },

    async getbillCate(id) {
      getbillCate(id).then((res) => {
        this.catePath = res.data.bill_cate_path
      })
    },
    // 获取表格数据
    async getTableData() {
      this.loading = true
      const result = await clientBillListApi(this.where)
      this.tableData = result.data.list
      this.census = result.data.census
      this.total = result.data.count
      this.loading = false
    },
    confirmData(data) {
      if (data == 'reset') {
        this.where = {
          types: '',
          page: 1,
          status: '',
          time: '',
          name: '',
          time_field: 'date',
          limit: 15,
          no_withdraw: 1,
          sort: 'created_at desc',
          scope_frame: 'all'
        }

        this.search[4].data_dict = this.timeValue
        this.where.time = this.timeValue[0] + '-' + this.timeValue[1]
      } else {
        this.where = { ...this.where, ...data }
      }
      this.where.page = 1
      this.getTableData()
    },
    isOk() {
      this.getTableData()
    }
  }
}
</script>

<style lang="scss" scoped>
.expend {
  font-size: 14px;
  color: #909399;

  div {
    > span {
      padding-right: 15px;
    }

    > span:last-of-type {
      padding-right: 0;
    }
  }

  .expend-color {
    color: #ffba00;
  }

  .income {
    color: #13ce66;
  }

  .positive {
    color: #1890ff;
  }
}

.btn-type {
  padding: 4px 10px;
  font-size: 13px;
}

.invoice-info {
  color: #909399;
}

.invoice-info > div > span {
  color: #606266;
}

.line {
  width: 100%;
  height: 4px;
  border-bottom: 1px solid #f2f6fc;
  margin-bottom: 30px;
}

.img {
  width: 40px;
  height: 40px;
}

.head-box {
  display: flex;
  align-items: center;

  .input {
    width: 240px;
    margin: 0 20px 0 10px;
  }
}

.detail-box {
  padding: 20px;
  color: #333;

  .item-box {
    display: flex;
    margin-bottom: 20px;
    font-size: 14px;

    span {
      /*width: 80px;*/
    }

    div {
      margin-left: 20px;
    }
  }

  .content-box {
    span {
      font-size: 14px;
    }
  }
}

::v-deep .el-drawer__body {
  height: 100%;
  overflow-y: auto;
}

.footer {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
  margin-bottom: 20px;
}

.from {
  margin: 0 24px;
}

.dialog {
  ::v-deep .el-dialog {
    border-radius: 6px;
    height: 249px;
  }

  ::v-deep .el-dialog__body {
    padding: 0;
  }

  ::v-deep .el-textarea__inner {
    width: 400px;
    height: 90px;
    font-size: 13px;
  }
}
</style>

<style lang="scss">
.content-box {
  font-size: 13px;
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
    border: 1px solid #1890ff;
    color: #1890ff;
  }

  &.yellow {
    background: rgba(255, 153, 0, 0.05);
    border: 1px solid #ff9900;
    color: #ff9900;
  }

  &.red {
    background: rgba(255, 153, 0, 0.05);
    border: 1px solid #ed4014;
    color: #ed4014;
  }

  &.green {
    background: rgba(0, 192, 80, 0.05);
    border: 1px solid #00c050;
    color: #00c050;
  }

  &.gray {
    background: rgba(153, 153, 153, 0.05);
    border: 1px solid #999999;
    color: #999999;
  }
}

.bill-types-tag {
  border: 0;
}

.status-tag {
  background-color: transparent !important;
}

.whole-card {
  position: relative;
}

.footer-expend {
  position: absolute;
  bottom: 30px;
  left: 20px;
  display: flex;
  justify-content: flex-start;
}
</style>
