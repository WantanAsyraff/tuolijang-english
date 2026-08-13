<!-- 查看客户信息侧滑页面 -->
<template>
<div class="station">
  <el-drawer
    :append-to-body="true"
    :direction="direction"
    :show-close="false"
    :size="DRAWER_SIZE.LG"
    :title="$('ui.layoutNoticeNoticeListView')"
    :visible.sync="drawer"
  >
    <div slot="title" class="invoice-title">
      <el-row class="invoice-header">
        <el-col class="invoice-left">
          <div class="invoice-logo"><i class="icon iconfont iconhetong"></i></div>
        </el-col>
        <el-col class="invoice-right">
          <div class="txt1 over-text">{{ title }}</div>

          <div class="txt2">
            <span class="title">
              {{ $("ui.customerWeChatMassGroupDetailsEnabledStatus") }}
              <span v-if="dataInfo.status == 1" class="info2">{{ $("ui.customerWeChatMassGroupDetailsEnable") }}</span>
              <span v-else class="info1">{{ $("ui.customerWeChatMassGroupDetailsClose") }}</span>
            </span>
          </div>
        </el-col>
      </el-row>
      <div @click="handleClose" class="el-icon-close"></div>
    </div>

    <el-form class="invoice-body" label-width="auto">
      <div class="title-box">{{ $("ui.customerWeChatMassGroupDetailsBasicInformation") }}</div>
      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsQrCodeName')">
        {{ dataInfo.name }}
      </el-form-item>
      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsVerificationMethod')">
        {{ dataInfo.skip_verify == 1 ? $('ui.customerWeChatMassGroupDetailsVerificationRequired') : $('ui.customerWeChatMassGroupDetailsApproveDirectly') }}
      </el-form-item>
      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsUseEmployee')">
        <div class="send-box">
          <div v-for="(item, index) in dataInfo.users" class="user-box">
            <img :src="item.avatar" alt="" class="img" />
            {{ item.name }}
          </div>
        </div>
      </el-form-item>
      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsCustomerLabels')">
        <template v-if="dataInfo.tags && dataInfo.tags.length > 0">
          <span v-for="(item, index) in dataInfo.tags" :key="index"
            >{{ item.name }} {{ dataInfo.tags.length == index + 1 ? '' : '、' }}</span
          >
        </template>
        <span v-else>--</span>
      </el-form-item>
      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsNewCustomerWelcomeMessage')">
        <span style="display: inline-block; width: 100%">{{
          dataInfo.welcome ? dataInfo.welcome.content : '--'
        }}</span>

        <div
          class="box mt10"
          v-if="dataInfo.welcome && dataInfo.welcome.attach && dataInfo.welcome.attach.length > 0"
        >
          <div v-for="(file, index) in dataInfo.welcome.attach" :key="index" class="item">
            <div class="flex lh-center">
              <span class="iconfont iconxiaochengxu1" v-if="file.types === 'mini_program'"></span>
              <span class="iconfont iconlianjie1" v-else-if="file.types === 'link'"></span>
              <template v-else>
                <span class="file" v-if="file.file && toSrcIcon(file.file.name) !== 'img'">{{
                  getFileTypeFn(file.file.name || '')
                }}</span>
                <img
                  v-if="file.file && toSrcIcon(file.file.name) == 'img' && file.file.url"
                  :src="file.file.url"
                  alt=""
                  class="img"
                  @click.stop="openFile(file.file)"
                />
              </template>
              <span v-if="['mini_program', 'link'].includes(file.types)" style="width: 230px" class="over-text"
                >{{ file.title }}
              </span>
              <span v-else style="width: 200px" class="over-text"> {{ file.file ? file.file.name : '--' }}</span>
            </div>
            <div class="file-actions" style="margin-left: auto" v-if="file.types !== 'mini_program'">
              <i class="iconfont iconyulan" @click.stop="openFile(file.file, file)"></i>
              <i class="iconfont iconxiazai" @click.stop="downLoad(file.file)" v-if="file.types !== 'link'"></i>
            </div>
          </div>
        </div>
      </el-form-item>
      <div class="line"></div>

      <div class="title-box">{{ $("ui.customerWeChatMassGroupDetailsInviteToGroup") }}</div>
      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsInviteToGroup2')">
        {{ dataInfo.invite_group == 1 ? $('ui.customerWeChatMassGroupDetailsEnable') : $('ui.customerWeChatMassGroupDetailsClose') }}
      </el-form-item>
      <el-table :data="dataInfo.group_chats" style="width: 100%" class="group-table">
        <el-table-column :label="$('ui.customerWeChatMassGroupChatGroupChatName')" min-width="140" :show-overflow-tooltip="true">
          <template slot-scope="scope">
            {{ scope.row.room_base_name || '--' }}
          </template>
        </el-table-column>
        <el-table-column :label="$('ui.customerWeChatMassGroupChatGroupOwner')" property="admin.name" min-width="120">
          <template slot-scope="scope">
            {{ scope.row.admin.name || '--' }}
          </template>
        </el-table-column>
        <el-table-column :label="$('ui.customerWeChatMassGroupChatGroupMemberCount')" min-width="100" property="member_num" />
        <el-table-column :label="$('ui.customerWeChatMassGroupChatMembersWhoLeft')" min-width="90" property="retreat_group_num" />
        <el-table-column :label="$('ui.invoiceInvoiceDetailsCreatedTime')" min-width="160" property="group_create_time" />
      </el-table>

      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsAutomaticallyAddToGroup')">
        {{ dataInfo.auto_create_room == 1 ? $('ui.customerWeChatMassGroupDetailsEnable') : $('ui.customerWeChatMassGroupDetailsClose') }}
      </el-form-item>

      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsGroupChatName')">{{ dataInfo.room_base_name || '--' }} </el-form-item>
      <el-form-item :label="$('ui.customerWeChatMassGroupDetailsGroupChatNumber')"
        >{{ $("ui.customerWeChatMassGroupDetailsGroupNumberingWillStartFrom") }}{{ dataInfo.room_base_id || 1 }}{{ $("ui.customerWeChatMassGroupDetailsStart") }}{{ dataInfo.room_base_id >= 1 ? $('ui.customerWeChatMassGroupDetailsGenerate') : $('ui.customerWeChatMassGroupDetailsDoNotGenerate') }}
      </el-form-item>
    </el-form>
  </el-drawer>
  <!-- 打开文件 -->
  <fileDialog ref="viewFile"></fileDialog>
</div>
</template>
<script>
import { getGroupChatyDetails } from '@/api/weCom'
import { DRAWER_SIZE } from '@/constants/popupSize'
import { getFileType, getFileExtension } from '@/libs/public'
export default {
  name: 'detailsDrawer',
  props: {},
  components: { fileDialog: () => import('@/components/openFile/previewDialog ') },
  data() {
    return {
      DRAWER_SIZE,
      dataInfo: {},

      id: 0,
      drawer: false,
      direction: 'rtl'
    }
  },
  computed: {
    title() {
      return this.dataInfo.name || '--'
    }
  },

  methods: {
    async getDetails(id) {
      const result = await getGroupChatyDetails(id)
      this.dataInfo = result.data || {}
    },
    formatMemberNum(row) {
      const current = row.member_num ?? 0
      const max = row.max_num ?? row.max_member_num ?? row.member_limit
      if (max !== undefined && max !== null && max !== '') {
        return `${current}/${max}`
      }
      return row.member_num ?? '--'
    },
    getFileTypeFn(name) {
      if (!name) return '--'
      return getFileExtension(name)
    },

    handleClose() {
      this.$nextTick(() => {
        this.drawer = false
        this.groupList = []
        this.dataInfo = {}
      })
    },
    toSrcIcon(name) {
      if (!name) return '--'
      return getFileType(name)
    },
    openFile(item, rowData) {
      if (rowData.types === 'link') {
        window.open(rowData.link, '_blank')
      } else {
        if (this.toSrcIcon(item.name) == 'img') {
          this.$refs.viewFile.openFile(null, null, item)
        } else if (item.name.toLowerCase().endsWith('.mp4')) {
          this.$refs.viewFile.openFile(null, null, item)
        } else {
          window.open(`https://view.officeapps.live.com/op/embed.aspx?src=${item.url}`, '_blank')
        }
      }
    },

    openBox(id) {
      this.id = id
      this.getDetails(id)
      this.drawer = true
    }
  }
}
</script>

<style lang="scss" scoped>
.send-box {
  display: flex;
  flex-wrap: wrap;
}
.invoice-body {
  margin: 20px;
  height: 100%;
}
.el-icon-close {
  width: 20px;
  height: 20px;
  cursor: pointer;
  font-size: 13px;
  z-index: 99;
}
::v-deep .el-drawer__body {
  padding-bottom: 50px;
}
::v-deep .el-drawer__header {
  height: 80px !important;
  border: none;
  padding: 14px 18px;
  border-bottom: 1px solid #eeeeee;
}
.box {
  // display: flex;
  // flex-wrap: wrap;
  .item {
    width: 300px;
    height: 30px;
    background: #f7f7f7;
    border-radius: 4px 4px 4px 4px;
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 12px;
    color: #606266;
    margin-bottom: 10px;
    line-height: 30px;
    padding-right: 12px;
    display: flex;
    margin-right: 10px;
    justify-content: space-between;
    align-items: center;
  }
  .iconlianjie1 {
    font-size: 18px;
    color: #1890ff;
    margin-right: 4px;
    margin-left: 4px;
  }
  .iconxiaochengxu1 {
    font-size: 18px;
    color: #19be6b;
    margin-right: 6px;
    margin-left: 4px;
  }
  .required {
    color: #f56c6c;
    margin-right: 6px;
    margin-left: 4px;
  }
  .img {
    width: 23px;
    height: 22px;
    border-radius: 4px;
    margin-right: 4px;
    margin-left: 4px;
  }
  .file {
    display: flex;
    width: 22px;
    height: 23px;
    background: url('../../../../assets/images/cloud/file-box.png') no-repeat;
    background-size: 22px 23px;
    color: #fff !important;
    justify-content: center;
    line-height: 22px;
    font-size: 11px;
    margin-right: 4px;
    margin-left: 4px;
  }
}
.user-box {
  height: 30px;
  padding: 0 8px;
  display: flex;
  align-items: center;
  background: #f0f2f5;
  margin-top: 5px;
  margin-right: 10px;
  border-radius: 4px;
  position: relative;
  .img {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin-right: 4px;
    margin-left: 4px;
  }
  .el-icon-success {
    position: absolute;
    right: -4px;
    top: -8px;
    color: #19be6b;
    font-size: 14px;
  }
}

.invoice-title {
  display: flex;
  justify-content: space-between;
  .invoice-header {
    display: flex;
    align-items: center;
    .invoice-left {
      width: 48px;
      margin-right: 10px;
      .invoice-logo {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #1890ff;
        border-radius: 4px;
        i {
          color: #ffffff;
          font-size: 30px;
        }
      }
    }
    .invoice-right {
      width: calc(100% - 55px);
      height: 48px;
    }
    .txt1 {
      font-size: 16px;
      font-weight: bold;
      color: rgba(0, 0, 0, 0.85);
    }
    .weight {
      font-family: PingFang SC, PingFang SC;
      font-weight: 400;
      font-size: 14px;
      color: #1a1a1a;
    }
    .txt3 {
      font-size: 14px;
    }
    .txt2 {
      margin-top: 10px;
      font-size: 13px;
      color: #000;
      .title {
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 14px;
        color: #606266;
        margin-right: 30px;
      }
      .title:first-of-type {
        padding-left: 0;
      }
      .info1 {
        color: #909399;
      }

      .info2 {
        color: #1890ff;
      }
    }
  }
}

.title-box {
  font-family: PingFang SC, sans-serif;
  font-weight: 500;
  font-size: 14px;
  color: #303133;
  margin: 0 0 15px 9px;
  position: relative;
  &:before {
    content: '';
    background-color: #1890ff;
    width: 3px;
    height: 14px;
    position: absolute;
    left: -9px;
    top: 50%;
    transform: translateY(-50%); // 垂直居中更可靠
  }
}

.line {
  width: 100% !important;
  height: 3px;
  border-bottom: 1px dashed #dcdfe6;
  margin-bottom: 20px;
}

::v-deep .el-form-item {
  margin-bottom: 10px;
}

.group-table {
  margin-bottom: 20px;
}

.admin-cell {
  display: flex;
  align-items: center;

  .admin-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-right: 6px;
    flex-shrink: 0;
  }
}
</style>
