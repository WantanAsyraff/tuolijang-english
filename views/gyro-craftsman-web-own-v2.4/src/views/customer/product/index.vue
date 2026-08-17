<template>
  <div class="divBox">
    <el-card :body-style="{ padding: '20px 20px 20px 20px' }" class="normal-page el-card-flex">
      <oaFromBox
        :search="search"
        :title="$route.meta.title"
        :total="total"
        :treeData="treeData"
        :dropdownList="dropdownList"
        :viewSearch="viewSearch"
        :category="keyword"
      :timeSearchObj="timeSearchObj"
      :btnText="$('ui.customerProductIndexAddProduct')"
        ref="fromBox"
        @addDataFn="addDataFn"
        @dropdownFn="dropdownFn"
        @confirmData="confirmData"
      ></oaFromBox>

      <customizeTable
          flexLayout
          ref="tableData"
          :keyword="keyword"
          :tableData="tableData"
          :where="where"
          :total="total"
          :loading="loading"
          @handleCheck="openDetails"
          @getSearch="getSearch"
          @handleSelectionChange="handleSelectionChange"
          @getTableData="getTableData"
        >
          <template #options="{ data }">
            <el-button type="text" @click="openDetails(data)">{{ $("ui.layoutNoticeNoticeListView") }}</el-button>
            <el-button type="text" @click="handleEdit(data)">{{ $("ui.formCommonOaLogEdit") }}</el-button>
            <el-button type="text" @click="handleDelete(data)">{{ $("ui.chatIndexDelete") }}</el-button>
          </template>
      </customizeTable>
    </el-card>
    <!-- 详情 -->
    <detailsDrawer ref="details" :formData="detailsFromData"></detailsDrawer>
  </div>
</template>
<script>
import { $ } from '@/lang'
import { roterPre } from '@/settings'
import { productListApi, productDelApi } from '@/api/client'
import { DRAWER_SIZE } from '@/constants/popupSize'
export default {
  name: 'product',
  components: {
    customizeTable: () => import('../components/customizeTable'),
    detailsDrawer: () => import('./details'),
    oaFromBox: () => import('@/components/common/oaFromBox')
  },

  data() {
    return {
      ids: [],
      tableData: [],
      where: {
        page: 1,
        limit: 15,
        types: 'product'
      },
      dropdownList: [
        { label: $('ui.developModuleTableStyleFilterSettings'), value: 1 },
        { label: $('ui.developModuleTableStyleColumnDisplaySettings'), value: 2 },
        { label: $('ui.customerDictOptionSettingDictionaryOptionSettings'), value: 3 },
      ],
      timeSearchObj: {},
      keyword: 'product',
      total: 0,
      loading: false,
      detailsFromData: {},
      search: [],
      treeData: [
        {
          label: $('finance.all'),
          id: ''
        }
      ],
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
          // 字典选项设置
          this.$router.push({ path: `${roterPre}/customer/product/dictSetting` })
          break
      }
    },

    //添加商品
    addDataFn() {
      this.$router.push({
        path: `${roterPre}/customer/product/addProduct`
      })
    },
    // 编辑商品
    handleEdit(item) {
      this.$router.push({
        path: `${roterPre}/customer/product/addProduct`,
        query: { id: item.id }
      })
    },

    async getTableData() {
      if (this.loading) return
      this.loading = true
      const res = await productListApi(this.where)
      this.tableData = res.data.list
      this.total = res.data.count
      this.loading = false
    },

    // 查看
    async openDetails(item) {
      this.detailsFromData = {
        title: $('legacyScript.viewProduct'),
        width: DRAWER_SIZE.LG,
        data: item,
        types: this.types
      }

      this.$refs.details.openBox(item.id, this.types)
    },
    handleSelectionChange(ids) {
      this.ids = ids
    },
    confirmData(data) {
      if (data == 'reset') {
        this.where = {
          page: 1,
          limit: 15,
          view_search: this.where.view_search,
          types: this.keyword
        }
        this.labelText = ''
        this.getTableData('')
      } else {
        this.where = {
          page: 1,
          limit: 15,
          types: 'product'
        }

        for (let key in data) {
          this.where[key] = data[key]
        }

        setTimeout(() => {
          this.getTableData()
        }, 100)
      }
    },
    // 删除
    async handleDelete(item) {
      await this.$modalSure('确定删除当前产品')
      await productDelApi(item.id)
      if (this.where.page > 1 && this.tableData.length <= 1) {
        this.where.page--
      }
      await this.getTableData()
    },

    handleClose() {
      this.dialogVisible = false
    }
  }
}
</script>

<style lang="scss" scoped></style>
