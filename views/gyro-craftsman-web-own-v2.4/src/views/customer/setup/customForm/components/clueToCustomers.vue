<template>
<el-dialog :visible.sync="dialogTableVisible" :title="$t('ui.customerSetupCustomFormClueToCustomersLeadToCustomerFieldSettings')">
  <div class="tips">{{ $t("ui.customerSetupCustomFormClueToCustomersWhenConvertingALeadToACustomerSelectThe") }}</div>
  <el-table :data="gridData" :height="450">
    <el-table-column :label="$t('ui.customerSetupCustomFormClueToCustomersLeadField')" property="name">
      <template slot-scope="scope">
        {{ scope.row.key_name }}
      </template>
    </el-table-column>
    <el-table-column label="" width="200">
      <template slot-scope="scope">
        <span class="el-icon-right"></span>
      </template>
    </el-table-column>
    <el-table-column :label="$t('ui.customerSetupCustomFormClueToCustomersCustomerField')" property="customer">
      <template slot-scope="scope">
        <el-select
          v-model="scope.row.related"
          :placeholder="$t('ui.developConditionGroupPleaseSelect')"
          multiple
          clearable
          filterable
          @change="changeSelect"
        >
          <el-option
            v-for="(item, index) in options"
            :key="index"
            :label="item.key_name"
            :value="item.key"
            :disabled="selectedList.includes(item.key)"
          ></el-option>
        </el-select>
      </template>
    </el-table-column>
  </el-table>
  <div slot="footer" class="dialog-footer">
    <el-button size="small" @click="dialogTableVisible = false">{{ $t("ui.formCommonSelectLabelCancel") }}</el-button>
    <el-button size="small" type="primary" @click="submit">{{ $t("ui.formCommonDialogFormOk") }}</el-button>
  </div>
</el-dialog>
</template>
<script>
import { getFormListApi } from '@/api/form'
export default {
  props: {
    clueList: {
      type: Array,
      default: () => {
        return []
      }
    }
  },
  data() {
    return {
      dialogTableVisible: false,
      options: [],
      gridData: [],
      selectedList: [] // 已选中的字段
    }
  },

  mounted() {
    if (this.clueList.length > 0) {
      this.clueList.map((item) => {
        item.data.map((el) => {
          let obj = {
            key_name: el.key_name,
            field: el.key,
            related: []
          }
          this.gridData.push(obj)
        })
      })
    }
  },
  methods: {
    submit() {
      this.$emit('gridData', this.gridData)
      this.dialogTableVisible = false
    },
    async openBox() {
      await this.getList()
      this.dialogTableVisible = true
    },

    changeSelect() {
      this.selectedList = []
      this.gridData.map((item) => {
        if (item.related) {
          this.selectedList.push(...item.related)
        }
      })
    },

    getList() {
      getFormListApi({ types: 1 }).then((res) => {
        res.data.map((item) => {
          this.options.push(...item.data)
        })
        this.gridData.forEach((items) => {
          this.options.forEach((val) => {
            if (items.field === val.link_field) {
              items.related = [val.key]
            }
          })
        })
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.tips {
  font-size: 12px;
  color: #999999;
  margin-bottom: 10px;
}
.el-icon-right {
  font-size: 16px;
  color: #1890ff;
}
::v-deep .el-select .el-tag__close.el-icon-close {
  background: #f4f4f5;
  font-size: 14px;
}
</style>
