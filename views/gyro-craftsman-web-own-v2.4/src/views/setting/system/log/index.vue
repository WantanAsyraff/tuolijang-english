<template>
  <div class="divBox">
    <el-card class="card-head normal-page" shadow="never" :body-style="{ padding: '20px 20px 0 20px' }">
      <oaFromBox
        :search="searchData"
        :title="$route.meta.title"
        :total="total"
        :isViewSearch="false"
        :sortSearch="false"
        :isAddBtn="false"
        @confirmData="confirmData"
      ></oaFromBox>
      <div class="table-box mt20">
        <el-table :data="tableData" style="width: 100%" :height="tableHeight">
          <el-table-column prop="id" label="ID" min-width="80" />
          <el-table-column prop="user_name" :label="$('toptable.operationuser')" min-width="100" />
          <!-- <el-table-column prop="event_name" :label="$('toptable.behavior')" min-width="120" /> -->
          <el-table-column prop="event_name" :label="$("ui.developCrudEventAction")" min-width="240" show-overflow-tooltip>
            <template slot-scope="scope">
              <span>{{ $(scope.row.event_name, scope.row.event_name_en) }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="terminal" :label="$('toptable.loginterminal')" min-width="100" />
          <el-table-column prop="last_ip" :label="$('toptable.operationip')" min-width="100" />
          <el-table-column prop="created_at" :label="$('toptable.operationtime')" min-width="165" />
        </el-table>
        <div class="page-fixed">
          <el-pagination
            :page-size="formData.limit"
            :current-page="formData.page"
            :page-sizes="[15, 20, 30]"
            layout="total, prev, pager, next, jumper"
            :total="total"
            @size-change="handleSizeChange"
            @current-change="pageChange"
          />
        </div>
      </div>
    </el-card>
  </div>
</template>
<script>
import { $ } from '@/lang'
import { enterpriseLogApi } from '@/api/enterprise'
import oaFromBox from '@/components/common/oaFromBox'
export default {
  name: 'SystemLog',
  components: {
    oaFromBox
  },
  data() {
    return {
      total: 0,
      formData: {
        user_name: '',
        path: '',
        event_name: '',
        time: '',
        page: 1,
        limit: 15
      },
      value1: [
        this.$moment().startOf('month').format('YYYY/MM/DD'),
        this.$moment().endOf('month').format('YYYY/MM/DD')
      ],
      pickerOptions: this.$pickerOptionsTimeEle,
      methodOpts: [
        {
          value: '选项1',
          label: $('legacyScript.goldenCake')
        },
        {
          value: '选项2',
          label: $('legacyScript.doubleSkinMilk')
        },
        {
          value: '选项3',
          label: $('legacyScript.oysterOmelette')
        },
        {
          value: '选项4',
          label: $('legacyScript.dragonBeardNoodles')
        },
        {
          value: '选项5',
          label: $('legacyScript.beijingRoastDuck')
        }
      ],
      tableData: [],
      searchData: [
        {
          field_name: '用户行为',
          field_name_en: 'event_name',
          form_value: 'input'
        },
        {
          field_name: '开始时间',
          field_name_end: '结束时间',
          field_name_en: 'time',
          form_value: 'date_picker'
        }
      ]
    }
  },
  created() {},
  mounted() {
    this.getList()
  },
  methods: {
    search() {
      if (this.value1 != '') {
        const time = this.value1.join('-')
        this.formData.time = time
      }
      this.formData.page = 1
      this.getList()
    },
    reset() {
      this.value1 = ''
      this.formData.event_name = ''
      ;(this.value1 = [
        this.$moment().startOf('month').format('YYYY/MM/DD'),
        this.$moment().endOf('month').format('YYYY/MM/DD')
      ]),
        (this.formData.time =
          this.$moment().startOf('month').format('YYYY/MM/DD') +
          '-' +
          this.$moment().endOf('month').format('YYYY/MM/DD'))
      this.getList()
    },
    getList() {
      enterpriseLogApi(this.formData).then((res) => {
        this.tableData = res.data.list
        this.total = res.data.count
      })
    },
    handleSizeChange(num) {
      this.formData.limit = num
      this.getList()
    },
    pageChange(num) {
      this.formData.page = num
      this.getList()
    },
    confirmData(data) {
      if (data == 'reset') {
        this.formData = {
          page: 1,
          limit: 15,
          event_name: '',
          time: ''
        }
        this.getList()
      } else {
        this.formData = { ...this.formData, ...data }
        this.formData.page = 1
        this.getList()
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.card-head {
  margin-bottom: 15px;
}
.btn-box {
  margin-left: 128px;
}
::v-deep .el-form-item {
  margin-right: 10px;
}
.btn-box button {
  width: 86px;
}
::v-deep .el-pager li {
  padding: 0;
}
</style>
