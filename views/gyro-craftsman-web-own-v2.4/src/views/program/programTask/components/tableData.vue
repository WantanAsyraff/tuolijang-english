import { $ } from '@/lang'
<template>
  <div>
    <el-table
      ref="tableData"
      v-loading="loading"
      :data="tableData"
      :default-expanded-row-keys="expandedKeys"
      :header-cell-style="{ background: '#f7fbff' }"
      class="draggable-table"
      row-key="id"
      style="width: 100%"
      @selection-change="handleSelectionChange"
    >
      <el-table-column fixed="left" type="" width="25">
        <template #default="{ row }">
          <i class="sort-handle iconfont icontuodong pointer"></i>
        </template>
      </el-table-column>
      <el-table-column v-if="headerShow" fixed="left" type="selection" width="55"></el-table-column>
      <el-table-column fixed="left" :label="$('ui.programProgramTaskTableDataTaskNumber')" min-width="120" prop="ident" type="">
        <template #default="{ row }" #header>
          <span v-if="!headerShow">{{ $("ui.programProgramTaskTableDataTaskNumber") }}</span>
        </template>
        <template #default="{ row }">
          <span
            :data-clipboard-text="identAddress(row)"
            class="ident copy pointer"
            :title="$('ui.programProgramTaskEditTaskClickToCopyTheWorkItemLink')"
            @click="copy"
            >#{{ row.ident }}</span
          >
        </template>
      </el-table-column>
      <el-table-column fixed="left" :label="$('ui.programProgramTaskTableDataTaskName')" min-width="320" prop="name">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.programProgramTaskTableDataTaskName") }}</span>
        </template>
        <template #default="{ row }">
          <!-- 新建 -->
          <div v-if="row.add">
            <el-input
              v-model="addName"
              :placeholder="$('ui.programProgramTaskTableDataEnterAVersionNamePressEnterToSave')"
              style="width: 260px"
              @blur="handleEnter(row, 0)"
              @keyup.native.enter="handleEnter(row, 1)"
            ></el-input>
          </div>
          <div
            v-if="!row.add"
            class="program-name"
            @mouseenter="showIcon(row.$index)"
            @mouseleave="hideIcon(row.$index)"
          >
            <el-tooltip v-if="!row.edit" class="item" effect="dark" placement="top">
              <div class="pointer flex align-center" @click="editProgram(row)">
                <i class="iconfont icontask1"> </i>
                <div class="task-name line1">{{ row.name }}</div>
              </div>
              <span slot="content">
                <span>{{ row.name }}</span>
              </span>
            </el-tooltip>
            <el-input
              v-if="row.edit"
              ref="addInput"
              v-model="row.name"
              @blur="putName(row, 0)"
              @keyup.native.enter="putName(row, 1)"
            ></el-input>
            <div v-if="!row.edit" class="icon-btn">
              <el-tooltip
                v-if="iconVisible[row.$index] && row.level !== 4"
                class="item"
                :content="$('ui.programProgramTaskTableDataNewChildWorkItem')"
                effect="dark"
                placement="top"
              >
                <i class="iconfont icontiaojian pointer" @click="addChild(row.id)"></i>
              </el-tooltip>
              <el-tooltip
                v-if="iconVisible[row.$index] && row.operate"
                class="item"
                :content="$('ui.formCommonOaLogEdit')"
                effect="dark"
                placement="top"
              >
                <i class="iconfont iconbianji pointer" @click="editChild(row.id)"></i>
              </el-tooltip>
            </div>
          </div>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.developConditionDialogPriority')" min-width="100" prop="priority">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.developConditionDialogPriority") }}</span>
        </template>
        <template #default="{ row }">
          <el-dropdown placement="bottom" trigger="click" @command="putProgramTask('priority', $event, row.id)">
            <span class="el-dropdown-link pointer">
              <el-tag v-if="row.priority === 1" effect="dark" size="mini" type="danger">{{ $("ui.programProgramTaskTableDataUrgent") }}</el-tag>
              <el-tag v-else-if="row.priority === 2" effect="dark" size="mini" type="warning">{{ $("ui.programProgramTaskTableDataHeight") }}</el-tag>
              <el-tag v-else-if="row.priority === 3" effect="dark" size="mini">{{ $("ui.programProgramTaskTableDataCenter") }}</el-tag>
              <el-tag v-else-if="row.priority === 4" effect="dark" size="mini" type="success">{{ $("ui.programProgramTaskTableDataLow") }}</el-tag>
              <el-tag v-else effect="dark" size="mini" type="info">{{ $("ui.programProgramTaskTableDataNoPriority") }}</el-tag>
            </span>
            <el-dropdown-menu v-if="row.operate" slot="dropdown">
              <div class="fixed-height">
                <el-dropdown-item
                  v-for="(item, index) in selectData.priorityOptions"
                  :key="index"
                  :command="item.value"
                >
                  <el-tag
                    :type="
                      item.value === 1
                        ? 'danger'
                        : item.value === 2
                        ? 'warning'
                        : item.value === 3
                        ? ''
                        : item.value === 4
                        ? 'success'
                        : 'info'
                    "
                    effect="dark"
                    size="mini"
                    >{{ item.label }}</el-tag
                  >
                </el-dropdown-item>
              </div>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.customerSetupDictionaryIndexStatus')" min-width="80" prop="status">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.customerSetupDictionaryIndexStatus") }}</span>
        </template>
        <template #default="{ row }">
          <el-dropdown placement="bottom" trigger="click" @command="putProgramTask('status', $event, row.id)">
            <span class="el-dropdown-link pointer">
              <el-tag v-if="row.status === 0" effect="plain" size="mini" type="warning">{{ $("ui.programProgramTaskTableDataUnprocessed") }}</el-tag>
              <el-tag v-else-if="row.status === 1" effect="plain" size="mini">{{ $("ui.programProgramListIndexInProgress") }}</el-tag>
              <el-tag v-if="row.status === 2" effect="plain" size="mini" type="info">{{ $("ui.programProgramTaskTableDataResolved") }}</el-tag>
              <el-tag v-if="row.status === 3" effect="plain" size="mini" type="success">{{ $("ui.programProgramTaskTableDataAccepted") }}</el-tag>
              <el-tag v-if="row.status === 4" effect="plain" size="mini" type="danger">{{ $("ui.userExamineExamineRejected") }}</el-tag>
            </span>
            <el-dropdown-menu v-if="row.operate" slot="dropdown">
              <div class="fixed-height">
                <el-dropdown-item v-for="(item, index) in selectData.statusOptions" :key="index" :command="item.value">
                  <el-tag
                    :type="
                      item.value === 4
                        ? 'danger'
                        : item.value === 0
                        ? 'warning'
                        : item.value === 1
                        ? ''
                        : item.value === 3
                        ? 'success'
                        : 'info'
                    "
                    effect="plain"
                    size="mini"
                    >{{ item.label }}</el-tag
                  >
                </el-dropdown-item>
              </div>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.developModuleTreeOwner')" min-width="150" prop="admins">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.developModuleTreeOwner") }}</span>
        </template>
        <template #default="{ row, index }">
          <div v-if="!adminsVisible[index]" class="pointer" @click="showAdmins(row, index)">
            <el-avatar
              v-if="row.admins && row.admins[0]"
              :src="$getAvatarSrc(row.admins[0])"
              size="small"
            />
            <span v-if="row.admins">{{ row.admins[0] ? row.admins[0].name : '- -' }}</span>
          </div>
          <el-select
            v-else
            v-model="adminsUid"
            :placeholder="$('ui.developModuleTreeOwner')"
            size="small"
            @change="chooseAdmins(row, row.$index)"
            @click.native="changeAdmins(row.program_id, row.$index)"
          >
            <el-option v-for="item in programMemberOptions" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.programProgramTaskTableDataCollaborators')" min-width="180" prop="members">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.programProgramTaskTableDataCollaborators") }}</span>
        </template>
        <template #default="{ row, index }">
          <el-tooltip v-if="!membersVisible[row.$index]" placement="top">
            <template>
              <div class="pointer line1 members-line" @click="showMembers(row, row.$index)">
                <span>
                  {{ row.members.map((obj) => obj.name).join('、') }}
                </span>
                <span v-if="row.members && !row.members.length">- -</span>
              </div>
            </template>
            <div slot="content">
              <div class="pointer tooltip">
                <span>
                  {{ row.members.map((obj) => obj.name).join('、') }}
                </span>
                <span v-if="row.members && !row.members.length">- -</span>
              </div>
            </div>
          </el-tooltip>
          <el-select
            v-else
            v-model="membersId"
            collapse-tags
            filterable
            multiple
            :placeholder="$('ui.programProgramTaskAddTaskAssignCollaborators')"
            size="small"
            @change="changeMembers(row, index)"
          >
            <el-option v-for="item in programMemberOptions" :key="item.id" :label="item.name" :value="item.id">
            </el-option>
          </el-select>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.programProgramTaskTableDataRelatedProject')" min-width="180" prop="program">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.programProgramTaskTableDataRelatedProject") }}</span>
        </template>
        <template #default="{ row }">
          <el-dropdown placement="bottom" trigger="click" @command="putProgramTask('program_id', $event, row.id)">
            <div class="pointer">
              <el-tooltip class="item" effect="dark" placement="top">
                <div class="line1 members-line">{{ row.program ? row.program.name : '- -' }}</div>
                <div slot="content" style="max-width: 180px">
                  {{ row.program ? row.program.name : '- -' }}
                </div>
              </el-tooltip>
            </div>
            <el-dropdown-menu v-if="row.operate" slot="dropdown">
              <div class="fixed-height">
                <el-dropdown-item v-for="(item, index) in projectList" :key="index" :command="item.value">
                  <div>
                    <span>{{ item.label }}</span>
                    <span
                      v-if="item.status == 1 || item.status == 2"
                      :class="item.status == 1 ? 'program-stop' : 'program-close'"
                      >{{ item.status == 1 ? $('ui.programProgramTaskAddTaskPaused') : $('ui.programProgramTaskAddTaskClosed') }}</span
                    >
                  </div>
                </el-dropdown-item>
              </div>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.programProgramTaskTableDataRelatedVersion')" min-width="100" prop="version">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.programProgramTaskTableDataRelatedVersion") }}</span>
        </template>
        <template #default="{ row, index }">
          <el-select
            v-if="showVersion === row.id"
            :ref="'showVersion' + row.id"
            v-model="row.version.id"
            :placeholder="$('ui.programProgramTaskAddTaskPleaseSelectRelatedVersion')"
            size="small"
            @change="changeParam('version_id', row)"
            @click.native="changeVersion(row.program_id, index)"
          >
            <el-option v-for="item in versionList" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
          <div v-else class="pointer" @click="setParamId('showVersion', row.id)">
            <span class="infos">{{ row.version ? row.version.name : '- -' }}</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$('ui.programProgramTaskTableDataPlannedStart')" min-width="100" prop="plan_start">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.programProgramTaskTableDataPlannedStart") }}</span>
        </template>
        <template #default="{ row }">
          <el-date-picker
            v-if="showStartTime === row.id"
            :ref="'showStartTime' + row.id"
            v-model="row.plan_start"
            :format="'yyyy-MM-dd'"
            :value-format="'yyyy-MM-dd'"
            clearable
            :placeholder="$('ui.programProgramListAddProgramSelectThePlannedProjectStartDate')"
            size="small"
            type="date"
            @blur="clearParamId('showStartTime')"
            @change="putProgramTask('plan_start', $event, row.id)"
          ></el-date-picker>
          <span v-else class="pointer width100" @click="setParamId('showStartTime', row.id)">
            {{ row.plan_start || '- -' }}
          </span>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$('ui.programProgramTaskTableDataPlannedEnd')" min-width="100" prop="plan_end">
        <template #default="{ row }" #header>
          <span v-if="!headerShow">{{ $("ui.programProgramTaskTableDataPlannedEnd") }}</span>
        </template>
        <template #default="{ row }">
          <el-date-picker
            v-if="showEndTime === row.id"
            v-model="row.plan_end"
            :format="'yyyy-MM-dd'"
            :value-format="'yyyy-MM-dd'"
            clearable
            :placeholder="$('ui.programProgramListAddProgramSelectThePlannedProjectStartDate')"
            size="small"
            type="date"
            @blur="clearParamId('showEndTime')"
            @change="putProgramTask('plan_end', $event, row.id)"
          ></el-date-picker>
          <span v-else class="pointer width100" @click="setParamId('showEndTime', row.id)">
            {{ row.plan_end || '- -' }}
          </span>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.hrAssessCheckIndexCreator')" min-width="80" prop="creator">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.hrAssessCheckIndexCreator") }}</span>
        </template>
        <template #default="{ row }">
          <span v-for="item in row.creator" :key="item.id">{{ item.name }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.invoiceInvoiceDetailsCreatedTime')" min-width="160" prop="plan_start">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.invoiceInvoiceDetailsCreatedTime") }}</span>
        </template>
        <template #default="{ row }">
          <span>{{ row.created_at || '- -' }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$('ui.hrToolHaishAssessmentHistoryListUpdatedTime')" min-width="160" prop="plan_end">
        <template #header>
          <span v-if="!headerShow">{{ $("ui.hrToolHaishAssessmentHistoryListUpdatedTime") }}</span>
        </template>
        <template #default="{ row }">
          <span>{{ row.updated_at || '- -' }}</span>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
import Sortable from 'sortablejs'
import manageRange from '@/components/form-common/select-manageRange'
import {
  getProgramSelectApi,
  getProgramTaskApi,
  getProgramVersionSelectApi,
  putProgramTaskApi,
  putTaskSortApi
} from '@/api/program'
import SettingMer from '@/libs/settingMer'

export default {
  name: 'TableData',
  components: {
    manageRange
  },
  props: {
    headerShow: {
      type: Boolean,
      default: () => false
    },
    projectList: {
      type: Array,
      default: () => []
    },
    programId: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      tableData: [],
      loading: false,
      expandedKeys: [],
      iconVisible: {},
      adminsVisible: {},
      membersVisible: {},
      showStartTime: '',
      showEndTime: '',
      showVersion: '',
      versionList: {},
      endShowStates: {},
      selectData: {
        priorityOptions: [
          {
            value: 1,
            label: $('ui.programProgramTaskTableDataUrgent')
          },
          {
            value: 2,
            label: $('ui.programProgramTaskTableDataHeight')
          },
          {
            value: 3,
            label: $('ui.programProgramTaskTableDataCenter')
          },
          {
            value: 4,
            label: $('ui.programProgramTaskTableDataLow')
          },
          {
            value: 0,
            label: $('ui.programProgramTaskTableDataNoPriority')
          }
        ],
        statusOptions: [
          {
            value: 0,
            label: $('ui.programProgramTaskTableDataUnprocessed')
          },
          {
            value: 1,
            label: $('customer.execution')
          },
          {
            value: 2,
            label: $('ui.programProgramTaskTableDataResolved')
          },
          {
            value: 3,
            label: $('ui.programProgramTaskTableDataAccepted')
          },
          {
            value: 4,
            label: $('ui.userExamineExamineRejected')
          }
        ]
      },
      total: 0,
      memberShow: false,
      timeVal: [],
      tableFrom: {
        page: 1,
        limit: 15,
        types: 0,
        pid: 0,
        status: '',
        name: '',
        admins: [],
        scope_frame: 'all',
        time_field: 'plan_start',
        scope_normal: 0,
        program_id: this.$route.query.id || [],
        version_id: [],
        eid: [],
        cid: []
      },
      showText: '展开',
      showForm: false,
      typesOptions: [
        {
          value: 0,
          label: $('legacyScript.allTasks')
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
      ],
      statusOptions: [
        {
          value: 0,
          label: $('ui.programProgramTaskTableDataUnprocessed')
        },
        {
          value: 1,
          label: $('customer.execution')
        },
        {
          value: 2,
          label: $('ui.programProgramTaskTableDataResolved')
        },
        {
          value: 3,
          label: $('ui.programProgramTaskTableDataAccepted')
        },
        {
          value: 4,
          label: $('ui.userExamineExamineRejected')
        }
      ],
      timeOptions: [
        {
          value: 'plan_start',
          label: $('ui.programProgramTaskTableDataPlannedStart')
        },
        {
          value: 'plan_end',
          label: $('ui.programProgramTaskTableDataPlannedEnd')
        },
        {
          value: 'created_at',
          label: $('customer.creationtime')
        }
      ],
      type: 0,
      userList: [],
      helperList: [],
      versionVisible: {}
    }
  },
  created() {
    this.getTableData()
    // this.getProgram()
  },
  mounted() {
    const tbody = document.querySelector('.el-table__body-wrapper tbody')
    new Sortable(tbody, {
      animation: 150,
      fallbackOnBody: true,
      swapThreshold: 0.65,
      onMove: ({ dragged, related }) => {
        const oldRow = this.activeRows[dragged.rowIndex] // 移动的那个元素
        const newRow = this.activeRows[related.rowIndex] // 新的元素
        this.childrenNewPid = newRow.pid
        this.childrenOldPid = oldRow.pid
        this.childrenLevel = newRow.level
        if (this.childrenNewPid !== this.childrenOldPid) {
          return false
        }
        let ids = this.activeIds
        ;[ids[dragged.rowIndex], ids[related.rowIndex]] = [ids[related.rowIndex], ids[dragged.rowIndex]]
        if (newRow.pid && oldRow.pid == newRow.pid) {
        }
      },
      onStart: () => {
        this.activeRows.map((item) => {
          this.activeIds.push(item.id)
        })
      }
    })
  },
  watch: {
    projectList(newVal) {
      this.projectList = newVal
    }
  },
  methods: {
    addChild(parentId) {
      this.parentId = parentId
      this.addName = ''
      const newRow = {
        id: Date.now(),
        name: '',
        children: [],
        add: true
      }
      const findAndAdd = (array) => {
        array.forEach((item) => {
          if (item.id === parentId) {
            if (!item.children) {
              this.$set(item, 'children', [])
            }
            item.children.push(newRow)
            this.$set(item, 'has_children', true)
            this.$nextTick(() => {
              this.focusOnNewRow(newRow.id)
            })
          } else if (item.children && item.children.length > 0) {
            findAndAdd(item.children)
          }
        })
      }
      findAndAdd(this.tableData)
    },
    focusOnNewRow(id) {
      this.$nextTick(() => {
        const element = this.$refs[`edit-${id}`]
        if (element && element[0]) {
          element[0].focus()
          element[0].addEventListener('keyup', (event) => {
            if (event.key === 'Enter') {
              this.submitNewRow(id)
            }
          })
        }
      })
    },
    handleSelectionChange(val) {
      this.checkedId = val.map((item) => item.id)
      this.ids = val
    },
    // 获取表格数据
    async getTableData() {
      this.loading = true
      const res = await getProgramTaskApi(this.tableFrom)
      this.loading = false
      this.tableData = res.data.list
      return
      let data = tableFrom ? tableFrom : this.tableFrom
      if (!this.parentRoleId) {
        data.pid = 0
        const res = await getProgramTaskApi(this.tableFrom)
        this.tableData = res.data.list
        this.tableList = res.data.list
        if (data.pid) {
          this.tableChildrenData = res.data.list
        }
        this.activeRows = [...this.tableData]
        this.total = res.data.count
        this.loading = false
      }
      if (this.parentRoleId && this.maps.get(this.parentRoleId)) {
        this.loading = false
        const { tree, treeNode, resolve } = this.maps.get(this.parentRoleId)
        this.load(tree, treeNode, resolve)
      }
      //懒加载刷新当前级
      if (this.currentRoleId && this.maps.get(this.currentRoleId)) {
        this.loading = false
        const { tree, treeNode, resolve } = this.maps.get(this.currentRoleId)
        this.load(tree, treeNode, resolve)
      }
    },
    // 获取项目数据
    async getProgram() {
      const result = await getProgramSelectApi()
      this.projectList = result.data
    },
    // 获取项目版本数据
    async getProgramVersion(program_id, index, type) {
      const result = await getProgramVersionSelectApi({ program_id })
      this.versionList = result.data
      if (this.versionList.length) {
        this.$set(this.versionVisible, index, true)
      } else if (type == 1) {
        this.$message($('legacyScript.pleaseSetTheVersionInTheTaskDetails'))
      }
    },
    chooseVersion(row, index) {
      this.putProgramTask('version_id', this.versionId, row.id)
      this.hideVersion(index)
    },
    async setParamId(param, id) {
      this.$set(this, param, id)
      if (param === 'showVersion') {
        await this.getProgramVersion(id)
      }
      if (this.$refs[param + id]) {
        this.$nextTick(() => {
          this.$refs[param + id].focus()
        })
      }
    },
    clearParamId(param) {
      if (param) {
        this.$set(this, param, '')
      } else {
        this.$set(this, 'showStartTime', '')
        this.$set(this, 'showEndTime', '')
        this.$set(this, 'showVersion', '')
      }
    },
    async changeParam(key, row) {
      this.putProgramTask(key, row.version.id, row.id)
      await this.clearParamId()
    },
    // 修改
    putProgramTask(field, data, id) {
      let datas = {}
      datas['field'] = field
      datas[field] = data
      if (field == 'pid') {
        datas['path'] = this.formData.path
      }
      const res = putProgramTaskApi(id, datas)
      if (res.status === 200) {
        this.getTableData()
        this.preventBlur = true
        this.$set(this.membersVisible, this.membersIndex, false)
      }
    },
    // 拖拽结束
    onEnd: ({ newIndex, oldIndex }) => {
      this.activeRows.map((item) => {
        if (item.id === this.childrenNewPid) {
          this.childrenTotal = item.children_count
        }
      })

      if (this.childrenNewPid) {
        let pidIndex = this.activeIds.indexOf(this.childrenNewPid)
        this.activeIds = this.activeIds.slice(pidIndex + 1, pidIndex + 1 + this.childrenTotal)
      }
      if (this.childrenNewPid !== this.childrenOldPid) {
        this.activeIds = []
        this.childrenNewPid = 0
        this.childrenTotal = 0
        this.$message.warning($('legacyScript.crossLevelDraggingIsNotAllowed'))
        return false
      }
      const filteredRows = this.activeRows.filter((row) => row.level === this.childrenLevel)
      this.activeIds = this.activeIds
        .map((id) => filteredRows.find((row) => row.id === id))
        .filter((row) => row !== undefined) // 移除未找到匹配项的情况
        .map((row) => row.id)
      if (!this.activeIds) return false
      putTaskSortApi({ data: this.activeIds }).then(() => {
        this.tableFrom.pid = 0
        this.getTableData()
        this.activeRows = []
        this.activeIds = []
        this.tableData = []
        this.childrenNewPid = 0
        this.childrenTotal = 0
        this.expandedKeys = []
      })
    },
    // 复制工作项地址
    identAddress(row) {
      let type = 'type',
        relation_id = 'relation_id',
        program_id = 'program_id'
      return `${SettingMer.httpUrl}${window.location.pathname}${window.location.search}?&${type}=2&${relation_id}=${row.id}&${program_id}=${row.program_id}`
    },
    // 复制工作项地址
    copy(row) {
      let type = 'type',
        relation_id = 'relation_id',
        program_id = 'program_id'
      return `${SettingMer.httpUrl}${window.location.pathname}${window.location.search}?&${type}=2&${relation_id}=${row.id}&${program_id}=${row.program_id}`
    }
  }
}
</script>
<style lang="scss" scoped>
::v-deep .el-table tr {
  height: 56px;
}
</style>
