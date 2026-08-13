<template>
  <el-form-item :label="$('designer.setting.plain')">
    <el-switch v-model="optionModel.plain"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "plain-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },

  }
</script>

<style scoped>

</style>