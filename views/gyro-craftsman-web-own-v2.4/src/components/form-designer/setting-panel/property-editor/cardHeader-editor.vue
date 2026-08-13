<template>
  <el-form-item :label="$('designer.setting.cardHeader')">
    <el-switch v-model="optionModel.cardHeader"></el-switch>
  </el-form-item>
</template>

<script>
  export default {
    name: "cardHeader-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>