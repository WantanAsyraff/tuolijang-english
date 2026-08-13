<template>
  <el-form-item :label="$('designer.setting.placeholder')">
    <el-input type="text" v-model="optionModel.placeholder"></el-input>
  </el-form-item>
</template>

<script>
  import propertyMixin from "@/components/form-designer/setting-panel/property-editor/propertyMixin"

  export default {
    name: "placeholder-editor",
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