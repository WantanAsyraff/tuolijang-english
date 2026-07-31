<!-- 办公-我的待办 Tab 内容 -->
<template>
<div class="my-todo">
  <!-- 左侧：分类 -->
  <div class="todo-side">
    <div class="category-header">
      <span class="category-title">{{ $t("ui.userCalendarMyTodoCategory") }}</span>
    </div>
    <ul class="category-list">
      <!-- 全部待办 -->
      <li class="category-item" :class="{ active: activeCategory === 'all' }" @click="handleCategoryChange('all')">
        <span class="category-label">
          <i class="category-icon iconfont iconquanbu" />
          <span class="category-text">{{ $t("ui.userCalendarMyTodoAllToDos") }}</span>
        </span>
        <span v-if="totalCount > 0" class="category-count">{{ totalCount }}</span>
      </li>
      <!-- 动态分类列表 -->
      <li
        v-for="cat in categories"
        :key="cat.key"
        class="category-item"
        :class="{ active: activeCategory === cat.key }"
        @click="handleCategoryChange(cat.key)"
      >
        <span class="category-label">
          <i class="category-icon iconfont icondaiban1" />
          <span class="category-text">{{ cat.label }}</span>
        </span>
        <span v-if="cat.count > 0" class="category-count">{{ cat.count }}</span>
      </li>
    </ul>
  </div>
  <!-- 右侧：待办列表 -->
  <div class="todo-main">
    <div class="todo-header">
      <span class="todo-main-title">{{ currentCategory.label }}</span>
    </div>
    <!-- 滚动区域 -->
    <div v-loading="listLoading" class="todo-scroll-area" @scroll.passive="handleScroll">
      <ul class="todo-list">
        <li v-for="item in todoList" :key="item.id" class="todo-item" @click="handleItemClick(item)">
          <div class="todo-content">
            <div class="todo-title">{{ item.title }}</div>
            <div class="todo-meta">{{ $t("ui.fdEnterpriseListViewDetailsCreatedTime") }}{{ item.source_created_at }}</div>
          </div>
        </li>
        <li v-if="todoList.length === 0 && pendingFinished && !listLoading" class="todo-empty mb14">{{ $t("ui.userCalendarMyTodoNoToDoItems") }}</li>
      </ul>

      <!-- 已完成 -->
      <el-collapse
        v-if="pendingFinished && (completedList.length > 0 || !completedFinished)"
        v-model="completedCollapseActive"
        accordion
        class="completed-collapse"
      >
        <el-collapse-item name="completed">
          <template slot="title">{{ completedCollapseTitle }}</template>
          <div class="box">
            <ul class="todo-list">
              <li v-for="item in completedList" :key="item.id" class="todo-item" @click="handleItemClick(item)">
                <div class="todo-content">
                  <div class="todo-title">{{ item.title }}</div>
                  <div class="todo-meta">{{ $t("ui.fdEnterpriseListViewDetailsCreatedTime") }}{{ item.source_created_at }}</div>
                </div>
              </li>
            </ul>
          </div>
        </el-collapse-item>
      </el-collapse>

      <div v-if="pendingLoading || completedLoading" class="load-more-tip">{{ $t("ui.userCalendarMyTodoLoading") }}</div>
      <!-- <div v-if="todoList.length > 0 || completedList.length > 0" class="load-more-tip">没有更多数据</div> -->
    </div>
  </div>
  <!-- 日程详情弹窗 -->
  <CalendarDetails ref="calendarDetailsRef" @deleteFn="init" @editFn="init" />

  <EditCustomerComp ref="customerEditRef" :form-data="customerFromData" />

  <CheckContractComp ref="checkContractRef" :form-data="contractFromData" />

  <InvoiceDetailsComp ref="invoiceEditRef" :form-data="invoiceFromData" />
  <EditTask
    :projectList="projectList"
    :programOptions="programOptions"
    :programMemberOptions="programMemberOptions"
    :programVersionList="programVersionList"
    ref="editTask"
  />
  <ExamineComp ref="examineEditRef" @getList="init" />
  <!-- 考核详情   -->

  <!-- <editTask ref="editTask" /> -->
</div>
</template>
<script setup>
import { ref, computed, onMounted, getCurrentInstance } from 'vue'
import { todoOverviewApi, todoListApi } from '@/api/user'
import CalendarDetails from './calendarDetails.vue'
import { roterPre } from '@/settings'
import {
  getProgramSelectApi,
  getProgramVersionSelectApi,
  getProgramTaskSelectApi,
  getProgramMemberApi
} from '@/api/program'
// import auditProcess from '@/views/user/assessment/components/auditProcess'
import EditTask from '@/views/program/programTask/components/editTask.vue'
import EditCustomerComp from '@/views/customer/list/components/editCustomer.vue'
import CheckContractComp from '@/views/customer/signing/components/checkContract.vue'
import InvoiceDetailsComp from '@/components/invoice/invoiceDetails.vue'
import ExamineComp from '@/views/user/examine/components/detailExamine.vue'
import { useCustomer } from '../composables/useCustomer'
import { useContract } from '../composables/useContract'
import { useInvoice } from '../composables/useInvoice'
import { useExamine } from '../composables/useExamine'

const editTask = ref(null)
const projectList = ref([])
const programOptions = ref([])
const programMemberOptions = ref([])
const programVersionList = ref([])

const { proxy } = getCurrentInstance()

const { customerEditRef, customerFromData, openCustomerPanel } = useCustomer()

const { checkContractRef, contractFromData, openCheckContractPanel } = useContract()

const { invoiceEditRef, invoiceFromData, openInvoicePanel } = useInvoice()

const { examineEditRef, openExaminePanel } = useExamine()

// 日程详情相关
const calendarDetailsRef = ref(null)

const completedList = ref([])
const completedCollapseActive = ref('')
const completedCollapseTitle = computed(() =>
  completedCollapseActive.value === 'completed' ? '隐藏已完成事项' : '显示已完成事项'
)

// 分类列表
const categories = ref([])

// 全部待办总数
const totalCount = computed(() => {
  return categories.value.reduce((sum, cat) => sum + (cat.count || 0), 0)
})

/**
 * 获取项目列表
 */
const getProgram = async () => {
  const result = await getProgramSelectApi()
  projectList.value = result.data
}

/**
 * 获取选择项目的任务列表
 */
const getProgramSelect = async (program_id) => {
  const result = await getProgramTaskSelectApi({ program_id: program_id })
  programOptions.value = result.data
}

// 获取任务负责人
const getProgramMember = async (program_id) => {
  const result = await getProgramMemberApi({ program_id })
  programMemberOptions.value = result.data
}

// 获取项目版本
const getProgramVersion = async (program_id) => {
  const result = await getProgramVersionSelectApi({ program_id })
  // 过滤掉 name 为空的选项
  programVersionList.value = result.data.filter((item) => item.name && item.name.trim() !== '')
}

const activeCategory = ref('all')

const currentCategory = computed(() => {
  if (activeCategory.value === 'all') {
    return { key: 'all', label: '全部待办', count: totalCount.value }
  }
  return (
    categories.value.find((c) => c.key === activeCategory.value) || {
      key: 'all',
      label: '全部待办',
      count: totalCount.value
    }
  )
})

const listLoading = ref(false)
const todoList = ref([])

const PAGE_SIZE = 20
const pendingPage = ref(1)
const completedPage = ref(1)
const pendingTotal = ref(0)
const pendingFinished = ref(false)
const completedFinished = ref(false)
const pendingLoading = ref(false)
const completedLoading = ref(false)

// 获取待办概览统计
async function fetchOverview() {
  try {
    const result = await todoOverviewApi()
    const data = result.data
    if (data && typeof data === 'object') {
      // 过滤掉 tips 字段，只保留分类数据
      const categoryData = { ...data }
      delete categoryData.tips
      categories.value = Object.entries(categoryData).map(([key, value]) => ({
        key,
        label: value.label,
        count: value.count || 0
      }))
    }
  } catch (e) {
    console.error('获取待办概览失败:', e)
  }
}

// 构建列表查询参数
function buildListParams(status, page) {
  const params = {
    page,
    limit: PAGE_SIZE,
    status
  }
  if (activeCategory.value !== 'all') {
    params.type = activeCategory.value
  }
  return params
}

function resetListState() {
  todoList.value = []
  completedList.value = []
  pendingPage.value = 1
  completedPage.value = 1
  pendingTotal.value = 0
  pendingFinished.value = false
  completedFinished.value = false
  completedCollapseActive.value = ''
}

// 加载未完成待办
async function loadPending(reset = false) {
  if (pendingLoading.value || pendingFinished.value) return

  pendingLoading.value = true
  try {
    const page = reset ? 1 : pendingPage.value
    const result = await todoListApi(buildListParams(1, page))
    const list = result.data.list || []
    pendingTotal.value = result.data.count || 0
    todoList.value = reset ? list : [...todoList.value, ...list]

    const noMore = list.length < PAGE_SIZE || todoList.value.length >= pendingTotal.value
    if (noMore) {
      pendingFinished.value = true
      await loadCompleted(true)
    } else {
      pendingPage.value = page + 1
    }
  } catch (e) {
    console.error('获取未完成待办失败:', e)
  } finally {
    pendingLoading.value = false
  }
}

// 加载已完成待办
async function loadCompleted(reset = false) {
  if (completedLoading.value || completedFinished.value) return

  completedLoading.value = true
  try {
    const page = reset ? 1 : completedPage.value
    const result = await todoListApi(buildListParams(2, page))
    const list = result.data.list || []
    const total = result.data.count || 0
    completedList.value = reset ? list : [...completedList.value, ...list]

    const noMore = list.length < PAGE_SIZE || completedList.value.length >= total
    if (noMore) {
      completedFinished.value = true
    } else {
      completedPage.value = page + 1
    }
  } catch (e) {
    console.error('获取已完成待办失败:', e)
  } finally {
    completedLoading.value = false
  }
}

// 初始化/切换分类时加载列表
async function fetchList() {
  listLoading.value = true
  resetListState()
  try {
    await loadPending(true)
  } catch (e) {
    console.error('获取待办列表失败:', e)
  } finally {
    listLoading.value = false
  }
}

function handleScroll(e) {
  const { scrollTop, scrollHeight, clientHeight } = e.target
  if (scrollHeight - scrollTop - clientHeight > 40) return
  if (listLoading.value || pendingLoading.value || completedLoading.value) return

  if (!pendingFinished.value) {
    loadPending()
  } else if (!completedFinished.value) {
    loadCompleted()
  }
}

// 分类切换
function handleCategoryChange(key) {
  activeCategory.value = key
  fetchList()
}

// 点击待办项
function handleItemClick(item) {
  const navigate = (path, query) => {
    proxy.$router.push({
      path: roterPre + path,
      query
    })
  }

  if (item.type === 'schedule') {
    // 打开日程详情弹窗
    calendarDetailsRef.value?.openBox({
      itemId: item.source_id,
      start_time: item.extra.start_time,
      end_time: item.extra.end_time
    })
  } else if (item.type === 'assess_self') {
    // 待自评绩效
    navigate('/user/assessment/my?tab=1&id=' + item.source_id)
  } else if (item.type === 'assess_check') {
    // 待上级评价
    navigate('/user/assessment/my?tab=1&id=' + item.source_id)
  } else if (item.type === 'assess_appeal') {
    // 待申诉处理
    navigate('/user/assessment/my?tab=1&id=' + item.source_id)
  } else if (item.type === 'customer') {
    // 待跟进客户
    openCustomerPanel(item.source_id)
  } else if (item.type === 'contract') {
    // 待审核合同
    openCheckContractPanel(item.source_id)
  } else if (item.type === 'invoice') {
    // 待审核发票
    openInvoicePanel(item.source_id)
  } else if (item.type === 'task') {
    // 待办任务
    getProgram()
    getProgramSelect(item.extra.program_id)
    getProgramMember(item.extra.program_id)
    getProgramVersion(item.extra.program_id)
    editTask.value?.openBox(item.source_id)
  } else if (item.type === 'notice') {
    // 未读企业动态
    navigate(`/user/notice/index?id=${item.source_id}`)
  } else if (item.type === 'approve_submit') {
    // 提交待审批
    openExaminePanel(item.source_id)
  } else if (item.type === 'approve_pending') {
    // 待我审批
    openExaminePanel(item.source_id)
  }
}

function init() {
  fetchOverview()
  fetchList()
}

onMounted(init)

defineExpose({ totalCount })
</script>

<style lang="scss" scoped>
.my-todo {
  width: 100%;
  height: calc(100vh - 77px);

  overflow-y: auto;
  scrollbar-width: none; /* firefox */
  -ms-overflow-style: none; /* IE 10+ */
  border-radius: 8px;
  display: flex;
}

.todo-side {
  width: 250px;
  display: flex;
  flex-flow: column;
  color: #303133;
  border-right: 1px solid #eeeeee;
  flex-shrink: 0;

  .category-header {
    padding: 20px 20px 12px;
    font-weight: bold;
    font-size: 14px;
  }

  .category-list {
    flex: 1;
    overflow-y: auto;
    padding: 0;
    margin: 0;
    font-size: 13px;
  }

  .category-item {
    list-style: none;
    margin-inline: 10px;
    height: 40px;
    border-radius: 8px;
    padding-inline: 10px;
    display: flex;
    align-items: center;
    cursor: pointer;

    &.active {
      background: #edf6ff;

      .iconfont,
      .category-text {
        color: #1890ff !important;
      }
    }
    &:hover {
      background: #f3f5f9;
    }

    .category-label {
      display: flex;
      align-items: center;
    }

    .iconquanbu {
      color: #606266;
      padding-inline: 1px;
    }

    .iconliebiaoyangshi {
      color: #c0c4cc;
    }

    .category-icon {
      margin-right: 8px;
    }

    .category-count {
      margin-left: auto;
      color: #909399;
    }
  }
}

.todo-main {
  flex: 1;
  display: flex;
  flex-flow: column;

  padding: 20px 30px 20px 16px;

  .todo-header {
    font-weight: bold;
    font-size: 14px;
    color: #303133;
    padding-left: 14px;
  }

  .todo-scroll-area {
    margin-top: 14px;
    flex: 1;
    overflow-y: auto;
  }

  .todo-list {
    margin: 0;
    padding: 0;
  }

  .todo-item {
    list-style: none;
    padding-left: 14px;
    cursor: pointer;

    &:hover {
      background: #f3f5f9;
      border-radius: 4px;
    }

    &:last-child {
      .todo-content {
        border-bottom: none;
      }
    }
  }

  .todo-content {
    padding-top: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid #eee;
  }

  .todo-title {
    font-size: 13px;
    color: #303133;
  }

  .todo-meta {
    margin-top: 6px;
    font-size: 12px;
    color: #909399;
  }

  .todo-empty {
    text-align: center;
    color: #909399;
    font-size: 13px;
    margin-left: -10px;
    margin-top: 250px;
  }
}
.load-more-tip {
  padding: 12px 0 16px;
  text-align: center;
  font-size: 12px;
  color: #909399;
}

::v-deep .el-collapse-item__header {
  border-bottom: none;
}

::v-deep .el-collapse-item__content {
  padding-bottom: 0;
}

.completed-collapse {
  .todo-title {
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;

    color: #909399 !important;
  }
  ::v-deep .el-collapse-item__header {
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: PingFang SC, PingFang SC;
    font-weight: 400;
    font-size: 13px;
    color: #909399;
    .el-collapse-item__arrow {
      margin: 0 !important;
      margin-left: 6px;
      font-size: 12px;
    }
  }
}
</style>
