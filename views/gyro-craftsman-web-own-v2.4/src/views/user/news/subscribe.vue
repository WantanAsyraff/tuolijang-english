<template>
<div class="divBox pt10">
  <el-card class="normal-page">
    <form-box ref="formBox" @confirmData="confirmData" />
    <div class="table-box">
      <div class="inTotal">{{ $t("ui.developModuleFormBoxTotal") }}{{ total }} {{ $t("ui.commonOaFromBoxItems") }}</div>
      <el-table ref="table" :data="tableData" :height="tableHeight">
        <el-table-column prop="title" :label="$t('ui.settingEnterpriseNewsIndexMessageTitle')" min-width="100" />
        <el-table-column prop="content" :label="$t('ui.settingEnterpriseNewsIndexMessageContent')" min-width="240" />
        <el-table-column prop="cate_name" :label="$t('ui.settingEnterpriseNewsIndexMessageType')" min-width="100" />
        <el-table-column prop="verify" :label="$t('ui.userNewsSubscribeNotifications')" min-width="100">
          <template slot-scope="scope">
            <el-switch
              v-if="scope.row.is_subscribe === 2"
              :value="1"
:active-text="$t('ui.userNewsSubscribeSubscribe')"
:inactive-text="$t('ui.formCommonSelectLabelCancel')"
              :disabled="true"
              :active-value="1"
              :inactive-value="0"
            />
            <el-switch
              v-else
              v-model="scope.row.is_subscribe"
:active-text="$t('ui.userNewsSubscribeSubscribe')"
:inactive-text="$t('ui.formCommonSelectLabelCancel')"
              @change="messageSubscribe(scope.row)"
              :active-value="1"
              :inactive-value="0"
            />
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
        :page-size="where.limit"
        :current-page="where.page"
        :page-sizes="[15, 20, 30]"
        layout="total,sizes, prev, pager, next, jumper"
        :total="total"
        @size-change="handleSizeChange"
        @current-change="pageChange"
      />
    </div>
  </el-card>
</div>
</template>
<script>
import { userNoticeSubscribeApi, userNoticeSubscribeShowApi } from '@/api/user'
export default {
  name: 'subscribe',
  components: {
    formBox: () => import('./components/formBox')
  },
  data() {
    return {
      tableData: [],
      messageData: {},
      where: {
        page: 1,
        limit: 15,
        cate_id: '',
        title: ''
      },
      total: 0,
      loadBtn: false,
      loading: false,
      type: [0, 1]
    }
  },
  mounted() {
    this.$store.commit('app/SET_PARENTCUR', 101)
    this.getTableData()
  },
  methods: {
    // 获取列表
    getTableData() {
      userNoticeSubscribeApi(this.where).then((res) => {
        this.tableData = res.data.list
        this.total = res.data.count
      })
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.getTableData()
    },
    pageChange(page) {
      this.where.page = page
      this.getTableData()
    },
    confirmData(data) {
      if (data == 'reset') {
        this.where.limit = 15
      }
      this.where.page = 1
      this.where.title = data.name
      this.where.cate_id = data.types
      this.getTableData()
    },
    //修改状态
    messageSubscribe(row) {
      userNoticeSubscribeShowApi(row.id, { status: row.is_subscribe })
        .then((res) => {
          this.getTableData()
        })
        .catch((error) => {
          row.row.user_sub = !row.is_subscribe
        })
    }
  }
}
</script>

<style lang="scss" scoped>
.pt0 {
  padding-top: 0px !important;
}
.divBox {
  margin-left: -145px;
}
</style>
