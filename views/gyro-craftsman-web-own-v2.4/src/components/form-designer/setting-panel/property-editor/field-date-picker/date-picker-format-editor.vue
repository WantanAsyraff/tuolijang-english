<template>
<el-form-item :label="$('designer.setting.format')">
  <el-select v-model="optionModel.format" filterable allow-create>
    <el-option label="YYYY-MM-dd" value="YYYY-MM-dd"></el-option>
    <el-option label="YYYY/MM/dd" value="YYYY/MM/dd"></el-option>
    <el-option :label="$('ui.formDesignerSettingPanelPropertyEditorFieldDatePickerDatePickerFormatEditorYyyyMmDd')" value="YYYY年MM月dd日"></el-option>
    <el-option label="YYYY-MM-dd HH:mm:ss" value="YYYY-MM-dd HH:mm:ss"></el-option>
    <el-option label="YYYY-MM-dd hh:mm:ss" value="YYYY-MM-dd hh:mm:ss"></el-option>
  </el-select>
</el-form-item>
</template>

<script>
export default {
  name: 'date-picker-format-editor',
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>

<style scoped></style>