import { $ } from '@/lang'
<template>
<div class="divBox">
  <el-card :body-style="{ padding: '20px 20px 0 20px' }" class="normal-page">
    <el-row>
      <el-col v-bind="gridl" class="p20">
        <tree :parentTree="options" @getTypesId="getTypesId" @addType="addType"></tree>
      </el-col>
      <el-col v-bind="gridr" class="boder-left pl20">
        <el-button
          v-if="selectedRows.length > 0"
          class="left-btn"
          type="primary"
          size="small"
          @click="handleBatchPush"
          >{{ $("ui.settingEnterpriseNewsIndexBatchSettings") }}
        </el-button>
        <oaFromBox
          :search="searchData"
          :title="$route.meta.title"
          :total="total"
          :isViewSearch="false"
          :sortSearch="false"
          :isAddBtn="false"
          :dropdownList="dropdownList"
          class="mb20"
          @confirmData="confirmData"
          @dropdownFn="handleDropdown"
        >
        </oaFromBox>
        <div class="table-box">
          <el-table ref="table" :data="tableData" :height="tableHeight" @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="55" />
            <el-table-column :label="$('ui.settingEnterpriseNewsIndexMessageTitle')" min-width="100" show-overflow-tooltip>
              <template slot-scope="scope">{{ $(scope.row.title) }}</template>
            </el-table-column>
            <el-table-column :label="$('ui.settingEnterpriseNewsIndexMessageContent')" min-width="240" show-overflow-tooltip>
              <template slot-scope="scope">{{ $(scope.row.content) }}</template>
            </el-table-column>
            <el-table-column :label="$('ui.settingEnterpriseNewsIndexMessageType')" min-width="100" show-overflow-tooltip>
              <template slot-scope="scope">{{ $(scope.row.cate_name) }}</template>
            </el-table-column>
            <el-table-column prop="verify" :label="$('ui.settingEnterpriseNewsIndexSystemNotifications')" min-width="100">
              <template slot-scope="scope">
                <el-switch
                  v-if="scope.row.system_template"
                  v-model="scope.row.system_template.status"
                  active-text="开启"
                  inactive-text="关闭"
                  @change="messageStatus(scope.row.id, scope.row.system_template, 0)"
                  :active-value="1"
                  :inactive-value="0"
                />
              </template>
            </el-table-column>
            <el-table-column prop="verify" :label="$('ui.settingEnterpriseNewsIndexSms')" min-width="100">
              <template slot-scope="scope">
                <el-switch
                  v-if="scope.row.sms_template"
                  v-model="scope.row.sms_template.status"
                  active-text="开启"
                  inactive-text="关闭"
                  @change="messageStatus(scope.row.id, scope.row.sms_template, 1)"
                  :active-value="1"
                  :inactive-value="0"
                />
              </template>
            </el-table-column>
            <el-table-column prop="verify" :label="$('ui.settingEnterpriseNewsIndexWeComMessage')" min-width="100">
              <template slot-scope="scope">
                <el-switch
                  v-if="scope.row.wework_template"
                  v-model="scope.row.wework_template.status"
                  active-text="开启"
                  inactive-text="关闭"
                  @change="messageStatus(scope.row.id, scope.row.wework_template, 5)"
                  :active-value="1"
                  :inactive-value="0"
                />
              </template>
            </el-table-column>
            <el-table-column prop="verify" :label="$('ui.settingEnterpriseNewsIndexWeComBot')" min-width="100">
              <template slot-scope="scope">
                <el-switch
                  v-if="scope.row.work_template"
                  v-model="scope.row.work_template.status"
                  active-text="开启"
                  inactive-text="关闭"
                  @change="messageStatus(scope.row.id, scope.row.work_template, 2)"
                  :active-value="1"
                  :inactive-value="0"
                />
              </template>
            </el-table-column>
            <el-table-column prop="verify" :label="$('ui.settingEnterpriseNewsIndexDingTalkBot')" min-width="100">
              <template slot-scope="scope">
                <el-switch
                  v-if="scope.row.ding_template"
                  v-model="scope.row.ding_template.status"
                  active-text="开启"
                  inactive-text="关闭"
                  @change="messageStatus(scope.row.id, scope.row.ding_template, 3)"
                  :active-value="1"
                  :inactive-value="0"
                />
              </template>
            </el-table-column>
            <el-table-column prop="verify" :label="$('ui.settingEnterpriseNewsIndexOtherBot')" min-width="100">
              <template slot-scope="scope">
                <el-switch
                  v-if="scope.row.other_template"
                  v-model="scope.row.other_template.status"
                  active-text="开启"
                  inactive-text="关闭"
                  @change="messageStatus(scope.row.id, scope.row.other_template, 4)"
                  :active-value="1"
                  :inactive-value="0"
                />
              </template>
            </el-table-column>
            <el-table-column prop="verify" :label="$('ui.settingEnterpriseNewsIndexCanUnsubscribe')" min-width="100">
              <template slot-scope="scope">
                <el-switch
                  v-model="scope.row.user_sub"
                  active-text="开启"
                  inactive-text="关闭"
                  @change="messageSubscribe(scope.row)"
                  :active-value="1"
                  :inactive-value="0"
                />
              </template>
            </el-table-column>
            <el-table-column :label="$('toptable.operation')" width="190" fixed="right">
              <template slot-scope="scope">
                <el-button type="text" @click="handlePush(scope.row)">{{ $("ui.settingEnterpriseNewsIndexDeliveryChannel") }}</el-button>
                <el-button
                  v-hasPermi="['enterprise:news:edit']"
                  v-if="scope.row.template_time === 1"
                  type="text"
                  @click="handleTime(scope.row)"
                  >{{ $("ui.settingEnterpriseNewsIndexSetTime") }}
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </el-col>
    </el-row>

    <div class="page-fixed">
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

  <message-times ref="messageTimes" :message-data="messageData" @isOk="getTableData" />
  <!-- 推送渠道 -->
  <messagePush ref="messagePush" @isOk="getTableData"></messagePush>
  <!--  -->
  <messageType ref="messageType"></messageType>
</div>
</template>
<script>
import { messageListApi, putStatusMessageApi, messageSubscribeApi, messageSyncApi, messageCateApi } from '@/api/setting'
import { status } from 'nprogress'

export default {
  name: 'Apply',
  components: {
    oaFromBox: () => import('@/components/common/oaFromBox'),
    messagePush: () => import('./components/messagePush'),
    messageType: () => import('./components/messageType'),
    tree: () => import('./components/tree'),
    messageTimes: () => import('./components/messageTimes')
  },
  data() {
    return {
      tableData: [],
      selectedRows: [],
      options: [],
      messageData: {},
      where: {
        page: 1,
        limit: 15,
        cate_id: '',
        title: ''
      },
      total: 0,

      gridl: {
        xl: 3,
        lg: 4,
        md: 5,
        sm: 6,
        xs: 24
      },
      gridr: {
        xl: 21,
        lg: 20,
        md: 19,
        sm: 18,
        xs: 24
      },
      loadBtn: false,
      type: [0, 1],
      searchData: [
        {
          field_name: '标题/内容',
          field_name_en: 'title',
          form_value: 'input'
        }
      ],
      dropdownList: [{ label: $('legacyScript.syncData'), value: 'sync' }]
    }
  },
  mounted() {
    this.getMessageCate()
    this.getTableData()
  },
  methods: {
    // 获取列表
    getTableData() {
      let statusObj = {
        system_template: {
          status: 0
        },
        ding_template: {
          status: 0
        },
        other_template: {
          status: 0
        },
        work_template: {
          status: 0
        },
        sms_template: {
          status: 0
        }
      }
      messageListApi(this.where).then((res) => {
        this.$nextTick(() => {
          this.tableData = res.data.list
          this.total = res.data.count
          if (this.tableData && this.tableData.length > 0) {
            this.tableData.forEach((value) => {
              for (let key in statusObj) {
                if (!value[key]) {
                  value[key] = { status: '0' }
                  value.id = value.id
                }
              }
              value.message_template.sort((a, b) => {
                return a.type - b.type
              }) //升序
            })
          }
        })
      })
    },
    getTypesId(type) {
      this.where.page = 1
      this.where.cate_id = type
      this.getTableData()
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
        this.where.title = ''
        this.where.cate_id = ''
      } else {
        this.where = { ...this.where, ...data }
      }
      this.where.page = 1
      this.getTableData()
    },

    handlePush(row) {
      this.$refs.messagePush.openBox(row)
    },
    addType() {
      this.$refs.messageType.openBox()
    },

    getMessageSync() {
      this.loadBtn = true
      messageSyncApi()
        .then((res) => {
          this.loadBtn = false
          this.where.page = 1
          this.getTableData()
        })
        .catch((error) => {
          this.loadBtn = false
        })
    },
    handleTime(row) {
      let type = ''
      if (
        row.template_type == 'clock_remind' ||
        row.template_type == 'clock_remind_after_work' ||
        row.template_type == 'remind_work_card_short'
      ) {
        type = 1
      }
      this.messageData = {
        width: '560px',
        title: $('legacyScript.setReminderTime'),
        type,
        data: row
      }
      this.$refs.messageTimes.handleOpen()
    },
    handleSelectionChange(rows) {
      this.selectedRows = rows || []
    },
    handleBatchPush() {
      if (!this.selectedRows.length) {
        this.$message.warning($('legacyScript.selectTheMessagesYouWantToConfigureFirst'))
        return
      }
      this.$refs.messagePush.openBatchBox(this.selectedRows.map((row) => row.id))
    },
    handleDropdown(item) {
      if (item.value === 'sync') {
        this.getMessageSync()
      }
    },
    //修改状态
    messageStatus(id, row, type) {
      putStatusMessageApi(id, type, { status: row.status })
        .then((res) => {
          this.getTableData()
        })
        .catch((error) => {
          row.status = !row.status
        })
    }, //修改状态
    messageSubscribe(row) {
      messageSubscribeApi(row.id, { status: row.user_sub })
        .then((res) => {
          this.getTableData()
        })
        .catch((error) => {
          row.row.user_sub = !row.user_sub
        })
    },
    getMessageCate() {
      messageCateApi().then((res) => {
        this.options = [{ label: $('finance.all'), value: '' }, ...res.data]
        for (let i = 0; i < res.data.length; i++) {
          res.data[i].name = res.data[i].label
        }
        // this.searchData[1].data_dict = res.data
      })
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep .el-table__row {
  height: 56px;
}
.boder-left {
  position: relative;
}
.left-btn {
  position: absolute;
  left: 20px;
  bottom: -54px !important;
  z-index: 999;
}
</style>
