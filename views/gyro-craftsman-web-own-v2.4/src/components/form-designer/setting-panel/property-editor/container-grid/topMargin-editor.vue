<template>
  <div>
    <el-form-item :label="$('designer.setting.topMargin')">
      <el-input-number v-model.number="optionModel.topMargin" :min="0" class="hide-spin-button"></el-input-number>
    </el-form-item>
  </div>
</template>

<script>
import propertyMixin from '@/components/form-designer/setting-panel/property-editor/propertyMixin'

export default {
  name: 'topMargin-editor',
  mixins: [propertyMixin],
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  }
}
</script>

<style scoped>
</style>