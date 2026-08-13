<template>
  <el-form-item :label="$('designer.setting.fontSize')">
    <el-input v-model="optionModel.fontSize"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "fontSize-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>