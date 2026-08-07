<template>
<div class="customer-management">
  <el-card :body-style="{ padding: '20px' }" class="normal-page">
    <!-- 头部区域 -->
    <div class="header-section">
      <div class="header-left">
        <h2 class="page-title">{{ $t("ui.businessFormSettingFormCreateDesignerFcDesignerCustomerManagement") }}</h2>
        <div class="total-count">{{ $t("ui.developModuleFormBoxTotal") }} {{ total }} {{ $t("ui.developModuleFormBoxItems") }}</div>
      </div>
      <div class="header-right">
        <el-input
          v-model="searchKeyword"
          :placeholder="$t('ui.developModuleTreeSearchCustomerName')"
          prefix-icon="el-icon-search"
          style="width: 200px; margin-right: 10px;"
          @keyup.enter.native="handleSearch"
        />
        <el-button type="primary" icon="el-icon-plus" @click="handleAdd">{{ $t("ui.businessHolidayTypeIndexAdd") }}</el-button>
        <el-button
          v-if="selectedRows.length > 0"
          class="ml10"
          @click="handleBatchDelete"
        >
          {{ $t("ui.developModuleTreeBulkDelete") }}{{ selectedRows.length }})
        </el-button>
        <el-button class="ml10">{{ $t("ui.developModuleTreeGroup") }}</el-button>

        <!-- 筛选下拉菜单 -->
        <el-dropdown class="ml10" @command="handleFilterCommand">
          <el-button>
            {{ $t("ui.developModuleTreeFilter") }} <i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="customer_name">{{ $t("ui.developModuleTreeCustomerName") }}</el-dropdown-item>
            <el-dropdown-item command="salesman">{{ $t("ui.developModuleTreeOwner") }}</el-dropdown-item>
            <el-dropdown-item command="customer_category">{{ $t("ui.developModuleTreeCustomerCategory") }}</el-dropdown-item>
            <el-dropdown-item command="customer_status">{{ $t("ui.developModuleTreeCustomerStatus") }}</el-dropdown-item>
            <el-dropdown-item command="last_follow_time">{{ $t("ui.developModuleTreeLastFollowUpTime") }}</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>

    <!-- 筛选条件显示区域 -->
    <div v-if="activeFilter" class="filter-condition mt10">
      <el-tag closable @close="clearFilter">
        {{ getFilterLabel(activeFilter) }}: {{ filterValue }}
      </el-tag>
    </div>

    <!-- 客户数据表格 - 使用Element UI原生表格 -->
    <div class="table-section mt20">
      <el-table
        :data="flattenedTableData"
        style="width: 100%"
        border
        v-loading="loading"
        @selection-change="handleSelectionChange"
        row-key="uniqueId"
        :span-method="objectSpanMethod"
        :row-style="getRowStyle"
        :cell-style="getCellStyle"
        :expand-row-keys="expandedRowKeys"
      >
        <!-- 选择列 -->
        <el-table-column type="selection" width="55" reserve-selection></el-table-column>
            
        <!-- 客户名称列 -->
        <el-table-column prop="customer_name" :label="$t('ui.developModuleTreeCustomerName')" min-width="150">
          <template slot-scope="scope">
            <div class="customer-info" v-if="scope.row.rowType === 'data'">
              <img
                v-if="scope.row.avatar"
                :src="scope.row.avatar"
                class="customer-avatar"
                :alt="$t('ui.developModuleTreeHeadPortrait')"
              />
              <span class="customer-name">{{ scope.row.customer_name }}</span>
            </div>
            <div class="group-header-cell" v-else>
              <div class="group-title-content">
                <i 
                  :class="scope.row.expanded ? 'el-icon-arrow-down' : 'el-icon-arrow-right'"
                  class="expand-icon"
                  @click="toggleGroup(scope.row.groupIndex)"
                ></i>
                <span class="group-name">{{ scope.row.groupName }}</span>
                <el-tag type="info" size="mini">{{ scope.row.totalCount }}{{ $t("ui.developModuleTreeCustomers") }}</el-tag>
              </div>
            </div>
          </template>
        </el-table-column>
            
        <!-- 客户来源列 -->
        <el-table-column prop="source" :label="$t('ui.developModuleTreeCustomerSource')" width="120">
          <template slot-scope="scope">
            <span v-if="scope.row.rowType === 'data'">{{ scope.row.source || '--' }}</span>
          </template>
        </el-table-column>
            
        <!-- 企业电话列 -->
        <el-table-column prop="phone" :label="$t('ui.developModuleTreeBusinessPhone')" width="120">
          <template slot-scope="scope">
            <span v-if="scope.row.rowType === 'data'">{{ scope.row.phone || '--' }}</span>
          </template>
        </el-table-column>
            
        <!-- 客户标签列 -->
        <el-table-column prop="tags" :label="$t('ui.developModuleTreeCustomerLabels')" width="150">
          <template slot-scope="scope">
            <div class="tags-container" v-if="scope.row.rowType === 'data'">
              <el-tag
                v-for="tag in scope.row.tags"
                :key="tag.id"
                size="mini"
                class="tag-item"
              >
                {{ tag.name }}
              </el-tag>
              <el-tag v-if="!scope.row.tags || scope.row.tags.length === 0" size="mini">--</el-tag>
            </div>
          </template>
        </el-table-column>
            
        <!-- 最后跟进时间列 -->
        <el-table-column prop="last_follow_time" :label="$t('ui.developModuleTreeLastFollowUpTime')" width="150">
          <template slot-scope="scope">
            <span v-if="scope.row.rowType === 'data'">{{ scope.row.last_follow_time || '--' }}</span>
          </template>
        </el-table-column>
            
        <!-- 客户状态列 -->
        <el-table-column prop="status" :label="$t('ui.developModuleTreeCustomerStatus')" width="100">
          <template slot-scope="scope">
            <el-tag
              v-if="scope.row.rowType === 'data'"
              :type="getStatusType(scope.row.status)"
              size="mini"
            >
              {{ getStatusText(scope.row.status) }}
            </el-tag>
          </template>
        </el-table-column>
            
        <!-- 业务人员列 -->
        <el-table-column prop="business_staff" :label="$t('ui.developModuleTreeBusinessPersonnel')" width="120">
          <template slot-scope="scope">
            <div class="staff-info" v-if="scope.row.rowType === 'data' && scope.row.business_staff">
              <img
                :src="scope.row.business_staff.avatar"
                class="staff-avatar"
                :alt="$t('ui.developModuleTreeHeadPortrait')"
              />
              <span>{{ scope.row.business_staff.name }}</span>
            </div>
            <span v-else-if="scope.row.rowType === 'data'">--</span>
          </template>
        </el-table-column>
            
        <!-- 创建时间列 -->
        <el-table-column prop="created_at" :label="$t('ui.invoiceInvoiceDetailsCreatedTime')" width="160">
          <template slot-scope="scope">
            <span v-if="scope.row.rowType === 'data'">{{ scope.row.created_at || '--' }}</span>
          </template>
        </el-table-column>
            
        <!-- 操作列 -->
        <el-table-column :label="$t('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" fixed="right" width="220">
          <template slot-scope="scope">
            <div v-if="scope.row.rowType === 'data'" class="action-buttons">
              <el-button type="text" size="small" @click="handleCopy(scope.row)">{{ $t("ui.settingWecomIndexCopy") }}</el-button>
              <el-button type="text" size="small" @click="handleDelete(scope.row)">{{ $t("ui.chatIndexDelete") }}</el-button>
              <el-button type="text" size="small" @click="handleRegenerate(scope.row)">{{ $t("ui.developModuleTreeRegenerate") }}</el-button>
            </div>
            <!-- 加载更多行 -->
            <div v-else-if="scope.row.rowType === 'loadmore'" class="load-more-row">
              <el-button 
                type="primary" 
                plain
                size="small"
                @click.stop="loadMoreGroupData(scope.row.groupIndex)"
                :loading="scope.row.loading"
              >
                <i class="el-icon-arrow-down"></i> {{ $t("ui.developModuleTreeLoadMore") }}
              </el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页区域 -->
      <div class="pagination-section mt20">
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :current-page="pagination.currentPage"
          :page-sizes="[10, 20, 30, 50]"
          :page-size="pagination.pageSize"
          layout="total, sizes, prev, pager, next, jumper"
          :total="total"
        />
      </div>

      <!-- 加载更多按钮 -->
      <div class="load-more-section mt20 text-center" v-if="hasMore">
        <el-button
          type="primary"
          plain
          @click="loadMore"
          :loading="loadingMore"
        >
          <i class="el-icon-arrow-down"></i> {{ $t("ui.developModuleTreeLoadMore") }}
        </el-button>
      </div>
    </div>
  </el-card>

  <!-- 筛选对话框 -->
  <el-dialog
    :title="`筛选 - ${getFilterLabel(activeFilter)}`"
    :visible.sync="filterDialogVisible"
    width="400px"
    @close="handleFilterDialogClose"
  >
    <div class="filter-dialog-content">
      <el-form :model="filterForm" label-width="80px">
        <el-form-item :label="getFilterLabel(activeFilter)">
          <el-input
            v-if="activeFilter !== 'customer_status'"
            v-model="filterForm.value"
            :placeholder="$t('ui.developModuleTreePleaseEnterFilterConditions')"
          />
          <el-select
            v-else
            v-model="filterForm.value"
            :placeholder="$t('ui.developModuleTreePleaseSelectCustomerStatus')"
            style="width: 100%"
          >
            <el-option :label="$t('ui.layoutNoticeNoticeListAll')" value=""></el-option>
            <el-option :label="$t('ui.customerListIndexFollowingUp')" value="following"></el-option>
            <el-option :label="$t('ui.customerSetupRuleSettingsFollowRulesClosed')" value="deal"></el-option>
            <el-option :label="$t('ui.customerContractContractRemindAbandoned')" value="abandoned"></el-option>
          </el-select>
        </el-form-item>
      </el-form>
    </div>
    <span slot="footer" class="dialog-footer">
      <el-button @click="filterDialogVisible = false">{{ $t("ui.formCommonSelectLabelCancel") }}</el-button>
      <el-button type="primary" @click="applyFilter">{{ $t("ui.formCommonDialogFormOk") }}</el-button>
    </span>
  </el-dialog>
</div>
</template>
<script>
import i18n from '@/lang'
export default {
  name: 'CustomerManagement',
  data() {
    return {
      // 分组数据
      groupedData: [],
      // 扁平化的表格数据（用于Element UI表格）
      flattenedTableData: [],
      // 展开的行keys
      expandedRowKeys: [],
      loading: false,
      total: 0,

      // 分页信息
      pagination: {
        currentPage: 1,
        pageSize: 10
      },

      // 筛选相关
      activeFilter: '',
      filterValue: '',
      filterDialogVisible: false,
      filterForm: {
        value: ''
      },

      // 加载更多
      loadingMore: false,
      hasMore: true,

      // 选中的行
      selectedRows: [],

      // 搜索关键词
      searchKeyword: ''
    }
  },

  computed: {
    tableHeight() {
      return 'calc(100vh - 300px)'
    }
  },

  created() {
    this.loadCustomerData()
  },

  methods: {
    // 加载分组客户数据
    async loadCustomerData() {
      this.loading = true
      try {
        // 模拟API调用
        await new Promise(resolve => setTimeout(resolve, 1000))

        // 生成分组数据
        this.groupedData = this.generateGroupedData()
        this.total = 50
                
        // 生成扁平化表格数据
        this.generateFlattenedData()
        
        // 检查是否还有更多数据
        this.hasMore = this.pagination.currentPage * this.pagination.pageSize < this.total
      } catch (error) {
        this.$message.error(i18n.t('legacyScript.failedToLoadData'))
      } finally {
        this.loading = false
      }
    },

    // 生成分组数据
    generateGroupedData() {
      // 模拟不同来源的客户数据
      const mockGroups = [
        {
          groupName: '星耀裂变',
          totalCount: 15,
          items: [
            {
              id: 1,
              customer_name: '顾文轩',
              avatar: 'https://avatars.githubusercontent.com/u/1?v=4',
              source: '星耀裂变',
              phone: '15199009988',
              tags: [
                { id: 1, name: '重要客户' },
                { id: 2, name: '标签' }
              ],
              last_follow_time: '2022-08-18 17:46',
              status: 'deal',
              business_staff: {
                name: '张三',
                avatar: 'https://avatars.githubusercontent.com/u/2?v=4'
              },
              created_at: '2023-07-18 14:15:25'
            },
            {
              id: 2,
              customer_name: '李明',
              avatar: 'https://avatars.githubusercontent.com/u/3?v=4',
              source: '星耀裂变',
              phone: '13800138001',
              tags: [
                { id: 3, name: '潜在客户' }
              ],
              last_follow_time: '2023-01-15 10:30',
              status: 'following',
              business_staff: {
                name: '李四',
                avatar: 'https://avatars.githubusercontent.com/u/4?v=4'
              },
              created_at: '2023-01-10 09:20:15'
            }
          ],
          expanded: true,
          loading: false,
          hasMore: true,
          showLoadMoreTip: false,
          displayCount: 4  // 默认显示4条
        },
        {
          groupName: '官网注册',
          totalCount: 12,
          items: [
            {
              id: 3,
              customer_name: '王小明',
              avatar: 'https://avatars.githubusercontent.com/u/5?v=4',
              source: '官网注册',
              phone: '13900139000',
              tags: [
                { id: 4, name: 'VIP客户' },
                { id: 5, name: '长期合作' }
              ],
              last_follow_time: '2023-03-10 16:20',
              status: 'deal',
              business_staff: {
                name: '王五',
                avatar: 'https://avatars.githubusercontent.com/u/6?v=4'
              },
              created_at: '2023-02-28 11:30:45'
            },
            {
              id: 4,
              customer_name: '李华',
              avatar: 'https://avatars.githubusercontent.com/u/7?v=4',
              source: '官网注册',
              phone: '13700137000',
              tags: [
                { id: 6, name: '新客户' }
              ],
              last_follow_time: '2023-04-05 09:15',
              status: 'following',
              business_staff: {
                name: '赵六',
                avatar: 'https://avatars.githubusercontent.com/u/8?v=4'
              },
              created_at: '2023-03-20 14:45:12'
            }
          ],
          expanded: false,
          loading: false,
          hasMore: true,
          showLoadMoreTip: false,
          displayCount: 4
        },
        {
          groupName: '朋友推荐',
          totalCount: 8,
          items: [
            {
              id: 5,
              customer_name: '陈志强',
              avatar: 'https://avatars.githubusercontent.com/u/9?v=4',
              source: '朋友推荐',
              phone: '13600136000',
              tags: [
                { id: 7, name: '大客户' },
                { id: 8, name: '重点关注' }
              ],
              last_follow_time: '2023-02-28 13:40',
              status: 'abandoned',
              business_staff: {
                name: '孙七',
                avatar: 'https://avatars.githubusercontent.com/u/10?v=4'
              },
              created_at: '2023-01-25 10:15:33'
            },
            {
              id: 6,
              customer_name: '(空)',
              avatar: '',
              source: '朋友推荐',
              phone: '13500135000',
              tags: [],
              last_follow_time: '2023-02-20 14:15',
              status: 'abandoned',
              business_staff: null,
              created_at: '2023-02-15 16:45:30'
            }
          ],
          expanded: false,
          loading: false,
          hasMore: false,
          showLoadMoreTip: false,
          displayCount: 4
        }
      ]

      // 为每个分组计算显示的项目
      return mockGroups.map(group => ({
        ...group,
        displayItems: group.items.slice(0, group.displayCount)
      }))
    },

    // 获取行样式
    getRowStyle({ row, rowIndex }) {
      if (row.rowType === 'group') {
        return { 
          height: '54px',
          'line-height': '54px'
        }
      }
      return { 
        height: '54px',
        'line-height': '54px'
      }
    },
    
    // 获取单元格样式
    getCellStyle({ row, column, rowIndex, columnIndex }) {
      if (row.rowType === 'group') {
        return { 
          height: '54px',
          padding: '0'
        }
      }
      return { 
        height: '54px',
        padding: '8px 0'
      }
    },

    // 切换分组展开/收起
    toggleGroup(groupIndex) {
      const group = this.groupedData[groupIndex]
      group.expanded = !group.expanded
      
      // 同步更新expandedRowKeys
      const groupUniqueId = `group_${groupIndex}`
      if (group.expanded) {
        this.expandedRowKeys.push(groupUniqueId)
      } else {
        const index = this.expandedRowKeys.indexOf(groupUniqueId)
        if (index > -1) {
          this.expandedRowKeys.splice(index, 1)
        }
      }
      
      // 如果是第一次展开且有更多数据，显示加载更多提示
      if (group.expanded && group.hasMore && group.displayItems.length < group.items.length) {
        this.$set(group, 'showLoadMoreTip', true)
      }
      
      // 重新生成扁平化数据
      this.generateFlattenedData()
    },

    // 加载更多分组数据
    async loadMoreGroupData(groupIndex) {
      const group = this.groupedData[groupIndex]
      if (!group.hasMore || group.loading) return

      group.loading = true
      try {
        // 模拟API调用延迟
        await new Promise(resolve => setTimeout(resolve, 800))
        
        // 增加显示数量
        group.displayCount += 4
        group.displayItems = group.items.slice(0, group.displayCount)
        
        // 检查是否还有更多数据
        group.hasMore = group.displayCount < group.items.length
        group.showLoadMoreTip = false
        
        // 重新生成扁平化数据
        this.generateFlattenedData()
        
        this.$message.success(`已加载更多${group.groupName}的客户数据`)
      } catch (error) {
        this.$message.error(i18n.t('legacyScript.failedToLoadMoreData'))
      } finally {
        group.loading = false
      }
    },

    // 表格合并方法
    objectSpanMethod({ row, column, rowIndex, columnIndex }) {
      // 只对客户名称列进行合并
      if (columnIndex === 1) {
        if (row.rowType === 'group') {
          // 分组行合并所有列（除了选择列）
          return {
            rowspan: 1,
            colspan: 9
          }
        } else if (row.rowType === 'loadmore') {
          // 加载更多行合并所有列
          return {
            rowspan: 1,
            colspan: 9
          }
        } else {
          // 数据行正常显示
          return {
            rowspan: 1,
            colspan: 1
          }
        }
      }
      // 其他列在分组行和加载更多行不显示
      else if (row.rowType === 'group' || row.rowType === 'loadmore') {
        return {
          rowspan: 0,
          colspan: 0
        }
      }
      // 数据行正常显示
      else {
        return {
          rowspan: 1,
          colspan: 1
        }
      }
    },

    // 生成扁平化的表格数据
    generateFlattenedData() {
      const result = []
      let uniqueId = 1
      
      this.groupedData.forEach((group, groupIndex) => {
        // 添加分组行
        result.push({
          uniqueId: `group_${groupIndex}`,
          rowType: 'group',
          groupName: group.groupName,
          totalCount: group.totalCount,
          expanded: group.expanded,
          hasMore: group.hasMore,
          loading: group.loading,
          groupIndex: groupIndex,
          // 以下字段为空，避免表格渲染错误
          customer_name: '',
          source: '',
          phone: '',
          tags: [],
          last_follow_time: '',
          status: '',
          business_staff: null,
          created_at: ''
        })
        
        // 如果分组展开，添加数据行
        if (group.expanded) {
          const itemsToShow = group.displayItems
          itemsToShow.forEach((item, itemIndex) => {
            result.push({
              ...item,
              uniqueId: `data_${uniqueId++}`,
              rowType: 'data',
              groupIndex: groupIndex,
              // 标识是否为分组中的最后一条数据
              isLastInGroup: itemIndex === itemsToShow.length - 1
            })
          })
          
          // 如果还有更多数据且当前显示的数据少于总数，添加加载更多行
          if (group.hasMore && itemsToShow.length < group.items.length) {
            result.push({
              uniqueId: `loadmore_${groupIndex}`,
              rowType: 'loadmore',
              groupIndex: groupIndex,
              hasMore: group.hasMore,
              loading: group.loading,
              groupName: group.groupName
            })
          }
        }
      })
      
      this.flattenedTableData = result
    },

    // 处理表格选择变化
    handleSelectionChange(selection) {
      this.selectedRows = selection.filter(row => row.rowType === 'data')
    },

    // 处理筛选命令
    handleFilterCommand(command) {
      this.activeFilter = command
      this.filterDialogVisible = true
    },

    // 应用筛选
    applyFilter() {
      if (this.filterForm.value) {
        this.filterValue = this.filterForm.value
        this.pagination.currentPage = 1
        this.loadCustomerData()
      }
      this.filterDialogVisible = false
    },

    // 清除筛选
    clearFilter() {
      this.activeFilter = ''
      this.filterValue = ''
      this.filterForm.value = ''
      this.pagination.currentPage = 1
      this.loadCustomerData()
    },

    // 获取筛选标签文本
    getFilterLabel(filterKey) {
      const filterMap = {
        customer_name: '客户名称',
        salesman: '负责人',
        customer_category: '客户分类',
        customer_status: '客户状态',
        last_follow_time: '最后跟进时间'
      }
      return filterMap[filterKey] || ''
    },

    // 获取跟进记录
    getFollowUpRecords(row) {
      // 模拟跟进记录数据
      return [
        {
          time: '2023-06-15 14:30',
          title: i18n.t('legacyScript.phoneFollowUp'),
          content: i18n.t('legacyScript.communicatedWithTheClientRegardingProductUsageTheClientExpressed'),
          staff: row.business_staff ? row.business_staff.name : '系统'
        },
        {
          time: '2023-06-10 10:15',
          title: i18n.t('legacyScript.onSiteVisit'),
          content: i18n.t('legacyScript.understandCustomerNeedsOnSiteAndDiscussFollowUpCooperation'),
          staff: row.business_staff ? row.business_staff.name : '系统'
        },
        {
          time: '2023-06-05 16:45',
          title: i18n.t('legacyScript.emailCommunication'),
          content: i18n.t('legacyScript.sendProductMaterialsAndTechnicalDocuments'),
          staff: row.business_staff ? row.business_staff.name : '系统'
        }
      ]
    },
    
    // 获取交易记录
    getTransactionRecords(row) {
      // 模拟交易记录数据
      return [
        {
          order_no: 'ORD20230601001',
          amount: '¥15,000.00',
          status: 'completed',
          date: '2023-06-01'
        },
        {
          order_no: 'ORD20230515002',
          amount: '¥8,500.00',
          status: 'completed',
          date: '2023-05-15'
        },
        {
          order_no: 'ORD20230420003',
          amount: '¥12,000.00',
          status: 'processing',
          date: '2023-04-20'
        }
      ]
    },
    
    // 获取状态文本
    getStatusText(status) {
      const statusMap = {
        following: '跟进中',
        deal: '已成交',
        abandoned: '已放弃'
      }
      return statusMap[status] || '--'
    },

    // 获取状态类型
    getStatusType(status) {
      const typeMap = {
        following: 'warning',
        deal: 'success',
        abandoned: 'danger'
      }
      return typeMap[status] || 'info'
    },

    



    // 处理选择变化
    handleSelectionChange(selection) {
      this.selectedRows = selection
    },

    // 搜索处理
    handleSearch() {
      if (this.searchKeyword.trim()) {
        this.$message.info(`搜索关键词: ${this.searchKeyword}`)
        // 这里可以调用API进行搜索
        this.pagination.currentPage = 1
        this.loadCustomerData()
      }
    },

    // 新增客户
    handleAdd() {
      this.$message.info(i18n.t('legacyScript.newFeaturesToBeImplemented'))
    },

    // 批量删除
    async handleBatchDelete() {
      try {
        await this.$confirm(`确定要删除选中的 ${this.selectedRows.length} 个客户吗?`, i18n.t('legacyScript.confirmBatchDeletion'), {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        })

        // 执行批量删除逻辑
        this.$message.success(`成功删除 ${this.selectedRows.length} 个客户`)
        this.selectedRows = []
        this.$refs.table.clearSelection()
        this.loadCustomerData()
      } catch (error) {
        // 用户取消删除
      }
    },

    // 复制客户
    handleCopy(row) {
      this.$message.success(`已复制客户: ${row.customer_name}`)
    },

    // 删除客户
    async handleDelete(row) {
      try {
        await this.$confirm(i18n.t('legacyScript.areYouSureYouWantToDeleteThisCustomer'), i18n.t('public.tips'), {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        })
        this.$message.success(i18n.t('hr.deleteok'))
        // 重新加载数据
        this.loadCustomerData()
      } catch (error) {
        // 用户取消删除
      }
    },

    // 再次生成
    handleRegenerate(row) {
      this.$message.info(`正在重新生成客户: ${row.customer_name}`)
    },

    // 分页大小改变
    handleSizeChange(size) {
      this.pagination.pageSize = size
      this.pagination.currentPage = 1
      this.loadCustomerData()
    },

    // 当前页改变
    handleCurrentChange(page) {
      this.pagination.currentPage = page
      this.loadCustomerData()
    },

    // 加载更多
    async loadMore() {
      if (!this.hasMore) return

      this.loadingMore = true
      try {
        // 模拟加载更多数据
        await new Promise(resolve => setTimeout(resolve, 800))
        this.pagination.currentPage++
        const newData = this.generateMockData()
        this.tableData = [...this.tableData, ...newData]
        this.hasMore = this.pagination.currentPage * this.pagination.pageSize < this.total
      } catch (error) {
        this.$message.error(i18n.t('legacyScript.failedToLoadMoreData'))
      } finally {
        this.loadingMore = false
      }
    },

    // 关闭筛选对话框
    handleFilterDialogClose() {
      this.filterForm.value = ''
    }
  }
}
</script>

<style scoped lang="scss">
.customer-management {
  .header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;

    .header-left {
      display: flex;
      align-items: center;
      gap: 20px;

      .page-title {
        margin: 0;
        font-size: 20px;
        font-weight: 500;
        color: #303133;
      }

      .total-count {
        font-size: 14px;
        color: #909399;
      }
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }
  }

  .filter-condition {
    .el-tag {
      font-size: 13px;
    }
  }

  .table-section {
    ::v-deep .el-table {
      th {
        background-color: #f5f7fa;
        font-weight: 500;
      }
      
      .group-header-cell {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        
        .group-title-content {
          display: flex;
          align-items: center;
          gap: 10px;
          
          .expand-icon {
            font-size: 16px;
            color: #409eff;
            cursor: pointer;
            transition: transform 0.3s;
            
            &:hover {
              color: #66b1ff;
            }
          }
          
          .group-name {
            font-size: 14px;
            font-weight: 500;
            color: #303133;
          }
          
          .el-tag {
            margin-left: 10px;
          }
        }
        
        .group-actions {
          .el-button {
            color: #409eff;
          }
        }
      }
      
      .customer-info {
        display: flex;
        align-items: center;
        gap: 8px;
        
        .customer-avatar {
          width: 24px;
          height: 24px;
          border-radius: 50%;
          object-fit: cover;
        }
        
        .customer-name {
          font-size: 13px;
          color: #303133;
        }
      }
      
      .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        
        .tag-item {
          margin: 0;
        }
      }
      
      .staff-info {
        display: flex;
        align-items: center;
        gap: 6px;
        
        .staff-avatar {
          width: 20px;
          height: 20px;
          border-radius: 50%;
          object-fit: cover;
        }
        
        span {
          font-size: 12px;
          color: #606266;
        }
      }
      
      .action-buttons {
        display: flex;
        gap: 8px;
          
        .el-button {
          padding: 4px 8px;
        }
      }
        
      .load-more-row {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 60px;
        background-color: #f5f7fa;
        border-top: 1px solid #ebeef5;
          
        .el-button {
          width: 120px;
        }
      }
    }
  }

  .pagination-section {
    display: flex;
    justify-content: center;
  }

  .load-more-section {
    .el-button {
      width: 120px;
    }
  }

  .filter-dialog-content {
    .el-form-item {
      margin-bottom: 0;
    }
  }
  
  /* 展开行动画优化 */
  .expand-content {
    padding: 20px;
    background-color: #fafafa;
    border-top: 1px solid #ebeef5;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 300px; /* 固定最小高度避免跳动 */
  }
  
  .expand-content.collapsing {
    opacity: 0;
    transform: translateY(-10px);
  }
  
  /* 固定行高减少跳动 */
  .el-table__body tr.el-table__row {
    transition: height 0.2s ease;
  }
  
  /* 分组行特殊样式 */
  .group-header-cell {
    height: 54px;
    line-height: 54px;
  }
  
  /* 展开行容器固定高度 */
  .el-table__expanded-cell {
    padding: 0 !important;
  }
  
  .el-table__expanded-cell > div {
    min-height: 300px;
  }
}

// 全局样式覆盖
::v-deep .el-table th {
  background-color: #f5f7fa;
  font-weight: 500;
}

::v-deep .el-table td {
  padding: 8px 0;
}

.ml10 {
  margin-left: 10px;
}

.mt10 {
  margin-top: 10px;
}

.mt20 {
  margin-top: 20px;
}

.text-center {
  text-align: center;
}
</style>
