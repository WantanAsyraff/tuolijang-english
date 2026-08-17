<template>
<div class="divBox">
  <el-card :body-style="{ padding: '20px 20px 20px 20px' }" class="mb14 normal-page">
    <oaFromBox
      :isViewSearch="false"
      :search="search"
      :title="$('ui.developEventLogTriggerLogs')"
      :total="total"
      :isAddBtn="false"
      @confirmData="confirmData"
    >
    </oaFromBox>
    <el-table
      :data="tableData"
      v-loading="loading"
      :height="tableHeight"
      row-key="id"
      class="mt10"
      style="width: 100%"
    >
      <el-table-column :label="$('ui.developCrudEventSettingTriggerName')" prop="name" />
      <el-table-column :min-width="100" :label="$('ui.developCrudEventSettingTriggerType')" prop="field_name">
        <template slot-scope="scope">
          {{ getEvent(scope.row.event) }}
        </template>
      </el-table-column>

      <el-table-column :label="$('ui.developCrudEventTriggerAction')" prop="name">
        <template slot-scope="scope">
          <span v-if="scope.row.action && scope.row.action.length > 0">
            {{ getAction(scope.row.action) }}
          </span>
          <span v-else class="color-file">{{ $("ui.developCrudEventSettingNoTriggerAction") }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.developApproveIndexLinkedEntity')" prop="name">
        <template slot-scope="scope">
          {{ scope.row.crud.table_name || '--' }}
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.developEventLogExecutionResult')" prop="log" min-width="100">
        <template slot-scope="scope">
          <el-tag size="small" :type="scope.row.result === 'success' ? 'success' : 'danger'" effect="plain">
            {{ scope.row.result === 'success' ? $('ui.developEventLogSuccess') : $('ui.developEventLogFail') }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.invoiceInvoiceDetailsCreatedTime')" prop="name">
        <template slot-scope="scope">
          <span> {{ scope.row.created_at }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" fixed="right" width="100">
        <template slot-scope="scope">
          <el-button type="text" @click="editFn(scope.row)">{{ $("ui.layoutNoticeNoticeListView") }}</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div class="page-fixed">
      <el-pagination
        :current-page="where.page"
        :page-size="where.limit"
        :page-sizes="[15, 20, 30]"
        :total="total"
        layout="total,sizes, prev, pager, next, jumper"
        @size-change="handleSizeChange"
        @current-change="pageChange"
      />
    </div>
    <!-- 查看 -->
    <oaDialog :fromData="fromData" ref="oaDialog">
      <div>
        <json-viewer style="height: 300px; width: 100%" :value="rowData.log" :expand-depth="8" copyable></json-viewer>
      </div>
    </oaDialog>
  </el-card>
</div>
</template>
<script>
import { $ } from '@/lang'
import { dataEventLogApi, dataEventTypeApi, dataEventActionApi, getDatabaseApi } from '@/api/develop'
import JsonViewer from 'vue-json-viewer'
import oaDialog from '@/components/form-common/drawer-form'
import oaFromBox from '@/components/common/oaFromBox'
export default {
  name: 'FinanceList',
  components: {
    oaFromBox,
    oaDialog,
    JsonViewer
  },
  data() {
    return {
      typesList: [],
      actionList: [],
      loading: false,
      search: [
        {
          field_name: '触发器名称',
          field_name_en: 'name',
          form_value: 'input'
        },
        {
          field_name: '关联实体',
          field_name_en: 'crud_id',
          form_value: 'cascaderSelect',
          data_type: 1,
          data_dict: [] // 关联实体
        }
      ],
      tableData: [],
      fromData: {
        title: $('customer.view'),
        type: 'slot',
        width: '40%'
      },
      rowData: {},
      total: 0,
      where: {
        page: 1,
        limit: 15,
        name: '',
        crud_id: ''
      }
    }
  },
  mounted() {
    this.getDatabase()
    this.getActionList()
    this.getOptions()
    this.getTableData()
  },
  methods: {
    // 表格数据
    getTableData() {
      this.loading = true
      dataEventLogApi(this.where).then((res) => {
        this.loading = false
        this.tableData = res.data.list
        this.total = res.data.count
      })
    },

    // 获取应用数据
    getDatabase() {
      getDatabaseApi().then((res) => {
        this.search[1].data_dict = res.data
      })
    },
    confirmData(data) {
      this.where.page = 1
      this.where.name = data.name || ''
      this.where.crud_id = data.crud_id || ''
      this.getTableData()
    },
    getEvent(val) {
      const targetOption = this.typesList[0]?.options?.find((item) => item.value === val)
      return targetOption ? targetOption.label : '--'
    },
    // 获取触发器类型
    async getOptions() {
      const data = await dataEventTypeApi(this.crud_id)
      this.typesList = [{ options: data.data }]
    },
    // 获取执行动作类型
    getActionList() {
      dataEventActionApi().then((res) => {
        this.actionList = res.data
      })
    },
    // 根据id获取触发动作
    getAction(val) {
      let textArr = []
      this.actionList.map((item) => {
        val.map((key) => {
          if (item.value == key) {
            textArr.push(item.label)
          }
        })
      })
      return textArr.join('/')
    },

    // 分页
    handleSizeChange(val) {
      this.where.limit = val
      this.getTableData()
    },

    pageChange(page) {
      this.where.page = page
      this.getTableData()
    },

    editFn(row) {
      this.rowData = row
      this.$refs.oaDialog.openBox()
    }
  }
}
</script>
<style scoped lang="scss"></style>
