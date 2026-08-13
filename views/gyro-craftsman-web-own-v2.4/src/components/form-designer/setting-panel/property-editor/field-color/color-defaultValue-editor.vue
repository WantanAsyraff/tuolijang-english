<template>
  <el-form-item :label="$('designer.setting.defaultValue')">
    <el-color-picker v-model="optionModel.defaultValue" @change="emitDefaultValueChange">
    </el-color-picker>
  </el-form-item>
</template>

<script>
  import propertyMixin from "@/components/form-designer/setting-panel/property-editor/propertyMixin"

  export default {
    name: "color-defaultValue-editor",
    mixins: [propertyMixin],
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>