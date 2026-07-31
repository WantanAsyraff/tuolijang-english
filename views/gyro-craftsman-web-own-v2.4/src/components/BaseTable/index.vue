<template>
  <div class="base-table-wrapper">
    <el-table ref="tableRef" class="base-table" :data="data" v-loading="loading" :row-key="rowKey || undefined"
      @selection-change="handleSelectionChange">
      <el-table-column v-if="selection" type="selection" width="50" :selectable="selectable" />
      <template v-for="col of tableColumns">
        <el-table-column v-if="col.slot" :label="col.name" :min-width="col.minWidth" :prop="col.field"
          :key="col.field + '_slot'">
          <template #default="scope">
            <slot :name="col.slot" :row="scope.row" :index="scope.$index" />
          </template>
        </el-table-column>
        <el-table-column v-else :label="col.name" :min-width="col.minWidth" :prop="col.field" :key="col.field" />
      </template>
    </el-table>

    <el-pagination v-if="showPagination" class="base-pagination el-pagination-reset" :current-page="page"
      :page-size="pageSize" :page-sizes="[15, 20, 30]" :total="total" layout="total, sizes,prev, pager, next, jumper"
      @size-change="handlePageSizeChange" @current-change="handlePageChange" />
  </div>
</template>

<script>
import { ref } from "vue";

export default {
  name: "BaseTable",
  props: {
    tableColumns: {
      type: Array,
      default: () => []
    },
    data: {
      type: Array,
      default: () => []
    },
    total: {
      type: Number,
      default: 0
    },
    page: {
      type: Number,
      default: 0
    },
    pageSize: {
      type: Number,
      default: 0
    },
    loading: {
      type: Boolean,
      default: false
    },
    showPagination: {
      type: Boolean,
      default: true
    },
    // 是否显示多选列
    selection: {
      type: Boolean,
      default: false
    },
    // 行唯一标识字段，开启多选时建议传入（如 "sku_id"）
    rowKey: {
      type: String,
      default: ""
    },
    // 多选列是否可勾选的判定函数 (row, index) => boolean
    selectable: {
      type: Function,
      default: undefined
    }
  },
  setup(props, ctx) {
    const tableRef = ref();

    const handlePageChange = (v) => {
      ctx.emit("update:page", v);
    };
    const handlePageSizeChange = (v) => {
      ctx.emit("update:pageSize", v);
    }
    const handleSelectionChange = (rows) => {
      ctx.emit("selection-change", rows);
    };

    // 透传 el-table 多选操作能力，供父组件通过 ref 调用
    const clearSelection = () => tableRef.value?.clearSelection();
    const toggleRowSelection = (row, selected) => tableRef.value?.toggleRowSelection(row, selected);
    const toggleAllSelection = () => tableRef.value?.toggleAllSelection();

    return {
      tableRef,
      handlePageChange,
      handlePageSizeChange,
      handleSelectionChange,
      clearSelection,
      toggleRowSelection,
      toggleAllSelection
    };
  }
}
</script>

<style scoped lang="scss">
.base-table-wrapper {
  flex: 1;
  display: flex;
  flex-flow: column nowrap;

  .base-table {
    flex: 1;
  }

  .base-pagination {
    text-align: right;
    margin-top: 14px;
  }

  .empty-text {
    text-align: center;
    padding: 40px 0;
    /* 增加上下间距，使视觉效果更好 */
    color: #606266;
    /* 保持与Element默认文本颜色一致 */
  }

  :deep(.el-table__body-wrapper) {
    min-height: 100%;
  }

  :deep(.el-table__empty-text) {
    margin-top: 20vh;
  }
}
</style>
