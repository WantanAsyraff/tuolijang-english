<template>
  <el-form-item v-if="!hasConfig('optionItems')" :label="$('designer.setting.defaultValue')">

    <template v-if="optionModel.type == 'date-picker' || optionModel.type == 'date-time-picker'">
      <div>sefdjn</div>
    </template>
    <el-input v-else type="text" v-model="optionModel.defaultValue" @change="emitDefaultValueChange"></el-input>
  </el-form-item>
</template>

<script>
import propertyMixin from '@/components/form-designer/setting-panel/property-editor/propertyMixin'

export default {
  name: 'defaultValue-editor',
  mixins: [propertyMixin],
  props: {
    designer: Object,
    selectedWidget: Object,
    optionModel: Object
  },
  data() {
    return {}
  }
}
</script>
