<template>
<div>
  <el-table :data="treeData">
    <el-table-column :label="$t('ui.settingModulePermissionsModule')" width="200">
      <template slot-scope="scope">
        <span @click="getData()"> {{ scope.row.module_name }} </span></template
      >
    </el-table-column>
    <el-table-column :label="$t('ui.settingModulePermissionsDataPermissions')" width="auto">
      <template slot-scope="scope">
        <div class="flex">
          <el-select
            v-model="scope.row.data_level"
            size="small"
            @change="dataLevelFn($event, scope.row)"
            :placeholder="$t('ui.developConditionGroupPleaseSelect')"
          >
            <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"> </el-option>
          </el-select>
          <select-department
            v-if="scope.row.data_level == '4'"
            :selectId="scope.row.frame_id || []"
            :placeholder="$t('ui.developConditionGroupPleaseSelect')"
            @changeMastart="changeMastart($event, scope.row)"
            style="width: 400px; margin-left: 20px"
          ></select-department>
        </div>
      </template>
    </el-table-column>
  </el-table>
</div>
</template>
<script>
export default {
  components: {
    selectDepartment: () => import('@/components/form-common/select-department')
  },
  props: {
    treeData: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      options: [
        {
          value: 1,
          label: this.$t('customer.meOnly')
        },
        {
          value: 2,
          label: this.$t('customer.directSubordinates')
        },
        {
          value: 3,
          label: this.$t('customer.thisDept')
        },
        {
          value: 4,
          label: this.$t('customer.custDept')
        },
        {
          value: 5,
          label: this.$t('customer.allData')
        }
      ]
    }
  },
  methods: {
    dataLevelFn(e, item) {
      if (e == 1) {
        item.directly = 0
      } else {
        item.directly = 1
      }
    },
    getData() {
      let obj = {}
      this.treeData.map((item) => {
        obj[item.key] = {
          directly: item.data_level == 1 ? 0 : 1,
          data_level: item.data_level,
          frame_id: item.frame_id
        }
      })
      return obj
    },
    changeMastart(e, row) {
      let ids = []
      e.forEach((item) => {
        ids.push(item.id)
      })
      row.frame_id = ids
    }
  }
}
</script>
<style lang="scss" scoped></style>
