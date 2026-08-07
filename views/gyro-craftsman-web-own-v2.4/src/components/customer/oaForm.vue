<!--
  @FileDescription: 客户/订单新增的动态表单组件
  功能：提供动态表单渲染，支持多种表单控件类型
-->
<template>
<div>
  <!-- 表单主体 -->
  <el-form ref="form" :model="ruleForm" :rules="rule" label-width="auto" @submit.native.prevent size="small">
    <!-- 遍历表单分组 -->

    <div v-for="(item, itemIndex) in formInfo" :key="itemIndex" class="p20">
      <!-- 分组标题 -->
      <div
        v-if="item.status == 1 && item.data.length > 0"
        class="from-item-title mb20 flex-between"
        style="width: 100%"
      >
        <span>{{ item.title }}</span>
        <!-- v-if="item.ident == 'product' && viewMode && editKey !== 'product'" -->
      </div>

      <div class="form-box" v-if="item.ident == 'product'">
        <!-- <div
          v-if="item.ident == 'product' && viewMode"
          class="iconfont iconbianji3"
          title="编辑产品清单"
          @click="editProduct"
        >
          编辑
        </div> -->
        <slot name="product" :type="productType"></slot>
      </div>

      <!-- 表单字段区域 -->
      <div class="form-box" v-else>
        <!-- 遍历表单字段 -->
        <el-row :gutter="20">
          <el-col
            v-for="(val, index) in item.data"
            :key="index"
            :span="viewMode && !['file', 'images', 'oaWangeditor'].includes(val.type) ? 12 : 24"
          >
            <el-form-item v-show="val.input_type !== 'hidden'" :prop="val.key" class="label-box inline-edit-item">
              <span slot="label" class="label">{{ val.key_name }}：</span>
              <i v-if="viewMode && savingKeyMap[val.key]" class="el-icon-loading inline-edit-saving-icon" />
              <!-- 文本输入框 -->
              <template v-if="val.input_type === 'input' && val.type === 'text'">
                <el-input
                  v-if="!viewMode || editKey == val.key"
                  v-model="ruleForm[val.key]"
                  :maxlength="val.max"
                  :min="val.min"
                  :placeholder="val.placeholder ? val.placeholder : $t('ui.customerOaFormPleaseEnter') + val.key_name"
                  clearable
                  size="small"
                  class="clickZone"
                  :ref="`input_${val.key}`"
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                  @keyup.enter.native="handlePopoverHide(ruleForm[val.key])"
                />
                <div v-else :class="fieldViewClass(val)">
                  {{ ruleForm[val.key] || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    class="edit-icon iconfont iconbianji1"
                    @click.stop="handleClick(val)"
                  ></i>
                </div>
              </template>

              <el-input v-if="val.input_type === 'hidden'" v-model="ruleForm[val.key]" type="hidden" />

              <!-- 数字输入框 -->
              <template v-if="val.type === 'number'">
                <el-input-number
                  v-if="!viewMode || editKey == val.key"
                  v-model="ruleForm[val.key]"
                  :controls="false"
                  :max="val.max"
                  :min="val.min"
                  :placeholder="val.placeholder ? val.placeholder : $t('ui.developConditionGroupPleaseSelect') + val.key_name"
                  :precision="val.decimal_place"
                  size="small"
                  style="width: 100%"
                  class="clickZone"
                  :ref="`input_${val.key}`"
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                  @keyup.enter="handlePopoverHide(ruleForm[val.key])"
                />
                <div v-else :class="fieldViewClass(val)">
                  {{ ruleForm[val.key] || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 文本域 -->
              <template v-if="val.type === 'textarea'">
                <el-input
                  v-if="!viewMode || editKey == val.key"
                  v-model="ruleForm[val.key]"
                  :autosize="autosize"
                  :maxlength="val.max"
                  :placeholder="val.placeholder ? val.placeholder : $t('ui.customerOaFormPleaseEnter') + val.key_name"
                  clearable
                  show-word-limit
                  size="small"
                  type="textarea"
                  class="clickZone"
                  style="width: 100%"
                  :ref="`input_${val.key}`"
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                  @keyup.enter="handlePopoverHide(ruleForm[val.key])"
                />

                <div v-else :class="[fieldViewClass(val), 'white-space-pre-line']">
                  {{ ruleForm[val.key] || '--'
                  }}<i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 级联选择器 -->
              <template v-if="val.input_type === 'select' && val.options_level > 1">
                <el-cascader
                  v-if="!viewMode || editKey == val.key"
                  :ref="`input_${val.key}`"
                  v-model="ruleForm[val.key]"
                  :options="localizedOptions(val.options, val.key)"
                  :placeholder="val.placeholder ? val.placeholder : $t('ui.developConditionGroupPleaseSelect') + val.key_name"
                  :props="{
                    checkStrictly: true,
                    label: 'label',
                    value: 'value',
                    multiple: val.type !== 'single' && val.type !== 'cascader'
                  }"
                  :show-all-levels="val.key !== 'path'"
                  clearable
                  collapse-tags
                  filterable
                  size="small"
                  style="width: 100%"
                  class="clickZone"
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                  @keyup.enter.native="handlePopoverHide(ruleForm[val.key])"
                />
                <div v-else :class="fieldViewClass(val)">
                  {{ val.text || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 下拉选择器 -->
              <template v-if="val.input_type === 'select' && val.options_level <= 1 && val.key !== 'clue_id'">
                <el-select
                  v-if="!viewMode || editKey == val.key"
                  v-model="ruleForm[val.key]"
                  :multiple="val.type !== 'single'"
                  :placeholder="val.placeholder ? val.placeholder : $t('ui.developConditionGroupPleaseSelect') + val.key_name"
                  clearable
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                  filterable
                  size="small"
                  @change="changeValue(val)"
                  style="width: 100%"
                  class="clickZone"
                  :ref="`input_${val.key}`"
                  @keyup.enter.native="handlePopoverHide(ruleForm[val.key])"
                >
                  <el-option
                    v-for="el in localizedOptions(val.options, val.key)"
                    :key="el.value"
                    :disabled="el.disabled"
                    :label="el.label"
                    :value="el.value"
                  />
                </el-select>
                <div v-else :class="fieldViewClass(val)">
                  {{ val.text || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 远程搜索 -->
              <div v-if="val.key === 'clue_id'" class="flex">
                <el-select
                  v-if="!viewMode || editKey == val.key"
                  v-model="ruleForm[val.key]"
                  :placeholder="$t('ui.customerOaFormPleaseSearchByLeadName')"
                  filterable
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                  remote
                  clearable
                  reserve-keyword
                  :remote-method="remoteMethod"
                  :loading="loading"
                  style="width: 100%"
                  class="clickZone"
                  :ref="`input_${val.key}`"
                  @keyup.enter.native="handlePopoverHide(ruleForm[val.key])"
                >
                  <el-option v-for="item in cluesOption" :key="item.value" :label="item.label" :value="item.value">
                  </el-option>
                </el-select>
                <div v-else :class="fieldViewClass(val)">
                  {{ val.text || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </div>

              <!-- 客户标签选择器 -->
              <template v-if="val.key == 'customer_label'">
                <div
                  v-if="!viewMode || editKey == val.key"
                  :style="{ height: heightInputRole + 'px' }"
                  class="el-input__inner select plan-footer-one clickZone"
                  :class="{ 'is-saving-disabled': !!savingKeyMap[val.key] }"
                  @click="handleLabel(val)"
                >
                  <div v-if="labelList && labelList.length == 0" class="placeholder">
                    {{ val.placeholder ? val.placeholder : $t('ui.developConditionGroupPleaseSelect') + val.key_name }}
                  </div>
                  <div ref="getHeight">
                    <span
                      v-for="(item, labelIndex) in labelList"
                      :key="labelIndex"
                      class="el-tag el-tag--small el-tag--info el-tag--light"
                      @click.stop="cardTag(labelIndex)"
                    >
                      {{ item.name }}
                      <i class="el-tag__close el-icon-close" @click.stop="cardTag(labelIndex)" />
                    </span>
                  </div>
                </div>
                <div v-else :class="fieldViewClass(val)">
                  <span>{{ labelList.length ? joinName(labelList) : '--' }}</span>
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 人员选择器 -->
              <template v-if="val.input_type === 'member'">
                <select-member
                  v-if="!viewMode || editKey == val.key"
                  :value="val.options"
                  :onlyOne="val.type == 'singleMember' ? true : false"
                  @handlePopoverHide="getSelectList($event, val)"
                  style="width: 100%"
                  class="clickZone"
                >
                </select-member>
                <div v-else :class="[fieldViewClass(val), 'member-box']">
                  <template v-if="val.options && val.options.length > 0">
                    <span v-for="(item, index) in val.options" :key="index" class="lh-center mr10">
                      <img v-default-avatar="item" :src="$getAvatarSrc(item)" alt="" class="avatar" />
                      {{ item.name }}
                    </span>
                  </template>
                  <template v-else> -- </template>
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 单选按钮组 -->
              <template v-if="val.type === 'radio'">
                <el-radio-group
                  v-model="ruleForm[val.key]"
                  v-if="!viewMode || editKey == val.key"
                  class="clickZone"
                  style="width: 100%"
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                  @keyup.enter.native="handlePopoverHide(ruleForm[val.key])"
                >
                  <el-radio v-for="(el, index) in localizedOptions(val.options, val.key)" :key="index" :label="el.value">
                    {{ el.label }}
                  </el-radio>
                </el-radio-group>
                <div v-else :class="fieldViewClass(val)">
                  {{ val.text || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 多选按钮组 -->
              <template v-if="val.type === 'checked' && val.key !== 'customer_label'">
                <el-checkbox-group
                  v-if="!viewMode || editKey == val.key"
                  v-model="ruleForm[val.key]"
                  class="clickZone"
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                >
                  <el-checkbox v-for="(check, checkIndex) in localizedOptions(val.options, val.key)" :key="checkIndex" :label="check.value">
                    {{ check.label }}
                  </el-checkbox>
                </el-checkbox-group>
                <div v-else :class="fieldViewClass(val)">
                  {{ val.text || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 日期选择器 -->
              <template v-if="val.type === 'date'">
                <el-date-picker
                  style="width: 100%"
                  v-if="!viewMode || editKey == val.key"
                  v-model="ruleForm[val.key]"
                  :format="'yyyy-MM-dd'"
                  :placeholder="val.placeholder ? val.placeholder : $t('ui.developConditionGroupPleaseSelect') + val.key_name"
                  :value-format="'yyyy-MM-dd'"
                  clearable
                  size="small"
                  type="date"
                  class="clickZone"
                  :ref="`input_${val.key}`"
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                />
                <div v-else :class="fieldViewClass(val)">
                  {{ ruleForm[val.key] || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 日期时间选择器 -->
              <template v-if="val.type === 'datetime'">
                <el-date-picker
                  v-if="!viewMode || editKey == val.key"
                  v-model="ruleForm[val.key]"
                  :format="'yyyy-MM-dd HH:mm:ss'"
                  :placeholder="val.placeholder ? val.placeholder : $t('ui.developConditionGroupPleaseSelect') + val.key_name"
                  :value-format="'yyyy-MM-dd HH:mm:ss'"
                  clearable
                  size="small"
                  style="width: 100%"
                  type="datetime"
                  class="clickZone"
                  :ref="`input_${val.key}`"
                  :disabled="isReadonlyField(val) || !!savingKeyMap[val.key]"
                />
                <div v-else :class="fieldViewClass(val)">
                  {{ ruleForm[val.key] || '--' }}
                  <i
                    v-if="!isReadonlyField(val)"
                    @click.stop="handleClick(val)"
                    class="edit-icon iconfont iconbianji1"
                  ></i>
                </div>
              </template>

              <!-- 文件上传 -->
              <template v-if="val.type === 'file'">
                <upload-file
                  :maxLength="val.max"
                  :only-image="false"
                  :value="val.files"
                  @getVal="getVal($event, val)"
                />
              </template>
              <!-- 图片上传 -->
              <upload-file
                v-if="val.type === 'images'"
                :maxLength="val.max"
                :only-image="true"
                :value="val.files"
                @getVal="getVal($event, val)"
              />

              <!-- 富文本编辑器 -->
              <template v-if="val.type === 'oaWangeditor'">
                <div v-if="!viewMode || editKey == val.key" class="clickZone" style="height: 100%; width: 100%">
                  <ueditor-from
                    ref="ueditorFrom"
                    :border="true"
                    :content="ruleForm[val.key]"
                    :height="`400px`"
                    @input="(value) => ueditorEdit(val, value)"
                  />
                  <div class="mt14" v-if="viewMode">
                    <el-button size="small" :disabled="!!savingKeyMap[val.key]" @click="editKey = ''">{{ $t("ui.formCommonSelectLabelCancel") }}</el-button>
                    <el-button
                      size="small"
                      type="primary"
                      :loading="!!savingKeyMap[val.key]"
                      @click="handlePopoverHide(ruleForm[val.key])"
                      >{{ $t("ui.formDesignerFormWidgetFieldWidgetRichTextWidgetSave") }}</el-button
                    >
                  </div>
                </div>

                <template v-else>
                  <div
                    v-if="ruleForm[val.key]"
                    class="readonly-mode-field"
                    @click.stop="handleClick(val)"
                    v-html="ruleForm[val.key]"
                  ></div>
                  <div v-else class="readonly-mode-field" @click.stop="handleClick(val)">--</div>
                </template>
              </template>
            </el-form-item>
          </el-col>
        </el-row>
      </div>
    </div>
  </el-form>

  <!-- 表单操作按钮 -->
  <div v-if="isShowFooter" class="button from-foot-btn fix btn-shadow">
    <el-button class="el-btn" size="small" @click="resetForm"> {{ $t("ui.formCommonSelectLabelCancel") }} </el-button>

    <el-button v-if="btnShow && types !== 3" :loading="addContractLoading" size="small" @click="addContract">
      {{ type == 'contract' ? $t('ui.customerOaFormSaveAndAddPayment') : $t('ui.customerOaFormSaveAndAddOpportunity') }}
    </el-button>

    <el-button :loading="saveLoading" size="small" type="primary" @click="handleConfirm('ruleForm')">
      {{ $t("ui.formDesignerFormWidgetFieldWidgetRichTextWidgetSave") }}
    </el-button>
  </div>
  <!-- 客户标签弹窗 -->
  <label-dialog ref="labelDialog" :config="labelData" @handleLabelConf="handleLabelConf"></label-dialog>
</div>
</template>
<script>
import i18n from '@/lang'
import { getStorageJson } from '@/utils/storage'
import { extractArrayIds } from '@/libs/public'
import { pinyin } from 'pinyin-pro'
import { contractCategorySelectApi, clientConfigLabelApi, chargeEditSubmitApi } from '@/api/enterprise'
import {
  clientCluesSearchApi,
  clientContractEditApi,
  clientLiaisonEditApi,
  savecluesEditApi,
  oddsEditApi
} from '@/api/client'

export default {
  name: 'OaForm',
  components: {
    selectMember: () => import('@/components/form-common/select-member'),
    uploadFile: () => import('@/components/form-common/oa-upload'),
    ueditorFrom: () => import('@/components/form-common/oa-wangeditor'),
    labelDialog: () => import('@/views/customer/list/components/labelDialog')
  },

  props: {
    // 表单配置信息
    formInfo: {
      type: Array,
      default: () => []
    },
    uid: {
      type: String,
      default: ''
    },
    viewMode: {
      // 查看模式true
      type: Boolean,
      default: false
    },
    id: {
      type: [String, Number], // 客户、线索、商机id
      default: undefined
    },
    keyWord: {
      type: String, // 线索、商机
      default: 'customer'
    },

    // 表单类型
    type: {
      type: [Number, String],
      default: ''
    },

    // 表单子类型
    types: {
      type: [Number, String],
      default: ''
    },

    // 是否显示附加按钮
    btnShow: {
      type: Boolean,
      default: true
    },
    // 是否显示附加按钮
    isShowFooter: {
      type: Boolean,
      default: true
    }
  },

  data() {
    return {
      defaultLabelList: [], // 默认标签列表
      drawer: true, // 抽屉状态
      loading: false,
      saveLoading: false, // 保存加载状态
      ruleForm: {}, // 表单数据
      rule: {}, // 表单验证规则
      autosize: {
        // 文本域自适应配置
        minRows: 6
      },
      itemData: {}, // 当前编辑的字段信息
      productType: 'edit',
      editKey: '',
      savingKeyMap: {}, // 字段保存中状态 { [field.key]: boolean }

      labelData: {},
      oldValue: null,
      cluesOption: [],
      treeData: [], // 树形数据
      imageList: [], // 图片列表
      labelAllList: [], // 全部客户标签
      addContractLoading: false, // 添加订单加载状态
      heightInputRole: 32, // 输入框高度
      attachList: [], // 附件列表
      labelList: [] // 选中客户标签
    }
  },
  watch: {
    // 监听表单配置变化
    formInfo: {
      handler(nVal, oVal) {
        // 精准监听 + 防重复触发
        if (JSON.stringify(nVal) === JSON.stringify(oVal)) return false
        if (nVal.length == 0) return false
        this.formatFormInfo(nVal)
      },
      immediate: true,
      deep: true
    },

    // 监听附件列表变化
    attachList: {
      handler(nVal) {
        if (nVal.length > 0) {
          const filekey = this.getKey('file')
          let ids = nVal.map((item) => item.id)
          this.ruleForm[filekey] = ids
        }
      }
    },

    // 监听图片列表变化
    imageList: {
      handler(nVal) {
        if (nVal.length > 0) {
          const imgkey = this.getKey('images')
          let ids = nVal.map((item) => item.id)
          this.ruleForm[imgkey] = ids
        }
      }
    },
    editKey(newVal) {
      this.handleFocus(newVal)
    },
    'ruleForm.clue_id'(newVal) {
      if (!newVal) return
      const clue = this.cluesOption.find((item) => item.value === newVal)
      if (clue) this.fillLiaisonInfo(clue)
    },
    viewMode(newVal) {
      // 如果从true变为false（非视图模式），触发聚焦
      if (!newVal) {
        this.handleFocus(this.editKey)
      }
    }
  },

  mounted() {
    this.$nextTick(() => {
      if (this.viewMode) {
        this.handleDocumentClick = () => {
          this.handlePopoverHide('')
        }
        document.addEventListener('click', this.handleDocumentClick)
      }
    })
  },

  beforeDestroy() {
    if (this.viewMode && this.handleDocumentClick) {
      document.removeEventListener('click', this.handleDocumentClick)
    }
  },

  methods: {
    localizedOptions(options, fieldKey) {
      return (options || []).map((option) => ({
        ...option,
        label: this.localizedOptionLabel(option.label, fieldKey),
        children: option.children ? this.localizedOptions(option.children, fieldKey) : option.children
      }))
    },
    localizedOptionLabel(label, fieldKey) {
      const translated = this.$ts(label)
      if (translated !== label || fieldKey !== 'area_cascade' || !/[\u3400-\u9fff]/.test(label || '')) {
        return translated
      }
      const suffixes = [
        ['特别行政区', ' Special Administrative Region'],
        ['自治州', ' Autonomous Prefecture'],
        ['自治区', ' Autonomous Region'],
        ['地区', ' Prefecture'],
        ['省', ' Province'],
        ['市', ' City'],
        ['区', ' District'],
        ['县', ' County'],
        ['旗', ' Banner'],
        ['盟', ' League'],
        ['镇', ' Town'],
        ['乡', ' Township']
      ]
      const matched = suffixes.find(([suffix]) => label.endsWith(suffix))
      const base = matched ? label.slice(0, -matched[0].length) : label
      const romanized = pinyin(base, { toneType: 'none', type: 'array' }).join('')
      if (!romanized) {
        return label
      }
      return romanized.charAt(0).toUpperCase() + romanized.slice(1) + (matched ? matched[1] : '')
    },

    removeEvent() {
      if (this.handleDocumentClick) {
        document.removeEventListener('click', this.handleDocumentClick)
      }
    },

    closeFn() {
      this.$refs.form.resetFields()
      for (let key in this.ruleForm) {
        this.ruleForm[key] = ''
      }
    },

    formatFormInfo(nVal, type) {
      // 1. 统一初始化：清空旧数据
      this.attachList = []
      this.imageList = []
      this.cluesOption = []

      // 2. 扁平化字段，一次循环完成所有逻辑
      const fields = nVal.flatMap((group) => group.data || [])

      fields.forEach((field) => {
        // 2.1 特殊类型预处理
        switch (field.key) {
          case 'customer_label':
            if (field.value) this.getLabel(field.value)
            break
          case 'clue_id':
            if (field.options.length) this.cluesOption = field.options
            break
          case 'contract_customer':
            field.options_level = 1
            break
        }

        field.value = this.normalizeFieldValue(field, field.value)

        // 2.2 文件/图片自动挂载
        if (field.files?.length) {
          if (field.type === 'file') this.attachList = field.files
          if (field.type === 'images') this.imageList = field.files
        }

        // 2.3 选项类文本一次性计算
        if (
          ['radio', 'select', 'checked', 'single', 'multiple'].includes(field.type) &&
          field.key !== 'customer_label'
        ) {
          field.text = this.getText(field.options, field.value)
        }
        if (['checked', 'multiple'].includes(field.type) && field.value == '') {
          field.value = []
        }
        if (field.input_type === 'select' && field.value == 0) {
          field.value = ''
        }

        // 2.4 校验规则动态注入
        if (field.required == 1) {
          this.$set(this.rule, field.key, [{ required: true, message: `请输入${field.key_name}`, trigger: 'blur' }])
        }

        // 2.5 表单值智能合并：优先保留用户已填 > 新默认值 > 空数组（多选）
        const userValue = type == 'edit' ? this.normalizeFieldValue(field, this.ruleForm[field.key]) : field.value

        let finalValue =
          userValue ?? (['checked', 'multiple'].includes(field.type) && field.value === '' ? [] : field.value)

        // 选项类字段：对齐值与选项的类型，避免类型不一致导致 el-select 严格相等匹配不到（回显/「无匹配数据」异常）
        if (field.input_type === 'select' && field.options_level <= 1) {
          finalValue = this.alignSelectValueType(finalValue, field.options)
        }

        this.$set(this.ruleForm, field.key, this.cloneValue(finalValue))
      })

      // 3. 统一高度计算 & 强制刷新（仅一次）
      this.$nextTick(() => {
        this.heightInput()
        this.$forceUpdate()
      })
    },
    /**
     * 获取上传文件的值
     * @param {Array} val - 文件数组
     * @param {Object} item - 当前字段配置
     */
    getVal(val, item) {
      let arr = val.map((el) => el.id)
      // if (this.ruleForm[item.key].length > 0) {
      //   this.ruleForm[item.key] = [...this.ruleForm[item.key], ...arr]
      // } else {
      this.ruleForm[item.key] = arr
      // }

      this.editKey = item.key
      this.itemData = item

      if (this.viewMode) {
        this.handlePopoverHide(this.ruleForm[item.key])
      }
    },
    joinName(obj) {
      return Object.values(obj)
        .filter((item) => item?.name) // 过滤无效项
        .map((item) => item.name)
        .join('、')
    },
    // 编辑产品清单
    editProduct() {
      this.editKey = 'product'
      this.productType = 'add'
    },

    isReadonlyField(field) {
      return !!(
        field?.readonly ||
        field?.system_field ||
        field?.disabled ||
        ['contract_no', 'odds_no'].includes(field?.key)
      )
    },
    fieldViewClass(field) {
      return this.isReadonlyField(field) ? 'readonly-display-field' : 'pointer'
    },
    handleClick(val) {
      if (val.key) {
        // 只读 div 上的 @click.stop 会阻止事件冒泡到 document，导致全局保存监听不触发。
        // 因此在切换到新字段前，先手动保存上一个正在编辑的字段，避免点击其他表单项时丢失修改。
        if (this.viewMode && this.editKey && this.editKey !== val.key) {
          this.handlePopoverHide('')
        }
        if (this.isReadonlyField(val)) {
          return
        }
        this.itemData = val
        this.editKey = val.key
        this.oldValue = this.cloneValue(this.ruleForm[val.key] ?? val.value)
        if (this.editKey === 'customer_label') {
          this.heightInput()
        }
      }
    },
    async handlePopoverHide(data) {
      if (!this.viewMode || !this.editKey) {
        return false
      }
      console.log(data, 99999)

      if (this.isReadonlyField(this.itemData) || ['contract_no', 'odds_no'].includes(this.editKey)) {
        this.editKey = ''
        return false
      }

      if (this.savingKeyMap[this.editKey]) {
        return false
      }

      // 富文本字段只能点击保存按钮触发
      if (this.itemData && this.itemData.type === 'oaWangeditor' && !data) {
        return false
      }
      // 获取所有el-form-item元素
      const formItems = document.querySelectorAll('.clickZone')
      let isClickInside = false

      // 检查点击目标是否在任何el-form-item内部
      formItems.forEach((item) => {
        if (item.contains(event.target)) {
          isClickInside = true
        }
      })

      // 如果点击的是外部区域
      if (!isClickInside || data) {
        const currentValue = this.normalizeFieldValue(this.itemData, this.ruleForm[this.editKey])
        if (!this.isSameValue(this.oldValue, currentValue)) {
          const obj = {
            field: this.editKey || this.itemData.key,
            value: data ? this.normalizeFieldValue(this.itemData, data) : currentValue
          }

          if ((this.editKey && this.editKey !== 'product') || data) {
            const apiMap = {
              clue: savecluesEditApi,
              clue_seas: savecluesEditApi,
              odds: oddsEditApi,
              customer: chargeEditSubmitApi,
              customer_seas: chargeEditSubmitApi,
              contract: clientContractEditApi,
              liaison: clientLiaisonEditApi
            }
            const currentApi = apiMap[this.keyWord]
            const fieldKey = this.editKey
            this.$set(this.savingKeyMap, fieldKey, true)

            try {
              await this.handleSubmit(currentApi, this.id, obj)
              this.$emit('fieldSaved', {
                field: fieldKey,
                value: obj.value
              })
              if (['status', 'eid', 'customer_label', 'clue_id'].includes(fieldKey)) {
                this.$emit('getDetails')
              } else {
                this.formInfo.forEach((item) => {
                  item.data.forEach((el) => {
                    if (el.key === fieldKey) {
                      el.value = this.cloneValue(currentValue)
                    }
                  })
                })

                setTimeout(() => {
                  this.formatFormInfo(this.formInfo, 'edit')
                }, 200)
              }

              if (this.editKey === fieldKey) {
                this.editKey = ''
                this.productType = 'edit'
              }
            } catch (error) {
              this.$message.error(error.data?.message || '保存失败')
            } finally {
              this.$set(this.savingKeyMap, fieldKey, false)
            }
          }
        } else {
          this.editKey = ''
        }
      }
    },
    async handleSubmit(api, id, data) {
      try {
        await api(id, data)
      } catch (error) {
        if (error?.data?.status === 2001) {
          try {
            await this.$modalSure(error.data.message)
            return this.handleSubmit(api, id, {
              ...data,
              force: 1
            })
          } catch {
            throw {
              data: {
                message: i18n.t('legacyScript.operationCanceled')
              }
            }
          }
        }

        throw error
      }
    },
    // 聚焦方法
    handleFocus(key) {
      // 确保v-if条件为true时才执行
      if (!this.viewMode || this.editKey === key) {
        this.$nextTick(() => {
          const ref = this.$refs[`input_${key}`]
          // 统一处理数组或单个组件
          const input = Array.isArray(ref) ? ref[0] : ref
          // 安全调用 focus，仅当组件实例存在且拥有 focus 方法时才执行
          input && typeof input.focus === 'function' && input.focus()
        })
      }
    },

    // 打开客户标签
    handleLabel(val) {
      if (this.isReadonlyField(val)) {
        return
      }
      this.editKey = val.key
      this.itemData = val
      this.labelData = {
        title: i18n.t('customer.customerlabel'),
        width: '540px',
        label: this.labelList,
        edit: 1
      }
      this.$refs.labelDialog.handleOpen()
    },

    /**
     * 获取订单分类数据
     * @param {Number} index - 表单分组索引
     * @param {Number} index1 - 字段索引
     */
    async getCategory(index, index1) {
      const result = await contractCategorySelectApi()
      this.formInfo[index].data[index1].options = result.data
    },
    handleChange() {
      this.$forceUpdate()
    },
    remoteMethod(query) {
      const userInfo = getStorageJson('userInfo', {})
      let uid = this.uid || userInfo.id || ''
      if (query) {
        this.loading = true
        let data = {
          uid: uid,
          name: query
        }
        clientCluesSearchApi(data).then((res) => {
          this.cluesOption = res.data
          this.loading = false
        })
      }
    },

    fillLiaisonInfo(clue) {
      const keys = this.formInfo.flatMap((group) => (group.data || []).map((field) => field.key))
      if (keys.includes('liaison_name')) {
        this.$set(this.ruleForm, 'liaison_name', clue.name || '')
      }
      if (keys.includes('liaison_tel')) {
        this.$set(this.ruleForm, 'liaison_tel', clue.phone || '')
      }
    },

    cloneValue(value) {
      if (Array.isArray(value) || (value && typeof value === 'object')) {
        return JSON.parse(JSON.stringify(value))
      }
      return value
    },

    normalizeFieldValue(field, value) {
      if (
        !field ||
        field.type !== 'multiple' ||
        field.input_type !== 'select' ||
        field.options_level > 1 ||
        !Array.isArray(value)
      ) {
        return value
      }

      const shouldFlatten = value.every((item) => Array.isArray(item) && item.length <= 1)
      return shouldFlatten ? value.map((item) => item[0]).filter((item) => item !== undefined) : value
    },

    // 将下拉值的类型对齐到 options 中的同值项，规避 el-select 严格相等匹配不到的问题。
    // 找到同值项时取其规范 value（类型跟随后端 options），找不到则原样保留，不影响远程搜索等部分选项场景。
    alignSelectValueType(value, options) {
      if (value === null || value === undefined || value === '' || !Array.isArray(options)) {
        return value
      }
      const toCanonical = (v) => {
        const matched = options.find((opt) => String(opt.value) === String(v))
        return matched ? matched.value : v
      }
      return Array.isArray(value) ? value.map(toCanonical) : toCanonical(value)
    },

    isSameValue(source, target) {
      return JSON.stringify(source) === JSON.stringify(target)
    },

    getSubmitData() {
      const data = { ...this.ruleForm }
      delete data.contract_no
      delete data.odds_no
      return data
    },

    resolveOptionValue(value) {
      if (Array.isArray(value)) {
        return value[value.length - 1]
      }
      return value
    },

    getText(options, val) {
      if (Array.isArray(val)) {
        let resultNames = []
        val.forEach((id) => {
          const name = this.findNameInTree(options, this.resolveOptionValue(id))

          if (name) {
            resultNames.push(name)
          }
        })

        return resultNames.join('/')
      } else {
        return this.findNameInTree(options, this.resolveOptionValue(val))
      }
    },

    findNameInTree(nodes, targetId) {
      if (targetId === null || targetId === undefined || targetId === '') {
        return null
      }
      const target = String(targetId)
      for (const node of nodes) {
        if (String(node.value) === target) {
          return node.label
        }
        if (node.children && node.children.length > 0) {
          const foundName = this.findNameInTree(node.children, targetId)
          if (foundName) {
            return foundName
          }
        }
      }
      return null
    },
    // 选择人员回调
    getSelectList(data, val) {
      val.options = data
      this.ruleForm[val.key] = extractArrayIds(data, 'value')
      if (this.viewMode) {
        this.handlePopoverHide(this.ruleForm[val.key])
      }
    },

    // 选中客户标签成功回调
    handleLabelConf(res) {
      let arr = []
      this.labelList = res.data
      this.heightInput()
      if (this.labelList.length > 0) {
        this.$refs.form.clearValidate('label')
      }

      res.data.map((item) => {
        arr.push(item.id)
      })
      this.ruleForm[this.itemData.key] = arr
      if (this.viewMode) {
        this.editKey = this.itemData.key
        this.handlePopoverHide(arr)
      }
    },

    heightInput() {
      setTimeout(() => {
        if (this.$refs.getHeight && this.$refs.getHeight[0]) {
          const height = this.$refs.getHeight[0].clientHeight
          this.heightInputRole = height === 0 ? 36 : height
        }
      }, 200)
    },

    // 删除客户标签
    cardTag(index) {
      this.labelList.splice(index, 1)
      let key = this.getKey('customer_label', 'key')
      let arr = []
      this.labelList.map((item) => {
        arr.push(item.id)
      })
      this.ruleForm[key] = arr
      this.heightInput()
    },

    changeValue(val) {
      if (val.key === 'oid' || val.key === 'contract_customer') {
        this.$emit('changeValue', val.key, this.ruleForm[val.key])
      }
    },

    // 获取标签数据
    async getLabel(arr) {
      this.labelAllList = []
      let data = {
        page: 1,
        limit: ''
      }
      const res = await clientConfigLabelApi(data)
      res.data.list.map((item) => {
        item.children.map((el) => {
          this.labelAllList.push(el)
        })
      })

      await this.myFilter(this.labelAllList, arr)
    },

    myFilter(arr1, arr2) {
      this.labelList = []
      const objArr = []
      arr1.map((item) => {
        arr2.map((el) => {
          if (item.id == el) {
            objArr.push(item)
          }
        })
      })

      this.$set(this, 'labelList', objArr)

      this.heightInput()
    },

    /**
     * 富文本编辑回调
     * @param {String} val - 富文本内容
     */
    ueditorEdit(formItem, val) {
      this.ruleForm[formItem.key] = val
    },

    /**
     * 获取指定类型的字段key
     * @param {String} row - 字段类型或key
     * @param {String} key - 是否按key查找
     * @returns {String} 字段key
     */
    getKey(row, key) {
      let formKey = ''
      this.formInfo.forEach((item) => {
        item.data.forEach((val) => {
          if (key && val.key === row) {
            formKey = val.key
          }
          if (val.type === row) {
            formKey = val.key
          }
        })
      })
      return formKey
    },

    // 清除点击事件
    clearClick(row) {
      this.removeEvent()
    },

    /**
     * 重置表单
     */
    resetForm() {
      this.$refs.form.resetFields()
      this.labelAllList = []
      this.attachList = []
      this.labelList = []
      this.imageList = []
      this.saveLoading = false
      this.addContractLoading = false
      this.$emit('handleClose')
    },

    /**
     * 提交表单
     */
    handleConfirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.saveLoading = true
          this.$emit('submitOk', this.getSubmitData())
        }
      })
    },

    /**
     * 添加订单
     */
    addContract() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.addContractLoading = true
          this.$emit('addContinueOk', this.getSubmitData())
        }
      })
    }
  }
}
</script>
<style lang="scss">
@import '@/styles/global.scss';
</style>
<style lang="scss" scoped>
/* 表单样式保持不变 */
.from-item-title {
  border-left: 3px solid #1890ff;
  margin-bottom: 20px;

  span {
    padding-left: 10px;
    font-weight: bold;
    font-size: 14px;
  }
}

.member-box {
  width: 100%;
  display: flex;
  flex-wrap: wrap;
  .avatar {
    width: 16px;
    height: 16px;
    margin-right: 4px;
    border-radius: 50%;
  }
}

.el-tag {
  margin-right: 4px;
}
.h-400 {
  height: 400px;
  overflow-y: auto;
}
.placeholder {
  color: #c0c4cc;
  height: 32px;
  line-height: 32px;
}

.pointer {
  padding: 7px 2em 8px 10px;
  word-break: break-all;
  position: relative;
  line-height: 21px;
}

.readonly-display-field {
  padding: 7px 10px 8px;
  word-break: break-all;
  position: relative;
  line-height: 21px;
  color: #606266;
}

.edit-icon {
  color: #606266;
  font-size: 14px;
  display: none;
  position: absolute;
  top: 8px;
  right: 15px;
}

.pointer:hover {
  width: 100%;
  display: flex;
  align-items: center;
  cursor: pointer;
  background-color: #f7f7f7;
  border-radius: 4px;
  .edit-icon {
    display: inline;
  }
}
.white-space-pre-line {
  white-space: pre-line;
  word-break: break-all;
  vertical-align: top;
}

.readonly-mode-field {
  ::v-deep p {
    img {
      width: 45px !important;
    }
  }
  ::v-deep table {
    width: 100%;
    border: 1px solid #e5e7eb;
    /* 表格字体：继承容器字体，确保一致性 */
    font-size: inherit;
    color: inherit;
  }
  ::v-deep th {
    background-color: #f3f4f6;
    text-align: center;
    min-width: 80px;
    border: 1px solid #e5e7eb;
    font-weight: 500;
  }
  ::v-deep td {
    text-align: left;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    word-wrap: break-word;
    word-break: break-all;
  }
}
.iconbianji3 {
  cursor: pointer;
  font-weight: 400 !important;
  font-size: 13px;
  color: #606266;
  padding: 10px;
  // margin-bottom: 10px;
}

::v-deep .el-radio {
  margin-right: 15px;
  margin-bottom: 0;
}

::v-deep .el-radio-group {
  display: flex;
  line-height: 0 !important;
  vertical-align: 0;
  font-size: 13px;
  margin-left: 10px;
}

.inline-edit-item {
  position: relative;
}
.inline-edit-saving-icon {
  position: absolute;
  top: 9px;
  right: 8px;
  color: #1890ff;
  font-size: 16px;
  z-index: 10;
  pointer-events: none;
}
.is-saving-disabled {
  background-color: #f5f7fa;
  border-color: #e4e7ed;
  color: #c0c4cc;
  cursor: not-allowed;
  pointer-events: none;
}
</style>
