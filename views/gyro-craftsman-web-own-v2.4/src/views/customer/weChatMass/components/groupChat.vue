<!-- 客户群聊弹窗 -->

<template>
<el-dialog :title="$('ui.customerWeChatMassGroupChatSelectGroupChat')" :visible.sync="dialogTableVisible" width="760px">
  <el-input
    :placeholder="$('ui.customerWeChatMassGroupChatPleaseEnterGroupChatName')"
    v-model="where.name"
    @change="getList"
    size="small"
    style="width: 250px"
    class="mr10"
  >
  </el-input>

  <div class="tips mb10 mt10">{{ $("ui.businessHolidayQueryIndexTotal") }}{{ total }}{{ $("ui.customerWeChatMassGroupChatGroupChats") }}</div>
  <div>
    <el-table :data="tableData" style="width: 100%" height="500px" @selection-change="handleSelectionChange">
      <el-table-column type="selection" width="55"> </el-table-column>
      <el-table-column property="name" :label="$('ui.customerWeChatMassGroupChatGroupChatName')" width="150">
        <template slot-scope="scope">
          {{ scope.row.name || '--' }}
        </template>
      </el-table-column>
      <el-table-column property="admin.name" :label="$('ui.customerWeChatMassGroupChatGroupOwner')"> </el-table-column>
      <el-table-column property="member_num" :label="$('ui.customerWeChatMassGroupChatGroupMemberCount')"></el-table-column>
      <el-table-column property="retreat_group_num" :label="$('ui.customerWeChatMassGroupChatMembersWhoLeft')"></el-table-column>
      <el-table-column property="group_create_time" :label="$('ui.invoiceInvoiceDetailsCreatedTime')" width="200"></el-table-column>
    </el-table>
    <el-pagination
      :page-size="where.limit"
      :current-page="where.page"
      :total="total"
      @current-change="pageChange"
      layout="total, prev, pager, next"
    />
  </div>

  <span slot="footer">
    <el-button @click="handleClose" size="small">{{ $("ui.formCommonSelectLabelCancel") }}</el-button>
    <el-button type="primary" @click="handleConfirm" size="small">{{ $("ui.formCommonDialogFormOk") }}</el-button>
  </span>
</el-dialog>
</template>
<script>
import { getWorkMassGroupChat, getGroupChat } from '@/api/weCom'

// type: 1-群聊群发 2-自动拉群
const GROUP_CHAT_API_MAP = {
  1: getWorkMassGroupChat,
  2: getGroupChat
}
export default {
  name: 'groupChat',
  data() {
    return {
      dialogTableVisible: false,
      tableData: [],
      selectValue: [],
      total: 0,
      type: 1,
      ids: [], //群主id
      where: { page: 1, limit: 10, admin_id: '', name: '' }
    }
  },

  methods: {
    openBox(ids, type = 1) {
      this.type = type
      this.where.admin_id = ids || ''
      this.getList()
      this.dialogTableVisible = true
    },

    handleSelectionChange(val) {
      this.selectValue = val
    },
    handleConfirm() {
      this.$emit('selectGroups', this.selectValue)
      this.handleClose()
    },
    pageChange(page) {
      this.where.page = page
      this.getList()
    },
    handleClose() {
      this.dialogTableVisible = false
    },

    getList() {
      const api = GROUP_CHAT_API_MAP[this.type] || getGroupChat
      api(this.where).then(({ data }) => {
        this.tableData = data?.list || []
        this.total = data?.count || 0
      })
    }
  }
}
</script>
<style scoped lang="scss">
.tips {
  font-size: 12px;
  color: #909399;
}
</style>
