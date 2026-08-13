<template>
  <el-form-item :label="$('designer.setting.defaultValue')">
    <!-- 日期选择器 -->

    <el-radio-group v-model="optionModel.defaultType">
      <el-radio :label="1">{{ $("ui.hrAttendanceSettingAddConentToday") }}</el-radio>
      <el-radio :label="2">{{ $("hr.placeholder4") }}</el-radio>
    </el-radio-group>

    <el-date-picker
      v-if="optionModel.defaultType == 2"
      v-model="optionModel.defaultValue"
      @change="emitDefaultValueChange"
      :format="optionModel.format"
      :value-format="optionModel.valueFormat"
      type="date"
      :placeholder='$("hr.placeholder4")'
      class="mt14"
      style="width: 100%"
    >
    </el-date-picker>
    <!-- <el-date-picker
      :type="optionModel.type"
      v-model="optionModel.defaultValue"
      @change="emitDefaultValueChange"
      :format="optionModel.format"
      :value-format="optionModel.valueFormat"
      style="width: 100%"
    >
    </el-date-picker> -->
  </el-form-item>
</template>

<script>
import propertyMixin from '@/components/form-designer/setting-panel/property-editor/propertyMixin'

export default {
  name: 'date-picker-defaultValue-editor',
  mixins: [propertyMixin],
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>

<style scoped lang="scss">
::v-deep .el-radio {
  font-weight: 400;
  font-size: 13px;
}
</style>