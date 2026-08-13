<!-- @FileDescription: 人事-审批设置-请假控件组对应的配置组件 -->
<template>
<div class="composite-selector">
  <el-table :data="tableData">
    <el-table-column prop="name" :label="$('ui.hrHolidaySettingLeaveTypes')" min-width="180">
      <template slot-scope="scope">
        <div class="flex">
          <div class="ml10">{{ scope.row.name }}</div>
        </div>
      </template>
    </el-table-column>
    <el-table-column prop="refuse" :label="$('ui.hrHolidaySettingLeaveUnit')" min-width="100">
      <template slot-scope="scope">
        <span>{{ scope.row.duration_type == 1 ? $('ui.hrHolidaySettingByHour') : $('ui.hrHolidaySettingByDay') }}</span>
      </template>
    </el-table-column>
  </el-table>
</div>
</template>
<script>
import { approveHolidayTypeApi } from '@/api/business'
export default {
  name: 'HolidaySetting',
  props: {
    value: {
      default: undefined,
      type: String
    }
  },
  data() {
    return {
      tableData: [],
      title: ''
    }
  },
  created() {
    this.getTableData()
  },
  methods: {
    handleSelectChange(value) {
      this.$emit('update:value', value)
    },
    handleInputChange(value) {
      this.$emit('input', value)
    },
    // 获取假期类型列表
    async getTableData() {
      const result = await approveHolidayTypeApi()
      this.tableData = result.data.list
    }
  }
}
</script>

<style scoped lang="scss">
.el-select .el-input {
  width: 130px;
}
.el-input-group__prepend .el-select {
  width: 100px;
}
</style>
