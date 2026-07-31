<script>
export default {
  name: 'headerSearch',
  components: {
    conditionDialog: () => import('@/components/develop/conditionDialog.vue'),
    formList: () => import('@/components/common/formList.vue')
  },
  props: {
    // 组件类型
    type: {
      type: String,
      default: ''
    },
    // 视图类型
    category: {
      type: String,
      default: ''
    },
    isCategory: {
      type: Boolean,
      default: true
    },
    // 系统视图
    treeData: {
      type: Array,
      default: () => []
    },
    // 自定义视图
    viewList: {
      type: Array,
      default: () => []
    },
    // 是否显示总数
    isTotal: {
      type: Boolean,
      default: true
    },
    // 是否可保存视图
    saveView: {
      type: Boolean,
      default: false
    },
    // 时间区间
    timeVal: {
      type: [Array, String],
      default: () => []
    },
    //搜索条件
    generalSearch: {
      type: Array,
      default: () => []
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
      // 视图名称
      viewText: '',
      // 视图索引
      viewIndex: 0,
      // 树形选择值
      treeValue: '',
      // 总数
      total: 0,
      // 时间排序
      timeSearch: [],
      // 时间排序索引
      activeIndex: '',
      // 排序索引
      systemId: [],
      advancedFilter: false,
      sortList: [
        { name: '升序', field: 'asc' },
        { name: '降序', field: 'desc' }
      ],
      sortIndex: '', // 当前排序索引
      searchData: {
        view_search: 2
      }
    }
  },
  watch: {
    searchData: {
      handler(val) {
        this.$emit('handleSubmit', val)
      },
      deep: true,
      immediate: true
    },
    fieldConfig: {
      handler(val) {
        // 字段配置变化处理
      },
      deep: true,
      immediate: true
    }
  },
  computed: {
    viewSearchData() {
      let treeData = this.treeData.map((item) => {
        return {
          label: item.label,
          value: 'sys' + item.id,
          isPublic: 0
        }
      })
      let viewData = this.viewList.map((item) => {
        return {
          label: item.title,
          value: item.id,
          isPublic: item.is_public ? 1 : 2
        }
      })
      return [...treeData, ...viewData]
    }
  },
  methods: {
    viewType(typeVal) {
      switch (typeVal) {
        case 0:
          return '系统'
        case 1:
          return '公共'
        case 2:
          return '个人'
        default:
          return '未知'
      }
    },
    // 打开视图管理
    openViewBox() {
      this.$refs.viewManagement.openBox()
    },
    handleEmit(data, type) {
      const emptyKeys = Object.keys(data || {}).filter((key) => {
        return data[key] === '' || data[key] === null || data[key] === undefined || (Array.isArray(data[key]) && data[key].length === 0)
      })
      emptyKeys.forEach((key) => {
        // 方式1：Vue2 用 $delete 确保响应式删除（推荐）
        if (this.$delete) {
          this.$delete(this.searchData, key)
          this.$delete(data, key)
        }
        // 方式2：原生JS删除（非响应式场景）
        else {
          delete this.searchData[key]
          delete data[key]
        }
      })
      this.searchData = { ...this.searchData, ...data }
    },
    resetSearch() {
      this.viewClick(this.viewSearchData[this.viewIndex],this.viewIndex)
    },
    viewClick(item, index) {
      this.searchData = {}
      this.viewIndex = index
      this.$refs.popoverType.doClose()
      if (String(item.value).includes('sys')) {
        this.searchData.view_search = item.value.replace('sys', '')
      } else {
        let viewInfo = this.viewList.find((val) => val.id === item.value)
        if (viewInfo.content) {
          const searchData = viewInfo.content.reduce((obj, item) => {
            obj[item.field] = item.option
            return obj
          }, {})
          let data = viewInfo.content.find((val) => val.field === 'view_search')
          data && (searchData.view_search = data.value)
          this.searchData = searchData
        }
      }
    }
  }
}
</script>

<template>
  <div class="flex">
    <!-- 视图筛选 -->
    <template v-if="category">
      <el-popover ref="popoverType" placement="bottom-start" popper-class="time-popover" trigger="click" width="140">
        <div class="field-box mb0 height300" :class="saveView || 'border-none'">
          <div
            v-for="(item, index) in viewSearchData"
            :key="index"
            :class="viewIndex !== index || 'field-color'"
            class="view-text"
            @click="viewClick(item, index)"
          >
            <span class="over-text">{{ item.label }} </span>
            <span class="tips">{{ viewType(item.isPublic) }}</span>
          </div>
        </div>
        <div class="view-text" v-if="category && isCategory" @click="openViewBox">
          <div><span class="iconfont iconshituguanli"></spa{{ $t("ui.commonHeaderSearchViewManagement") }}管理</div>
        </div>

        <div slot="reference" class="view-box mr10">
          <span class="over-text1">{{ viewSearchData[viewIndex]?.label || $t('ui.commonHeaderSearchChooseAView') }}</span>
          <span class="el-icon-arrow-down"></span>
        </div>
      </el-popover>
    </template>
    <!-- 总数显示 -->
    <div v-if="isTotal" class="total-16">共 {{ total }} 条</div>
    <!-- 表单筛选 -->
    <formList
      v-show="generalSearch.length > 0"
      ref="formList"
      :isTimeArray="false"
      :list="generalSearch"
      :timeValue="timeVal"
      :type="type"
      @handleEmit="handleEmit"
      @resetSearch="resetSearch"
    />
  </div>
</template>

<script>
export default {
  name: 'headerSearch',
  components: {
    conditionDialog: () => import('@/components/develop/conditionDialog.vue'),
    formList: () => import('@/components/common/formList.vue')
  },
  props: {
    // 组件类型
    type: {
      type: String,
      default: ''
    },
    // 视图类型
    category: {
      type: String,
      default: ''
    },
    isCategory: {
      type: Boolean,
      default: true
    },
    // 系统视图
    treeData: {
      type: Array,
      default: () => []
    },
    // 自定义视图
    viewList: {
      type: Array,
      default: () => []
    },
    // 是否显示总数
    isTotal: {
      type: Boolean,
      default: true
    },
    // 是否可保存视图
    saveView: {
      type: Boolean,
      default: false
    },
    // 时间区间
    timeVal: {
      type: [Array, String],
      default: () => []
    },
    //搜索条件
    generalSearch: {
      type: Array,
      default: () => []
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
      // 视图名称
      viewText: '',
      // 视图索引
      viewIndex: 0,
      // 树形选择值
      treeValue: '',
      // 总数
      total: 0,
      // 时间排序
      timeSearch: [],
      // 时间排序索引
      activeIndex: '',
      // 排序索引
      systemId: [],
      advancedFilter: false,
      sortList: [
        { name: '升序', field: 'asc' },
        { name: '降序', field: 'desc' }
      ],
      sortIndex: '', // 当前排序索引
      searchData: {
        view_search: 2
      }
    }
  },
  watch: {
    searchData: {
      handler(val) {
        this.$emit('handleSubmit', val)
      },
      deep: true,
      immediate: true
    },
    fieldConfig: {
      handler(val) {
        // 字段配置变化处理
      },
      deep: true,
      immediate: true
    }
  },
  computed: {
    viewSearchData() {
      let treeData = this.treeData.map((item) => {
        return {
          label: item.label,
          value: 'sys' + item.id,
          isPublic: 0
        }
      })
      let viewData = this.viewList.map((item) => {
        return {
          label: item.title,
          value: item.id,
          isPublic: item.is_public ? 1 : 2
        }
      })
      return [...treeData, ...viewData]
    }
  },
  methods: {
    viewType(typeVal) {
      switch (typeVal) {
        case 0:
          return '系统'
        case 1:
          return '公共'
        case 2:
          return '个人'
        default:
          return '未知'
      }
    },
    // 打开视图管理
    openViewBox() {
      this.$refs.viewManagement.openBox()
    },
    handleEmit(data, type) {
      const emptyKeys = Object.keys(data || {}).filter((key) => {
        return data[key] === '' || data[key] === null || data[key] === undefined || (Array.isArray(data[key]) && data[key].length === 0)
      })
      emptyKeys.forEach((key) => {
        // 方式1：Vue2 用 $delete 确保响应式删除（推荐）
        if (this.$delete) {
          this.$delete(this.searchData, key)
          this.$delete(data, key)
        }
        // 方式2：原生JS删除（非响应式场景）
        else {
          delete this.searchData[key]
          delete data[key]
        }
      })
      this.searchData = { ...this.searchData, ...data }
    },
    resetSearch() {
      this.viewClick(this.viewSearchData[this.viewIndex],this.viewIndex)
    },
    viewClick(item, index) {
      this.searchData = {}
      this.viewIndex = index
      this.$refs.popoverType.doClose()
      if (String(item.value).includes('sys')) {
        this.searchData.view_search = item.value.replace('sys', '')
      } else {
        let viewInfo = this.viewList.find((val) => val.id === item.value)
        if (viewInfo.content) {
          const searchData = viewInfo.content.reduce((obj, item) => {
            obj[item.field] = item.option
            return obj
          }, {})
          let data = viewInfo.content.find((val) => val.field === 'view_search')
          data && (searchData.view_search = data.value)
          this.searchData = searchData
        }
      }
    }
  }
}
</script>

<template>
<div class="flex">
  <!-- 视图筛选 -->
  <template v-if="category">
    <el-popover ref="popoverType" placement="bottom-start" popper-class="time-popover" trigger="click" width="140">
      <div class="field-box mb0 height300" :class="saveView || 'border-none'">
        <div
          v-for="(item, index) in viewSearchData"
          :key="index"
          :class="viewIndex !== index || 'field-color'"
          class="view-text"
          @click="viewClick(item, index)"
        >
          <span class="over-text">{{ item.label }} </span>
          <span class="tips">{{ viewType(item.isPublic) }}</span>
        </div>
      </div>
      <div class="view-text" v-if="category && isCategory" @click="openViewBox">
        <div><span class="iconfont iconshituguanli"></span>视图管理</div>
      </div>

      <div slot="reference" class="view-box mr10">
        <span class="over-text1">{{ viewSearchData[viewIndex]?.label || $t('ui.commonHeaderSearchChooseAView') }}</span>
        <span class="el-icon-arrow-down"></span>
      </div>
    </el-popover>
  </template>
  <!-- 总数显示 -->
  <div v-if="isTotal" class="total-16">共 {{ total }} 条</div>
  <!-- 表单筛选 -->
  <formList
    v-show="generalSearch.length > 0"
    ref="formList"
    :isTimeArray="false"
    :list="generalSearch"
    :timeValue="timeVal"
    :type="type"
    @handleEmit="handleEmit"
    @resetSearch="resetSearch"
  />
</div>
</template>

<style scoped lang="scss">
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
.time-popover {
  padding: 0;
}

.monitor-yt-popover {
  background: #edf5ff;
  border: 1px solid #97c3ff;
  padding: 11px 15px 0px 15px;
}
</style>
