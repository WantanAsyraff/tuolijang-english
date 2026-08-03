<!-- 群发素材页面 -->
<template>
<div>
  <div class="divBox" v-if="!isShow">
    <el-card :body-style="{ padding: '20px 20px 20px 20px' }" class="normal-page">
      <oaFromBox
        :search="search"
        :title="$t('ui.customerWeChatMassWechatMomentsMomentsMassSendList')"
        :total="total"
        :treeData="treeData"
        :isViewSearch="false"
        ref="fromBox"
        btnText="添加群发"
        @addDataFn="addDataFn"
        @treeChange="treeChange"
        @confirmData="confirmData"
      ></oaFromBox>

      <div class="mt10">
        <el-table
          :height="tableHeight"
          :data="tableData"
          style="width: 100%"
          v-loading="loading"
          @selection-change="handleCheck"
        >
          <el-table-column :label="$t('ui.customerWeChatMassWechatMomentsMomentsContent')" prop="temp_content" :show-overflow-tooltip="true"></el-table-column>
          <el-table-column :label="$t('ui.customerWeChatMassClientGroupChatSendTime')" prop="send_time"></el-table-column>
          <el-table-column :label="$t('ui.customerWeChatMassClientGroupChatTaskStatus')" prop="name">
            <template slot-scope="scope">
              <el-tag size="small" v-if="scope.row.status == 1">{{ $t("ui.customerWeChatMassClientGroupChatScheduled") }}</el-tag>
              <el-tag size="small" v-else-if="scope.row.status == 2" type="warning">{{ $t("ui.customerWeChatMassMassDetailsSending") }}</el-tag>
              <el-tag size="small" v-else-if="scope.row.status == 0" type="info">{{ $t("ui.customerWeChatMassMassDetailsStopped") }}</el-tag>
              <el-tag size="small" v-else-if="scope.row.status == 3" type="success">{{ $t("ui.customerWeChatMassMassDetailsCompleted") }}</el-tag>
            </template></el-table-column
          >
          <el-table-column :label="$t('ui.customerWeChatMassClientMassSentEmployees')" prop="sent_user_string">
            <template slot-scope="scope">{{ scope.row.sent_user_string || '--' }}</template>
          </el-table-column>
          <el-table-column :label="$t('ui.customerWeChatMassClientMassUnsentEmployees')" prop="not_sent_user_string">
            <template slot-scope="scope">{{ scope.row.not_sent_user_string || '--' }}</template>
          </el-table-column>
          <el-table-column :label="$t('ui.invoiceInvoiceDetailsCreatedTime')" prop="updated_at"></el-table-column>
          <el-table-column :label="$t('ui.hrAssessCheckIndexCreator')" prop="creator.name"></el-table-column>
          <el-table-column :label="$t('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" fixed="right" width="190">
            <template slot-scope="scope">
              <el-button type="text" size="mini" @click="handleCheck(scope.row)">{{ $t("ui.layoutNoticeNoticeListView") }}</el-button>
              <template v-if="scope.row.status == 1">
                <el-button type="text" size="mini" @click="handleEdit(scope.row)">{{ $t("ui.formCommonOaLogEdit") }}</el-button>
                <el-button type="text" size="mini" @click="handleDel(scope.row)">{{ $t("ui.chatIndexDelete") }}</el-button>
              </template>
              <template v-if="scope.row.status == 2">
                <!-- <el-button type="text" size="mini" @click="handleRemind(scope.row)">提醒发送</el-button> -->
                <el-button type="text" size="mini" @click="handleStopped(scope.row)">{{ $t("ui.customerWeChatMassClientGroupChatStopSending") }}</el-button>
              </template>
            </template>
          </el-table-column>
        </el-table>
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
    </el-card>
  </div>
  <!-- 添加群发 -->
  <addGroupPosting v-if="isShow" :types="2" :editId="editId" @backFn="backFn"></addGroupPosting>
  <!-- 详情 -->
  <massDetails ref="massDetails"></massDetails>
</div>
</template>
<script>
import i18n from '@/lang'
import { getMassList, delWorkMass, getWorkMassStatus, getWorkMassRemind } from '@/api/weCom'
import { roterPre } from '@/settings'
export default {
  name: '',
  components: {
    massDetails: () => import('./components/massDetails'),
    addGroupPosting: () => import('./addGroupPosting'),
    oaFromBox: () => import('@/components/common/oaFromBox')
  },

  data() {
    return {
      isShow: false,
      editId: 0,
      treeData: [
        {
          options: [
            {
              label: i18n.t('finance.all'),
              id: '',
              value: ''
            },
            {
              label: i18n.t('ui.customerWeChatMassClientGroupChatScheduled'),
              id: 1,
              value: 1
            },
            {
              label: i18n.t('ui.customerWeChatMassMassDetailsSending'),
              id: 2,
              value: 2
            },
            {
              label: i18n.t('ui.customerWeChatMassMassDetailsCompleted'),
              id: 3,
              value: 3
            },
            {
              label: i18n.t('ui.customerWeChatMassMassDetailsStopped'),
              id: '0',
              value: '0'
            }
          ]
        }
      ],
      search: [
        {
          form_value: 'input',
          field_name_en: 'content',
          field_name: '朋友圈内容'
        },
        {
          form_value: 'date_picker',
          field_name_en: 'send_time',
          field_name: '发送时间'
        }
      ],

      tableData: [],
      where: {
        page: 1,
        limit: 15,
        types: '2',
        status: ''
      },

      total: 0,
      loading: false
    }
  },

  mounted() {
    this.getTableData()
  },
  methods: {
    handleCheck(val) {
      this.$refs.massDetails.openBox(val.id, '2')
    },
    handleEdit(item) {
      this.editId = item.id
      this.isShow = true
      // this.$router.push({ path: `${roterPre}/customer/weChatMass/addGroupPosting`, query: { types: '2', id: item.id } })
    },
    backFn() {
      this.editId = 0
      this.isShow = false
      this.getTableData()
    },
    // 停止发送
    async handleStopped(row) {
      await this.$modalSure('你确定要停止发送此朋友圈吗')
      await getWorkMassStatus(row.id, { status: 0 })
      await this.getTableData()
    },

    async handleRemind(row) {
      await this.$modalSure('再次提醒员工进行朋友圈发送')
      await getWorkMassRemind(row.id)
      await this.getTableData()
    },

    //添加联系人
    addDataFn() {
      this.isShow = true
      // this.$router.push({ path: `${roterPre}/customer/weChatMass/addGroupPosting`, query: { types: '2' } })
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.where.page = 1
      this.getTableData()
    },
    pageChange(page) {
      this.where.page = page
      this.getTableData()
    },

    async getTableData() {
      if (this.loading) return
      this.loading = true
      const res = await getMassList(this.where)
      this.tableData = res.data.list
      this.total = res.data.count
      this.loading = false
    },
    treeChange(data) {
      this.where.status = data.value
      this.getTableData()
    },

    confirmData(data) {
      if (data == 'reset') {
        this.where = {
          page: 1,
          limit: 15,
          types: '2'
        }

        this.getTableData('')
      } else {
        this.where.page = 1

        this.where = { ...this.where, ...data }
        this.getTableData()
      }
    },

    // 删除
    async handleDel(item) {
      await this.$modalSure('确定删除当前数据')
      await delWorkMass(item.id)
      if (this.where.page > 1 && this.tableData.length <= 1) {
        this.where.page--
      }
      await this.getTableData()
    }
  }
}
</script>

<style lang="scss" scoped></style>
