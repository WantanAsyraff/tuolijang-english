<template>
<div class="main">
  <!-- <div class="title-16 mt20">属性信息</div> -->
  <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="90px" class="mt20">
    <el-form-item :label="$('ui.developCrudBasicSettingDisplayName')" prop="table_name">
      <el-input v-model="ruleForm.table_name" size="small"></el-input>
    </el-form-item>
    <el-form-item :label="$('ui.developCrudBasicSettingEntityName')" prop="table_name_en">
      <el-input disabled v-model="ruleForm.table_name_en" size="small"></el-input>
    </el-form-item>

    <el-form-item :label="$('ui.developCrudBasicSettingParentEntity')" prop="region" v-if="ruleForm.crud_id">
      <el-cascader
        disabled
        v-model="ruleForm.crud_id"
        :options="localizedMetadataOptions(options, 'table_name')"
        style="width: 100%"
        size="small"
        :show-all-levels="false"
        :props="{ checkStrictly: true, label: 'table_name', value: 'id', emitPath: false }"
      >
        <template slot-scope="{ node, data }">
          <span>{{ $(data.table_name) }}</span>
          <span> （{{ data.table_name_en }}）</span>
        </template>
      </el-cascader>
      <div class="tips">{{ $("ui.developCrudBasicSettingAfterLinkingAParentEntityThisEntityBecomesA") }}</div>
    </el-form-item>
    <el-form-item :label="$('ui.developCrudBasicSettingOperationLogs')">
      <el-switch
        v-model="ruleForm.show_log"
        active-value="1"
        inactive-value="0"
        :inactive-text="$('public.close')"
        :active-text="$('hr.open')"
        size="small"
      ></el-switch>
    </el-form-item>

    <el-form-item :label="$('ui.developCrudBasicSettingComments')">
      <div :class="ruleForm.show_comment == 1 ? 'flex-col-center' : ''">
        <el-switch
          v-model="ruleForm.show_comment"
          active-value="1"
          inactive-value="0"
          :inactive-text="$('public.close')"
          :active-text="$('hr.open')"
        ></el-switch>
        <el-input
          v-if="ruleForm.show_comment == 1"
          class="ml10"
          v-model="ruleForm.comment_title"
          maxlength="5"
          size="small"
          :placeholder="$('ui.developCrudBasicSettingRenameComments')"
        ></el-input>
      </div>
    </el-form-item>

    <el-form-item :label="$('ui.developCrudBasicSettingLinkedApplication')" prop="region">
      <el-select
        style="width: 100%"
        v-model="ruleForm.cate_ids"
        multiple
        filterable
        size="small"
        :placeholder="$('ui.developCrudBasicSettingSearchAndSelectApplicationsMultiple')"
      >
        <el-option v-for="(v, index) in cateOptions" :key="v.id" :label="$(v.name)" :value="v.id"> </el-option>
      </el-select>
    </el-form-item>
    <el-form-item :label="$('ui.developCrudBasicSettingParentMenu')" prop="region">
      <el-cascader
        v-model="ruleForm.path"
        :options="localizedMetadataOptions(menuList, 'menu_name')"
        :props="{ checkStrictly: true, label: 'menu_name', value: 'id', emitPath: false }"
        clearable
        style="width: 100%"
      ></el-cascader>
    </el-form-item>
    <el-form-item :label="$('ui.developCrudBasicSettingMenuIcon')" prop="region">
      <el-input
        :placeholder="$('ui.formCommonOaFormPleaseSelectAnIcon')"
        v-model="ruleForm.icon"
        readonly
        @click.native="showIconDialog = true"
        clearable
      >
        <i v-if="!ruleForm.icon" slot="suffix" class="el-icon-circle-plus-outline" style="cursor: pointer"></i>
        <i
          v-else
          slot="suffix"
          class="el-icon-circle-close"
          style="cursor: pointer"
          @click.stop="handleClearIcon"
        ></i>
      </el-input>
    </el-form-item>
    <el-form-item :label="$('ui.developCrudBasicSettingMobileIcon')" prop="region">
      <oa-systemImage ref="systemImageRef" @getImage="getImage"></oa-systemImage>
    </el-form-item>
    <el-form-item :label="$('ui.developCrudBasicSettingEntityDescription')" prop="region">
      <el-input
        type="textarea"
        v-model="ruleForm.info"
        :autosize="{ minRows: 4, maxRows: 8 }"
        size="small"
      ></el-input>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" size="small" :loading="loading" @click="submit">{{ $("ui.formDesignerFormWidgetFieldWidgetRichTextWidgetSave") }}</el-button>
    </el-form-item>
  </el-form>
  <!-- 选择图标 -->
  <el-dialog :title="$('ui.formCommonOaFormSelectMenuIcon')" :visible.sync="showIconDialog" :append-to-body="true" width="50%">
    <div class="icon-box">
      <select-icon ref="selectIconRef" :isEmit="true" @select="handleSelectIcon"></select-icon>
    </div>
  </el-dialog>
</div>
</template>
<script>
import oaSystemImage from '@/components/form-common/oa-systemImage'
import selectIcon from '@/components/form-common/select-icon'
import Commnt from '@/components/develop/commonData'
import { menuListApi } from '@/api/system'
import { getcrudCateListApi, databaseListApi, databasePutApi } from '@/api/develop'

export default {
  props: {
    infoData: {
      type: Object,
      default: () => {}
    }
  },
  components: {
    oaSystemImage,
    selectIcon
  },
  data() {
    return {
      ruleForm: Commnt.formDataInit,
      rules: Commnt.formRules,
      cateOptions: [],
      showIconDialog: false,
      options: [],
      menuList: [],
      loading: false
    }
  },
  created() {
    this.getList()
    this.getCateList()
    this.getMenuList()
  },
  watch: {
    infoData(val) {
      this.initForm(val)
    }
  },
  mounted() {
    this.initForm(this.infoData)
  },

  methods: {
    localizedMetadataOptions(options, labelKey) {
      return (options || []).map((item) => ({
        ...item,
        [labelKey]: this.$(item[labelKey]),
        children: item.children ? this.localizedMetadataOptions(item.children, labelKey) : item.children
      }))
    },
    initForm(val) {
      this.ruleForm.table_name = val.table_name
      this.ruleForm.table_name_en = val.table_name_en
      this.ruleForm.info = val.info
      this.ruleForm.crud_id = val.crud_id
      this.ruleForm.uni_img = val.uni_img
      this.$refs.systemImageRef.url = val.uni_img
      this.ruleForm.show_log = val.show_log + ''
      this.ruleForm.comment_name = val.comment_title || '评论'
      this.ruleForm.show_comment = val.show_comment + ''
      if (val.cate_ids && val.cate_ids.length > 0) {
        this.ruleForm.cate_ids = val.cate_ids.map(Number)
      } else {
        this.ruleForm.cate_ids = []
      }
    },
    submit() {
      this.$refs.ruleForm.validate((valid) => {
        if (valid) {
          this.loading = true
          databasePutApi(this.infoData.id, this.ruleForm).then((res) => {
            this.loading = false
          })
        }
      })
    },
    handleSelectIcon(data) {
      this.ruleForm.icon = data
      this.showIconDialog = false
    },
    getImage(e) {
      this.ruleForm.uni_img = e.url
    },
    async getMenuList() {
      let obj = {
        menu_name: '顶级菜单',
        menu_name_en: 'Top-level menu',
        id: 0
      }
      const result = await menuListApi()
      this.menuList = result.data
      this.menuList.unshift(obj)
    },
    handleClearIcon() {
      this.$set(this.ruleForm, 'icon', '')
    },
    async getList() {
      let obj = {
        cate_id: ''
      }
      const data = await databaseListApi(obj)
      this.options = data.data.list
    },
    async getCateList() {
      const result = await getcrudCateListApi()
      this.cateOptions = result.data.list
    }
  }
}
</script>
<style scoped lang="scss">
.main {
  width: 800px;
  margin: 0 auto;

  .title {
    font-size: 16px;
    font-weight: 500;
  }
}
.tips {
  font-size: 12px;
  color: #909399;
}
.flex-col-center {
  display: flex;
  align-items: center;
}
</style>
