<template>
  <el-form-item :label="$('designer.setting.showWordLimit')">
    <el-switch v-model="optionModel.showWordLimit"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "showWordLimit-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>