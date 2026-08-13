<template>
  <el-form-item :label="$('designer.setting.buttonText')">
    <el-input type="text" v-model="optionModel.buttonText"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "buttonText-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>