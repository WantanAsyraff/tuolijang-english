<template>
  <el-form-item :label="$('designer.setting.defaultValue')">
    <el-input-number
      v-model="optionModel.defaultValue"
      :min="0"
      :max="optionModel.max"
      style="width: 100%"
      @change="emitDefaultValueChange"
    >
    </el-input-number>
  </el-form-item>
</template>

<script>
import propertyMixin from '@/components/form-designer/setting-panel/property-editor/propertyMixin'

export default {
  name: 'rate-defaultValue-editor',
  mixins: [propertyMixin],
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>

<style scoped></style>