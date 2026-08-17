<template>
<div class="divBox">
  <el-dialog
    class="box"
    :visible.sync="dialogVisible"
    width="550px"
    top="15vh"
    :close-on-click-modal="false"
    :show-close="false"
  >
    <div slot="title" class="header">
      <div class="iconfont iconguanbi1" @click="dialogVisible = false"></div>
      <div class="title" v-if="info.title && info.title.length < 19">
        {{ info.title }}
      </div>
      <el-tooltip v-else effect="dark" :content="info.title" placement="bottom">
        <div class="title">
          {{ info.title }}
        </div>
      </el-tooltip>

      <div
        class="time"
        v-if="
          getYear(dateInfo.start, 'yyyy') == getYear(dateInfo.end, 'yyyy') &&
          getYear(dateInfo.start, 'MM-DD') !== getYear(dateInfo.end, 'MM-DD')
        "
      >
        {{ getYear(dateInfo.start, $('ui.userCalendarCalendarDetailsMmmDd')) }}({{ getWeek(dateInfo.start) }})
        <template v-if="info.all_day == 0">{{ $moment(dateInfo.start).format('HH:mm:ss') }}</template>
        - {{ getYear(dateInfo.end, $('ui.userCalendarCalendarDetailsMmmDd')) }}({{ getWeek(dateInfo.end) }})
        <template v-if="info.all_day == 0">{{ getYear(info.end_time, 'HH:mm:ss') }}</template>
      </div>
      <div class="time" v-else-if="getYear(dateInfo.start, 'yyyy') !== getYear(dateInfo.end, 'yyyy')">
        {{ getYear(dateInfo.start, 'yyyy-MM-DD HH:mm:ss') }}
        -
        {{ getYear(dateInfo.end, 'yyyy-MM-DD HH:mm:ss') }}
      </div>
      <div class="time" v-else>
        {{ getYear(dateInfo.start, $('ui.userCalendarCalendarDetailsMmmDd')) }} ({{ getWeek(dateInfo.start) }})
        {{ getYear(dateInfo.start, 'HH:mm') }} -
        {{ getYear(dateInfo.end, 'HH:mm') }}
      </div>
    </div>

    <div class="content" v-loading="loading">
      <el-form>
        <el-form-item :label="$('ui.userCalendarCalendarDetailsOrganizer')">
          <div class="flex">
            <img :src="info.master ? info.master.avatar : ''" alt="" class="img" />
            <span class="name">{{ info.master ? info.master.name : '--' }}</span>
          </div>
        </el-form-item>

        <el-form-item :label="$('ui.userCalendarCalendarDetailsParticipants')" v-if="info.user && info.user.length !== 0">
          <div class="user-flex">
            <div class="flex mr10 finish" v-for="(item, index) in info.user" :key="index">
              <img :src="item.avatar" alt="" class="img" />

              <span class="name">{{ item.name }}</span>
              <i class="el-icon-remove finish-ab" v-if="item.finish == 0"></i>
              <i class="el-icon-error finish-ab" v-if="item.finish == 2"></i>
              <i class="el-icon-success finish-ab" v-if="item.finish == 3"></i>
              <img src="../../../../assets/images/accept.png" alt="" v-if="item.finish == 1" class="finish-ab" />
            </div>
          </div>
        </el-form-item>
        <el-form-item :label="$('ui.userCalendarCalendarDetailsParticipants')" v-else>
          <div class="flex finish">
            <img :src="info.master ? info.master.avatar : ''" alt="" class="img" />
            <img src="../../../../assets/images/accept.png" alt="" class="finish-ab" />
            <span class="name b-color">{{ info.master ? info.master.name : '--' }}</span>
          </div>
        </el-form-item>
        <el-form-item :label="$('ui.customerInvoiceInvoiceViewOrderName')" v-if="info.cid == 3 || info.cid == 4">
          <span class="name b-color cursor" @click="openContract()">{{ info.linkName }}</span>
        </el-form-item>
        <el-form-item :label="$('ui.customerDetailsCustomerName')" v-if="info.cid == 2">
          <span class="name b-color cursor" @click="openCustomer">{{ info.linkName }}</span>
        </el-form-item>
        <el-form-item :label="$('ui.userCalendarCalendarDetailsScheduleDescription')">
          <div v-if="info.content" class="name" v-html="info.content"></div>
          <div class="name" v-else>--</div>
        </el-form-item>
        <el-form-item :label="$('ui.userCalendarAddTodoScheduleType')">
          <span class="name b-color">{{ info.type ? info.type.name : '--' }}</span>
        </el-form-item>
        <el-form-item :label="$('ui.userCalendarAddTodoReminderTime')">
          <span v-if="[2, 3, 4, 5].includes(info.cid)"
            >{{ $moment(info.remind.remind_day).format($('ui.userCalendarCalendarDetailsMmmDd')) }}
            {{ info.remind ? info.remind.remind_time : '09:00:00' }}</span
          >
          <span class="name" v-else>{{ info.remindInfo ? info.remindInfo.text : '--' }}</span>
        </el-form-item>
      </el-form>

      <div class="splitLine mt20" v-if="commentList.length !== 0" />
      <!-- 评论回复功能 -->
      <div class="comment" v-if="commentList.length !== 0">
        {{ $("ui.userCalendarCalendarDetailsScheduleComments") }}
        <comment-list :commentList="commentList" @replyFn="replyFn" @commentDel="commentDel"></comment-list>
      </div>
    </div>

    <!-- 底部编辑删除按钮 -->
    <div class="mt20 footer" v-if="infoId == userId">
      <el-button plain size="small" style="width: 100%" @click="onDelete(info)">{{ $("ui.chatIndexDelete") }}</el-button>
      <el-button
        plain
        v-if="info.cid !== 2 && info.cid !== 3 && info.cid !== 4 && info.cid !== 5"
        size="small"
        style="width: 100%"
        @click="onEdit(info)"
        >{{ $("ui.formCommonOaLogEdit") }}</el-button
      >
      <el-button
        size="small"
        :class="info.finish == 3 ? 'primary' : 'btn'"
        style="width: 100%"
        @click="putStatus('完成', 3)"
        v-if="isShow(info)"
        >{{ info.finish == 3 ? $('ui.customerWeChatMassMassDetailsCompleted') : $('ui.customerTargetStatisticsIndexCompleted') }}</el-button
      >

      <el-tooltip class="item" effect="dark" :content="$('ui.developModuleCheckDrawerComments')" placement="top">
        <img src="../../../../assets/images/comment.png" @click="openComment" alt="" class="commentImg" />
      </el-tooltip>
    </div>

    <!-- 底部状态按钮 -->
    <div class="mt20 footer" v-else>
      <el-button
        size="small"
        :class="info.finish == 1 ? 'success' : 'btn'"
        style="width: 148px"
        @click="putStatus('已接受', 1)"
        >{{ info.finish == 1 ? $('ui.userCalendarCalendarDetailsAccepted') : $('ui.userCalendarCalendarDetailsAccept') }}</el-button
      >
      <el-button
        size="small"
        :class="info.finish == 2 ? 'danger' : 'btn'"
        style="width: 148px"
        @click="putStatus('拒绝', 2)"
        >{{ info.finish == 2 ? $('ui.userExamineExamineRejected') : $('ui.settingEnterpriseUpgradeIndexRefuse') }}</el-button
      >

      <el-button
        size="small"
        :class="info.finish == 0 ? 'warning' : 'btn'"
        style="width: 148px"
        @click="putStatus('待定', 0)"
        >{{ info.finish == 0 ? $('ui.userCalendarCalendarDetailsTentative') : $('ui.userCalendarCalendarDetailsTentative2') }}</el-button
      >
      <el-button
        size="small"
        :class="info.finish == 3 ? 'primary' : 'btn'"
        style="width: 148px"
        @click="putStatus('完成', 3)"
        >{{ info.finish == 3 ? $('ui.customerWeChatMassMassDetailsCompleted') : $('ui.customerTargetStatisticsIndexCompleted') }}</el-button
      >
      <img src="../../../../assets/images/comment.png" @click="openComment" alt="" class="commentImg" />
    </div>
    <div class="mt20 footer" v-if="false">
      <el-button type="text" size="small" style="width: 148px; font-size: 16px" @click="onDelete(info)"
        >{{ $("ui.userCalendarCalendarDetailsJoinSchedule") }}</el-button
      >
    </div>
  </el-dialog>

  <!-- 删除弹窗 -->
  <el-dialog
    :title="deleteText == 1 ? $('ui.userCalendarCalendarDetailsDeleteRecurringSchedule') : $('ui.userCalendarCalendarDetailsEditRecurringSchedule')"
    :visible.sync="deleteVisible"
    width="20%"
    top="20vh"
    :before-close="handleClose"
  >
    <el-radio-group v-model="type" class="radio">
      <el-radio label="0">{{ $("ui.userCalendarCalendarDetailsThisSchedule") }}</el-radio>
      <el-radio label="1">{{ $("ui.userCalendarCalendarDetailsThisAndFollowingSchedules") }}</el-radio>
      <el-radio label="2">{{ $("ui.userCalendarCalendarDetailsAllSchedules") }}</el-radio>
    </el-radio-group>

    <span slot="footer" class="dialog-footer">
      <el-button size="small" @click="returnFn">{{ $("ui.customerProductAddProductResponse") }}</el-button>
      <el-button size="small" type="danger" @click="deleteFn">{{ deleteText == 1 ? $('ui.chatIndexDelete') : $('ui.formCommonOaLogEdit') }}</el-button>
    </span>
  </el-dialog>
  <message-handle-popup ref="messageHandlePopup" :detail="detail"></message-handle-popup>
  <!-- 跟进弹窗 -->
  <el-dialog :title="$('ui.customerClueIndexAddFollowUpRecord')" class="record" :visible.sync="dialogShow" width="40%">
    <recordUpload :form-info="formInfo" @change="recordChange"></recordUpload>
  </el-dialog>
  <!-- 评论回复弹窗 -->
  <el-dialog :visible.sync="commentShow" top="25vh" :title="commentTitle" width="400px" :close-on-click-modal="false">
    <div class="mt14 comment-box">
      <el-input type="textarea" autosize :placeholder="$('ui.chatModelFormEnterContent')" resize="none" maxlength="256" v-model="textarea">
      </el-input>

      <div class="uploadBox" v-if="uploadList.length > 0">
        <el-tag v-for="(item, i) in uploadList" :key="i" class="mt10 mr10" type="info">
          <div class="info">
            <i class="el-icon-error" @click="deleteTag(item)"></i>
            <img v-if="toSrc(item.real_name) === 1" alt="" class="img" src="@/assets/images/doc.png" />
            <img v-else-if="toSrc(item.real_name) === 2" alt="" class="img" src="@/assets/images/ppt.png" />
            <img v-else-if="toSrc(item.real_name) === 3" alt="" class="img" src="@/assets/images/xls.png" />
            <img v-else-if="toSrc(item.real_name) === 4" alt="" class="img" src="@/assets/images/record2.png" />
            <img v-else-if="toSrc(item.real_name) === 5" alt="" class="img" src="@/assets/images/pdf.png" />
            <span class="text-info line1">{{ item.real_name }}</span>
          </div>
        </el-tag>
      </div>
    </div>
    <span slot="footer" class="dialog-footer flex-between">
      <el-upload
        :headers="myHeaders"
        :http-request="uploadServerLog"
        :show-file-list="false"
        action="##"
        class="mr10 upload-real"
      >
        <div v-if="!percentShow" class="addText"><span class="iconfont iconfujian"></span> {{ $("ui.customerWeChatMassMaterialContentAddAttachment") }}</div>
        <div v-else class="addText">
          <img alt="" class="l_gif" src="@/assets/images/loading.gif" />
        </div>
      </el-upload>
      <div>
        <el-button size="small" @click="commentCancel">{{ $("ui.xmindEditorNodeHyperlinkCancel") }}</el-button>
        <el-button type="primary" @click="commentSubmit">{{ $("ui.xmindEditorNodeHyperlinkOk") }}</el-button>
      </div>
    </span>
  </el-dialog>

  <!-- 客户付款 -->
  <edit-examine ref="editExamine" :parameterData="parameterData" @isOk="isOk"></edit-examine>
</div>
</template>
<script>
import { $ } from '@/lang'
// 集中导入用户相关的日程API
import {
  scheduleInfoApi,
  scheduleDeleteApi,
  scheduleStatusApi,
  scheduleReplySaveApi,
  scheduleReplyListApi,
  scheduleReplyDelApi
} from '@/api/user'
import { uploader } from '@/utils/uploadCloud'
import file from '@/utils/file'
import { toSrcFn } from '@/utils/format'
import Vue from 'vue'
Vue.use(file)

// 导入企业相关的客户提醒详情API
import { clientRemindDetailApi } from '@/api/enterprise'
import { getStorageJson } from '@/utils/storage'

// 导入配置相关的规则审批API
import { configRuleApproveApi } from '@/api/config'

// 导入提示工具类
import Tips from '@/utils/tips'

// 导入日期格式化工具函数
import { toGetWeek } from '@/utils/format'

export default {
  name: 'CrmebOaEntCalendarDetails',
  components: {
    editExamine: () => import('@/views/user/examine/components/editExamine'),
    messageHandlePopup: () => import('@/components/common/messageHandlePopup'),
    recordUpload: () => import('@/views/customer/list/components/recordUpload'),
    commentList: () => import('./commentList')
  },

  data() {
    return {
      dialogVisible: false,
      deleteVisible: false,
      dialogShow: false,
      commentShow: false,
      loading: false,
      textarea: '',
      commentList: [],
      parameterData: {
        contract_id: '',
        customer_id: '',
        invoice_id: '',
        bill_id: '',
        remind_id: ''
      },
      uploadList: [],
      myHeaders: {
        authorization: 'Bearer ' + localStorage.getItem('token')
      },

      percentShow: false,
      completionStatus: '',
      deleteText: 1,
      dateInfo: {},
      reply_id: '', // 关联评价id
      extendedProps: {},
      type: '0',
      userId: '',
      info: {},
      detail: {},
      infoId: '',
      commentTitle: '评论',
      formInfo: {
        avatar: '',
        type: 'add',
        show: 1,
        data: {},
        follow_id: 0
      },
      buildData: {}
    }
  },

  mounted() {
    this.type = '0'
  },
  methods: {
    // 格式化日期
    getYear(val, text) {
      return this.$moment(val).format(text)
    },
    isOk() {
      this.completeFn(this.completionStatus)
    },

    openBox(data) {
      this.dialogVisible = true
      this.extendedProps = data
      if (data) {
        this.dateInfo = {
          start: data.start_time,
          end: data.end_time
        }
        this.getInfo(data)
        this.getReplyList(data)
        this.getWeek()
        this.userId = getStorageJson('userInfo', {}).id
      }
    },

    async getConfigApprove() {
      const result = await configRuleApproveApi(0)
      this.buildData = result.data
    },
    // 删除附件
    deleteTag(row) {
      this.uploadList = this.uploadList.filter((item) => {
        return item.id !== row.id
      })
    },
    // 判断上传的文件格式文件是否有无图片，无图则为默认
    toSrc(e) {
      return toSrcFn(e)
    },

    // 上传文件方法
    uploadServerLog(params) {
      this.percentShow = true
      const file = params.file
      let options = {}
      uploader(file, 0, options)
        .then((res) => {
          // 获取上传文件渲染页面
          if (res.data) {
            this.uploadList.push({
              id: res.data.attach_id,
              real_name: res.data.name
            })
            this.percentShow = false
          }
        })
        .catch((err) => {
          this.percentShow = false
        })
      this.percentShow = false
    },
    getInfo(data) {
      this.loading = true
      const info = {
        end_time: data.end_time,
        start_time: data.start_time
      }
      scheduleInfoApi(data.itemId, info)
        .then((res) => {
          this.loading = false
          const userId = this.userId
          const masterId = res.data.master.id
          const taskMap = new Map()
          res.data.task.forEach((li) => {
            taskMap.set(li.uid, li.status)
          })
          res.data.user.forEach((item) => {
            item.finish = 6
            if (item.id === masterId) {
              item.finish = 1
            } else if (taskMap.has(item.id)) {
              item.finish = taskMap.get(item.id)
            }
          })
          this.info = res.data
          if ([3, 4].includes(this.info.type.id)) {
            this.getConfigApprove()
          }
          this.infoId = masterId
        })
        .catch((error) => {
          console.error($('legacyScript.failedToRetrieveScheduleInformation'), error)
          this.loading = false
        })
    },
    isShow(info) {
      let ids = []
      if (info.user && info.user.length > 0) {
        info.user.map((item) => {
          ids.push(item.id)
        })
        return ids.includes(info.master.id)
      }
    },

    // 打开评论
    openComment() {
      this.commentTitle = '评论'
      this.commentShow = true
    },
    commentCancel() {
      this.commentShow = false
      this.textarea = ''
      this.uploadList = []
    },
    // 删除评论
    async commentDel(data) {
      await Tips.confirm({ message: $('legacyScript.areYouSureYouWantToDeleteThisComment') })
      await scheduleReplyDelApi(data.id)
      await this.getReplyList(this.extendedProps)
    },

    // 回复
    replyFn(data, row) {
      let name = row ? row.from_user.name : data.from_user.name
      this.commentTitle = '回复' + '（' + name + '）'
      this.reply_id = data.id
      if (row) {
        this.to_uid = row.from_user.id
      } else {
        this.to_uid = data.from_user.id
      }
      this.commentShow = true
    },

    // 提交评论/回复
    async commentSubmit() {
      if (this.textarea == '') {
        return this.$message.error($('legacyScript.pleaseEnterCommentsContent'))
      }
      const ids = []
      if (this.uploadList.length > 0) {
        this.uploadList.map((res) => {
          ids.push(res.id)
        })
      }

      let data = {
        schedule_id: this.info.id,
        reply_id: this.reply_id,
        to_uid: this.to_uid,
        content: this.textarea,
        start: this.dateInfo.start,
        end: this.dateInfo.end,
        files: ids
      }
      await scheduleReplySaveApi(data)
      await this.getReplyList(this.extendedProps)
      await this.commentCancel()
      this.uploadList = []
    },

    // 获取评论列表数据
    async getReplyList(data) {
      let newData = {
        time: data.start_time + ' - ' + data.end_time,
        schedule_id: data.itemId
      }
      const result = await scheduleReplyListApi(newData)
      this.commentList = result.data
    },

    // 打开订单详情
    async openContract() {
      this.detail = {
        template_type: 'dealt_money_work',
        link_id: this.info.link_id
      }
      await this.$nextTick()
      this.$refs.messageHandlePopup.openMessage(this.info)
    },
    // 打开客户详情
    async openCustomer() {
      this.detail = {
        template_type: 'dealt_client_work',
        link_id: this.info.link_id
      }
      await this.$nextTick()
      this.$refs.messageHandlePopup.openMessage(this.info)
    },
    recordChange(val) {
      this.dialogVisible = false
      if (!val) {
        this.dialogShow = false
      }
      this.$emit('deleteFn')
    },

    // 修改状态
    putStatus(text, status) {
      this.completionStatus = status
      if (this.extendedProps.cid_value == 3 || this.extendedProps.cid_value == 4) {
        if (this.extendedProps.finish == 3) return false
        let id =
          this.extendedProps.bill_id.bill_id !== 0
            ? this.extendedProps.bill_id.bill_id
            : this.extendedProps.bill_id.remind_id
        if (this.extendedProps.bill_id.bill_id == 0) {
          clientRemindDetailApi(id).then((res) => {
            this.parameterData.customer_id = res.data.eid
            this.parameterData.contract_id = res.data.cid
            this.parameterData.remind_id = id
            if (this.extendedProps.cid_value == 4) {
              this.$refs.editExamine.openBox(
                this.buildData.contract_refund_switch,
                res.data.cid,
                'contract_refund_switch'
              )
            } else {
              this.$refs.editExamine.openBox(
                this.buildData.contract_renew_switch,
                res.data.cid,
                'contract_renew_switch'
              )
            }
          })
        }
      } else if (this.extendedProps.cid_value == 2) {
        if (this.extendedProps.finish == 3) return false

        this.formInfo.follow_id = this.extendedProps.bill_id ? this.extendedProps.bill_id.follow_id : 0
        this.formInfo.data.eid = this.extendedProps.link_id
        this.formInfo.data.id = this.extendedProps.link_id
        this.formInfo.link_type = 'customer'
        this.dialogShow = true
      } else {
        this.completeFn(status)
      }
    },

    // 任务完成
    completeFn(status) {
      let data = {
        status,
        start: this.dateInfo.start,
        end: this.dateInfo.end
      }
      scheduleStatusApi(this.info.id, data)
        .then((res) => {
          this.dialogVisible = false
          this.$emit('deleteFn')
        })
        .catch((err) => {})
    },
    // 删除弹窗
    onDelete() {
      this.deleteText = 1
      if (this.info.period == 0) {
        this.$modalSure('确认删除当前日程').then(() => {
          let data = {
            start: this.dateInfo.start,
            end: this.dateInfo.end,
            type: '2'
          }
          this.scheduleDeleteFn(data)
          this.dialogVisible = false
        })
      } else {
        this.dialogVisible = false
        setTimeout(() => {
          this.deleteVisible = true
        }, 300)
      }
    },

    handleClose() {
      this.deleteVisible = false
      this.type = '0'
    },

    // 编辑弹窗
    onEdit() {
      this.deleteText = 2
      if (this.info.period == 0) {
        this.dialogVisible = false
        let date = {
          start: this.dateInfo.start,
          end: this.dateInfo.end
        }
        this.type = '2'
        this.$emit('editFn', this.info.id, this.type, date)
      } else {
        this.dialogVisible = false
        setTimeout(() => {
          this.deleteVisible = true
        }, 300)
      }
    },

    returnFn() {
      this.deleteVisible = false
      this.type = '0'
      setTimeout(() => {
        this.dialogVisible = true
      }, 300)
    },

    // 删除
    deleteFn() {
      let data = {
        start: this.dateInfo.start,
        end: this.dateInfo.end,
        type: this.type
      }

      if (this.deleteText == 1) {
        this.scheduleDeleteFn(data)
      } else {
        let date = {
          start: this.dateInfo.start,
          end: this.dateInfo.end
        }

        this.deleteVisible = false
        this.$emit('editFn', this.info.id, this.type, date)
      }
    },

    async scheduleDeleteFn(data) {
      await scheduleDeleteApi(this.info.id, data)
      this.deleteVisible = false
      this.type = '0'
      await this.$emit('deleteFn')
    },

    getWeek(date) {
      // 参数时间戳

      if (this.$moment(date).format('YYYY-MM-DD') == this.$moment().format('YYYY-MM-DD')) {
        return '今天'
      }
      return toGetWeek(date)
    }
  }
}
</script>

<style lang="scss" scoped>
.divBox {
  margin: 0;
  padding: 0;
}
.record {
  ::v-deep .el-dialog__body {
    padding: 20px 20px 30px 20px;
  }
}
.commentImg {
  cursor: pointer;
  display: block;
  width: 20px;
  height: 20px;
  margin-left: 20px;
}
.comment {
  padding-top: 20px;
  font-size: 13px;
  font-family: PingFang SC-Regular, PingFang SC;
  font-weight: 400;
  color: #909399;
}
.cursor {
  cursor: pointer;
}
.comment-box {
  width: 100%;
  min-height: 134px;
  border: 1px solid #dcdfe6;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border-radius: 4px;
  ::v-deep .el-textarea__inner {
    min-height: 96px;
    border: none;
    font-size: 12px;
  }
  .info {
    height: 28px;
    display: flex;
    align-items: center;
    // padding: 0 8px;
    position: relative;
    margin-bottom: 10px;
    .el-icon-error {
      color: #ccc;
      font-size: 13px;
      position: absolute;
      top: -6px;
      right: -10px;
    }
    .text-info {
      display: inline-block;
      max-width: 180px;
    }
  }
  .img {
    width: 20px;
    height: 20px;
    margin-right: 4px;
    vertical-align: middle;
  }
  .uploadBox {
    margin: 14px;
  }
}
.addText {
  font-size: 12px;
  font-family: PingFang SC-Regular, PingFang SC;
  font-weight: 400;
  color: #303133;
  .iconfont {
    color: #303133;
    font-size: 12px;
  }
}
.header {
  width: 550px;
  height: 106px;
  background: url('../../../../assets/images/calender.png') no-repeat;
  background-size: 100%;
  color: #fff;
  padding: 15px 20px;

  .iconguanbi1 {
    cursor: pointer;
    font-size: 12px;
    text-align: right;
  }
  .title {
    width: 100%;
    margin-top: 15px;
    font-size: 18px;
    font-family: PingFang SC-Medium, PingFang SC;
    font-weight: 500;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .time {
    font-size: 15px;
    font-family: PingFang SC-Regular, PingFang SC;
    line-height: 15px;
    font-weight: 400;
    color: #ffffff;
    margin-top: 10px;
  }
}
.content {
  max-height: 450px;
  overflow-y: auto;
  overflow-x: hidden;
  scrollbar-width: none; /* firefox */
  -ms-overflow-style: none; /* IE 10+ */
  ::v-deep .el-form-item__label {
    font-size: 13px;
    font-family: PingFang SC-Regular, PingFang SC;
    font-weight: 400;
    color: #909399;
  }
  .user-flex {
    display: flex;
    flex-wrap: wrap;
  }
  .el-form-item {
    margin-bottom: 0;
  }
  .flex {
    display: flex;
    align-items: center;
  }
  .finish {
    position: relative;
    .finish-ab {
      width: 10px;
      height: 10px;
      position: absolute;
      bottom: 4px;
      left: 15px;
    }
  }
  .img {
    position: relative;
    display: block;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-right: 4px;
  }

  .name {
    font-size: 13px;
    font-family: PingFang SC-Regular, PingFang SC;
    font-weight: 400;
    color: #303133;
  }
  ::v-deep p {
    display: block;
    margin: 0;
    padding: 0;
    margin-block-start: 0;
    margin-block-end: 0;
    img {
      width: 80%;
    }
  }
}
::v-deep .el-button--warning.is-plain:hover {
  background: transparent !important;
  color: transparent !important;
}
::v-deep .el-dialog {
  border-radius: 8px;
}
::v-deep .content::-webkit-scrollbar {
  height: 0;
  width: 0;
}
.footer {
  margin-bottom: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.radio {
  margin-left: 15px;
  margin-top: 14px;
  display: flex;
  flex-direction: column;
  .el-radio {
    margin-bottom: 10px;
  }
}

.box {
  ::v-deep .el-dialog {
    border-radius: 12px 12px 10px 10px;
  }

  ::v-deep .el-dialog__header {
    padding: 0 !important;
  }
}

::v-deep .el-dialog__body {
  // padding-left: 10px;
}
::v-deep .fileItem {
  background: #fff;
}
.b-color {
  color: #1890ff !important;
}
.el-icon-remove {
  font-size: 10px;
  color: #ff9900;
  background-color: #fff;
  border-radius: 50%;
}
.el-icon-success {
  font-size: 10px;
  color: #1890ff;
  background-color: #fff;
  border-radius: 50%;
}
.el-icon-error {
  font-size: 10px;
  color: #ed4014;
  background-color: #fff;
  border-radius: 50%;
}
.warning {
  color: #f90;
  background: #fff5e6;
  border-color: #ffd699;
}
.btn {
  border: 1px solid #dcdfe6;
  border-color: #dcdfe6;
  color: #606266;
}
.success {
  background: #e8f9f0;
  border-color: #a3e5c4;
  color: #19be6b;
}
.danger {
  color: #ed4014;
  background-color: #fdece8;
  border-color: #f8b3a1;
}
.primary {
  color: #1890ff;
  background: #e8f4ff;
  border-color: #a3d3ff;
}

::v-deep .comment-dialog .el-dialog {
  position: absolute;
  left: 60%;
  top: 10%;
}
</style>
