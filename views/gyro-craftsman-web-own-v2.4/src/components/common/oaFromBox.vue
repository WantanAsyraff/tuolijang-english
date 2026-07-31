<!--
  @FileDescription: 全局筛选组件
  功能：提供统一的筛选、排序、新增等功能
-->
<template>
<div>
  <!-- 头部区域 -->
  <div v-if="showHeader" class="header-16 mb10">
    <div class="title-16" @click="backFn">
      <span v-if="isBack" class="el-icon-arrow-left pointer"></span>
      {{ title }}
      <!-- 提示信息 -->
      <el-popover placement="right" popper-class="monitor-yt-popover" trigger="hover">
        <div class="prompt-bag">{{ alert }}</div>
        <i v-if="alert" slot="reference" class="el-icon-question"></i>
      </el-popover>
      <slot name="title"></slot>
    </div>

    <!-- 右侧按钮区域 -->
    <div class="lh-center">
      <slot name="rightBtn"></slot>

      <!-- 新增/导出按钮 -->
      <el-button
        v-if="isAddBtn && btnType !== 'default'"
        :icon="btnIcon ? 'el-icon-plus' : ''"
        :loading="loading"
        :type="btnText == '导出' ? '' : 'primary'"
        class="h32"
        size="small"
        @click="addDataFn()"
      >
        {{ btnText }}
      </el-button>

      <!-- 默认按钮 -->
      <el-button v-if="btnType === 'default'" class="h32" size="small" @click="addDataFn()">
        {{ btnText }}
      </el-button>

      <!-- 下拉菜单 -->
      <div v-if="dropdownList.length > 0">
        <el-dropdown>
          <div class="xiala-box ml10">
            <span class="iconfont icongenjinjilu-gengduo"></span>
          </div>
          <!-- <span class="iconfont icongengduo2 pointer ml10"></span> -->
          <el-dropdown-menu style="text-align: left">
            <el-dropdown-item v-for="item in dropdownList" :key="item.value" @click.native="dropdownSearch(item)">
              <span>{{ item.label }}</span>
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
  </div>

  <!-- 搜索区域 -->
  <div class="search flex">
    <div class="flex">
      <!-- 视图筛选 -->
      <template v-if="category">
        <el-popover
          ref="popoverType"
          placement="bottom-start"
          popper-class="time-popover"
          trigger="click"
          width="140"
        >
          <div class="field-box mb0 height300" :class="category && isCategory ? '' : 'border-none'">
            <div
              v-for="(item, index) in treeData"
              :key="index"
              :class="{ 'field-color': viewIndex == item.id, spliceline: item.line }"
              class="view-text"
              @click="typeClick(item, index)"
            >
              <span class="over-text">{{ item.label }} </span>
              <span class="tips">{{ $t("ui.commonOaFromBoxSystem") }}</span>
            </div>

            <div
              v-for="(itemT, indexT) in viewList"
              :key="indexT + 'a'"
              :class="viewIndex == indexT + 'a' ? 'field-color' : ''"
              class="view-text"
              @click="viewClick(itemT, indexT)"
            >
              <span class="over-text">{{ itemT.title }}</span>
              <span class="tips">{{ itemT.is_public == 0 ? $t('ui.commonOaFromBoxPersonal') : $t('ui.commonOaFromBoxPublic') }}</span>
            </div>
          </div>
          <div class="view-text" v-if="category && isCategory" @click="openViewBox">
            <div><span class="iconfont iconshituguanli"></span>{{ $t("ui.commonHeaderSearchViewManagement") }}</div>
          </div>

          <div slot="reference" class="view-box mr10">
            <span class="over-text1">{{ viewText }}</span>
            <span class="el-icon-arrow-down"></span>
          </div>
        </el-popover>
      </template>
      <!-- 树形选择器 -->

      <el-select
        v-if="treeData.length && !category"
        v-model="treeValue"
        class="grey-bga mr10"
        :placeholder="$t('ui.developConditionGroupPleaseSelect')"
        popper-class="tree-select"
        size="small"
        style="width: 120px"
        @change="treeChange"
      >
        <el-option-group v-for="group in treeData" :key="group.label || group.id" :label="group.label">
          <el-option v-for="item in group.options" :key="item.value" :label="item.label" :value="item.value" />
        </el-option-group>
      </el-select>
      <!-- 总数显示 -->
      <div v-if="isTotal" class="total-16">{{ $t("ui.developModuleFormBoxTotal") }} {{ total }} {{ $t("ui.commonOaFromBoxItems") }}</div>
      <!-- 表单筛选 -->
      <formList
        v-show="seniorSearch.length > 0"
        ref="formList"
        :isTimeArray="false"
        :list="seniorSearch"
        :timeValue="timeVal"
        :type="type"
        @handleEmit="handleEmit"
        @resetSearch="resetSearch"
      />
    </div>

    <!-- 右侧操作区域 -->
    <div class="right" v-if="rightAreaVisible">
      <!-- 视图更新 -->
      <el-popover placement="bottom" trigger="click" popper-class="view-popover">
        <div>
          <div v-if="!systemId.includes(viewIndex)" class="view-item mb14" @click="updateView">{{ $t("ui.developModuleFormBoxUpdateCurrentView") }}</div>
          <div :class="!['0', '1', '2'].includes(viewIndex) ? '' : ''" class="view-item" @click="addViewFn">
            {{ $t("ui.developModuleFormBoxSaveAsNewView") }}
          </div>
        </div>

        <div slot="reference" v-show="advancedFilter && isCategory" class="shitu">
          {{ $t("ui.developModuleFormBoxSaveView") }}&nbsp;<span class="el-icon-arrow-down"></span>
        </div>
      </el-popover>
      <!-- 高级筛选 -->
      <el-popover
        v-model="$store.state.business.conditionDialog"
        placement="bottom-start"
        popper-class="condition-popover"
        trigger="manual"
        width="600"
      >
        <div class="condition-box">
          <div class="flex-between">
            <div class="title">{{ $t("ui.developModuleFormBoxFilterConditions") }}</div>
            <div class="el-icon-close pointer" @click="$store.state.business.conditionDialog = false" />
          </div>
          <condition-dialog
            v-if="$store.state.business.conditionDialog"
            :eventStr="`event`"
            :formArray="viewSearch"
            :max="9"
            :noRule="false"
            @saveCondition="saveCondition"
          />
        </div>
        <div v-if="isViewSearch" slot="reference" class="pointer text-16 el-dropdown-link" @click="onShow">
          {{ $t("ui.developModuleTreeFilter") }}&nbsp;<span class="iconfont iconshaixuan2"></span>
          <span v-if="additional_search.length > 0" class="yuan">
            {{ additional_search ? additional_search.length : 0 }}
          </span>
        </div>
      </el-popover>
      <!-- 时间排序 -->
      <el-popover
        ref="popover"
        placement="bottom"
        popper-class="time-popover"
        trigger="click"
        width="140"
        @show="handleSortPopoverShow"
      >
        <div class="field-box">
          <div
            v-for="(item, index) in timeSearch"
            :key="index"
            :class="activeIndex == item.field ? 'field-bga' : ''"
            class="field-text"
            @click="handleClick(item, index)"
          >
            <span v-if="activeIndex == item.field" class="el-icon-check"></span>
            <span class="over-text">{{ item.name }}</span>
          </div>
        </div>
        <div class="field-box">
          <div
            v-for="(item, index) in sortList"
            :key="index"
            :class="sortIndex == item.field ? 'field-bga' : ''"
            class="field-text"
            @click="sortFn(item, index)"
          >
            <span v-if="sortIndex == item.field" class="el-icon-check"></span>
            {{ item.name }}
          </div>
        </div>

        <div v-if="sortSearch" slot="reference" class="text-16 paixuBox pointer">
          <span class="iconfont iconpaixu5"></span>
        </div>
      </el-popover>
    </div>
  </div>
  <!-- 新建视图 -->
  <oa-dialog
    ref="oaDialog"
    :formConfig="formConfig"
    :formDataInit="formDataInit"
    :formRules="formRules"
    :fromData="fromData"
    @submit="submit"
  ></oa-dialog>
  <!-- 视图管理 -->

  <view-management
    ref="viewManagement"
    viewType="customer"
    :category="category"
    :list="viewList"
    :search_boolean="where.view_search_boolean"
    :senior_search="additional_search"
    @getViewList="getViewList"
  ></view-management>
</div>
</template>

<script>
import viewManagement from '@/components/develop/viewManagement'
import oaDialog from '@/components/form-common/dialog-form'
import formList from './formList'
import conditionDialog from '@/components/develop/conditionDialog'
import selectLabel from '@/components/form-common/select-label'
import { getViewSeachApi, putViewSeachInfoApi, saveViewSeachApi } from '@/api/client'
import { clientConfigLabelApi } from '@/api/enterprise'
import { getStorageJson } from '@/utils/storage'
import { translateRuntimeText } from '@/utils/i18ns'

export default {
  name: 'OaFromBox',
  components: {
    formList,
    conditionDialog,
    selectLabel,
    oaDialog,
    viewManagement
  },

  props: {
    // 组件类型
    type: {
      type: String,
      default: ''
    },
    isCategory: {
      type: Boolean,
      default: true
    },
    // 视图类型
    category: {
      type: String,
      default: ''
    },
    viewShow: {
      type: Boolean,
      default: true
    },

    // 是否显示返回按钮
    isBack: {
      type: Boolean,
      default: false
    },

    // 树形数据
    treeData: {
      type: Array,
      default: () => []
    },

    // 树形默认值
    treeDefault: {
      type: [String, Number],
      default: ''
    },

    // 下拉菜单列表
    dropdownList: {
      type: Array,
      default: () => []
    },

    // 总数
    total: {
      type: Number,
      default: 0
    },

    // 搜索条件
    search: {
      type: Array,
      default: () => []
    },

    // 多选ID集合
    ids: {
      type: Array,
      default: () => []
    },

    // 高级筛选条件
    viewSearch: {
      type: Array,
      default: () => []
    },

    // 时间区间
    timeVal: {
      type: [Array, String],
      default: () => []
    },

    // 是否显示高级筛选
    isViewSearch: {
      type: Boolean,
      default: true
    },

    timeSearchObj: {
      type: Object,
      default: () => {
        return {}
      }
    },

    // 是否显示排序
    sortSearch: {
      type: Boolean,
      default: true
    },

    // 是否显示新增按钮
    isAddBtn: {
      type: Boolean,
      default: true
    },

    // 标题
    title: {
      type: String,
      default: ''
    },

    // 按钮文字
    btnText: {
      type: String,
      default: '新增'
    },

    // 是否显示按钮图标
    btnIcon: {
      type: Boolean,
      default: true
    },

    // 按钮类型
    btnType: {
      type: String,
      default: ''
    },

    // 提示信息
    alert: {
      type: String,
      default: ''
    },

    // 是否显示总数
    isTotal: {
      type: Boolean,
      default: true
    },
    whereData: {
      type: Object,
      default: () => {
        return {}
      }
    },

    rightAreaVisible: {
      type: Boolean,
      default: true
    }
  },

  data() {
    return {
      // 筛选条件
      where: {
        sort_field: 'created_at',
        sort_value: 'desc'
      },
      viewText: '全部',
      viewIndex: '',

      loading: false, // 加载状态
      seniorSearch: this.search, // 高级搜索条件
      additional_search: [], // 附加搜索条件
      filterData: [], // 筛选数据用于不是响应式

      // 排序选项
      sortList: [
        { name: '升序', field: 'asc' },
        { name: '降序', field: 'desc' }
      ],
      // 时间搜索选项
      timeSearch: [
        { name: '创建时间', field: 'created_at' },
        { name: '修改时间', field: 'updated_at' }
      ],
      activeIndex: 'created_at', // 当前激活的时间索引
      sortIndex: 'desc', // 当前排序索引
      treeValue: '', // 树形选择值
      fromData: {
        width: '500px',
        title: '新建视图',
        btnText: '确定',
        labelWidth: '100px',
        type: ''
      },
      formDataInit: {
        senior_title: '',
        senior_type: '0'
      },
      formConfig: [
        {
          type: 'input',
          label: '视图名称：',
          placeholder: '请输入视图名称(10个字以内)',
          key: 'senior_title'
        },
        {
          type: 'radio',
          label: '视图类型：',
          placeholder: '请选择视图类型',
          key: 'senior_type',
          options: [
            {
              value: '个人',
              label: '0'
            },
            {
              value: '公共',
              label: '1'
            }
          ]
        }
      ],
      viewList: [],
      labelList: [],
      formRules: {
        senior_title: [
          {
            required: true,
            message: '请输入视图名称',
            trigger: 'blur'
          },
          { min: 0, max: 10, message: '最多输入10个字', trigger: 'blur' }
        ],
        senior_type: [
          {
            required: true,
            message: '请选择视图类型',
            trigger: 'change'
          }
        ]
      },
      defaultSearch: ['page', 'limit', 'sort_field', 'sort_value', 'types', 'view_search'],
      viewItem: {},
      viewData: {},
      systemId: [],
      searchDataList: [],
      advancedFilter: false
    }
  },

  watch: {
    // 监听搜索条件变化
    search(val) {
      val.map((item) => {
        this.defaultSearch.push(item.field)
      })
      this.seniorSearch = val
    },

    // 监听树形默认值变化
    treeDefault: {
      handler(val) {
        this.treeValue = val
      },
      immediate: true
    }
  },

  computed: {
    // 是否显示头部
    showHeader() {
      return this.title !== '' || this.isAddBtn || this.dropdownList.length > 0
    }
  },
  created() {
    if (this.category) {
      this.getViewList()
      this.viewDataFn()
    }
  },

  mounted() {
    // 视图管理回显
    // this.getLableList()
    if (this.treeData.length > 0) {
      this.treeData.map((item) => {
        this.systemId.push(item.id)
      })
    }
    setTimeout(() => {
      if (this.timeSearchObj && this.timeSearchObj.sort_field) {
        this.$set(this, 'timeSearch', this.timeSearchObj.sort_field)
        this.$set(this, 'sortList', this.timeSearchObj.sort_value)
      }
    }, 500)
  },
  methods: {
    translateText(text) {
      return translateRuntimeText(text, this)
    },
    handleSortPopoverShow() {
      this.$store.commit('updateConditionDialog', false)
    },
    /**
     * 处理表单提交
     * @param {Object} data - 表单数据
     */
    handleEmit(data, type) {
      if (this.category && !type) {
        this.advancedFilter = true
      }
      this.searchDataList = data
      this.where = { ...this.filterData, ...data }

      // 因为后端只识别空字符串，空数组的话就筛选不到
      for (let key in this.where) {
        if (Array.isArray(this.where[key]) && this.where[key].length === 0) {
          this.where[key] = ''
        }
      }
      this.$emit('confirmData', this.where)
    },

    // 视图管理缓存回显
    viewDataFn() {
      this.viewData = getStorageJson('viewData', {})
      if (this.viewData[this.category]) {
        this.viewText = this.viewData[this.category].viewText
        this.viewIndex = this.viewData[this.category].viewIndex
        this.where.view_search = this.viewData[this.category].view_search
        this.viewItem = this.viewData[this.category]
        if (this.viewData[this.category].content) {
          setTimeout(() => {
            this.viewList.map((item, index) => {
              if (item.id == this.viewData[this.category].id) {
                this.viewClick(item, index)
              }
            })
          }, 500)
        } else {
          this.searchFn()
        }
      } else if (this.treeData.length > 0) {
        this.viewText = this.treeData[0].label
        this.where.view_search = this.treeData[0].id
        this.viewIndex = this.treeData[0].id
        this.searchFn()
      } else {
        this.viewText = '全部'
        this.where.view_search = ''
        this.searchFn()
      }
    },

    // 获取视图列表
    getViewList(type) {
      let data = {
        category: this.category,
        title: '' // 视图类型
      }
      getViewSeachApi(data).then((res) => {
        this.viewList = res.data.list
      })
      if (type == 1) {
        this.viewDataFn()
      }
    },

    addViewFn() {
      this.$refs.oaDialog.openBox()
    },

    searchFn(type) {
      let obj = {}
      if (this.additional_search.length == 0) {
        const resetList = this.$store.state.business.fieldOptions.resetList
        if (resetList && resetList.length > 0) {
          resetList.map((item) => {
            if (item.type === 'date_picker') {
              obj[item.field] = ''
            } else {
              obj[item.field] = ''
            }
          })
        }
        if (type == 1) {
          this.where = { ...this.where, ...obj }
        } else {
          this.$emit('confirmData', { ...this.where, ...obj })
        }
      } else {
        this.additional_search.map((item) => {
          if (item.type === 'date_picker') {
            obj[item.field] = item.option[0] + '-' + item.option[1]
          } else if (item.type === 'tag') {
            obj[item.field] = item.optionsList
          } else {
            obj[item.field] = item.option
          }
        })
        if (type == 1) {
          this.where = { ...this.where, ...obj }
        } else {
          this.$emit('confirmData', { ...this.where, ...obj })
        }
      }
      this.filterData = { ...this.where, ...obj }
    },
    // 更新视图
    updateView() {
      let obj = {
        sort: 0,
        category: this.category,
        content: this.additional_search,
        title: this.viewItem.viewText || this.viewItem.title,
        is_public: this.viewItem.is_public
      }

      if (this.$refs.formList?.ruleForm) {
        let searchData = this.$refs.formList.ruleForm
        searchData = Object.entries(searchData)
          .filter(([_, value]) => value !== '')
          .map(([key, value]) => ({
            field: key,
            option: value
          }))
        obj.content = [...obj.content, ...searchData]
      }
      obj.content = obj.content.filter((item) => item.option && item.option.length > 0)
      putViewSeachInfoApi(this.viewItem.id, obj).then((res) => {
        this.getViewList()
        if (this.category) {
          this.viewData[this.category] = {
            viewText: this.viewText,
            viewIndex: this.viewItem.viewIndex,
            view_search: '2',
            is_public: this.viewItem.is_public,
            id: this.viewItem.id,
            viewText: this.viewItem.viewText,
            content: obj.content
          }
          localStorage.setItem('viewData', JSON.stringify(this.viewData))
        }
      })
    },

    // 新建视图

    async submit(data) {
      let obj = {
        title: data.senior_title,
        category: this.category,
        is_public: data.senior_type,
        sort: data.sort || 0,
        content: this.additional_search
      }
      if (this.$refs.formList && this.$refs.formList.ruleForm) {
        let searchData = this.$refs.formList.ruleForm
        for (let key in searchData) {
          let keyObj = {
            field: key,
            option: searchData[key]
          }
          obj.content.push(keyObj)
        }
      }
      obj.content = obj.content.filter((item) => item.option)
      const res = await saveViewSeachApi(obj)
      if (res.status == 200) {
        this.getViewList()
        setTimeout(() => {
          this.viewList.map((item, index) => {
            if (item.id == res.data.id) {
              this.viewClick(item, index)
            }
          })
        }, 100)

        this.$refs.oaDialog.handleClose()
      }
    },

    // 打开视图管理
    openViewBox() {
      this.$refs.viewManagement.openBox()
    },
    typeClick(item) {
      if (this.$refs.formList) {
        this.$refs.formList.resetSearch('typeClick')
      }
      if (this.$refs.formList) {
        this.treeValue = this.treeDefault
        this.where = {}
        this.additional_search = []
        this.searchDataList = []
        let data = {
          list: [],
          type: '',
          additional_search_boolean: '1'
        }

        this.$store.commit('uadatefieldOptions', data)
        this.advancedFilter = false
      }
      this.where.view_search = item.id

      this.$emit('confirmData', this.where)
      this.filterData = { ...this.where }

      this.where = {
        sort_field: 'created_at',
        sort_value: ''
      }

      this.viewIndex = item.id
      this.viewText = item.label

      if (this.category) {
        this.viewData[this.category] = { view_search: item.id, viewText: this.viewText, viewIndex: this.viewIndex }
        localStorage.setItem('viewData', JSON.stringify(this.viewData))
      }

      this.advancedFilter = false
      if (this.$refs.popoverType) {
        this.$refs.popoverType.doClose()
      }
    },
    viewClick(item, index) {
      // 先重置所以筛选条件
      if (this.$refs.formList) {
        this.$refs.formList.resetSearch('typeClick')
      }
      this.$set(this, 'additional_search', [])
      this.where = {
        sort_field: 'created_at',
        sort_value: ''
      }

      this.viewIndex = index + 'a'
      this.viewItem = item
      let NewItem = JSON.parse(JSON.stringify(item))
      this.viewText = item.title
      this.where.view_search = '2'
      this.viewData[this.category] = {
        viewText: this.viewText,
        viewIndex: this.viewIndex,
        view_search: '2',
        is_public: this.viewItem.is_public,
        id: this.viewItem.id,
        content: this.viewItem.content
      }
      localStorage.setItem('viewData', JSON.stringify(this.viewData))

      if (NewItem.content.length == 0) {
        this.$emit('confirmData', this.where)
        return false
      }
      // 判断有没有列表上方的筛选条件
      if (NewItem.content.length > 0) {
        NewItem.content.forEach((item) => {
          this.seniorSearch.forEach((el) => {
            if (el.field === item.field) {
              el.value = item.option
            }
          })
        })

        let filteredContent = NewItem.content.filter((contentItem) => {
          if (!contentItem?.field) return false // 过滤无效数据
          return !this.seniorSearch.some((el) => el.field === contentItem.field)
        })
        filteredContent = filteredContent.filter((item) => item.option)
        this.$set(this, 'additional_search', filteredContent)
        this.$store.commit('uadatefieldOptions', { list: filteredContent })
        this.$nextTick(() => {
          const formList = this.$refs.formList
          if (formList && typeof formList.setValue === 'function') {
            formList.setValue()
          }
        })
        this.searchFn()
      } else {
        this.$set(this, 'additional_search', [])
        this.$store.commit('uadatefieldOptions', { list: [] })
      }
      this.advancedFilter = false
      if (this.$refs.popoverType) {
        this.$refs.popoverType.doClose()
      }
    },
    getLableList() {
      let data = {
        page: 0,
        limit: 0
      }
      clientConfigLabelApi(data).then((res) => {
        this.labelList = res.data.list
      })
    },

    /**
     * 显示高级筛选
     */
    onShow() {
      if (this.additional_search.length > 0) {
        this.additional_search.forEach((item) => {
          if (item.type == 'tag') {
            item.options = this.labelList
          }
        })
      }
      setTimeout(() => {
        let data = {
          list: this.additional_search
        }
        this.$store.commit('uadatefieldOptions', data)
        this.$store.commit('updateConditionDialog', true)
      }, 100)
    },

    /**
     * 返回操作
     */
    backFn() {
      if (this.isBack) {
        this.$emit('backFn')
      }
    },

    /**
     * 重置搜索
     */
    resetSearch(type) {
      this.treeValue = this.treeDefault
      this.where = {
        sort_field: 'created_at',
        sort_value: 'desc'
        // view_search: this.where.view_search
      }

      this.additional_search = []
      this.searchDataList = []

      let data = {
        list: [],
        type: '',
        additional_search_boolean: '1'
      }

      this.$store.commit('uadatefieldOptions', data)
      this.advancedFilter = false
      if (!type) {
        this.$emit('confirmData', 'reset')
      }
    },

    /**
     * 新增数据
     */
    addDataFn() {
      this.$emit('addDataFn')
    },

    /**
     * 下拉菜单搜索
     * @param {Object} item - 菜单项
     */
    dropdownSearch(item) {
      this.$emit('dropdownFn', item)
    },

    /**
     * 处理标签配置
     * @param {Object} val - 标签值
     * @param {Object} item - 菜单项
     */
    handleLabelConf(val, item) {
      this.$emit('dropdownFn', item, val)
    },

    /**
     * 处理时间点击
     * @param {Object} item - 时间项
     * @param {Number} index - 索引
     */
    handleClick(item, index) {
      this.where.sort_field = item.field
      this.activeIndex = item.field
      this.$emit('confirmData', this.where)
    },

    /**
     * 排序操作
     * @param {Object} item - 排序项
     * @param {Number} index - 索引
     */
    sortFn(item, index) {
      this.where.sort_value = item.field
      this.sortIndex = item.field
      this.$emit('confirmData', this.where)
    },

    /**
     * 保存筛选条件
     * @param {Object} data - 筛选数据
     */
    saveCondition(data) {
      if (this.category) {
        this.advancedFilter = true
      }

      for (let key in this.whereData) {
        if (this.defaultSearch.includes(key)) {
        } else {
          this.whereData[key] = ''
        }
      }
      this.additional_search = data.list
      this.searchFn()
    },

    /**
     * 树形选择变化
     * @param {String} value - 选择的值
     */
    treeChange(value) {
      this.$emit('treeChange', { value })
    }
  }
}
</script>

<style lang="scss" scoped>
.grey-bga {
  ::v-deep .el-input__inner {
    border: none;
    // background: #f7f7f7;
  }
}

.search {
  //margin-top: 11px;
  display: flex;
  justify-content: space-between;
}

.title-16 {
  height: 32px !important;
  line-height: 32px;
}

.total-16 {
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #909399;
  min-width: fit-content;
  white-space: nowrap;
  height: 32px;
  line-height: 32px;
}

.field-bga {
  color: #1890ff;
  background: rgba(24, 144, 255, 0.07);
}

.field-color {
  color: #1890ff !important;
}
.spliceline {
  border-bottom: 1px solid #f5f5f5;
}

.el-icon-check {
  position: absolute;
  left: 10px;
  top: 11px;
}

.right {
  display: flex;
  flex-shrink: 0;
  gap: 8px;

  .yuan {
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 12px;
    color: #909399;
  }

  .iconpaixu5 {
    color: #999999;
    font-size: 15px;
  }

  .iconshaixuan2 {
    color: #999999;
    font-size: 15px;
  }

  .el-dropdown-link {
    height: 32px;
    padding: 0 10px;
    line-height: 32px;
    min-width: 55px;
    border: 1px solid #fff;
  }
}

.view-text {
  cursor: pointer;
  height: 36px;
  width: 100%;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #303133;
  line-height: 36px;
  padding-right: 16px;
  padding-left: 12px;
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: center;

  .tips {
    flex-shrink: 0;
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 12px;
    color: #909399;
  }

  .iconshituguanli {
    font-size: 12px;
    color: #909399;
    margin-right: 4px;
  }
}

.shitu {
  cursor: pointer;
  width: 85px;
  height: 33px;
  text-align: center;
  line-height: 33px;
  border-radius: 4px;
  border: 1px solid #1890ff;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #1890ff;
}

.view-text:hover {
  background-color: #f2f3f5;
}

.el-icon-question {
  cursor: pointer;
  color: #1890ff;
  font-size: 15px;
}

.el-dropdown-link:hover {
  // border: 1px solid #1890ff;
  background: #f7f7f7;
}

.field-box {
  margin-top: 8px;
  border-bottom: 1px solid #f5f5f5;
  margin-bottom: 8px;

  &:not(:has(~ .field-box)) {
    border-bottom: none;
  }
}

.paixuBox {
  width: 25px;
  height: 32px;
  line-height: 32px;
  display: flex;
  justify-content: center;
  border: 1px solid #fff;
}

.paixuBox:hover {
  background: #f7f7f7;
  // border: 1px solid #1890ff;
}

.view-item {
  cursor: pointer;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #303133;
}

.field-text {
  cursor: pointer;
  height: 32px;
  // background-color: pink;
  width: 100%;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #303133;
  line-height: 32px;
  padding-right: 15px;
  padding-left: 29px;
  position: relative;
}

.field-text:hover {
  background-color: #f2f3f5;
}

.ml29 {
  margin-left: 29px;
}

.ml3 {
  margin-left: 3px;
}

.field-bga {
  color: #1890ff;
  background: rgba(24, 144, 255, 0.07);
}

.el-icon-check {
  position: absolute;
  left: 14px;
  top: 11px;
}

.prompt-bag {
  background-color: #edf5ff;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 12px;
  line-height: 12px;
  color: #606266;
  margin-bottom: 6px;
}

.condition-box {
  padding-top: 5px;
  max-height: 350px !important;
  overflow-y: auto !important;

  .flex-between {
    display: flex;
    // border-bottom: 1px solid hsl(223, 13%, 89%);
    padding-bottom: 15px;
  }

  .title {
    font-size: 14px;
    font-family: PingFangSC-Semibold, PingFang SC;
    font-weight: 500;
    color: #333333;
  }
}

.condition-box::-webkit-scrollbar {
  height: 0;
  width: 0;
}

.height300 {
  max-height: 300px;
  overflow-y: auto;
  scrollbar-width: none; /* firefox */
  -ms-overflow-style: none; /* IE 10+ */
}

.xiala-box {
  width: 32px;
  height: 32px;
  cursor: pointer;
  border: 1px solid #909399;
  text-align: center;
  line-height: 32px;
  border-radius: 4px;
}

.xiala-box:hover {
  border: 1px solid #1890ff;
  box-shadow: 0 0 4px rgba(24, 144, 255, 0.35);
}
.border-none {
  border: none !important;
}
</style>
<style>
.time-popover {
  padding: 0;
  min-width: 0;
  transform: translateX(-10px);
}
.view-popover {
  min-width: 0;
  padding: 12px;
}
.condition-popover {
  transform: translateX(-10px);
}

.monitor-yt-popover {
  background: #edf5ff;
  border: 1px solid #97c3ff;
  padding: 11px 15px 0px 15px;
}
</style>
