<template>
<div class="box-container">
  <el-drawer :visible.sync="drawer" direction="rtl" :before-close="handleClose" size="75%" :append-to-body="true"
    :modal="true" :wrapper-closable="true">
    <slot slot="title">
      <div class="tabsEdit">
        <div class="tabs">
          <el-tabs v-model="tabsName" class="cr-header-tabs" @tab-click="handleClick">
            <el-tab-pane :label="$('ui.layoutNoticeNoticeListUnread')" name="1" />
            <el-tab-pane :label="$('ui.layoutNoticeNoticeListAll')" name="2" />
          </el-tabs>
        </div>
      </div>
    </slot>
    <div class="flex">
      <div class="left">
        <!-- <div class="title mb15">{{ $('消息类型') }}</div> -->
        <div class="type" @click="handleTypes({ id: '' })" :class="activeId == 0 ? 'active' : ''">
          {{ $("ui.layoutNoticeNoticeListAllTypes") }}
        </div>
        <div class="type" v-for="(item, index) in options" :key="index" @click="handleTypes(item)"
          :class="activeId == item.value ? 'active' : ''"  v-show="(tabsName==1&&item.count > 0)||tabsName==2">
          {{ $(item.cate_name, item.cate_name_en) }} <span class="num" v-if="item.count != 0">{{ item.count>99?'99+':item.count }}</span>
        </div>

        <div class="dingyue" @click="toSubscribe">
          
          <span class="iconfont icondingyuexiaoxi"></span>{{ $("ui.layoutNoticeNoticeListSubscriptions") }}</div>
      </div>
      <div class="right">
        <div class="mt20">
          <el-input v-model="where.title" prefix-icon="el-icon-search" clearable size="small" @change="getList"
            @keyup.native.stop.prevent.enter="getList" :placeholder="$('ui.layoutNoticeNoticeListPleaseEnterTitleAndContent')" style="width: 250px"></el-input>
        </div>
        <!-- 消息列表 -->
        <div class="mt10">
          <el-table ref="table" :data="tableData" :height="height" v-loading="loading"
            @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="35" />
            <el-table-column :label="$('ui.layoutNoticeNoticeListView')" width="70">
              <template slot-scope="scope">
                <el-image :src="scope.row.is_read === 0 ? unreadIcon : readIcon"></el-image>
              </template>
            </el-table-column>
            <el-table-column :label="$('ui.settingEnterpriseNewsIndexMessageTitle')" min-width="80"><template slot-scope="scope">{{ $(scope.row.title, scope.row.title_en) }}</template></el-table-column>
            <el-table-column :label="$('ui.settingEnterpriseNewsIndexMessageContent')" min-width="360"><template slot-scope="scope">{{ $(scope.row.message, scope.row.message_en) }}</template></el-table-column>
            <el-table-column :label="$('ui.developViewManagementType')" min-width="80"><template slot-scope="scope">{{ $(scope.row.cate_name, scope.row.cate_name_en) }}</template></el-table-column>
            <el-table-column prop="created_at" :label="$('ui.customerWeChatMassClientGroupChatSendTime')" min-width="120"></el-table-column>
            <el-table-column :label="$('toptable.operation')" width="100" fixed="right">
              <template slot-scope="scope">
                <el-button type="text" v-for="(item, index) in scope.row.buttons" :key="index"
                  :disabled="selectedType.includes(item.action)" @click="handleDetails(scope.row, item)">
                  <span v-if="scope.row.cate_name !== '考勤'"> {{ $(item.title, item.title_en) }}</span>
                </el-button>
              </template>
            </el-table-column>
          </el-table>
          <div class="footer">
            <div class="isSelect">
              <div class="flex" style="display: inline-block">
                <el-button size="small" @click="handleRead()">{{ $("ui.layoutNoticeNoticeListMarkAllAsRead") }}</el-button>
                <el-button size="small" :disabled="multipleSelection.length == 0"
                  @click="handleRead(1)">{{ $("ui.userNewsIndexMarkAsRead") }}</el-button>
                <el-button size="small" @click="handleDelete"
                  :disabled="multipleSelection.length == 0">{{ $("ui.customerSetupDictionaryManagementBatchDelete") }}</el-button>
              </div>
              <!-- <span v-if="multipleSelection.length !== 0">已选中{{ multipleSelection.length }} 条</span> -->
            </div>
            <div class="paginationClass">
              <el-pagination :page-size="where.limit" :current-page="where.page" :page-sizes="[15, 20, 30]"
                layout="total,sizes, prev, pager, next, jumper" :total="total" @size-change="handleSizeChange"
                @current-change="pageChange" />
            </div>
          </div>
        </div>
      </div>

    </div>
  </el-drawer>
  <message-handle-popup ref="messageHandlePopup" :detail="detail" @handleClose="handleClose"></message-handle-popup>
</div>
</template>
<script>
import { $ } from '@/lang'
import { noticeMessageListApi, noticeMessageDeleteApi, noticeMessageReadApi } from '@/api/user'
import { messageCateApi } from '@/api/setting'
import { messageListApi } from '@/api/public'
import  { roterPre } from '@/settings'

import unreadIcon from '@/assets/images/unread-icon.png';
import readIcon from '@/assets/images/read-icon.png';

export default {
  name: 'noticeList',
  components: {
    messageHandlePopup: () => import('@/components/common/messageHandlePopup')
  },
  data() {
    return {
      unreadIcon,
      readIcon,
      drawer: false,
      loading: false,
      tabsName: '1',
      activeId: 0,
      height: `calc(100vh - 240px)`,
      tableData: [],
      detail: {},
      options: [],
      multipleSelection: [],
      selectedType: ['delete', 'recall'],
      total: 0,
      where: {
        page: 1,
        limit: 15,
        title: '',
        cate_id: '',
        is_read: '0'
      }
    }
  },

  methods: {
    openBox() {
      this.getMessageCate()
      this.getList()
      this.drawer = true
      this.tabsName = '1'
    },
    // 获取列表
    getList(type = 1) {
      if (type === 1) {
        this.loading = true
      }
      noticeMessageListApi(this.where)
        .then((res) => {
          if (type === 1) {
            this.loading = false
          }
          this.tableData = res.data.list
          this.total = res.data.messageNum
          // this.$store.commit('user/SET_MESSAGE', this.total)
        })
        .catch((error) => {
          if (type === 1) {
            this.loading = false
          }
        })
    },
    handleRead(type, id) {
      let ids = []

      if (type == 1) {
        this.multipleSelection.forEach(item => {
          ids.push(item.id)
        })
        noticeMessageReadApi(1, id ? id : { ids }).then(res => {
          this.getList()
          this.getMessageCate()

        })
      } else {
        // if(this.where.cate_id){
          noticeMessageReadApi(1, {cate_id:this.where.cate_id||'all'}).then(res => {
          this.getList()
          this.getMessageCate()
        })

        // } else {
        //   noticeMessageReadApi(1, {}).then(res => {
        //   this.getList()
        //   this.getMessageCate()
        // })
        // }

      }

    },

    handleTypes(item) {
      this.where.cate_id = item.id
      this.activeId = item.id
      this.where.page = 1
      this.getList()
    },
    async getMessageCate() {
      const result = await messageCateApi()
      this.options = result.data
    },
    handleDelete() {
      if (this.multipleSelection.length <= 0) {
        this.$message.error($('legacyScript.selectAtLeastOneItem2'))
      } else {
        this.$modalSure('删除后不可恢复,您确认要删除吗').then(() => {
          const ids = []
          this.multipleSelection.map((value) => {
            ids.push(value.id)
          })
          this.batchMessageDelete({ ids: ids })
        })
      }
    },

    // 批量删除消息
    batchMessageDelete(data) {
      noticeMessageDeleteApi(data).then((res) => {
        let totalPage = Math.ceil((this.total - data.ids.length) / this.where.limit)
        let currentPage = this.where.page > totalPage ? totalPage : this.where.page
        this.where.page = currentPage < 1 ? 1 : currentPage
        this.getList()
        this.getMessageCate()
      })
    },
    handleSelectionChange(val) {
      this.multipleSelection = val
    },

    async handleDetails(row, item) {
      this.detail = row
      await this.$nextTick()
      this.$refs.messageHandlePopup.openMessage(item, row)

      if (row.is_read === 0) {
        this.handleRead(1, { ids: [row.id] })

      }
    },
    // 去订阅消息
    toSubscribe() {
      this.$router.push(`${roterPre}/user/news/subscribe`)
      this.handleClose()
    },

    pageChange(page) {
      this.where.page = page
      this.getList()
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.getList()
    },
    handleClose() {
      this.drawer = false
      messageListApi({page:1,limit:1}).then((res) => {
        let count = res.data.messageNum ? res.data.messageNum : 0
        this.$store.commit('user/SET_MESSAGE', count)
      })
    },
    handleClick() {
      this.where.page = 1
      if (this.tabsName == 2) {
        this.where.is_read = ''
      } else {
        this.where.is_read = '0'
      }
      this.getList()
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep .el-drawer__body {
  padding-bottom: 0;
}

::v-deep .el-drawer__header {
  padding: 0;
  border-bottom: 1px solid #eeeeee;
  padding-left: 30px;
}

::v-deep .el-tabs__item {
  height: 56px;
  line-height: 56px;
  font-weight: 500;
  z-index: 9999;
}

::v-deep .el-drawer__close-btn {
  width: 50px;
}


.tabsEdit {
  display: flex;
  justify-content: space-between;

  .invitationUrl {
    margin-top: 2px;
    margin-right: 20px;
  }
}

.footer {
  padding-top: 32px;
  border-top: 1px solid #e8e8e8;
}

.right {
  width: calc(100% - 230px);
  padding-left: 20px;
}

.left {
  width: 230px;
  flex-shrink: 0;
  height: calc(100vh - 60px);
  padding: 18px 10px;
  background-color: #f8f9fa;
  position: relative;
  .dingyue {
    cursor: pointer;
    position: absolute;
    left: 10px;
    right: 10px;
    bottom: 36px;
    padding: 10px 36px 10px 20px;
    min-height: 40px;
    height: auto;
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 14px;
    color: #303133;
    line-height: 20px;
    overflow-wrap: anywhere;
    border-radius: 4px;
    .icondingyuexiaoxi {
      margin-right: 6px;
    }

  }
  .dingyue:hover {
    background-color: #f1f9ff;
    color: #1890ff;
  }

  .title {
    margin-left: 20px;
    font-family: PingFang SC, PingFang SC;
    font-weight: 500;
    font-size: 15px;
    color: #303133;
  }

  .type {
    position: relative;
    cursor: pointer;
    padding: 10px 36px 10px 20px;
    min-height: 40px;
    height: auto;
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 14px;
    color: #303133;
    line-height: 20px;
    overflow-wrap: anywhere;
    border-radius: 4px;

    .num {
      position: absolute;
      top: 50%;
      right: 17px;
      transform: translateY(-50%);
      display: inline-block;
      width: 16px;
      height: 16px;
      background: #EA0000;
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 10px;
      color: #FFFFFF;
      line-height: 16px;
      border-radius: 50%;
      text-align: center;
    }
  }

  .active {
    background-color: #f1f9ff;
    color: #1890ff;
  }
}

.isSelect {
  height: 32px;
  line-height: 32px;
  position: absolute;
  font-size: 13px;
  margin-top: 14px;
}

.paginationClass {
  margin-top: 14px;
  display: flex;
  justify-content: flex-end;
}

::v-deep .el-image {
  width: 23px !important;
  height: 23px !important;
}

// ::v-deep .el-table th {
//   background-color: #f7fbff;
// }

::v-deep .el-table-column--selection .cell {
  padding: 0 10px;
}
</style>
