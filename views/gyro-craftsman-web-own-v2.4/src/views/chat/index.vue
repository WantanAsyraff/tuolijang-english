import { $ } from '@/lang'
<template>
<div class="divBox">
  <el-card class="normal-page">
    <formBox
      :title="$('ui.chatIndexApplicationList')"
      :total="total"
      :search="search"
      :btnText="$('ui.chatIndexCreateApplication')"
      :isViewSearch="false"
      :sortSearch="false"
      @confirmData="confirmData"
      @addDataFn="addDataFn"
    ></formBox>
    <div id="content-box " class="mt20" v-loading="loading">
      <default-page v-if="listData.length == 0" :index="14" :min-height="520" />
      <div class="list" id="listBox" ref="container">
        <div class="item" id="item" v-for="item in listData" :key="item.id">
          <div class="flex">
            <img v-if="item.pic" :src="item.pic" alt="" class="img" />
            <img v-else src="../../assets/images/ai.png" alt="" class="img" />
            <div class="flex-column" style="width: 100%">
              <div class="title over-text">{{ item.name }}</div>
              <span class="name">{{ $("ui.chatIndexCreator") }}{{ item.user ? item.user.name : '--' }} </span>
            </div>
            <div class="status" v-if="item.status == 1">{{ $("ui.chatIndexRelease") }}</div>
            <div class="status" v-else>{{ $("ui.chatIndexUnreleased") }}</div>
          </div>
          <div class="over-text2 content">
            {{ item.info }}
          </div>
          <div class="operate flex flex-center">
            <span @click="handleOpenChatApp(item.id)"> {{ $("ui.chatIndexUse") }}</span>
            <template v-if="item.auth">
              <el-divider direction="vertical"></el-divider>
              <span @click="handleEdit(item)"> {{ $("ui.chatIndexSettings") }}</span>
              <el-divider direction="vertical"></el-divider>
              <span @click="deleteFn(item.id)"> {{ $("ui.chatIndexDelete") }}</span>
            </template>
          </div>
        </div>
      </div>
      <el-pagination
        :current-page="where.page"
        :page-size="where.limit"
        :total="total"
        class="page-fixed"
        layout="total, prev, pager, next, jumper"
        @current-change="pageChange"
      />
    </div>
  </el-card>
  <oa-dialog
    ref="oaDialog"
    @submit="addSubmit"
    :fromData="fromData"
    :formConfig="formConfig"
    :formRules="formRules"
    :formDataInit="formDataInit"
  />
</div>
</template>
<script>
import { roterPre } from '@/settings'
import formBox from '@/components/common/oaFromBox'
import oaDialog from '@/components/form-common/dialog-form'
import defaultPage from '@/components/common/defaultPage'
import { getApplicationsListApi, chatSaveApplicationsApi, delApplicationsApi } from '@/api/chatAi'
import { AiEmbeddedManager } from '@/libs/ai'
import { getStorageJson } from '@/utils/storage'

export default {
  name: 'chat',
  components: { formBox, oaDialog, defaultPage },
  props: {},
  data() {
    return {
      loading: false,
      search: [
        {
          form_value: 'input',
          field_name: '应用名称',
          field_name_en: 'name'
        }
      ],
      fromData: {
        title: $('legacyScript.createApplication'),
        width: '650px',
        type: 'add',
        btnText: '创建'
      },
      formConfig: [
        {
          type: 'input',
          label: $('legacyScript.applicationName'),
          placeholder: $('legacyScript.enterApplicationName'),
          key: 'name',
          maxlength: 20,
          showWordLimit: true
        },
        {
          type: 'textarea',
          label: $('legacyScript.applicationDescription'),
          maxlength: 100,
          showWordLimit: true,
          placeholder:
            $('legacyScript.youAreAnEnterpriseManagementAssistantThatAnalyzesChallengesAnd'),
          key: 'info',
          height: '120px'
        }
      ],
      formDataInit: {
        name: '',
        info: ''
      },
      formRules: {
        name: [{ required: true, message: $('legacyScript.enterApplicationName'), trigger: 'blur' }]
      },
      total: 0,
      where: {
        page: 1,
        limit: 15
      },
      listData: []
    }
  },
  created() {
    this.initAiEmbedded()
  },
  mounted() {
    this.getLimit()
    window.addEventListener('resize', this.getLimit)
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.getLimit)
    if (this.chatInstance) {
      this.chatInstance.destroy()
    }
  },
  methods: {
    initAiEmbedded() {
      this.chatInstance = AiEmbeddedManager.createPreviewClient()
      this.chatInstance.ensureReady(this.$store.getters.token, {
        scene: 'app-preview-use',
        appId: 0,
        defaultShow: false
      })
    },
    handleOpenChatApp(appId) {
      this.chatInstance?.openApp(appId)
    },
    addSubmit(data) {
      // 应用默认值
      let user = getStorageJson('userInfo', {})
      let sitedata = getStorageJson('sitedata', {})
      data.pic = sitedata.site_logo || ''
      data.data_arrange_text =
        '根据用户提出的内容，整理数据！如果超过1条数据，请用表格展示！如果为一条数据请分析数据意思用语意化输出内容'
      data.edit = user.id ? [user.id] : []
      data.auth_ids = user.id ? [user.id] : []
      data.json = [
        {
          name: 'temperature',
          filed: '采样温度',
          value: '0.95',
          message: $('legacyScript.range0To2HigherValuesEG08')
        },
        {
          name: 'max_tokens',
          filed: '最大tokens',
          value: '2048',
          message:
            $('legacyScript.maximumTokensForModelGeneratedCompletionsPerRequestTotalInput')
        }
      ]
      chatSaveApplicationsApi(data).then((res) => {
        if (res.status == '200') {
          this.$router.push(`${roterPre}/chat/setting?id=${res.data.id}`)
          this.$refs.oaDialog.handleClose()
        }
      })
    },

    handleEdit(item) {
      this.$router.push(`${roterPre}/chat/setting?id=${item.id}`)
    },
    getList(val) {
      if (val) {
        this.where.page = 1
      }
      this.loading = true
      getApplicationsListApi(this.where).then((res) => {
        this.listData = res.data.list
        this.total = res.data.count
        this.loading = false
      })
    },
    confirmData(val) {
      if (val === 'reset') {
        this.where.name = ''
      } else {
        this.where.name = val.name
      }
      this.getList(1)
    },
    pageChange(val) {
      this.where.page = val
      this.getList()
    },
    addDataFn() {
      this.$refs.oaDialog.openBox()
    },
    // 删除
    deleteFn(id) {
      this.$modalSure('删除后该应用将不再提供服务，请谨慎操作').then(async () => {
        await delApplicationsApi(id)
        let totalPage = Math.ceil((this.total - 1) / this.where.limit)
        let currentPage = this.where.page > totalPage ? totalPage : this.where.page
        this.where.page = currentPage < 1 ? 1 : currentPage
        await this.getList()
      })
    },
    getLimit() {
      const windowHeight = document.documentElement.clientHeight - 287
      const winWidth = document.getElementById('listBox').offsetWidth
      const col = Math.floor((winWidth + 15) / (367 + 15))
      const row = Math.floor((windowHeight + 15) / (153 + 15))
      this.where.limit = col * row
      this.getList()
    }
  }
}
</script>
<style scoped lang="scss">
.list {
  box-sizing: border-box; /* 防止padding影响高度 */
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(367px, 0.33fr));
  grid-auto-rows: minmax(153px, auto); /* 行高自适应内容，最小150px */
  gap: 15px; /* 卡片间距 */
}
.item {
  border: 1px solid #dcdfe6;
  border-radius: 10px 10px 10px 10px;
  padding: 14px;
  font-family: PingFang SC, PingFang SC;
  position: relative;
  .status {
    position: absolute;
    right: 14px;
    width: 48px;
    height: 20px;
    background: rgba(24, 144, 255, 0.05);
    border-radius: 3px 3px 3px 3px;
    border: 1px solid #1890ff;
    display: flex;
    align-items: center;

    justify-content: center;
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 12px;
    color: #1890ff;
  }
  .content {
    margin-top: 10px;
    font-weight: 400;
    font-size: 14px;
    color: #606266;
    line-height: 20px;
  }
  .operate {
    position: absolute;
    bottom: 14px;
    font-weight: 400;
    font-size: 13px;
    color: #1890ff;
    cursor: pointer;
  }
  .img {
    display: block;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    margin-right: 8px;
  }
  .title {
    width: 80%;
    font-weight: 500;
    font-size: 14px;
    color: #303133;
  }
  .name {
    margin-top: 3px;
    font-weight: 400;
    font-size: 12px;
    color: #606266;
  }
}
</style>
