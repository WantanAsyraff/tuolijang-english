import { $ } from '@/lang'
<template>
<div>
  <oaDialog :fromData="fromData" ref="oaDialog" @submit="submit">
    <!-- 内容 -->
    <el-form :model="form" label-width="100px" :rules="rules">
      <el-form-item :label="$('ui.developModuleButtonDialogButtonPosition')">
        <el-radio-group v-model="form.position">
          <el-radio :label="1">{{ $("ui.developModuleButtonDialogInList") }}</el-radio>
          <el-radio :label="2">{{ $("ui.developModuleButtonDialogListHeader") }}</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item :label="$('ui.developModuleButtonDialogEnabledRules')">
        <el-button size="small"> {{ $("ui.developModuleButtonDialogSetEnablementRules") }} </el-button>
      </el-form-item>
      <el-form-item :label="$('ui.developModuleButtonDialogSelectForm')" prop="form">
        <el-select v-model="form.form" :placeholder="$('ui.developModuleButtonDialogSearchAndSelectAFormSingleSelection')" size="small" style="width: 100%">
          <el-option :label="$('ui.developModuleButtonDialogForm1')" value="1"></el-option>
          <el-option :label="$('ui.developModuleButtonDialogForm2')" value="2"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item :label="$('ui.developModuleButtonDialogDialogTitle')" prop="title">
        <el-input v-model="form.title" size="small" :placeholder="$('ui.developModuleButtonDialogPleaseEnterADialogTitle')"></el-input>
      </el-form-item>
      <el-form-item :label="$('ui.developModuleButtonDialogAction')">
        <el-radio-group v-model="form.action">
          <el-radio :label="1">{{ $("ui.businessHolidayTypeIndexAdd") }}</el-radio>
          <el-radio :label="2">{{ $("ui.formCommonOaLogEdit") }}</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item :label="$('ui.developModuleButtonDialogParameterSettings')">
        <el-table>
          <el-table-column :label="$('ui.formCommonOaTableSerialNumber')" prop="name" width="80"></el-table-column>
          <el-table-column :label="$('ui.developModuleButtonDialogParameterName')" prop="name"></el-table-column>
          <el-table-column :label="$('ui.developModuleButtonDialogParameterSource')" prop="value"></el-table-column>
        </el-table>
      </el-form-item>
    </el-form>
  </oaDialog>
</div>
</template>
<script>
export default {
  name: 'buttonDialog',
  components: {
    oaDialog: () => import('@/components/form-common/dialog-form')
  },
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      fromData: {
        width: '700px',
        title: $('legacyScript.buttonEventSettings'),
        btnText: '确定',
        labelWidth: '90px',
        type: 'slot'
      },
      form: {
        position: 1,
        form: '',
        title: ''
      },
      rules: {
        form: [{ required: true, message: $('legacyScript.pleaseSelectAForm'), trigger: 'blur' }],
        title: [{ required: true, message: $('ui.developModuleButtonDialogPleaseEnterADialogTitle'), trigger: 'blur' }]
      }
    }
  },
  mounted() {},
  methods: {
    openBox(data) {
      this.$refs.oaDialog.openBox()
    },
    submit() {}
  }
}
</script>
