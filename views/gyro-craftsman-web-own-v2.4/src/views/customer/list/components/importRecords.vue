<template>
<el-dialog :title="$('ui.customerListImportRecordsImportExportRecords')" :visible.sync="dialogVisible" width="1100px" @close="handleClose">
  <template v-if="recordList.length>0">
  <el-table :data="recordList" size="small" style="width: 100%;" height="400px" >
    <el-table-column :label="$('ui.customerListImportRecordsFileName')" prop="name" min-width="200">
      <template slot-scope="scope">
        <el-tooltip v-if="scope.row.name" effect="dark" :content="scope.row.name" placement="top-start">
          <div class="lh-center">
            <i class="iconfont iconExcelgeshi"></i>
            <span class="name over-text">{{ scope.row.name }}</span>
            <i :title="$('ui.customerListImportRecordsDownloadFile')" class="iconfont iconxiazai" v-if="scope.row.file_path"
              @click="downloadFile(scope.row)"></i>
          </div>
        </el-tooltip>
        <span v-else>--</span>

      </template>
    </el-table-column>
    <el-table-column :label="$('ui.administrationMaterialChartIndexOperator')" prop="admin.name" />
    <el-table-column :label="$('ui.administrationMaterialFixedLogOperationTime')" prop="created_at" width="180" />
    <el-table-column :label="$('ui.developViewManagementType')" prop="created_at" width="130" >
      <template slot-scope="scope">
        {{scope.row.types == 1 ? $('ui.commonImportExcelImport') : $('ui.fdExamineIndexExport')}}
      </template>
    </el-table-column>
    <el-table-column :label="$('ui.customerListImportRecordsOperationResult')" prop="fail_msg">
      <template slot-scope="scope">
        <!-- 失败时显示带提示的文本 -->
        <el-tooltip v-if="scope.row.status === 2" class="item" effect="dark" :content="scope.row.fail_msg"
          placement="top-start">
          <span v-html="getStatusText(scope.row, scope.row.status)"></span>
        </el-tooltip>
        <!-- 非失败时直接显示文本 -->
        <span v-else v-html="getStatusText(scope.row, scope.row.status)"></span>
      </template>
    </el-table-column>
    <el-table-column :label="$('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" width="80">
      <template slot-scope="scope">
        <el-button type="text" size="mini" @click="deleteRecord(scope.row)">
          {{ $("ui.chatIndexDelete") }}
        </el-button>
      </template>
    </el-table-column>
  </el-table>
  <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="where.page"
    :page-size="where.limit" layout="total, prev, pager, next, jumper" :total="total" class="mb10"
    style="margin-top: 15px; text-align: right;">
  </el-pagination>
  </template>
  <template v-else>
 <default-page :index="14" :min-height="400" />
  </template>
</el-dialog>
</template>
<script>
import { clientExportRecordApi,clientExportRecordDeleteApi } from '@/api/client'
export default {
  name: 'ImportExportRecord',
components: {
    defaultPage: () => import('@/components/common/defaultPage')
  },
  data() {
    return {
      dialogVisible: false,

      total: 0,
      where: {
        page: 1,
        limit: 10,
        types: ''
      },
      recordList: [

      ]
    }
  },

  methods: {
    // 关闭弹窗，通知父组件
    handleClose() {
      this.dialogVisible = false;
    },
    openBox(keyword) {
      this.dialogVisible = true;
      this.where.types = keyword
      this.getRecordList()
    },
    // 获取导入导出记录列表
    async getRecordList() {
      const res = await clientExportRecordApi(this.where)
      this.recordList = res.data.list
      this.total = res.data.count

    },
    getStatusText(row, status) {
      let statusText = ''
      if (row.types == 0) {
        // 导出
        if (status == 0) {
          statusText = this.$('ui.customerImportRecords.exporting')

        } else if (status == 1) {
          statusText = this.$('ui.customerImportRecords.exportSucceeded')
        } else if (status == 2) {
          statusText = this.$('ui.customerImportRecords.exportFailed')
        }

      } else if (row.types == 1) {
        // 导入
        if (status == 0) {
          statusText = this.$('ui.customerImportRecords.importing')
        } else if(status == 2){
          statusText = this.$('ui.customerImportRecords.importFailed')
        }else {
          statusText = this.$('ui.customerImportRecords.completed', {
            success: `<span style="color:#19BE6B">${row.success_count}</span>`,
            failure: `<span style="color:#F56C6C">${row.fail_count}</span>`
          })
        }
      }
      return statusText
    },

    // 下载文件（实际需对接后端接口）
    downloadFile(row) {
      this.fileLinkDownLoad(row.file_path, row.name)
    },
   async deleteRecord(row) {
      await this.$modalSure(this.$('确定删除当前数据'))
      await clientExportRecordDeleteApi(row.id)
      this.getRecordList()
    },
    // 每页条数改变
    handleSizeChange(val) {
      this.where.limit = val;
      this.getRecordList()
    },
    // 当前页码改变
    handleCurrentChange(val) {
      this.where.page = val;
      this.getRecordList()
    }
  }
};
</script>

<style scoped lang="scss">
.iconExcelgeshi {
  color: #19BE6B;
}

.iconxiazai {
  margin-left: 5px;
  cursor: pointer;
}

.name {
  margin-left: 10px;
  width: 200px;
}
</style>
