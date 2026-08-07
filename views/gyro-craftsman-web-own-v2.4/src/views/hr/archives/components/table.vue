<template>
<div>
  <el-table
    ref="multipleTable"
    :data="tableData"
    :height="tableHeight"
    class="mt10"
    style="width: 100%"
    v-bind="$attrs"
    @selection-change="handleSelectionChange"
  >
    <el-table-column :label="$t('ui.businessHolidayQueryIndexName')" min-width="100" prop="name"> </el-table-column>
    <el-table-column :label="$t('ui.hrArchivesTableGender')" min-width="90" prop="sex">
      <template slot-scope="scope">
        <span>{{ getSex(scope.row.sex) }}</span>
      </template>
    </el-table-column>
    <el-table-column :label="$t('ui.hrArchivesTableEducation')" prop="education">
      <template slot-scope="scope">
        <span>{{ getEducation(scope.row.education) }}</span>
      </template>
    </el-table-column>
    <el-table-column :label="notEmployees == '未入职' ? $t('ui.hrArchivesTableInterviewPosition') : $t('ui.hrAssessConfigAssessBoxPosition')" min-width="140">
      <template slot-scope="scope">
        <span v-if="notEmployees == '未入职'">{{ scope.row.interview_position || '--' }}</span>
        <span v-else>{{ scope.row.job ? scope.row.job.name : '--' }}</span>
      </template>
    </el-table-column>
    <el-table-column v-if="notEmployees !== '未入职'" :label="$t('ui.businessHolidayQueryIndexDepartment')" min-width="120" prop="frame.name">
      <template slot-scope="scope">
        <div v-for="(item, index) in scope.row.frames" :key="index">
          <span class="icon-h">
            {{ item.name }}
            <span v-show="item.is_mastart === 1 && scope.row.frames.length > 1" :title="$t('ui.formCommonSelectDepartmentPrimaryDepartment')">{{ $t("ui.formCommonSelectDepartmentMain") }}</span>
          </span>
        </div>
      </template>
    </el-table-column>
    <el-table-column :label="$t('ui.hrArchivesTableMobileNumber')" min-width="110" prop="phone" show-overflow-tooltip />
    <el-table-column :label="$t('ui.developViewManagementType')" min-width="80" prop="is_part" show-overflow-tooltip>
      <template slot-scope="scope">
        <span>{{ getIsPart(scope.row.is_part) }}</span>
      </template>
    </el-table-column>
    <el-table-column :label="$t('ui.hrArchivesTableEmployeeStatus')" prop="name" width="100">
      <template slot-scope="scope">
        <!-- 提取当前行的类型信息 -->
        <template v-if="scope.row.type !== undefined">
          <span :class="['table-btn', typeMap[scope.row.type] ? typeMap[scope.row.type].color : 'gray']">
            {{ typeMap[scope.row.type] ? typeMap[scope.row.type].text : $t('ui.hrArchivesTableNotOnboarded') }}
          </span>
        </template>
      </template>
    </el-table-column>
    <el-table-column :label="addTime" :prop="propTime" min-width="110px" show-overflow-tooltip>
      <template slot-scope="scope">
        <span>{{ getTime(scope.row) || '--' }}</span>
      </template>
    </el-table-column>
    <el-table-column v-if="notEmployees !== '未入职'" :label="$t('ui.hrArchivesTableAccountStatus')" min-width="110" prop="name">
      <template slot-scope="scope">
        <div v-if="getStatusInfo(scope.row)" :class="['table-txt', getStatusInfo(scope.row).class]">
          <i :class="getStatusInfo(scope.row).iconClass" />
          {{ getStatusInfo(scope.row).text }}
        </div>
      </template>
    </el-table-column>
    <el-table-column v-if="notEmployees == '未入职'" fixed="right" :label="$t('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" min-width="180" prop="address">
      <template slot-scope="scope">
        <slot :data="scope.row" name="options"></slot>
      </template>
    </el-table-column>
    <el-table-column v-if="notEmployees !== '未入职'" fixed="right" :label="$t('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" min-width="180" prop="address">
      <template slot-scope="scope">
        <slot :data="scope.row" name="options"></slot>
      </template>
    </el-table-column>
  </el-table>
  <div class="page-fixed">
    <el-pagination
      :current-page="where.page"
      :page-size="where.limit"
      :page-sizes="[15, 20, 30]"
      :total="total"
      layout="total,sizes, prev, pager, next, jumper"
      @size-change="handleSizeChange"
      @current-change="pageChange"
    />
  </div>
</div>
</template>
<script>
import i18n from '@/lang'
export default {
  name: 'Table',
  props: {
    selectAll: {
      type: Boolean,
      default: true
    },

    tableData: {
      type: Array,
      default: {}
    },
    total: {
      type: Number,
      default: 0
    },
    notEmployees: {
      type: String,
      default: ''
    },
    showText: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      where: {
        page: 1,
        limit: 15
      },
      typeMap: {
        1: { text: this.$t('toptable.formal'), color: 'blue' },
        2: { text: this.$t('toptable.ontrial'), color: 'yellow' },
        3: { text: this.$t('toptable.internship'), color: 'green' },
        4: { text: this.$t('toptable.dimission'), color: 'yellow' },
        0: { text: i18n.t('ui.hrArchivesTableNotOnboarded'), color: 'gray' }
      },

      multipleSelectList: [],
      tabTypes: localStorage.getItem('tabTypes')
    }
  },

  computed: {
    addTime() {
      if (this.notEmployees == '未入职') {
        return '面试时间'
      } else if (this.notEmployees == '离职') {
        return '离职时间'
      } else {
        return '入职时间'
      }
    },
    propTime() {
      if (this.notEmployees == '未入职') {
        return 'interview_date'
      } else if (this.notEmployees == '离职') {
        return 'quit_time'
      } else {
        return 'work_time'
      }
    }
  },
  methods: {
    getTime(row) {
      if (this.notEmployees == '未入职') {
        return row.interview_date
      } else if (this.notEmployees == '离职') {
        return row.quit_time
      } else {
        return row.work_time
      }
    },
    getSex(val) {
      let sexData = {
        0: '未知',
        1: '男',
        2: '女',
        3: '其他'
      }
      return sexData[val]
    },
    getEducation(val) {
      let educationData = {
        2: '高中及以下',
        4: '专科',
        5: '本科',
        6: '研究生'
      }
      return educationData[val] || '--'
    },
    getIsPart(val) {
      let partData = {
        0: '全职',
        1: '兼职',
        2: '实习',
        3: '劳务派遣',
        4: '退休返聘',
        5: '劳务外包',
        6: '其他'
      }
      return partData[val]
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.$emit('limitFn', val)
    },
    pageChange(page) {
      this.where.page = page
      this.$emit('pageFn', page)
    },
    // 勾选表格
    handleSelectionChange(val) {
      this.multipleSelectList = val
      this.$emit('multipleSelection', val)
    },
    getStatusInfo(row) {
      const statusMap = {
        emptyUid: { text: i18n.t('legacyScript.inactive'), iconClass: 'bg-danger', class: 'table-txt' },
        status0: { text: i18n.t('legacyScript.inactive'), iconClass: 'bg-danger', class: 'table-txt' },
        status2: { text: i18n.t('hr.blockup'), iconClass: 'bg-danger', class: 'table-txt' },
        status1: { text: this.$t('setting.info.normal'), iconClass: 'bg-default', class: 'table-txt' }
      }

      if (row.uid === '') {
        return statusMap.emptyUid
      } else if (row.status === 0) {
        return statusMap.status0
      } else if (row.status === 2) {
        return statusMap.status2
      } else if (row.status === 1) {
        return statusMap.status1
      }
      return null
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep .default-expand-all {
  color: #909399;
  background-color: pink;
}
.iconzhuyaobumen {
  color: #ff9900;
}
::v-deep .el-table th {
  background-color: #f7fbff;
}
.bg-default {
  background-color: green !important;
}
// .divBox {
//   height: 200px;
// }
.icon-h {
  position: relative;
  & > span {
    color: #1890ff;
  }
}
.icon {
  position: absolute;
  top: 0;
  right: -15px;
  display: inline-block;
  width: 15px;
  height: 15px;
  font-size: 9px;
  font-weight: 500;
  text-align: center;
  line-height: 15px;
  color: #fff;
  border-radius: 50%;
  background-color: #ff9900;
}

.table-txt {
  display: flex;
  align-items: center;
  justify-content: left;
  position: relative;
  font-size: 13px;

  i {
    width: 4px;
    height: 4px;
    margin-right: 5px;
    border-radius: 50%;
  }
}
.add-height {
  height: 100%;
  overflow-y: auto;
}
.table-btn {
  cursor: text;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 22px;
  border-radius: 3px;
  font-size: 13px;
  border: 1px solid transparent;

  &.blue {
    background: rgba(24, 144, 255, 0.05);
    border: 1px solid #1890ff;
    color: #1890ff;
  }

  &.yellow {
    background: rgba(255, 153, 0, 0.05);
    border: 1px solid #ff9900;
    color: #ff9900;
  }

  &.green {
    background: rgba(0, 192, 80, 0.05);
    border: 1px solid #00c050;
    color: #00c050;
  }

  &.gray {
    background: rgba(153, 153, 153, 0.05);
    border: 1px solid #999999;
    color: #999999;
  }
}
</style>
