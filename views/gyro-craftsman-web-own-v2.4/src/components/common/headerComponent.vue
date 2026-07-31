<!--
  @FileDescription: 全局筛选组件
  功能：提供统一的筛选、排序、新增等功能
-->
<template>
  <div>
    <!-- 头部区域 -->
    <div v-if="showHeader" class="header-16 mb20">
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
      <div class="flex lh-center">
        <slot name="rightBtn"></slot>

        <!-- 新增/导出按钮 -->
        <el-button
          v-if="isAddBtn && btnType !== 'default'"
          :icon="btnIcon ? 'el-icon-plus' : ''"
          :loading="loading"
          :type="btnText == 'customer.export' ? '' : 'primary'"
          class="h32"
          size="small"
          @click="addDataFn()"
        >
          {{ t(btnText) }}
        </el-button>

        <!-- 默认按钮 -->
        <el-button v-if="btnType === 'default'" class="h32" size="small" @click="addDataFn()">
          {{ btnText }}
        </el-button>

        <!-- 下拉菜单 -->
        <div v-if="dropdownList.length > 0">
          <el-dropdown>
            <span class="iconfont icongengduo2 pointer ml10"></span>
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
    <div class="search flex lh-center">
      <header-search
        :tree-data="treeData"
        :view-list="viewList"
        :category="category"
        :generalSearch="generalSearch"
        :fleld-config="fieldConfig"
        @handleSubmit="handleEmit"
      />

      <!-- 右侧操作区域 -->
      <div class="right">
        <!-- 视图更新 -->
        <el-popover placement="bottom" trigger="click" width="117">
          <div>
            <div v-if="!systemId.includes(viewIndex)" class="view-item" @click="updateView">{{ $ts("更新当前视图") }}</div>
            <div :class="!['0', '1', '2'].includes(viewIndex) ? 'mt14' : ''" class="view-item" @click="addViewFn">
              {{ $ts("存为新视图") }}
            </div>
          </div>

          <div slot="reference" v-show="advancedFilter" class="shitu">
            {{ $ts("保存视图&nbsp;") }}<span class="el-icon-arrow-down"></span>
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
              <div class="title">{{ $ts("筛选条件") }}</div>
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
            {{ $ts("筛选&nbsp;") }}<span class="iconfont iconshaixuan2"></span>
            <span v-if="additional_search.length > 0" class="yuan">
              {{ additional_search ? additional_search.length : 0 }}
            </span>
          </div>
        </el-popover>
        <!-- 时间排序 -->
        <el-popover ref="popover" placement="bottom" popper-class="time-popover" trigger="click" width="140">
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
      :senior_search="seniorSearch"
      @getViewList="getViewList"
    ></view-management>
  </div>
</template>

<script>
import viewManagement from '@/components/develop/viewManagement'
import oaDialog from '@/components/form-common/dialog-form'
import formList from '@/views/develop/module/components/formList'
import conditionDialog from '@/components/develop/conditionDialog'
import selectLabel from '@/components/form-common/select-label'
import { getViewSeachApi, putViewSeachInfoApi, saveViewSeachApi } from '@/api/client'
import { clientConfigLabelApi } from '@/api/enterprise'
import HeaderSearch from '@/components/common/headerSearch.vue'
import { getStorageJson } from '@/utils/storage'

export default {
  name: 'OaFromBox',
  components: {
    HeaderSearch,
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
    // search: {
    //   type: Array,
    //   default: () => []
    // },

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
    fieldConfig: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },

  data() {
    return {
      // 定时器引用
      timers: {
        mounted: null,
        viewData: null,
        onShow: null
      },
      // 筛选条件
      where: {
        sort_field: 'created_at',
        sort_value: 'desc'
      },
      viewText: '全部',
      viewIndex: '',

      loading: false, // 加载状态
      additional_search: [], // 附加搜索条件
      filterData: [], // 筛选数据

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
      sortIndex: '', // 当前排序索引
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
      advancedFilter: false,
      generalSearch:[],// 普通搜索条件
      seniorSearch:[]// 高级搜索条件
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
    fieldConfig: {
      handler(val) {
        const { search_select, search } = val
        const generalItem = []
        const seniorItem = []
        search.forEach(item => {
          const processed = { ...item }
          if (processed.input_type === 'date') {
            processed.input_type = 'date_picker'
          } else if (processed.input_type === 'select') {
            if (processed.field === 'area_cascade') {
              processed.input_type = 'cascader_address'
            } else if (processed.type === 'single') {
              processed.input_type = 'cascader_radio'
            } else if (processed.type === 'multiple' || !processed.input_type) {
              processed.input_type = 'cascader'
            }
          }
          // 单独处理customer_label字段
          if (processed.field === 'customer_label') {
            processed.input_type = 'tag'
          }
          // 处理字典数据
          if (processed.dict) {
            this.mapDict(processed.dict)
          }
          // 批量赋值（用对象展开替代重复赋值）
          Object.assign(processed, {
            form_value: processed.input_type,
            field_name_en: processed.field,
            field_name: processed.name,
            title: processed.name,
            options: processed.dict,
            data_dict: processed.dict,
            type: processed.input_type,
            is_city_show: ''
          })
          search_select.includes(processed.field) ? generalItem.push(processed) : seniorItem.push(processed)
        });
        this.generalSearch = generalItem
        this.seniorSearch = seniorItem
      },
      deep: true,
      immediate: true
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
    },
    search() {
      const {
        search_select: searchSelectList,
        search: searchList,
        list,
        list_select: listSelect,
        sort_field: sortField,
        sort_value: sortValue
      } = this.fieldConfig
      if (!searchList) {
        return []
      }
      const search = []
      const viewSearch = []
      // 遍历处理搜索项，用forEach替代for循环
      searchList.forEach((item) => {
        // 复制对象避免直接修改源数据（可选）
        const processed = { ...item }

        if (processed.input_type === 'date') {
          processed.input_type = 'date_picker'
        } else if (processed.input_type === 'select') {
          if (processed.field === 'area_cascade') {
            processed.input_type = 'cascader_address'
          } else if (processed.type === 'single') {
            processed.input_type = 'cascader_radio'
          } else if (processed.type === 'multiple' || !processed.input_type) {
            processed.input_type = 'cascader'
          }
        }
        // 单独处理customer_label字段
        if (processed.field === 'customer_label') {
          processed.input_type = 'tag'
        }
        // 处理字典数据
        if (processed.dict) {
          this.mapDict(processed.dict)
        }
        // 批量赋值（用对象展开替代重复赋值）
        Object.assign(processed, {
          form_value: processed.input_type,
          field_name_en: processed.field,
          field_name: processed.name,
          title: processed.name,
          options: processed.dict,
          data_dict: processed.dict,
          type: processed.input_type,
          is_city_show: ''
        })

        // 分类添加到数组
        searchSelectList.includes(processed.field) ? search.push(processed) : viewSearch.push(processed)
      })
      return search
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
    this.getLableList()
    if (this.treeData.length > 0) {
      this.treeData.map((item) => {
        this.systemId.push(item.id)
      })
    }
    this.timers.mounted = setTimeout(() => {
      if (this.timeSearchObj && this.timeSearchObj.sort_field) {
        this.$set(this, 'timeSearch', this.timeSearchObj.sort_field)
        this.$set(this, 'sortList', this.timeSearchObj.sort_value)
      }
    }, 500)
  },
  beforeDestroy() {
    // 清理所有定时器
    Object.keys(this.timers).forEach(key => {
      if (this.timers[key]) {
        clearTimeout(this.timers[key])
        this.timers[key] = null
      }
    })
  },
  methods: {
    mapDict(dict) {
      for (let i = 0; i < dict.length; i++) {
        dict[i].name = dict[i].label
        if (dict[i].children) {
          this.mapDict(dict[i].children)
        }
      }
    },
    /**
     * 处理表单提交
     * @param {Object} data - 表单数据
     */
    handleEmit(data, type) {
      this.$emit('confirmData', data)
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
          this.timers.viewData = setTimeout(() => {
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

      //
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
      if (this.additional_search == 0) {
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
    submit(data) {
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
      saveViewSeachApi(obj).then((res) => {
        if (res.status == 200) {
          this.getViewList()
          this.viewList.map((item, index) => {
            if (item.id == res.data.id) {
              this.viewClick(item, index)
            }
          })

          this.$refs.oaDialog.handleClose()
        }
      })
    },

    // 打开视图管理
    openViewBox() {
      this.$refs.viewManagement.openBox()
    },
    typeClick(item) {
      if (this.$refs.formList) {
        this.$refs.formList.resetSearch()
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
      // 判断有没有列表上方的筛选条件
      if (NewItem.content.length > 0) {
        NewItem.content.forEach((item) => {
          this.seniorSearch.forEach((el) => {
            el.value = ''
            if (el.field === item.field) {
              el.value = item.option
            }
          })
        })

        const filteredContent = NewItem.content.filter((contentItem) => {
          if (!contentItem?.field) return false // 过滤无效数据
          return !this.seniorSearch.some((el) => el.field === contentItem.field)
        })

        this.$set(this, 'additional_search', filteredContent)
        this.$store.commit('uadatefieldOptions', { list: filteredContent })
        this.$nextTick(() => {
          const formList = this.$refs.formList
          if (formList && typeof formList.setValue === 'function') {
            formList.setValue()
          }
        })
        this.searchFn(1)
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
      this.timers.onShow = setTimeout(() => {
        let data = {
          list: this.additional_search
        }
        this.$store.commit('uadatefieldOptions', data)
        this.$store.commit('updateConditionDialog', true)
      }, 500)
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
    resetSearch() {
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
      this.$emit('confirmData', 'reset')
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
    background: #f7f7f7;
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
  min-width: 50px;
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

.el-icon-check {
  position: absolute;
  left: 10px;
  top: 11px;
}

.right {
  display: flex;
  flex-shrink: 0;

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
  height: 32px;
  width: 100%;
  font-family: PingFang SC, PingFang SC;
  font-weight: 400;
  font-size: 13px;
  color: #303133;
  line-height: 32px;
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
}

.icongengduo2 {
  font-size: 32px !important;
}
.border-none {
  border: none !important;
}
</style>
<style>
.time-popover {
  padding: 0;
}

.monitor-yt-popover {
  background: #edf5ff;
  border: 1px solid #97c3ff;
  padding: 11px 15px 0px 15px;
}
</style>
