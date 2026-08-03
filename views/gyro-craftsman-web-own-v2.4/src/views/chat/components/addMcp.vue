<template>
  <div>
    <el-dialog :visible.sync="show" width="854px" :show-close="false">
      <div slot="title" class="flex-between">
        <span>{{ $ts("选择MCP") }}</span>
        <span class="el-icon-close" @click="handleClose"></span>
      </div>
      <div class="search-box">
        <div class="flex lh-center">
          <span class="shrink mr10">{{ $ts("共") }}{{ total }}{{ $ts("项") }}</span>
          <el-input
            v-model="where.name"
            size="small"
            :placeholder='$ts("请输入知识库名称")'
            prefix-icon="el-icon-search"
            @change="handleSearch"
          />
          <div class="reset ml10 shrink" @click="resetSearch()"><i class="iconfont iconqingchu"></i></div>
        </div>
        <el-button @click="handleCustom" type="primary" icon="el-icon-plus" size="small">{{ $ts("自定义创建") }}</el-button>
      </div>
      <div class="box-content">
        <div class="box-list">
          <div
            class="box"
            v-for="(item, index) in list"
            :key="index"
            :class="{ active: isActive === index }"
            @click="handleBoxClick(index)"
          >
            <div class="check-box">
              <span
                @click.stop="handleSelect(item, 0)"
                class="iconfont icontongyonggouxuan-01"
                v-if="selectIds.includes(item.id)"
              />
              <span @click.stop="handleSelect(item, 1)" class="iconfont iconweigouxuan" v-else />
            </div>
            <div style="line-height: 1.8; flex: 1">
              <span class="title"
                >{{ item.name }}
                <span class="tip1" v-if="item.is_default == 1">{{ $ts("系统") }}</span>
                <span class="tip2" v-if="item.is_default != 1">{{ $ts("自定义") }}</span>
              </span>
              <span class="over-text1 mr24">
                {{ item.info }}
              </span>
            </div>

            <div class="edit-box">
              <span class="el-icon-edit-outline right" :title='$ts("编辑")' @click.stop="handleEdit(item)" />
              <span
                class="el-icon-delete right"
                v-if="item.is_default != 1"
                :title='$ts("删除")'
                @click.stop="handleDelete(item.id)"
              />
            </div>
          </div>
        </div>
      </div>

      <div>
        <el-pagination
          :current-page="where.page"
          :page-size="where.limit"
          :page-sizes="[10, 20, 30]"
          :total="total"
          layout="total, sizes,prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="pageChange"
        >
        </el-pagination>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="handleClose" size="small">{{ $ts("取消") }}</el-button>
        <el-button type="primary" @click="submitFn" size="small">{{ $ts("确定") }}</el-button>
      </span>
    </el-dialog>

    <!-- 自定义mcp -->
    <oa-dialog :fromData="fromData" ref="oaDialog" @submit="submit">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
        <el-form-item :label='$ts("功能名称：")' prop="name">
          <el-input
            v-model="ruleForm.name"
            size="small"
            maxlength="20"
            show-word-limit
            :placeholder='$ts("请输入名称")'
          ></el-input>
        </el-form-item>
        <el-form-item :label='$ts("功能简介：")' prop="info">
          <el-input
            v-model="ruleForm.info"
            type="textarea"
            :placeholder='$ts("请输入功能简介")'
            maxlength="200"
            show-word-limit
          ></el-input>
        </el-form-item>
        <el-form-item :label='$ts("JSON配置：")' prop="config_json">
          <code-editor :mode="'json'" :readonly="false" v-model="ruleForm.config_json"></code-editor>
          <div>{{ $ts("JSON配置数据需按照示例填写") }}</div>
        </el-form-item>
      </el-form>
    </oa-dialog>
  </div>
</template>
<script>
import i18n from '@/lang'
import { getMcpListApi, saveMcpServiceApi, getMcpServiceApi, putMcpServiceApi, delMcpServiceApi } from '@/api/chatAi'
import oaDialog from '@/components/form-common/dialog-form'
import CodeEditor from '@/components/code-editor/index'

export default {
  name: 'AddMcp',
  components: {
    oaDialog,
    CodeEditor
  },
  data() {
    return {
      show: false,
      selectIds: [],
      selectList: [],
      isActive: -1,
      ruleForm: {
        name: '',
        info: '',
        app_id: '',
        config_json: ''
      },
      id: 0,
      total: 0,
      where: {
        page: 1,
        limit: 10,
        name: '',
        app_id: ''
      },
      rules: {
        name: [{ required: true, message: i18n.t('legacyScript.enterFunctionName'), trigger: 'blur' }],
        info: [{ required: true, message: i18n.t('legacyScript.enterFunctionDescription'), trigger: 'blur' }],
        config_json: [{ required: true, message: i18n.t('legacyScript.enterJSONConfig'), trigger: 'blur' }]
      },
      fromData: {
        width: '650px',
        title: i18n.t('legacyScript.customMCP'),
        btnText: '确定',
        labelWidth: '90px',
        type: 'slot'
      },
      list: []
    }
  },
  mounted() {},
  methods: {
    resetSearch() {
      this.where.name = ''
      this.where.page = 1
      this.getList()
    },
    handleSearch() {
      this.where.page = 1
      this.getList()
    },
    openBox(id, data) {
      if (data) {
        this.selectIds = data.map((item) => item.id)
        this.selectList = data
      }
      this.where.app_id = id
      this.ruleForm.app_id = id
      this.show = true
      this.isActive = -1
      this.getList()
    },

    getList() {
      getMcpListApi(this.where).then((res) => {
        this.list = res.data.list
        this.total = res.data.count
      })
    },

    handleSelect(item, type) {
      if (type == 1) {
        this.selectIds.push(item.id)
        this.selectList.push(item)
      } else {
        this.selectIds = this.selectIds.filter((id) => id !== item.id)
        this.selectList = this.selectList.filter((s) => s.id !== item.id)
      }
    },
    handleClose() {
      this.show = false
    },
    handleBoxClick(index) {
      this.isActive = index
    },
    submit() {
      if (!this.$refs.ruleForm) return

      this.$refs.ruleForm.validate(async (valid) => {
        if (!valid) return

        let configJson = this.ruleForm.config_json
        if (configJson) {
          try {
            configJson = typeof configJson === 'string' ? JSON.parse(configJson) : configJson
          } catch (error) {
            this.$message.error(i18n.t('legacyScript.invalidJSONConfigurationFormatPleaseCheckAndTryAgain'))
            return
          }
        }

        const params = {
          ...this.ruleForm,
          config_json: configJson
        }

        const api = this.id ? putMcpServiceApi(this.id, params) : saveMcpServiceApi(params)
        const res = await api

        if (res.status == 200) {
          this.getList()
          this.$refs.oaDialog.handleClose()
        }
      })
    },

    submitFn() {
      if (this.selectList.length == 0) {
        this.$message.error(i18n.t('legacyScript.pleaseSelectTheMCPToAdd'))
        return false
      }
      this.show = false
      this.$emit('mcpSubmit', this.selectList)
    },

    handleSizeChange(val) {
      this.where.limit = val
      this.where.page = 1
      this.getList()
    },
    pageChange(page) {
      this.where.page = page
      this.getList()
    },

    handleEdit(data) {
      this.ruleForm.name = data.name
      this.ruleForm.info = data.info
      let json = JSON.stringify(data.config_json, null, 2)
      this.$set(this.ruleForm, 'config_json', json)
      this.id = data.id
      this.$refs.oaDialog.openBox()
    },

    // 删除
    handleDelete(id) {
      this.$modalSure('确定删除此MCP服务吗').then(async () => {
        delMcpServiceApi(id).then((res) => {
          if (res.status == 200) {
            this.getList()
          }
        })
      })
    },
    handleCustom() {
      let json = JSON.stringify(
        {
          transport: 'sse',
          url: 'http://127.0.0.1:8080/sse',
          headers: {
            Authorization: 'Bearer your-token-123456',
            'X-MCP-Version': '2025-06-18',
            'X-Client-Id': 'my-mcp-client',
            'Cache-Control': 'no-cache',
            'User-Agent': 'MCP-Client/1.0'
          },
          timeout: 3000088
        },
        null,
        2
      )
      this.$set(this.ruleForm, 'config_json', json)
      this.ruleForm.name = ''
      this.ruleForm.info = ''
      this.id = ''
      this.$refs.oaDialog.openBox()
    }
  }
}
</script>
<style lang="scss" scoped>
.search-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.check-box {
  cursor: pointer;
  color: #cccccc;
  font-size: 13px;
  margin-right: 14px;
  .icontongyonggouxuan-01 {
    color: #1890ff;
  }
}
.box-content {
  height: 400px;
  overflow-y: auto;
  scrollbar-width: none; /* firefox */
  -ms-overflow-style: none; /* IE 10+ */
}
.box-list {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;

  .box {
    position: relative;
    width: 400px;
    height: 63px;
    padding: 12px;
    padding-right: 24px;
    border-radius: 4px;
    border: 1px solid #dcdfe6;
    display: flex;
    align-items: center;
    font-size: 12px;
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    color: #909399 !important;
    .tip1 {
      height: 16px;
      line-height: 16px;
      padding: 0 4px;
      margin-left: 4px;
      border-radius: 2px 2px 2px 2px;
      background: rgba(24, 144, 255, 0.15);
      font-size: 10px;
      color: #1890ff;
    }
    .tip2 {
      height: 16px;
      line-height: 16px;
      padding: 0 4px;
      margin-left: 4px;
      border-radius: 2px 2px 2px 2px;
      background: rgba(255, 153, 0, 0.15);
      font-size: 10px;
      color: #ff9900;
    }
    .title {
      font-weight: 500;
      font-size: 13px;
      color: #303133;
    }
    .right {
      display: inline-block;
      cursor: pointer;
      font-size: 17px;
      color: #909399;
      width: 17px;
    }
  }
}

.edit-box {
  position: absolute;
  right: 0;
  width: 79px;
  height: 61px;
  display: flex;
  align-items: center;
  padding: 0 12px;
  justify-content: space-between;
  background: linear-gradient(270deg, #e1f0ff 0%, rgba(255, 255, 255, 0.7) 100%);
  opacity: 0;
  transition: opacity 0.2s;
}
.box:hover {
  .edit-box {
    opacity: 1;
  }
}

.shrink {
  flex-shrink: 0;
}
::v-deep .ace-editor {
  background-color: #f9fafb;
}
</style>
