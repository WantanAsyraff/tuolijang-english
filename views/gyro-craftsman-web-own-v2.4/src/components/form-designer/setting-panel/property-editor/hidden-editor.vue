<template>
  <el-form-item :label="$('designer.setting.hidden')">
    <el-switch v-model="optionModel.hidden"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "hidden-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>