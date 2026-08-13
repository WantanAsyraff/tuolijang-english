<template>
  <el-form-item :label="$('designer.setting.prefixIcon')">
    <el-input type="text" v-model="optionModel.prefixIcon"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "prefixIcon-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>