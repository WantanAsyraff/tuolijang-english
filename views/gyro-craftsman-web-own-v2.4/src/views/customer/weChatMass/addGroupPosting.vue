<template>
  <div class="divBox">
    <div class="box-height">
      <el-card body-style="padding: 0">
        <!-- 头部返回按钮 -->
        <div class="header">
          <span @click="backFn" class="el-icon-arrow-left"></span> {{ this.id ? $t('ui.customerWeChatMassAddGroupPostingEditMassSendPage') : $t('ui.customerWeChatMassAddGroupPostingAddMassSendPage') }}
        </div>
        <!-- 表单内容 -->
        <div class="flex">
          <div style="width: 55%" class="p20">
            <el-form ref="formRef" :model="formData" :rules="rules" label-width="auto">
              <div class="title">{{ $t("ui.customerWeChatMassAddGroupPostingBasicInformation") }}</div>
              <el-form-item>
                <div slot="label">
                  <span class="required">*</span>{{ formData.types == 1 ? $t('ui.customerWeChatMassAddGroupPostingSelectGroupOwner') : $t('ui.customerWeChatMassAddGroupPostingMassSendEmployees') }}
                </div>
                <select-member :value="userList" @getSelectList="getSelectList" :isqiWeiWork="true"></select-member>
                <div class="tips mt10">
                  {{ $t("ui.customerWeChatMassAddGroupPostingSelectTheEmployeesWhoShouldReceiveThisMassSend") }}
                </div>
              </el-form-item>
              <el-form-item :label="formData.types == 1 ? $t('ui.customerWeChatMassAddGroupPostingCustomerGroupScope') : $t('ui.customerWeChatMassAddGroupPostingCustomerScope')" prop="is_all">
                <el-radio-group v-model="formData.is_all" @change="radioChange">
                  <el-radio :label="1">{{ formData.types == 1 ? $t('ui.customerWeChatMassAddGroupPostingAllGroupChats') : $t('ui.customerWeChatMassAddGroupPostingAllCustomers') }}</el-radio>
                  <el-radio v-if="formData.types == 0" :label="2">{{ $t("ui.customerWeChatMassAddGroupPostingWeComCustomer") }}</el-radio>
                  <el-radio :label="0"> {{ formData.types == 1 ? $t('ui.customerWeChatMassAddGroupPostingFilterGroupChats') : $t('ui.customerWeChatMassAddGroupPostingFilteredCustomers') }}</el-radio>
                </el-radio-group>
                <div class="tips mb10">{{ $t("ui.customerWeChatMassAddGroupPostingChooseWhichCustomersReceiveThisMessageFiltersSendIt") }}</div>
                <template v-if="formData.types == '0' && (formData.is_all == 0 || formData.is_all == 2)">
                  <div class="search-box">
                    <condition-dialog
                      ref="condition"
                      :eventStr="`event`"
                      :formArray="formData.is_all == 2 ? workSearch : viewSearch"
                      :noRule="false"
                      :isFooter="false"
                    />
                  </div>
                  <div class="tips mt10">
                    {{ $t("ui.customerWeChatMassAddGroupPostingViewTheEstimatedNumberOfRecipientsForThisMessage") }}<span class="text1" @click="getCount">{{ $t("ui.customerWeChatMassAddGroupPostingCalculateNow") }}</span
                    ><span class="text2" v-if="countShow"> {{ $t("ui.customerWeChatMassAddGroupPostingEstimated") }} {{ numberPeople }}{{ $t("ui.customerWeChatMassAddGroupPostingPeople") }}</span>
                  </div>
                </template>

                <!-- 选择标签 -->
                <template v-if="formData.types == '2' && formData.is_all === 0">
                  <div class="select plan-footer-one mr10 mt10" @click="handleLabel(val)">
                    <span v-if="labelList && labelList.length == 0" class="placeholder">{{ $t("ui.customerWeChatMassAddGroupPostingSelectLabels") }}</span>
                    <div ref="getHeight">
                      <span
                        v-for="(item, labelIndex) in labelList"
                        :key="labelIndex"
                        class="el-tag el-tag--small el-tag--info el-tag--light mr10"
                        @click.stop="cardTag(labelIndex)"
                      >
                        {{ item.name }}
                        <i class="el-tag__close el-icon-close" @click.stop="delTag(labelIndex)" />
                      </span>
                    </div>
                  </div>
                </template>
                <!-- 选择群聊 -->
                <div
                  class="select plan-footer-one mr10"
                  ref="select"
                  v-if="formData.types == '1' && formData.is_all === 0"
                  @click="groupChatFn"
                >
                  <span class="el-icon-arrow-down"></span>
                  <span v-if="groupList && groupList.length == 0" class="placeholder">{{ $t("ui.customerWeChatMassAddGroupPostingClickToSelectAGroupChat") }}</span>
                  <div class="flex-box" v-if="groupList.length > 0">
                    <span
                      v-for="(item, index) in groupList"
                      :key="index"
                      class="el-tag el-tag--small el-tag--info el-tag--light mr10"
                      @click.stop=""
                    >
                      {{ item.name || '--' }}
                      <i class="el-tag__close el-icon-close" @click.stop="cardTag(index)" />
                    </span>
                  </div>
                </div>
              </el-form-item>

              <el-form-item :label="formData.types == 1 ? $t('ui.customerWeChatMassAddGroupPostingOwnerAdjustedSendingScope') : $t('ui.customerWeChatMassAddGroupPostingMemberAdjustedSendingScope')" prop="is_modify">
                <el-radio-group v-model="formData.is_modify">
                  <el-radio :label="0">{{ $t("ui.customerWeChatMassAddGroupPostingNotAllowed") }}</el-radio>
                  <el-radio :label="1">{{ $t("ui.customerWeChatMassAddGroupPostingAllowed") }}</el-radio>
                </el-radio-group>
                <div class="tips">
                  {{
                    formData.types == 1
                      ? $t('ui.customerWeChatMassAddGroupPostingControlsWhetherTheOwnerMayChooseWhichGroupChats')
                      : $t('ui.customerWeChatMassAddGroupPostingControlsWhetherEmployeesMayChooseWhichCustomersReceiveThe')
                  }}
                </div>
              </el-form-item>

              <!-- 群发内容区域 -->
              <div class="flex flex-between lh-center">
                <div class="title">{{ $t("ui.customerWeChatMassClientGroupChatMassSendContent") }}</div>
                <el-button type="text" @click="openLibrary">{{ $t("ui.customerQuickReplyAddReplySelectFromTheMaterialLibrary") }}</el-button>
              </div>
              <el-form-item prop="temp_id">
                <div slot="label"><span class="required">*</span>{{ $t("ui.customerWeChatMassAddGroupPostingMassSendContent") }}</div>
                <materialContent
                  ref="materialContentRef"
                  @contentChange="handleContentChange"
                  @getAttachData="getAttachData"
                  :types="formData.types"
                ></materialContent>
                <div class="tips mt10" v-if="formData.types == 2">
                  {{ $t("ui.customerWeChatMassAddGroupPostingSupportsUpTo9Images1VideoOr1") }}
                </div>
              </el-form-item>

              <!-- 群发时间设置 -->
              <el-form-item :label="$t('ui.customerWeChatMassAddGroupPostingMassSendTime')" prop="is_timed">
                <el-radio-group v-model="formData.is_timed" @change="handleTimeChange">
                  <el-radio :label="0">{{ $t("ui.customerWeChatMassAddGroupPostingSendImmediately") }}</el-radio>
                  <el-radio :label="1">{{ $t("ui.customerWeChatMassAddGroupPostingScheduleSend") }}</el-radio>
                </el-radio-group>
                <div class="tips">{{ $t("ui.customerWeChatMassAddGroupPostingChooseWhenEmployeesAreNotifiedToSendThisMass") }}</div>

                <el-date-picker
                  v-if="formData.is_timed == 1"
                  v-model="formData.send_time"
                  size="small"
                  type="datetime"
                  :placeholder="$t('ui.administrationNoticeAddNoticeSelectDateTime')"
                  value-format="yyyy-MM-dd HH:mm:ss"
                  style="width: 200px; margin-top: 10px"
                >
                </el-date-picker>

                <div class="attention mt10">
                  {{ $t("ui.customerWeChatMassAddGroupPostingCustomersCanReceiveUpTo30MassMessagesFrom") }}
                </div>

                <el-button type="primary" size="small" @click="submitFn" class="mt20"> {{ $t("ui.customerWeChatMassAddGroupPostingNotifyGroupMembersToMassSend") }} </el-button>
              </el-form-item>
            </el-form>
          </div>

          <!-- 右侧预览图 -->
          <div class="right">
            <div class="image">
              <div class="right-box" v-if="formData.temp.content">
                <div class="flex mb10">
                  <img :src="userInfo.avatar" alt="" class="avatar" />
                  <div class="msg">{{ formData.temp.content }}</div>
                </div>
                <div class="flex mb10" v-for="(item, index) in attachList" :key="index">
                  <img :src="userInfo.avatar" alt="" class="avatar" />
                  <img v-if="item.types === 'image'" :src="item.file.url" alt="" class="img" />
                  <div v-else-if="item.types === 'video'" class="img">
                    <video ref="{videoRef}" style="width: 58px; height: 58px">
                      <track kind="captions" />
                      <source :src="item.file.url" type="video/mp4" />
                    </video>
                    <div class="mask">
                      <span class="iconfont iconbofang"></span>
                    </div>
                  </div>
                  <div v-else class="msg">
                    <div v-if="item.types === 'link'">{{ item.link }}</div>
                    <div v-if="item.types === 'mini_program'">
                      <div class="file-box line pb10">
                        <div>
                          <span class="over-text2">{{ item.title }} {{ item.title }}{{ item.title }}</span>
                        </div>

                        <img :src="item.file.url" alt="" style="width: 44px; height: 44px; border-radius: 4px" />
                      </div>
                      <div class="size"><span class="iconfont iconxiaochengxu2" /> {{ $t("ui.customerWeChatMassAddGroupPostingMiniProgram") }}</div>
                    </div>
                    <div v-if="item.types === 'file'" class="file-box">
                      <div class="left">
                        <div class="over-text">{{ item.file.name }}</div>
                        <span class="size">{{ toSizeFile(item.file.size) }}</span>
                      </div>
                      <img src="../../../assets/images/word.png" alt="" class="file-img" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- 素材库组件 -->
        <materialLibrary ref="libraryRef" @selectMaterial="handleSelectMaterial"></materialLibrary>
        <!-- 群聊选择组件 -->
        <groupChat ref="groupChatRef" @selectGroups="handleSelectGroups"></groupChat>
        <!-- 客户标签弹窗 -->
        <label-dialog ref="labelDialog" :config="labelData" @handleLabelConf="handleLabelConf"></label-dialog>
      </el-card>
    </div>
  </div>
</template>

<script>
import i18n from '@/lang'
import { workMassTempApi, workMassSave, getWorkMassEdit, getWorkMassCustomerCount, putWorkMassEdit } from '@/api/weCom'
import { salesmanCustomApi } from '@/api/client'
import { formatBytes } from '@/libs/public'
import { roterPre } from '@/settings'
import { getStorageJson } from '@/utils/storage'
export default {
  name: 'AddGroupPosting', // 组件名规范（ PascalCase ）
  components: {
    selectMember: () => import('@/components/form-common/select-member'),
    materialContent: () => import('./components/materialContent'),
    materialLibrary: () => import('./components/materialLibrary'),
    conditionDialog: () => import('@/components/develop/conditionDialog'),
    labelDialog: () => import('@/views/customer/list/components/labelDialog'),
    groupChat: () => import('./components/groupChat')
  },
  props: {
    types: {
      type: [String, Number],
      default: '0'
    },
    editId: {
      type: [String, Number],
      default: 0
    }
  },
  data() {
    return {
      id: '',
      numberPeople: 0,
      countShow: false,
      formData: {
        send_uid: [],
        is_all: 1,
        send_group: [],
        is_modify: 0,
        search: [],
        temp: {
          content: '',
          attach: []
        },

        temp_id: '',
        is_timed: 0,
        send_time: '',
        types: ''
      },
      conditionList: [],
      userList: [],
      attachList: [],
      viewSearch: [],
      workSearch: [], // 企微客户筛选
      labelList: [],
      groupList: [],
      userInfo: getStorageJson('userInfo', {}),
      labelData: {},
      rules: {
        is_all: [{ required: true, message: i18n.t('legacyScript.pleaseSelectTheBroadcastScope'), trigger: 'change' }],

        is_modify: [{ required: true, message: i18n.t('legacyScript.pleaseSelectWhetherToAllowAdjustments'), trigger: 'change' }],
        is_timed: [{ required: true, message: i18n.t('legacyScript.pleaseSelectMassSendTimeType'), trigger: 'change' }]
      }
    }
  },
  mounted() {
    this.formData.types = this.types

    this.salesmanCustom()

    if (this.editId) {
      this.id = this.editId
      this.getInfo()
    }
  },
  methods: {
    getCount() {
      this.countShow = true
      if (this.formData.send_uid.length == 0) {
        this.$message.error(i18n.t('legacyScript.pleaseSelectAnEmployeeFirst'))
        return false
      }
      let data = {
        search: []
      }
      if (this.formData.types != '1' && this.$refs.condition) {
        let arr = this.$refs.condition.conditionConfig.conditionList

        let list = JSON.parse(JSON.stringify(arr))
        if (list.length > 0) {
          list.map((item) => {
            if (item.type === 'date_picker') {
              item.option = item.option[0] + '-' + item.option[1]
            }

            let obj = {
              field: item.field,
              value: item.option
            }
            data.search.push(obj)
          })
        }
      }
      data.send_uid = this.formData.send_uid
      getWorkMassCustomerCount(data).then((res) => {
        this.numberPeople = res.data.count
      })
    },
    // 打开客户标签
    handleLabel(val) {
      this.labelData = {
        title: i18n.t('customer.customerlabel'),
        width: '540px',
        label: this.labelList,
        edit: 1
      }
      this.$refs.labelDialog.handleOpen()
    },
    // 选中客户标签成功回调
    handleLabelConf(res) {
      this.labelList = res.data
    },

    radioChange(val) {
      let list = []
      if (this.$refs.condition) {
        this.$refs.condition.conditionConfig.conditionList = []
        if (list.length == 0) {
          this.$refs.condition.addCondition()
        }
      }
    },
    // 打开群聊选择弹窗
    groupChatFn() {
      if (this.formData.send_uid.length == 0) {
        return this.$message.error(i18n.t('legacyScript.pleaseSelectTheGroupOwnerFirst'))
      }
      if (this.$refs.groupChatRef) {
        // 检查组件实例是否存在
        this.$refs.groupChatRef.openBox(this.formData.send_uid)
      } else {
        this.$message.error(i18n.t('legacyScript.failedToLoadGroupChatSelectionComponent'))
      }
    },

    cardTag(index) {
      this.groupList.splice(index, 1)
    },
    delTag(index) {
      this.labelList.splice(index, 1)
    },

    // 群聊选择回调
    handleSelectGroups(group) {
      this.groupList = group
    },

    // 打开素材库
    openLibrary() {
      if (this.$refs.libraryRef) {
        this.$refs.libraryRef.openBox()
      } else {
        this.$message.error(i18n.t('legacyScript.failedToLoadAssetLibraryComponent'))
      }
    },

    getAttachData(data) {
      this.attachList = data
    },

    // 素材选择回调
    handleSelectMaterial(val) {
      this.formData.temp_id = val.id
      this.formData.temp.content = val.content
      workMassTempApi(val.id).then((res) => {
        if (this.$refs.materialContentRef) {
          if (res.data.attach.length > 0) {
            res.data.attach.forEach((item, index) => {
              if (item.file) {
                item.file_id = item.file.id
              }
            })

            if (this.formData.types == 2) {
              let arr = []
              const FILTER_TYPES = ['mini_program', 'file']
              res.data.attach.map((item) => {
                if (!FILTER_TYPES.includes(item.types)) {
                  arr.push(item)
                }
              })
              this.attachList = arr
            } else {
              this.attachList = res.data.attach
            }
          }
          this.$refs.materialContentRef.getData({ attach: this.attachList || [], content: res.data.content })
        }
      })
    },

    // 监听素材内容手动修改
    handleContentChange(content) {
      this.formData.temp.content = content
    },

    // 群发时间类型变更
    handleTimeChange(val) {
      if (val === 0) {
        this.formData.send_time = ''
      }
    },

    filterData() {
      let data = []
      if (this.formData.types == '0' && this.$refs.condition && this.formData.is_all != 1) {
        let list = JSON.parse(JSON.stringify(this.$refs.condition.conditionConfig.conditionList))
        if (list.length > 0) {
          list.map((item) => {
            if (item.type === 'date_picker') {
              item.option = item.option[0] + '-' + item.option[1]
            }
            let obj = {
              field: item.field,
              value: item.option,
              options: item.options
            }
            data.push(obj)
          })

          const hasEmptyValue = data.some((item) => {
            return !item.value || item.value.toString().trim() === ''
          })
          if (hasEmptyValue) {
            this.$message.error(i18n.t('legacyScript.pleaseSelectFilterConditions'))
            return false
          }
          this.formData.search = data
        } else {
          this.$message.error(i18n.t('legacyScript.pleaseSelectFilterConditions'))
          return false
        }
      }
    },

    // 提交表单
    submitFn() {
      this.$refs.formRef.validate((valid) => {
        if (valid) {
          if (this.formData.send_uid.length == 0) return this.$message.error(i18n.t('legacyScript.pleaseSelectEmployee'))
          // 筛选客户校验
          this.filterData()

          if (this.formData.types == '2' && this.labelList.length > 0) {
            this.labelList.map((item) => {
              this.formData.send_group.push(item.id)
            })
          }

          if (this.formData.types == '2' && this.formData.is_all == 0 && this.formData.send_group.length == 0) {
            return this.$message.error(i18n.t('legacyScript.pleaseSelectFilterConditions'))
          }

          if (this.formData.types == 1 && this.groupList.length > 0) {
            this.groupList.map((item) => {
              this.formData.send_group.push(item.chat_id)
            })
          }
          if (!this.formData.temp.content) {
            return this.$message.error(i18n.t('legacyScript.pleaseEnterTheMessageContent'))
          }
          if (this.formData.is_timed == 1 && !this.formData.send_time) {
            return this.$message.error(i18n.t('legacyScript.selectScheduledSendTime'))
          }

          if (this.$refs.materialContentRef) {
            this.formData.temp.attach = this.$refs.materialContentRef.uploadFileList
          }
          this.formData.temp.attach.forEach((obj) => {
            delete obj.file
          })
          if (this.id) {
            putWorkMassEdit(this.id, this.formData).then((res) => {
              if (res.status == 200) {
                this.backFn()
              }
            })
          } else {
            workMassSave(this.formData).then((res) => {
              if (res.status == 200) {
                this.backFn()
              }
            })
          }
        }
      })
    },

    getInfo() {
      getWorkMassEdit(this.id).then((res) => {
        for (let key in this.formData) {
          this.formData[key] = res.data[key]
        }
        this.attachList = res.data.temp.attach
        if (!res.data.send_group) {
          this.formData.send_group = []
        }
        this.userList = res.data.send_user
        let temp = res.data.temp
        this.$refs.materialContentRef.getData(temp)
        if (this.formData.types != 1) {
          // 高级筛选数据处理
          res.data.search.forEach((item) => {
            item.option = item.value
            item.form_value = item.input_type
            item.type = item.input_type
            if (item.field === 'customer_label') {
              item.optionsList = []

              item.form_value = 'tag'
              item.type = 'tag'
            } else if (item.input_type === 'date') {
              item.form_value = 'date_picker'
              item.type = 'date_picker'
              item.option = item.value.split('-')
            } else if (['single', 'multiple'].includes(item.input_type)) {
              item.form_value = 'cascader_radio'
              item.type = 'cascader_radio'
              item.option.forEach((el) => {
                el = el.map(String)
              })
            } else if (item.input_type === 'select' && item.field !== 'repeat' && item.field !== 'work_customer') {
              item.type = 'cascader'
            } else if (item.input_type === 'personnel') {
              item.options.userList = item.value || []
              item.category = 2
            }
          })
          this.conditionList = res.data.search
          this.$store.commit('uadatefieldOptions', { list: this.conditionList })
        }
      })
    },

    backFn() {
      this.id = ''
      this.$emit('backFn')
      // if (this.formData.types == 1) {
      //   this.$router.push({
      //     path: `${roterPre}/customer/weChatMass/clientGroupChat`
      //   })
      // } else if (this.formData.types == 2) {
      //   this.$router.push({
      //     path: `${roterPre}/customer/weChatMass/wechatMoments`
      //   })
      // } else {
      //   this.$router.push({
      //     path: `${roterPre}/customer/weChatMass/clientMass`
      //   })
      // }
    },
    // 员工选择回调
    getSelectList(data) {
      if (!Array.isArray(data)) return // 防止非数组数据导致报错
      this.userList = data
      this.formData.send_uid = data.map((item) => item.value).filter(Boolean) // 过滤无效值
    },
    salesmanCustom() {
      this.workSearch = []
      this.viewSearch = []
      salesmanCustomApi('customer').then((res) => {
        let search_list = res.data.search

        for (let i = 0; i < search_list.length; i++) {
          if (search_list[i].input_type == 'date') {
            search_list[i].input_type = 'date_picker'
          }
          if (search_list[i].field == 'customer_label') {
            search_list[i].input_type = 'tag'
          }
          if (search_list[i].input_type == 'select') {
            if (search_list[i].field == 'area_cascade') {
              search_list[i].input_type = 'cascader_address'
            } else if (search_list[i].type == 'single') {
              search_list[i].input_type = 'cascader_radio'
            } else if (search_list[i].type == 'multiple') {
              search_list[i].input_type = 'cascader'
            } else if (search_list[i].input_type == '') {
              search_list[i].input_type = 'cascader'
            }
          }
          if (search_list[i].dict) {
            this.mapDict(search_list[i].dict)
          }
          search_list[i].form_value = search_list[i].input_type
          search_list[i].field_name_en = search_list[i].field
          search_list[i].field_name = search_list[i].name

          search_list[i].title = search_list[i].name
          search_list[i].options = search_list[i].dict
          search_list[i].data_dict = search_list[i].dict
          search_list[i].type = search_list[i].input_type
          search_list[i].is_city_show = ''
          if (search_list[i].field == 'customer_label' || search_list[i].field == 'created_at') {
            this.workSearch.push(search_list[i])
          }
          let excludedFields = [
            'repeat',
            'work_customer',
            'contract_name',
            'contract_no',
            'customer_name',
            'b37a3f16',
            '9bfe77e4',
            'clue_id'
          ]
          if (!excludedFields.includes(search_list[i].field)) {
            this.viewSearch.push(search_list[i])
          }
        }
      })
    },
    toSizeFile(size) {
      return formatBytes(size)
    },

    mapDict(dict) {
      for (let i = 0; i < dict.length; i++) {
        dict[i].name = dict[i].label
        if (dict[i].children) {
          this.mapDict(dict[i].children)
        }
      }
    }
  }
}
</script>

<style scoped lang="scss">
.header {
  font-family: PingFang SC, sans-serif;
  font-weight: 500;
  font-size: 18px;
  color: #303133;
  display: flex;
  align-items: center;
  cursor: pointer;
  border-bottom: 1px solid #eeeeee;
  padding: 20px;
  .el-icon-arrow-left {
    font-size: 16px;
    color: #606266;
    margin-right: 6px;
  }
}

.title {
  font-family: PingFang SC, sans-serif;
  font-weight: 600;
  font-size: 14px;
  color: #333;
  margin: 0 0 20px 9px;
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

.p20 {
  padding: 20px;
}

.tips {
  // height: 14px;
  line-height: 14px;
  font-family: PingFang SC, sans-serif;
  font-size: 12px;
  color: #909399;

  .text1 {
    font-family: PingFang SC, PingFang SC;
    cursor: pointer;
    font-size: 13px;
    color: #2d8cf0;
  }
  .text2 {
    font-family: PingFang SC, PingFang SC;
    margin-left: 16px;
    font-size: 13px;
    color: #303133;
  }
}
.iconxiaochengxu2 {
  font-size: 5px;
}

.attention {
  font-family: PingFang SC, sans-serif;
  font-size: 12px;
  color: #606266;
  width: 100%; // 自适应宽度，避免固定宽度溢出
  max-width: 566px;
  // height: 30px;
  line-height: 30px;
  background: #f7f7f7;
  text-align: center;
}

.plan-footer-one {
  position: relative;
  cursor: pointer;
  -webkit-appearance: none;
  background-color: #fff;
  background-image: none;
  border-radius: 4px;
  border: 1px solid #dcdfe6;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  color: #c0c4cc;
  display: inline-block;
  font-size: inherit;
  min-height: 32px;
  line-height: 30px;
  outline: none;
  font-size: 13px;
  padding: 0 10px;
  -webkit-transition: border-color 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
  transition: border-color 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
  width: 100%;
  .el-tag.el-tag--info {
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 13px;
    color: #303133;
  }
}
.el-icon-arrow-down {
  font-weight: 400;
  position: absolute;
  right: 10px;
  top: 8px;
}

.right {
  padding: 40px 0 0 90px;
  .image {
    width: 235px;
    height: 474px;
    background: url(../../../assets/images/mobilePhone.png) no-repeat;
    background-size: 100% 100%;
    object-fit: contain; // 图片自适应，避免拉伸
    position: relative;
    .right-box {
      position: absolute;
      left: 20px;
      top: 60px;
      width: 200px;
      height: 350px;
      overflow: auto;
      scrollbar-width: none; /* firefox */
      -ms-overflow-style: none; /* IE 10+ */
      overflow-x: hidden;
      .avatar {
        width: 20px;
        height: 21px;
        border-radius: 2px 2px 2px 2px;
        margin-right: 6px;
      }
      .video-content {
        width: 58px;
        height: 58px;
        border-radius: 4px;
      }
      .msg {
        width: 166px;
        min-height: 32px;
        background: #ffffff;
        border-radius: 2px;
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 10px;
        line-height: 11px;
        color: #333333;
        padding: 10px 12px;
      }
      .img {
        display: block;
        border-radius: 4px;
        width: 58px;
        height: 58px;
        position: relative;
        .mask {
          position: absolute;
          top: 0;
          left: 0;
          width: 58px;
          height: 58px;
          border-radius: 4px;
          background-color: rgba(0, 0, 0, 0.3);
          display: flex;
          justify-content: center;
          align-items: center;
          .iconbofang {
            color: #fff;
          }
        }
      }
      .line {
        border-bottom: 1px solid #eeeeee;
      }
      .file-box {
        display: flex;
        justify-content: space-between;
        .left {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
        }
        .file-img {
          flex-shrink: 0;
          display: block;
          width: 31px;
          height: 38px;
        }
      }
      .size {
        font-family: PingFang SC, PingFang SC;
        font-weight: 400;
        font-size: 5px;
        color: #999999;
      }
    }
  }
}

::v-deep .el-radio {
  // input[aria-hidden='true'] {
  //   display: block !important; // 还原原生input，避免焦点隐藏
  //   opacity: 0; // 视觉隐藏但保留焦点功能
  //   width: 100%;
  //   height: 100%;
  //   position: absolute;
  //   top: 0;
  //   left: 0;
  //   z-index: 1;
  // }
}

::v-deep .el-radio:focus:not(.is-focus):not(:active):not(.is-disabled) .el-radio__inner {
  box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.2); // 保留焦点样式，提升可访问性
}

.required {
  color: #f56c6c;
  margin-right: 4px;
}

::v-deep .el-form-item__label {
  white-space: nowrap;
}


// 补充flex布局样式
.flex {
  display: flex;
}

.flex-between {
  justify-content: space-between;
}

.lh-center {
  align-items: center;
}

.search-box {
  background: #f9f9f9;
  min-height: 70px;
  border-radius: 4px 4px 4px 4px;
  padding-bottom: 10px;
}
::v-deep .conditions {
  margin-bottom: 6px !important;
}
</style>
