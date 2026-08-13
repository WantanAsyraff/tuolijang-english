<template>
  <el-form-item :label="$('designer.setting.defaultValue')">
    <el-switch
      v-model="optionModel.defaultValue"
      @change="emitDefaultValueChange"
      active-value="1"
      inactive-value="0"
    ></el-switch>
  </el-form-item>
</template>

<script>
import propertyMixin from '@/components/form-designer/setting-panel/property-editor/propertyMixin'

export default {
  name: 'switch-defaultValue-editor',
  mixins: [propertyMixin],
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>

<style scoped></style>