<template>
  <el-form-item :label="$('designer.setting.requiredHint')">
    <el-input type="text" v-model="optionModel.requiredHint"></el-input>
  </el-form-item>
</template>

<script>
  import propertyMixin from "@/components/form-designer/setting-panel/property-editor/propertyMixin"

  export default {
    name: "requiredHint-editor",
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