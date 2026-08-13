<template>
  <el-form-item :label="$('designer.setting.multiple')">
    <el-switch v-model="optionModel.multiple" @change="onMultipleSelected"></el-switch>
  </el-form-item>
</template>

<script>
  import propertyMixin from "@/components/form-designer/setting-panel/property-editor/propertyMixin"

  export default {
    name: "multiple-editor",
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