<template>
  <div class="divBox">
    <el-card :body-style="{ padding: '20px 20px 20px 20px' }" class="normal-page el-card-flex">
      <oaFromBox
        :search="search"
        :title="$route.meta.title"
        :total="total"
        :dropdownList="dropdownList"
        :treeData="treeData"
        :viewSearch="viewSearch"
        :whereData="where"
        :timeSearchObj="timeSearchObj"
        :category="keyword"
      ref="fromBox"
      :btnText="$('ui.customerLiaisonIndexAddContact')"
        :isAddBtn="false"
        @dropdownFn="dropdownFn"
        @addDataFn="addDataFn"
        @confirmData="confirmData"
      ></oaFromBox>

      <customizeTable
        flexLayout
        ref="tableData"
        :keyword="keyword"
        :where="where"
        :tableData="tableData"
        :total="total"
        :loading="loading"
        :isChecked="false"
        @getSearch="getSearch"
        @getTableData="getTableData"
      >
        <template #options="{ data }">
          <el-button type="text" @click="handleCheck(data)">{{ $("ui.layoutNoticeNoticeListView") }}</el-button>
          <el-button type="text" @click="handleDel(data)">{{ $("ui.chatIndexDelete") }}</el-button>
        </template>
      </customizeTable>
    </el-card>
    <!-- 详情 -->
    <detailsDrawer ref="details" :formData="detailsFromData" @getTableData="getTableData"></detailsDrawer>
    <!-- 导入联系人 -->
    <dragUpload ref="dragUpload" @getTableData="getTableData()"></dragUpload>
    <!-- 导入/导出记录 -->
    <importRecords ref="importRecords"></importRecords>

    <!-- 通用弹窗表单   -->
    <liaison-dialog ref="liaisonDialog" :formData="liaisonConfig" @isLiaison="getTableData"></liaison-dialog>
  </div>
</template>
<script>
import { $ } from '@/lang'
import { clientExportApi, clientLiaisonDeleteApi, clientLiaisonListApi as liaisonViewApi } from '@/api/client'
import { DRAWER_SIZE } from '@/constants/popupSize'
import { roterPre } from '@/settings'

export default {
  name: 'Liaison',
  components: {
    customizeTable: () => import('../components/customizeTable'),
    detailsDrawer: () => import('../components/details'),
    dragUpload: () => import('../components/dragUpload'),
    importRecords: () => import('@/views/customer/list/components/importRecords'),
    liaisonDialog: () => import('@/views/customer/list/components/liaisonDialog'),
    oaFromBox: () => import('@/components/common/oaFromBox')
  },

  data() {
    return {
      treeData: [
        {
          label: $('legacyScript.ownedByMe'),
          id: 1
        },
        {
          label: $('legacyScript.ownedBySubordinates'),
          id: 2
        }
      ],
      liaisonConfig: {},
      detailsFromData: {},
      tableData: [],
      where: {
        page: 1,
        limit: 15,
        view_search: 1
      },
      dropdownList: [
        { label: $('ui.developModuleTableStyleFilterSettings'), value: 1 },
        { label: $('ui.developModuleTableStyleColumnDisplaySettings'), value: 2 },
        { label: $('customer.export'), value: 3 },
        { label: $('finance.batchupload'), value: 4 },
        { label: $('legacyScript.importExportRecords'), value: 5 },
        { label: $('legacyScript.fieldOptionSettings'), value: 6 }
      ],
      keyword: 'liaison',
      total: 0,
      loading: false,
      search: [],
      timeSearchObj: {},
      customInfo: {},
      viewSearch: []
    }
  },

  mounted() {
    // this.getTableData()
  },
  methods: {
    getSearch(val) {
      this.search = val.search
      this.viewSearch = val.viewSearch
      this.timeSearchObj = val.timeSearchObj
    },

    //添加联系人
    addDataFn() {
      this.liaisonConfig = {
        title: $('legacyScript.addContact'),
        width: '570px'
      }
      this.$refs.liaisonDialog.openBox('', this.customInfo, '')
    },

    async getTableData() {
      if (this.loading) return
      this.loading = true
      const res = await liaisonViewApi(this.where)
      this.tableData = res.data.list
      this.total = res.data.count
      this.loading = false
    },

    handleCheck(item) {
      this.detailsFromData = {
        title: $('legacyScript.viewContact'),
        width: DRAWER_SIZE.LG,
        data: item,
        types: 'liaison',
        link_type: 'liaison'
      }

      this.$refs.details.openBox(item.id, 'liaison')
    },

    // 导出列表数据
    async exportList() {
      await clientExportApi(this.keyword, { ...this.where, page: 0, limit: 0, types: this.keyword })
    },
    dropdownFn(item) {
      switch (item.value) {
        case 1:
          // 筛选条件设置
          this.$refs.tableData.customSearchEvt(1)
          break
        case 2:
          // 表头显示设置
          this.$refs.tableData.customSearchEvt(2)
          break
        case 3:
          // 导出
          this.exportList()
          break
        case 4:
          // 导入
          this.$refs.dragUpload.openBox(this.keyword)
          break
        case 5:
          // 导入导出记录
          this.$refs.importRecords.openBox(this.keyword)
          break
        case 6:
          // 字典选项设置
          this.$router.push({ path: `${roterPre}/customer/liaison/dictSetting` })
          break
      }
    },

    confirmData(data) {
      if (data == 'reset') {
        this.where = {
          page: 1,
          limit: 15,
          view_search: this.where.view_search
        }
        this.labelText = ''
        this.getTableData('')
      } else {
        this.where = {
          page: 1,
          limit: 15
        }

        for (let key in data) {
          this.where[key] = data[key] || ''
        }
        setTimeout(() => {
          this.getTableData()
        }, 100)
      }
    },
    // 删除
    async handleDel(item) {
      await this.$modalSure('确定删除当前联系人')
      await clientLiaisonDeleteApi(item.id)
      if (this.where.page > 1 && this.tableData.length <= 1) {
        this.where.page--
      }
      await this.getTableData()
    }
  }
}
</script>

<style lang="scss" scoped></style>
