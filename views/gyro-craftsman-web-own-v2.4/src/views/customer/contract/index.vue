import { $ } from '@/lang'
<template>
<div class="divBox">
  <el-card :body-style="{ padding: '20px 20px 20px 20px' }" class="normal-page el-card-flex">
    <oaFromBox
      :title="$route.meta.title"
      :btnText="$('ui.customerListContractAddOrder')"
      :treeData="treeDataGroup"
      :search="search"
      :viewSearch="viewSearch"
      :dropdownList="dropdownList"
      :total="total"
      :category="keyword"
      :timeSearchObj="timeSearchObj"
      class="from-box"
      ref="fromBox"
      :whereData="where"
      @treeChange="handleNodeClick"
      @addDataFn="addContract"
      @dropdownFn="dropdownFn"
      @confirmData="confirmData"
    ></oaFromBox>

    <customizeTable
      flexLayout
      ref="tableData"
      :keyword="keyword"
      :tableData="tableData"
      :where="where"
      :total="total"
      :loading="loading"
      @handleCheck="handleCheck"
      @handleCustomerCheck="handleClient"
      @getSearch="getSearch"
      @handleSelectionChange="handleSelectionChange"
      @getTableData="getTableData"
    >
      <template #options="{ data }">
        <el-button type="text" @click="handleCheck(data)">
          {{ $('public.check') }}
        </el-button>
        <el-button type="text" @click="handleBuild(data, buildData.contract_refund_switch, 'contract_refund_switch')">
          {{ $("ui.customerContractContractPaymentAddPayment") }}
        </el-button>
        <el-dropdown>
          <span class="el-dropdown-link el-button--text el-button more">
            {{ $("ui.layoutNavbarMore") }}
            <i class="el-icon-arrow-down" />
          </span>
          <el-dropdown-menu class="dropdown-menu-left" placement="top-start">
            <el-dropdown-item
              @click.native="handleBuild(data, buildData.contract_renew_switch, 'contract_renew_switch')"
            >
              {{ $("ui.customerContractContractPaymentAddRenewal") }}
            </el-dropdown-item>
            <el-dropdown-item
              @click.native="handleBuild(data, buildData.contract_disburse_switch, 'contract_disburse_switch')"
            >
              {{ $("ui.customerContractContractPaymentAddExpense") }}
            </el-dropdown-item>
            <el-dropdown-item
              v-customer-module="CUSTOMER_MODULE_KEYS.INVOICE"
              style="border-bottom: 1px solid #f5f5f5"
              @click.native="handleBuild(data, buildData.invoicing_switch, 'invoicing_switch')"
            >
              {{ $("ui.customerContractIndexApplyForInvoice") }}
            </el-dropdown-item>
            <el-dropdown-item @click.native="handleTransfer(2, data)"> {{ $("ui.customerClueIndexTransferToColleague") }} </el-dropdown-item>
            <el-dropdown-item @click.native="markedAbnormal(data)">
              {{ data.contract_status.value == 3 ? $('ui.customerContractIndexMarkAsNormalOrder') : $('ui.customerContractIndexMarkAsAbnormalOrder') }}
            </el-dropdown-item>
            <el-dropdown-item @click.native="handleDelete(data)"> {{ $("ui.chatIndexDelete") }} </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </template>
    </customizeTable>
  </el-card>
  <add-contract ref="addContract" :form-data="contractFromData" @getTableData="getTableData()"></add-contract>
  <edit-contract ref="editContract" :form-data="fromData" @isOk="getTableData(true)"></edit-contract>
  <edit-customer ref="editCustomer" :form-data="fromData" @isOkEdit="getTableData(true)"></edit-customer>
  <transfer-dialog ref="transferDialog" :from-data="transferData" @handleTransfer="getTableData"></transfer-dialog>

  <edit-examine
    ref="editExamine"
    :ids="formInfo.id"
    :parameterData="parameterData"
    @isOk="getTableData()"
  ></edit-examine>

  <!-- 导入合同 -->
  <dragUpload ref="dragUpload" @getTableData="getTableData()"></dragUpload>
  <!-- 导入/导出记录 -->
  <importRecords ref="importRecords"></importRecords>
</div>
</template>
<script>
import { clientContractDeleteApi, contractViewApi, contractAbnormalApi, contractImport } from '@/api/enterprise'
import { clientExportApi } from '@/api/client'
import { configRuleApproveApi } from '@/api/config'
import { DRAWER_SIZE } from '@/constants/popupSize'
import { roterPre } from '@/settings'
import { CUSTOMER_MODULE_KEYS } from '@/constants/customerModules'

export default {
  name: 'ContractList',
  components: {
    oaFromBox: () => import('@/components/common/oaFromBox'),
    editContract: () => import('./components/editContract'),
    addContract: () => import('./components/addContract'),
    customizeTable: () => import('../components/customizeTable'),
    dragUpload: () => import('../components/dragUpload'),
    importRecords: () => import('@/views/customer/list/components/importRecords'),
    transferDialog: () => import('@/views/customer/list/components/transferDialog'),
    editCustomer: () => import('@/views/customer/list/components/editCustomer'),
    editExamine: () => import('@/views/user/examine/components/editExamine')
  },
  props: {
    types: {
      type: Number,
      default: 1
    }
  },
  data() {
    return {
      CUSTOMER_MODULE_KEYS,
      transferDataList: [],

      tableData: [], // 表格的数据
      total_price: 0,

      fromData: {},
      labelText: '',
      contractFromData: {},
      where: {
        page: 1,
        limit: 15,
        view_search: 1,
        types: 'contract'
      },
      formInfo: {
        id: ''
      },
      parameterData: {
        contract_id: '',
        customer_id: '',
        invoice_id: '',
        bill_id: ''
      },
      total: 0,
      transferData: {},
      ids: [],
      loading: false,

      searchForm: {},

      keyword: 'contract',
      buildData: [],

      search: [],
      timeSearchObj: {},
      viewSearch: [],
      dropdownList: [
        { label: $('ui.customerClueIndexTransferToColleague'), value: 1 },
        { label: $('ui.developModuleTableStyleFilterSettings'), value: 4 },
        { label: $('ui.developModuleTableStyleColumnDisplaySettings'), value: 5 },
        { label: $('customer.export'), value: 2 },
        { label: $('finance.batchupload'), value: 3 },
        { label: $('legacyScript.importExportRecords'), value: 6 },
        { label: $('legacyScript.fieldOptionSettings'), value: 7 }
      ],
      treeDataGroup: [
        {
          id: 1,
          label: $('legacyScript.ownedByMe')
        },
        {
          id: 2,
          label: $('legacyScript.ownedBySubordinates')
        },
        {
          id: 3,
          label: $('legacyScript.followedByMe')
        },
        {
          id: 4,
          label: $('ui.customerSigningInfoItemSigned')
        },
        {
          id: 5,
          label: $('legacyScript.notSigned')
        },
        {
          id: 6,
          label: $('legacyScript.contractVoided')
        },
        {
          id: 7,
          label: $('legacyScript.expiredOrders')
        },
        {
          id: 8,
          label: $('customer.urgentrenewal')
        },
        {
          id: 9,
          label: $('legacyScript.feeExpired'),
          line: true
        }
      ]
    }
  },

  created() {
    this.getConfigApprove()
  },

  methods: {
    getSearch(val) {
      this.search = val.search
      this.viewSearch = val.viewSearch
      this.timeSearchObj = val.timeSearchObj
      this.transferDataList = val.tableHeaders
    },
    // 导出列表数据
    async exportList() {
      await clientExportApi(this.keyword, { ...this.where, page: 0, limit: 0, types: this.keyword })
    },

    // 标为异常
    markedAbnormal(row) {
      const isNormal = Number(row.contract_status.value) === 3
      const status = isNormal ? 0 : 1
      const statusText = this.$(isNormal ? 'customer.normal' : 'customer.abnormal')
      this.$modalSure(this.$('customer.abnormalMessage', { status: statusText })).then(() => {
        contractAbnormalApi(row.id, status)
        this.getTableData()
      })
    },
    handleBuild(item, val, type) {
      this.parameterData.customer_id = item.eid
      this.parameterData.contract_id = item.id
      this.$refs.editExamine.openBox(val, item.id, type)
    },

    isOk() {
      this.getTableData()
    },
    async getConfigApprove() {
      const result = await configRuleApproveApi(0)
      this.buildData = result.data
    },

    // 添加订单
    addContract(row) {
      this.contractFromData = {
        title: row ? '编辑订单' : '添加订单',
        edit: false,
        eid: row ? row.eid : '',
        width: '1129px'
      }
      this.$refs.addContract.openBox(row)
    },

    handleNodeClick(data) {
      this.where.page = 1
      this.where.statistics_type = data.value
      this.getTableData()
    },

    getTableData() {
      if (this.loading) return
      this.loading = true
      contractViewApi(this.where)
        .then((res) => {
          this.tableData = res.data.list
          this.total = res.data.count
          this.total_price = res.data.total_price || 0
          this.loading = false
          // setTimeout(() => {
          //   this.loading = false
          //   this.$refs.multipleTable?.doLayout()
          // }, 300)
        })
        .catch((error) => {
          this.loading = false
        })
    },

    // 转移
    handleTransfer(type, row = []) {
      if (this.ids.length <= 0 && type === 1) {
        this.$message.error(this.$('customer.placeholder22'))
      } else {
        let ids = []
        if (type === 1) {
          // 批量
          this.ids.map((value) => {
            ids.push(value.id)
          })
        } else {
          if (this.ids.length > 0) {
            this.$nextTick(() => {
              for (let i = 0; i < this.$refs.multipleTable.length; i++) {
                this.$refs.multipleTable[i].clearSelection()
              }
            })
          }
          ids.push(row.id)
        }
        this.transferData = {
          title: type === 1 ? '移交其他同事' : this.$('customer.transfersettings'),
          width: '520px',
          type: 2,
          ids
        }
        this.$refs.transferDialog.handleOpen(this.keyword)
      }
    },

    // 删除
    handleDelete(item) {
      this.$modalSure(this.$('customer.placeholder27')).then(() => {
        clientContractDeleteApi(item.id).then((res) => {
          if (this.where.page > 1 && this.tableData.length <= 1) {
            this.where.page--
          }
          this.getTableData()
        })
      })
    },

    // 打开客户
    handleClient(item) {
      item.eid = item.eid
      item.cid = item.id
      this.fromData = {
        title: this.$('customer.editcustomer'),
        width: '1100px',
        link_type: 'customer',
        data: item
      }
      this.$refs.editCustomer.tabIndex = '1'
      this.$refs.editCustomer.tabNumber = 1
      this.$refs.editCustomer.openBox(item.eid, 'customer')
    },

    // 查看
    async handleCheck(item) {
      item.cid = item.id
      this.fromData = {
        title: $('legacyScript.viewOrder'),
        width: DRAWER_SIZE.LG,
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

    confirmData(data) {
      if (data == 'reset') {
        this.where = {
          page: 1,
          limit: 15,
          view_search: this.where.view_search
        }
        this.getTableData()
      } else {
        this.where = {
          page: 1,
          limit: 15,
          view_search: this.where.view_search
        }

        for (let key in data) {
          this.where[key] = data[key] || ''
        }

        setTimeout(() => {
          this.getTableData()
        }, 100)
      }
    },

    handleSelectionChange(val) {
      this.ids = val
    },
    dropdownFn(item) {
      switch (item.value) {
        case 1:
          this.handleTransfer(1)
          break
        case 2:
          this.exportList(item)
          break
        case 3:
          this.$refs.dragUpload.openBox(this.keyword)
          break
        case 4:
          // 筛选条件设置
          this.$refs.tableData.customSearchEvt(1)
          break
        case 5:
          // 表头显示设置
          this.$refs.tableData.customSearchEvt(2)
          break
        case 6:
          // 导入导出记录
          this.$refs.importRecords.openBox(this.keyword)
          break
        case 7:
          // 字典选项设置
          this.$router.push({ path: `${roterPre}/customer/contract/dictSetting` })
          break
      }
    },
    importExcelData(arrRes) {
      // 提取表头
      const [thead, ...rows] = arrRes
      let data = []

      // 过滤掉全为空字符串的行，并转换为对象格式
      rows.forEach((row) => {
        const isAllEmpty = row.every((cell) => cell.trim() === '')
        if (!isAllEmpty) {
          const rowData = {}
          row.forEach((cell, index) => {
            this.transferDataList.map((item) => {
              if (item.name == thead[index]) {
                rowData[item.field] = cell
              }
            })
          })
          data.push(rowData)
        }
      })
      contractImport('', data).then((res) => {
        this.getTableData(this.labelText, this.searchForm)
      })
    }
  }
}
</script>

<style>
.el-tooltip__popper {
  max-width: 300px;
}
</style>
<style lang="scss" scoped>
.p14 {
  padding: 0 14px;
}

::v-deep .el-card__body {
  padding: 0;
}

.point {
  cursor: pointer;
  color: #1890ff;
}

.dropdown-menu-left {
  position: relative;
}
.dropdown-menu-right {
  width: 100px;
  position: absolute;
  top: -50px;
  right: 0;
}
.more {
  margin-left: 10px;
}
</style>
