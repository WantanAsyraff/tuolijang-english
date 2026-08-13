import { $ } from '@/lang'
<template>
<div>
  <el-form ref="form" :model="form" :rules="formRules" label-position="top">
    <div class="p20">
      <div class="title">{{ $("ui.chatModelFormModelSettings") }}</div>
      <el-form-item prop="models_id">
        <div slot="label" class="label">
          {{ $("ui.chatModelFormAiModel") }}
          <popover :tips="$('ui.chatModelFormSelectAnAiModelConfiguredUnderModelSettings')"></popover>
          <div class="parameter" @click="openFn">{{ $("ui.chatModelFormParameterSettings") }}</div>
        </div>
        <el-select v-model="form.models_id" :placeholder="$('ui.chatModelFormSelectModel')" size="small" style="width: 100%">
          <el-option v-for="item in options" :key="item.value" :label="item.lable" :value="item.value"></el-option>
        </el-select>
        <!-- <el-input v-model="form.name" size="small" maxlength="20" show-word-limit></el-input> -->
      </el-form-item>
    </div>
    <el-divider></el-divider>
    <div class="p20">
      <el-form-item>
        <div slot="label" class="label">
          <span class="el-icon-caret-right" :class="show4 ? 'rotating' : 'norotating'" @click="show4 = !show4"></span>
          {{ $("ui.chatModelFormOpeningMessage") }}
          <popover :tips="$('ui.chatModelFormShownWhenUsersStartAConversationToIntroduceThe')" :width="250"></popover>
        </div>
        <el-collapse-transition>
          <div v-show="show4">
            <el-input
              type="textarea"
              class="textareaBox height90"
              :placeholder="$('ui.chatModelFormEnterContent')"
              v-model="form.prologue_text"
              maxlength="200"
            >
            </el-input>
            <div class="append">
              <span class="num-color">{{ form.prologue_text.length }} / 200</span>
              <span class="iconfont iconzhankai2" @click="openText(200, form.prologue_text, 'prologue_text')"></span>
            </div>
          </div>
        </el-collapse-transition>
      </el-form-item>
      <el-form-item>
        <div slot="label" class="label">
          <span class="el-icon-caret-right" :class="show1 ? 'rotating' : 'norotating'" @click="show1 = !show1"></span>
          {{ $("ui.chatModelFormOpeningQuestions") }}
          <popover :tips="$('ui.chatModelFormAddAtLeastThreeQuestionsIfMoreAreAdded')"></popover>
        </div>
        <el-collapse-transition>
          <div v-show="show1">
            <div v-for="(item, index) in form.prologue_list" :key="index">
              <el-input
                v-model="form.prologue_list[index]"
                class="parameter-input mb8"
                size="small"
                maxlength="50"
                :placeholder="$('ui.chatModelFormPleaseEnterOpeningQuestions')"
              >
                <div slot="suffix" class="del-text">
                  <span class="num-color">{{ form.prologue_list[index].length }}/50</span>
                  <i class="el-icon-delete" @click="delprologue(index)"></i>
                </div>
              </el-input>
            </div>
            <div class="parameter1">
              <span @click.stop="addprologue"><span class="el-icon-circle-plus-outline" />{{ $("ui.chatModelFormAddQuestion") }}</span>
            </div>
          </div>
        </el-collapse-transition>
      </el-form-item>
    </div>
    <el-divider style="width: 100%"></el-divider>
    <div class="p20">
      <div class="title">{{ $("ui.chatModelFormPlugin") }}</div>
      <el-radio-group v-model="form.source_type" class="mb14">
        <el-radio :label="0">{{ $("ui.chatModelFormDatabase") }}</el-radio>
        <el-radio :label="1">MCP</el-radio>
      </el-radio-group>

      <template v-if="form.source_type == 0">
        <el-form-item>
          <div slot="label" style="width: 100%" class="label flex-between" @click.prevent>
            <div class="flex">
              <span
                class="el-icon-caret-right"
                :class="show5 ? 'rotating' : 'norotating'"
                @click.prevent="show5 = !show5"
              ></span>
              {{ $("ui.chatModelFormDatabase") }}
              <popover
                :tips="$('ui.chatModelFormExpandTheAssistantSDatabaseKnowledgeToProvideMore')"
              ></popover>
            </div>
            <div class="flex-center">
              <div class="json-text">
                <span @click.prevent="addJson">{{ $("ui.chatModelFormDatabaseSettings") }}</span>
              </div>

              <el-switch
                v-model="form.is_table"
                active-text="开启"
                inactive-text="关闭"
                :active-value="1"
                :inactive-value="0"
                style="width: 60px"
              >
              </el-switch>
            </div>
          </div>
          <el-collapse-transition>
            <div v-show="show5 && form.is_table == 1">
              <el-input type="textarea" class="textareaBox height90" :placeholder="$('ui.chatModelFormEnterContent')" v-model="form.content">
              </el-input>
              <div class="append">
                <span class="iconfont iconzhankai2" @click="addJson"></span>
              </div>
            </div>
          </el-collapse-transition>
        </el-form-item>
        <el-form-item v-if="form.is_table == 1">
          <div slot="label" style="width: 100%" class="label flex-between" @click.prevent>
            <div class="flex">
              <span
                class="el-icon-caret-right"
                :class="show6 ? 'rotating' : 'norotating'"
                @click.prevent="show6 = !show6"
              ></span>
              {{ $("ui.chatModelFormDataFormattingRules") }}
              <popover :tips="$('ui.chatModelFormDescribeHowTheContentReturnedFromADatabaseQuery')"></popover>
            </div>
          </div>
          <el-collapse-transition>
            <div v-show="show6">
              <el-input
                type="textarea"
                class="textareaBox height90"
                :placeholder="$('ui.chatModelFormEnterContent')"
                v-model="form.data_arrange_text"
                maxlength="1000"
              >
              </el-input>
              <div class="append">
                <span class="num-color">{{ form.data_arrange_text.length }} / 1000</span>
                <span
                  class="iconfont iconzhankai2"
                  @click="openText(1000, form.data_arrange_text, 'data_arrange_text')"
                ></span>
              </div>
            </div>
          </el-collapse-transition>
        </el-form-item>

        <el-form-item v-if="form.is_table == 1">
          <div slot="label" class="label">
            <span
              class="el-icon-caret-right"
              :class="show2 ? 'rotating' : 'norotating'"
              @click="show2 = !show2"
            ></span>
            {{ $("ui.chatModelFormKeywords") }}
            <popover :tips="$('ui.chatModelFormTheDatabaseIsQueriedOnlyWhenAUserS')"></popover>
          </div>
          <el-collapse-transition>
            <div v-show="show2">
              <div v-for="(item, index) in form.keyword" :key="index">
                <el-input
                  v-model="form.keyword[index]"
                  class="parameter-input mb8"
                  size="small"
                  maxlength="20"
                  :placeholder="$('ui.chatModelFormEnterKeywords')"
                >
                  <div slot="suffix" class="del-text">
                    <span class="num-color">{{ form.keyword[index].length }}/20</span>
                    <i class="el-icon-delete" @click="delkeyWord(index)"></i>
                  </div>
                </el-input>
              </div>
              <div class="parameter1">
                <span @click="addkeyWord"> <span class="el-icon-circle-plus-outline" />{{ $("ui.chatModelFormAddKeyword") }} </span>
              </div>
            </div>
          </el-collapse-transition>
        </el-form-item>
      </template>
      <!-- MCP设置 -->
      <template v-if="form.source_type == 1">
        <el-form-item>
          <div slot="label" style="width: 100%" class="label flex-between" @click.prevent>
            <div class="flex">
              <span
                class="el-icon-caret-right"
                :class="show5 ? 'rotating' : 'norotating'"
                @click.prevent="show5 = !show5"
              ></span>
              MCP
              <popover
                :tips="$('ui.chatModelFormExpandTheAssistantSDatabaseKnowledgeToProvideMore')"
              ></popover>
            </div>
            <div class="flex-center">
              <div class="json-text">
                <span @click="addMcpFn">{{ $("ui.chatModelFormMcpSettings") }}</span>
              </div>
            </div>
          </div>
          <el-collapse-transition>
            <div v-show="show5 && form.is_table == 1">
              <div class="mcp-item" v-for="(item, index) in form.mcp_json" :key="index">
                <div style="line-height: 1.8">
                  <span class="title">{{ item.name }}</span>
                  <span class="over-text1 mr10">
                    {{ item.info }}
                  </span>
                </div>
                <span class="el-icon-delete right" @click="delMcp(index)"></span>
              </div>
            </div>
          </el-collapse-transition>
        </el-form-item>
      </template>
    </div>
    <el-divider style="margin: 20px -20px"></el-divider>
    <div class="p20">
      <el-form-item>
        <div slot="label" class="label">
          <span class="el-icon-caret-right" :class="show3 ? 'rotating' : 'norotating'" @click="show3 = !show3"></span>
          {{ $("ui.chatModelFormReferenceConversationRounds") }}
          <popover :tips="$('ui.chatModelFormThisResponseUsesTheMostRecentConversationTurnsAs')" :width="270"></popover>
        </div>
        <el-collapse-transition>
          <div v-show="show3" class="flex">
            <el-slider v-model="form.count_number" :max="10" style="width: 100%"></el-slider>
            <el-input-number
              v-model="form.count_number"
              :controls="false"
              size="small"
              :max="10"
              :min="0"
              style="width: 61px"
              class="ml20"
            ></el-input-number>
          </div>
        </el-collapse-transition>
      </el-form-item>
    </div>
  </el-form>
  <!-- 参数设置 -->
  <oa-dialog ref="oaDialog" :fromData="fromData" @submit="submit" @handleClose="handleClose">
    <el-table :data="tableList" style="width: 100%">
      <el-table-column prop="date" :label="$('ui.developModuleButtonDialogParameterName')" width="180">
        <template slot-scope="scope">
          <el-input v-model="scope.row.name" size="small" :placeholder="$('ui.chatModelFormPleaseEnterParameterName')"></el-input>
        </template>
      </el-table-column>
      <el-table-column prop="filed" :label="$('ui.chatModelFormParameterKey')" width="180">
        <template slot-scope="scope">
          <el-input v-model="scope.row.filed" size="small" :placeholder="$('ui.chatModelFormPleaseEnterParameterKey')"></el-input> </template
      ></el-table-column>
      <el-table-column prop="value" :label="$('ui.chatModelFormDefaultValue')" width="180">
        <template slot-scope="scope">
          <el-input v-model="scope.row.value" size="small" :placeholder="$('ui.chatModelFormEnterDefaultValue')"></el-input>
        </template>
      </el-table-column>
      <el-table-column prop="message" :label="$('ui.workFlowDialogErrorDialogHint')"
        ><template slot-scope="scope">
          <el-input v-model="scope.row.message" size="small" :placeholder="$('ui.chatModelFormPleaseEnterHint')"></el-input> </template
      ></el-table-column>
      <el-table-column :label="$('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" width="90">
        <template slot-scope="scope">
          <el-button type="text" @click="handlerDelet(scope.row, scope.$index)">{{ $("ui.chatIndexDelete") }}</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div class="addText">
      <span @click="addJsonTable">{{ $("ui.chatModelFormAddParameter") }}</span>
    </div>
  </oa-dialog>
  <!-- 选择MCP -->
  <addMcp ref="addMcp" @mcpSubmit="getMcpData"></addMcp>
  <!-- 选择数据库 -->
  <jsonDialog ref="jsonDialog" @submit="getJsonData" :content="form.content" :list="form.tables"></jsonDialog>
  <textDialog ref="textDialog" @submit="getTextData"></textDialog>
  <!-- <databaseTable></databaseTable> -->
</div>
</template>
<script>
import popover from './popover'
import databaseTable from './databaseTable'
import jsonDialog from './jsonDialog'
import addMcp from './addMcp'
import textDialog from './textDialog'
import { getModelsSelectListApi } from '@/api/chatAi'
import oaDialog from '@/components/form-common/dialog-form'

export default {
  name: '',
  components: { popover, oaDialog, databaseTable, jsonDialog, textDialog, addMcp },
  props: {
    info: {
      type: Object,
      default: () => {
        return {}
      }
    },
    appId: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      fromData: {
        title: $('ui.chatModelFormParameterSettings'),
        width: '980px',
        type: 'slot',
        btnText: '确定'
      },
      radio: '2',

      options: [],
      tableList: [],

      form: {
        models_id: '',
        json: [], // 参数设置
        count_number: 0,
        is_table: 1,
        tables: [],
        mcp_json: [],
        keyword: [],
        content: '',
        data_arrange_text: '',
        prologue_text: '',
        source_type: '0',
        prologue_list: [''] // 问题
      },
      formRules: {
        models_id: {
          required: true,
          message: $('legacyScript.selectModel'),
          trigger: 'change'
        }
      },
      input: 3,
      textarea: '',
      show1: true,
      show2: true,
      show3: true,
      show4: true,
      show5: true,
      show6: true,
      placeholder: $('legacyScript.pleaseEnterAPrompt')
    }
  },
  computed: {
    previewState() {
      const { prologue_text, prologue_list } = this.form
      return {
        prologueText: prologue_text,
        prologueList: prologue_list.filter((item) => item !== '')
      }
    }
  },
  watch: {
    info: {
      handler(val) {
        const isUndef = (val) => val === undefined || val === null
        for (let key in this.form) {
          if (!isUndef(val[key])) {
            // 直接使用 props 传递来的数组，需要深拷贝
            // 否则 prologue_list 变化时，会引起 info 的变化，导致表单状态丢失
            if (Array.isArray(val[key])) {
              this.form[key] = JSON.parse(JSON.stringify(val[key]))
            } else {
              this.form[key] = val[key]
            }
          }
        }
      },
      immediate: true,
      deep: true
    },
    previewState(state) {
      this.$emit('update-preview', state)
    }
  },
  mounted() {
    this.getOptions()
  },

  methods: {
    addJson(event) {
      event.stopImmediatePropagation()

      this.$refs.jsonDialog.openBox(this.form.content, this.form.tables)
    },

    // 提交数据
    getData(val) {
      if (this.form.prologue_list.length > 0) {
        let newArr = []
        this.form.prologue_list.forEach((item) => {
          if (item) {
            newArr.push(item)
          }
        })
        this.form.prologue_list = newArr
      }
      if (this.form.keyword.length > 0) {
        let newArr = []
        this.form.keyword.forEach((item) => {
          if (item) {
            newArr.push(item)
          }
        })
        this.form.keyword = newArr
      }

      if (val) {
        return new Promise((resolve, reject) => {
          this.$refs['form'].validate((valid) => {
            if (!valid) {
              reject({ target: 'modelForm' })
              return
            }
            resolve({ modelForm: this.form })
          })
        })
      } else {
        return new Promise((resolve, reject) => {
          resolve({ modelForm: this.form })
        })
      }
    },

    getMcpData(data) {
      this.form.mcp_json = data
    },

    addMcpFn() {
      this.$refs.addMcp.openBox(this.appId, this.form.mcp_json)
    },

    getTextData(val, type) {
      this.form[type] = val
    },
    delMcp(index) {
      this.$modalSure('确定删除此MCP服务吗').then(() => {
        this.form.mcp_json.splice(index, 1)
      })
    },
    getJsonData(list, data) {
      this.form.tables = list
      this.form.content = data
    },

    handleClose() {
      this.tableList = [{ name: '', filed: '', value: '', message: '' }]
    },

    handlerDelet(row, index) {
      this.$modalSure('确定删除此参数').then(() => {
        this.tableList.splice(index, 1)
      })
    },
    openText(max, text, type) {
      let obj = {
        max,
        text,
        type
      }
      this.$refs.textDialog.openBox(obj)
    },
    addprologue() {
      this.form.prologue_list.push('')
    },
    delprologue(index) {
      this.form.prologue_list.splice(index, 1)
    },
    addkeyWord() {
      this.form.keyword.push('')
    },
    delkeyWord(index) {
      this.form.keyword.splice(index, 1)
    },
    submit() {
      for (const obj of this.tableList) {
        for (const value of Object.values(obj)) {
          let values = Object.values(obj)
          if (values.every((value) => value === '')) {
          } else if (obj.value === '' || obj.name === '' || obj.filed === '') {
            this.$message({
              type: 'error',
              message: $('legacyScript.pleaseCompleteAllFieldsParameterSettingsCannotBeEmpty')
            })
            return false
          }
        }
      }
      this.tableList.forEach((item, index) => {
        let values = Object.values(item)
        if (values.every((value) => value === '')) {
          this.tableList.splice(index, 1)
        }
      })

      this.form.json = JSON.parse(JSON.stringify(this.tableList))
      this.$refs.oaDialog.handleClose()
    },
    addJsonTable() {
      this.tableList.push({
        name: '',
        filed: '',
        value: '',
        message: ''
      })
    },
    async getOptions() {
      const data = await getModelsSelectListApi()
      this.options = data.data
    },
    addItem() {
      this.form.list.push({
        value: ''
      })
    },
    openFn() {
      this.$refs.oaDialog.openBox()
      this.tableList = JSON.parse(JSON.stringify(this.form.json))
    }
  }
}
</script>
<style scoped lang="scss">
::v-deep .el-form-item__label {
  display: flex;
  align-items: center;
  height: 18px;
  margin-bottom: 10px;
  padding: 0;
  position: relative;
}
.num-color {
  color: #909399;
}
.parameter-input {
  ::v-deep .el-input__inner {
    padding-right: 70px;
  }
}
.p20 {
  padding: 0 20px;
}
.mb8 {
  margin-bottom: 8px;
}
.required-icon {
  color: #ed4014;
  margin-right: 4px;
}
.json-text {
  cursor: pointer;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #1890ff;
  margin-right: 12px;
}
.del-text {
  display: flex;
  align-items: center;
  // width: 70px;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 12px;
  color: #606266;

  .el-icon-delete {
    font-size: 14px;
    margin-left: 8px;
    cursor: pointer;
  }
}
.label {
  display: flex;
  font-weight: 500;
  font-size: 13px;
  color: #303133;
  line-height: 13px;
}
.iconshuoming {
  font-size: 14px;
  color: rgba(0, 0, 0, 0.45);
}
.parameter {
  position: absolute;
  right: 0;
  cursor: pointer;
  font-weight: 400;
  font-size: 13px;
  color: #1890ff;
}
.parameter1 {
  cursor: pointer;
  font-weight: 400;
  font-size: 12px;
  color: #1890ff;
}
.el-icon-circle-plus-outline {
  font-size: 13px;
  margin-right: 4px;
}
.el-icon-plus {
  position: absolute;
  right: 0;
  cursor: pointer;
  color: #606266;
  font-size: 13px;
}
.el-icon-caret-right {
  font-size: 14px;
  cursor: pointer;
  margin-right: 4px;
}
.rotating {
  transform: rotate(90deg);
}

.title {
  font-family: PingFang SC, PingFang SC;
  font-weight: 500;
  font-size: 12px;
  color: #606266;
  margin-bottom: 10px;
}
.textareaBox {
  position: relative;

  ::v-deep .el-textarea__inner {
    resize: none;
  }
}
.height395 {
  ::v-deep .el-textarea__inner {
    height: 395px;
  }
}
.height90 {
  ::v-deep .el-textarea__inner {
    height: 90px;
  }
}
::v-deep .el-slider__button-wrapper {
  z-index: 50;
}
.append {
  // background: #fff;
  display: flex;
  position: absolute;
  bottom: 1px;
  right: 10px;
  font-weight: 400;
  font-size: 12px;
  color: #606266;
  .iconfont {
    font-size: 14px;
    margin-left: 8px;
    cursor: pointer;
  }
}
.addText {
  cursor: pointer;
  margin-top: 20px;
  font-weight: 400;
  font-size: 13px;
  color: #1890ff;
  .el-icon-plus {
    display: inline-block;

    color: #1890ff;
  }
}
.mcp-item {
  width: 100%;
  height: 63px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 4px;
  border: 1px solid #dcdfe6;
  padding: 12px;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 12px;
  color: #909399;
  margin-bottom: 8px;
  &:last-child {
    margin-bottom: 0;
  }
  .title {
    font-weight: 500;
    font-size: 13px;
    color: #303133;
  }
  .right {
    display: inline-block;
    cursor: pointer;
    font-size: 14px;
    color: #606266;
    width: 17px;
  }
}
</style>
