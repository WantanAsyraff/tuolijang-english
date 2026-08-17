<!-- 项目-我的项目-项目列表页面 -->
<template>
  <div class="divBox bill-type">
    <el-card class="normal-page">
      <oaFromBox
        :isViewSearch="false"
        :search="search"
        :title="$route.meta.title"
        :total="total"
        :treeData="treeData"
      :treeDefault="treeDefault"
      :btnText="$('ui.programProgramListIndexCreateProject')"
        @addDataFn="addProgram"
        @confirmData="confirmData"
        @treeChange="treeChange"
      ></oaFromBox>

      <div class="mt10">
        <oa-table
          :height="tableHeight"
          :loading="loading"
          :tableData="tableData"
          :tableOptions="tableOptions"
          :total="total"
          @handleSizeChange="handleSizeChange"
          @handleCurrentChange="pageChange"
        >
          <template #name="{ row }">
            <div class="flex">
              <i class="iconfont iconxiangmuguanli"></i>
              <div v-if="row.name.length < 18" class="point line1" @click="goTask(row)">
                {{ row.name || '- -' }}
              </div>
              <el-popover v-else placement="top" trigger="hover" width="250">
                <div>{{ row.name || '- -' }}</div>
                <div slot="reference" class="line1 point" style="max-width: 200px" @click="goTask(row)">
                  {{ row.name || '- -' }}
                </div>
              </el-popover>
            </div>
          </template>

          <template #status="{ row }">
            <el-tag v-if="row.status == 1" effect="plain" type="warning">{{ $("ui.programProgramTaskAddTaskPaused") }}</el-tag>
            <el-tag v-else-if="row.status == 2" effect="plain" type="info">{{ $("ui.programProgramTaskAddTaskClosed") }}</el-tag>
            <el-tag v-else-if="row.end_date && nowTime() > row.end_date" effect="plain" type="danger">{{ $("ui.programProgramListIndexDelayed") }}</el-tag>
            <el-tag v-else-if="row.start_date && nowTime() < row.start_date" effect="plain" type="success"
              >{{ $("ui.programProgramListIndexNotStarted") }}</el-tag
            >
            <el-tag v-else effect="plain">{{ $("ui.programProgramListIndexInProgress") }}</el-tag>
          </template>

          <template #admins="{ row }">
            <img :src="row.admins[0].avatar" alt="" class="img" />
            <span>{{ row.admins[0].name }}</span>
          </template>

          <template #progress="{ row }">
            <!-- :text-inside="true"
              :stroke-width="16" -->
            <el-progress
              :percentage="Math.round((row.task_statistics.incomplete / row.task_statistics.total) * 100) || 0"
            ></el-progress>
          </template>
        </oa-table>
      </div>
    </el-card>

    <!-- 新建项目 -->
    <el-drawer
      :before-close="handleClose"
      :visible.sync="taskDrawer"
      :wrapper-closable="false"
      size="1120px"
      :title="$('ui.programProgramListIndexCreateProject')"
    >
      <add-program
        v-if="taskDrawer"
        ref="addProgram"
        :customer="customerList"
        :type="`edit`"
        @getTableData="getTableData"
        @handleClose="handleClose"
      />
    </el-drawer>
  </div>
</template>
<script>
import { $ } from '@/lang'
import { getProgramListApi, deleteProgramApi } from '@/api/program'
import { customerSelectApi } from '@/api/enterprise'
import { roterPre } from '@/settings'

export default {
  name: 'programList',
  components: {
    oaFromBox: () => import('@/components/common/oaFromBox'),
    oaTable: () => import('@/components/form-common/oa-table'),
    addProgram: () => import('./components/addProgram'),
    taskDrawer: () => import('../programTask/index'),
    dynamicsDrawer: () => import('./dynamics')
  },
  data() {
    return {
      loading: false,
      tableData: [],
      tableFrom: {
        page: 1,
        limit: 15,
        types: 0,
        status: '',
        admins: [],
        scope_frame: 'all',
        scope_normal: 0,
        eid: [],
        cid: []
      },
      total: 0,
      customerList: [],
      taskDrawer: false,
      taskTitle: '',
      programId: 0,
      treeData: [
        {
          options: [
            {
              value: 0,
              label: $('legacyScript.allProjects')
            },
            {
              value: 1,
              label: $('legacyScript.ownedByMe')
            },
            {
              value: 2,
              label: $('legacyScript.myParticipations')
            },
            {
              value: 3,
              label: $('legacyScript.createdByMe')
            }
          ]
        }
      ],
      search: [
        {
          field_name: '项目名称',
          field_name_en: 'name',
          form_value: 'input'
        },
        {
          field_name: '状态',
          field_name_en: 'status',
          form_value: 'select',
          multiple: true,
          props: {
            collapseTags: true
          },
          data_dict: [
            {
              value: 5,
              name: '已延期'
            },
            {
              value: 4,
              name: '进行中'
            },
            {
              value: 3,
              name: '待开始'
            },
            {
              value: 1,
              name: '已暂停'
            },
            {
              value: 2,
              name: '已关闭'
            }
          ]
        },
        {
          field_name: '关联客户',
          field_name_en: 'eid',
          form_value: 'input'
        },
        {
          field_name: '关联订单',
          field_name_en: 'cid',
          form_value: 'input'
        },
        {
          form_value: 'manage'
        }
      ],
      tableOptions: [
        {
          label: $('legacyScript.projectNumber'),
          prop: 'ident',
          width: '120px'
        },
        {
          label: $('legacyScript.projectName'),
          type: 'slot',
          name: 'name'
          // width: '450px'
        },
        {
          label: $('hr.state'),
          type: 'slot',
          name: 'status'
          // width: '120px'
        },
        {
          label: $('ui.developModuleTreeOwner'),
          type: 'slot',
          name: 'admins'
          // width: '150px'
        },
        {
          label: $('ui.programProgramTaskTableDataPlannedStart'),
          prop: 'start_date'
          // width: '140px'
        },
        {
          label: $('ui.programProgramTaskTableDataPlannedEnd'),
          prop: 'end_date'
          // width: '140px'
        },
        {
          label: $('legacyScript.projectProgress'),
          type: 'slot',
          name: 'progress'
        },
        {
          label: $('legacyScript.uncompletedTotalTasks'),
          // width: '120px',
          render: (row) => {
            return (
              <span>
                {row.task_statistics.incomplete}/{row.task_statistics.total}
              </span>
            )
          }
        }
      ],
      treeDefault: 0
    }
  },
  created() {
    this.getCustomer()
  },
  mounted() {
    this.getTableData()
  },
  methods: {
    handleClose() {
      this.taskDrawer = false
    },
    nowTime() {
      const now = new Date()
      const year = now.getFullYear()
      const month = (now.getMonth() + 1).toString().padStart(2, '0') // 月份从0开始计数，所以加1，然后用padStart保证两位数
      const day = now.getDate().toString().padStart(2, '0') // 日期使用padStart保证两位数
      let nowData = `${year}-${month}-${day}`
      return nowData
    },

    // 获取表格数据
    async getTableData(tableFrom, type) {
      this.loading = true
      let data = tableFrom ? tableFrom : this.tableFrom
      const res = await getProgramListApi(data)
      this.tableData = res.data.list
      this.total = res.data.count
      this.loading = false
      if (type === 1) {
        this.programId = res.data.list[0].id
        this.taskTitle = res.data.list[0].name
        this.taskDrawer = false
      }
    },
    addProgram() {
      this.taskDrawer = true
    },
    addTask() {
      this.$refs.taskDrawerRef.addProgram()
    },
    batchOperation() {
      this.$refs.taskDrawerRef.batchOperation()
    },
    // 获取客户数据
    async getCustomer() {
      const result = await customerSelectApi()
      this.customerList = result.data
    },
    pageChange(page) {
      this.tableFrom.page = page
      this.getTableData()
    },
    handleSizeChange(val) {
      this.tableFrom.limit = val
      this.getTableData()
    },
    // 编辑项目
    handleEdit(row, type) {
      this.$refs.addProgram.openBox(row.id, type)
    },
    // 项目动态
    goDynamics(row) {
      this.programId = row.id
      this.$refs.dynamicsDrawer.drawer = true
      // this.$router.push(`${roterPre}/program/programList/dynamics?id=${row.id}`)
    },
    // 项目任务
    goTask(row) {
      this.$router.push(`${roterPre}/program/programList/taskDetails?id=${row.id}`)
    },
    // 删除项目
    handleDelete(row) {
      this.$modalSure('删除项目，同时会删除项目中的工作项！你确定要删除该项目吗').then(() => {
        deleteProgramApi(row.id).then((res) => {
          this.getTableData()
          this.$refs.addProgram.handleClose()
        })
      })
    },
    async setPayStatus(row, status) {
      const data = {
        id: row.id,
        name: row.name,
        status
      }
      await enterprisePayTypeStatusApi(data)
      this.getTableData()
    },
    confirmData(data) {
      if (data == 'reset') {
        this.tableFrom = {
          page: 1,
          limit: 15,
          types: [],
          name: '',
          status: '',
          eid: [],
          cid: []
        }
        this.treeDefault = 0
      } else {
        this.tableFrom = { ...this.tableFrom, ...data }
      }

      this.getTableData(this.tableFrom)
    },
    treeChange(data) {
      this.tableFrom.types = data.value
      this.getTableData(this.tableFrom)
    }
  }
}
</script>

<style lang="scss" scoped>
.bill-type {
  .header {
    display: flex;
    justify-content: space-between;
    span {
      font-size: 18px;
      line-height: 32px;
      color: #303133;
    }
  }
  .text-right {
    text-align: right;
  }
  .el-radio {
    margin-right: 15px;
  }
  .title {
    font-size: 15px;
    font-weight: 600;
    margin-left: 10px;
    position: relative;
    &:after {
      content: '';
      height: 100%;
      width: 3px;
      background-color: #1890ff;
      position: absolute;
      left: -10px;
      top: 0;
    }
  }
}
.img {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: inline-block;
  vertical-align: top;
  margin-right: 4px;
}
::v-deep .el-table .cell {
  line-height: 26px;
}

.point {
  cursor: pointer;
  &:hover {
    color: #1890ff;
  }
}
.iconxiangmuguanli {
  color: #ff9900;
  margin-right: 4px;
}
.more {
  margin-left: 10px;
}
.btn-box {
  display: flex;
  justify-content: space-between;
  span {
    font-size: 18px;
    line-height: 32px;
    color: #303133;
  }
  .fz30 {
    font-size: 30px;
    margin-left: 14px;
    margin-right: 8px;
    color: #909399;
    font-weight: 400;
  }
}
::v-deep .el-drawer__header {
  padding: 10px 24px 10px 20px;
  font-size: 14px;
}
.flex-between {
  height: 32px;
}
</style>
