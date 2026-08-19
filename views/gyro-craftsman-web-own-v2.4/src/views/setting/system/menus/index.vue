<template>
<div class="divBox">
  <div class="box-height">
    <el-card :body-style="{ padding: '20px 20px 0 20px' }" class="card-head normal-page" shadow="never">
      <oaFromBox
        :isTotal="false"
        :isViewSearch="false"
        :search="searchData"
        :sortSearch="false"
      :title="$($route.meta.title, $route.meta.title_en)"
      :btnText="$('ui.settingSystemMenusIndexAddMenu')"
        @addDataFn="addMenu"
        @confirmData="confirmData"
      ></oaFromBox>
      <div class="mb10"></div>
      <div class="table-box">
        <el-table
          :data="tableData"
          :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
          row-key="id"
          style="width: 100%"
        >
          <el-table-column :label="$('ui.settingSystemMenusIndexMenuName')" prop="menu_name">
            <template slot-scope="props"><span>{{ $(props.row.menu_name, props.row.menu_name_en) }}</span></template>
          </el-table-column>
          <el-table-column :label="$('ui.settingSystemMenusIndexMenuIcon')" prop="menu_name">
            <template slot-scope="props">
              <span class="icon iconfont" :class="props.row.icon" v-if="props.row.icon"></span>
              <span v-else>--</span>
            </template>
          </el-table-column>
          <el-table-column :label="$('ui.developViewManagementType')" prop="type" width="200">
            <template slot-scope="props">
              <el-tag v-if="props.row.type === 'A'" type="success">{{ $("ui.settingSystemMenusIndexApi") }}</el-tag>
              <el-tag v-if="props.row.type === 'M'" type="info">{{ $("ui.settingSystemMenusIndexMenu") }}</el-tag>
              <el-tag v-if="props.row.type === 'B'">{{ $("ui.settingSystemMenusIndexButton") }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column :label="$('ui.settingSystemMenusIndexDisplayStatus')" prop="is_show" width="150">
            <template slot-scope="props">
              <el-switch
                v-if="!['A', 'B'].includes(props.row.type)"
                v-model="props.row.is_show"
                :active-value="1"
                :inactive-value="0"
                :active-text="$('public.display')"
                :inactive-text="$('hr.hide')"
                @change="changeStatus(props.row)"
              >
              </el-switch>
            </template>
          </el-table-column>
          <el-table-column :label="$('ui.businessExamineIndexSort')" prop="sort" width="150"> </el-table-column>
          <el-table-column fixed="right" :label="$('ui.formDesignerFormWidgetContainerWidgetDetailsItemOperation')" prop="desc" width="150">
            <template slot-scope="scope">
              <el-button type="text" @click="editMenu(scope.row.id)">{{ $("ui.formCommonOaLogEdit") }}</el-button>
              <el-button type="text" @click="delMenu(scope.row.id)">{{ $("ui.chatIndexDelete") }}</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>
  </div>
  <rightDrawer ref="rightBox" :config="drawerConfig" @changge="getAllMenus"></rightDrawer>
</div>
</template>
<script>
import { $ } from '@/lang'
import rightDrawer from '@/components/setting/rightDrawer'
import oaFromBox from '@/components/common/oaFromBox'
import formCreate from '@form-create/element-ui'
import { menuListApi, menuDeleteitApi, menuShowApi } from '@/api/system'
import Tips from '@/utils/tips'

export default {
  name: 'list',
  components: { formCreate: formCreate.$form(), rightDrawer, oaFromBox },
  data() {
    return {
      formData: {
        menu_name: ''
      },
      tableData: [],
      drawerConfig: {
        title: $('layout.addMenu'),
        api: 'system/menus/create'
      },
      searchData: [
        {
          field_name: $('ui.settingSystemMenusIndexMenuName'),
          field_name_en: 'menu_name',
          form_value: 'input'
        }
      ]
    }
  },
  mounted() {
    this.getAllMenus()
  },
  methods: {
    async getAllMenus() {
      const result = await menuListApi(this.formData)
      this.tableData = result.data
    },
    // 添加菜单
    addMenu() {
      this.drawerConfig.title = this.$("legacy.7cde0b57ca9ed54a")
      this.drawerConfig.api = 'system/menus/create'
      this.$refs.rightBox.handelOpen()
    },
    // 编辑菜单
    async editMenu(id) {
      this.drawerConfig.title = this.$("legacy.daf5abc900170a67")
      this.drawerConfig.api = 'system/menus/' + id + '/edit'
      this.$refs.rightBox.handelOpen()
    },
    async delMenu(id) {
      await Tips.confirm({ message: $('legacyScript.deleteThisMenuThisActionCannotBeUndone') })
      await menuDeleteitApi(id)
      await this.getAllMenus()
    },
    async changeStatus(item) {
      await menuShowApi(item.id, { is_show: item.is_show })
    },
    search() {
      this.getAllMenus()
    },
    reset() {
      this.formData.menu_name = ''
      this.getAllMenus()
    },
    confirmData(data) {
      if (data == 'reset') {
        this.formData = { menu_name: '' }
      } else {
        this.formData = { ...this.formData, ...data }
      }
      this.getAllMenus()
    }
  }
}
</script>

<style scoped>
.icon {
  font-size: 18px;
  color: #606266 !important;
}
</style>
