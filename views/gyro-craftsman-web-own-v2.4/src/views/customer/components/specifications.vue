<template>
<div>
  <el-dialog
    :title="$('ui.customerSpecificationsSelectProducts')"
    :visible.sync="dialogTableVisible"
    :before-close="handleClose"
    :append-to-body="true"
    width="800px"
  >
    <div class="flex mb10">
      <el-cascader
        v-model="where.pid"
        :options="options"
        @change="getList(1)"
        @keydown="getList(1)"
        :props="{ label: 'name', value: 'id', emitPath: false }"
        size="small"
        class="mr10"
        :placeholder="$('ui.customerSpecificationsProductCategories')"
        style="width: 200px"
        clearable
      ></el-cascader>
      <el-input
        :placeholder="$('ui.customerSpecificationsPleaseEnterAName')"
        v-model="where.name"
        size="small"
        style="width: 200px"
        class="mr10"
        @change="getList(1)"
        @keydown="getList(1)"
        clearable
      ></el-input>
      <el-input
        :placeholder="$('ui.customerSpecificationsPleaseEnterSpecValue')"
        v-model="where.attr"
        @change="getList(1)"
        @keydown="getList(1)"
        size="small"
        style="width: 200px"
        clearable
      ></el-input>
    </div>

    <el-table
      :data="gridData"
      height="400px"
      row-key="unique"
      ref="multipleTable"
      :tree-props="{ children: 'attr_value', hasChildren: 'hasChildren' }"
    >
      <el-table-column property="date" :label="$('ui.customerSpecificationsProductInformation')">
        <template #header>
          <span class="iconfont icona-tongyongweigouxuanbiankuang ml40" v-if="!allId" @click="selectAll()"></span>

          <span class="iconfont icontongyonggouxuan-01 ml40" v-if="allId" @click="selectAll()"></span>
          {{ $("ui.customerSpecificationsProductInformation") }}
        </template>
        <template slot-scope="scope">
          <div class="flex">
            <div>
              <span
                v-if="getInclude4(scope.row)"
                class="iconfont icontongyonggouxuan-01"
                :class="getCheckboxClass(scope.row)"
                @click="selectFn(true, scope.row)"
              ></span>
              <span
                class="iconfont icona-tongyongweigouxuanbiankuang"
                v-else
                :class="getCheckboxClass(scope.row)"
                @click="selectFn(false, scope.row)"
              ></span>
            </div>

            <img
              v-if="scope.row.name && scope.row.attr_value.length == 1"
              :src="scope.row.attr_value[0].image"
              alt=""
              class="img"
            />
            <span v-if="scope.row.name" style="width: 360px" class="over-text2">
              {{ scope.row.name }}
            </span>
            <img v-if="scope.row.image" :src="scope.row.image" alt="" class="img" />
            <span v-if="scope.row.sku">{{ scope.row.sku }}</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column property="name" :label="$('ui.customerSpecificationsProductPrice')" width="120">
        <template slot-scope="scope">
          <div v-if="scope.row.attr_value && scope.row.attr_value.length == 1" class="ml10">
            {{ scope.row.attr_value[0].price || '0.00' }}
          </div>
          <div v-if="!scope.row.attr_value" class="ml10">
            {{ scope.row.price || '0.00' }}
          </div>
        </template>
      </el-table-column>
    </el-table>
    <div class="page">
      <el-pagination
        :current-page="where.page"
        :page-size="where.limit"
        :total="total"
        layout="total,prev, pager, next"
        @current-change="pageChange"
      />
    </div>
    <div slot="footer" class="dialog-footer">
      <el-button @click="handleClose()">{{ $("ui.formCommonSelectLabelCancel") }}</el-button>
      <el-button type="primary" @click="submit">{{ $("ui.formCommonDialogFormOk") }}</el-button>
    </div>
  </el-dialog>
</div>
</template>
<script>
import { getProductsAttrsApi, productCateListApi } from '@/api/client'
export default {
  data() {
    return {
      dialogTableVisible: false,
      where: {
        page: 1,
        limit: 10,
        pid: '',
        name: '',
        attr: ''
      },
      ids: [],
      allId: false,
      selectedList: [],
      options: [],
      list: [],
      total: 0,
      gridData: []
    }
  },
  watch: {
    selectedList: function (val) {
      this.setChecked(val)
    }
  },

  mounted() {
    this.getOptions()
    this.getList()
  },
  methods: {
    getList(type) {
      if (type == 1) {
        this.where.page = 1
        this.allId = false
      }
      getProductsAttrsApi(this.where).then((res) => {
        this.gridData = res.data.list
        this.gridData.forEach((item) => {
          item.unique = item.id + ''
          if (item.attr_value.length <= 1) {
            item.hasChildren = true
          }
          // 将父级商品名 / 单位挂到每个规格项上，方便选中后回填展示（增量字段，不影响原有消费方）
          ;(item.attr_value || []).forEach((el) => {
            el.product_name = item.name
            el.unit_name = item.unit_name
          })
        })
        this.total = res.data.count
      })
    },
    // 勾选全部
    selectAll() {
      this.allId = !this.allId
      this.ids = []
      if (this.allId) {
        this.gridData.map((item) => {
          this.ids.push(item.unique)
          item.attr_value.map((el) => {
            this.ids.push(el.unique)
            this.selectedList.push(el)
          })
        })
      } else {
        this.ids = []
        this.selectedList = []
      }
    },
    getInclude4(row) {
      if (!row.attr_value) {
        return this.ids.includes(row.unique)
      }
      const targetId =
        row.attr_value.length === 1
          ? row.attr_value[0].unique // 单规格
          : row.unique // 多规格

      return this.ids.includes(targetId)
    },

    // 勾选和反选
    selectFn(type, row) {
      // 1. 提取当前行相关的所有唯一标识（统一处理不同规格）
      const rowIds = this.getRowIds(row)

      if (type) {
        // 2. 取消选中：从ids和selectedList中移除相关项
        this.ids = this.ids.filter((id) => !rowIds.includes(id))
        this.selectedList = this.selectedList.filter((item) => !rowIds.includes(item.unique))
      } else {
        // 3. 选中：添加相关项到ids和selectedList
        this.ids.push(...rowIds)
        this.selectedList.push(...this.getItemsToAdd(row))
      }
    },

    // 辅助函数：获取当前行所有需要处理的唯一标识
    getRowIds(row) {
      // 基础ID（当前行自身的unique）
      const ids = [row.unique]
      // 如有子项，添加子项的unique
      if (row.attr_value && row.attr_value.length) {
        row.attr_value.forEach((el) => ids.push(el.unique))
      }
      return ids
    },

    // 辅助函数：获取需要添加到selectedList的项
    getItemsToAdd(row) {
      if (!row.attr_value || !row.attr_value.length) {
        return [row]
      }

      return row.attr_value
    },
    openBox(row, data) {
      this.selectedList = JSON.parse(JSON.stringify(data))
      this.dialogTableVisible = true
    },
    getCheckboxClass(row) {
      if (!row.name) return ''
      const { attr_value = [] } = row
      return attr_value.length === 1 ? 'ml40' : attr_value.length > 1 ? 'ml18' : ''
    },

    handleClose() {
      this.ids = []
      this.selectedList = []
      this.dialogTableVisible = false
    },

    setChecked(data) {
      this.ids = []
      if (data.length == 0) return
      data.map((item) => {
        if (item.unique) {
          this.ids.push(item.unique)
        }
      })
      let allId = []
      this.gridData.map((item) => {
        allId.push(item.unique)
        const idsToCheckone = item.attr_value.map((item) => item.unique)
        if (this.isArrayContained(this.ids, idsToCheckone)) {
          this.ids.push(item.unique)
        } else {
          this.ids = this.ids.filter((item) => item != item.unique)
        }
      })
      if (this.isArrayContained(this.ids, allId)) {
        this.allId = true
      } else {
        this.allId = false
      }
    },

    isArrayContained(arr1, arr2) {
      return arr2.every((element) => arr1.includes(element))
    },
    submit() {
      this.list = JSON.parse(JSON.stringify(this.selectedList))

      this.$emit('getselectList', this.selectedList, this.ids)
      this.dialogTableVisible = false
    },
    getOptions() {
      productCateListApi().then((res) => {
        this.options = res.data
      })
    },
    handleSizeChange(val) {
      this.where.limit = val
      this.getList()
    },
    pageChange(val) {
      this.where.page = val
      this.allId = false
      this.getList()
    }
  }
}
</script>
<style scoped lang="scss">
.img {
  width: 34px;
  height: 34px;
  margin-right: 8px;
}
.page {
  display: flex;
  justify-content: flex-end;
  margin-top: 10px;
}
.icontongyonggouxuan-01 {
  cursor: pointer;
  color: #1890ff;
  font-size: 13px;
  margin-right: 12px;
}
.icona-tongyongweigouxuanbiankuang {
  cursor: pointer;
  font-size: 13px;
  font-weight: 400;
  color: #909399;
  margin-right: 12px;
}
.pl50 {
  padding-left: 50px;
  padding-right: 40px;
}

.ml40 {
  margin-left: 36px !important;
}
.ml18 {
  margin-left: 13px;
}
::v-deep .el-table td.el-table__cell div {
  display: flex;
  align-items: center;
}
</style>
