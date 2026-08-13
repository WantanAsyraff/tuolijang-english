<template>
  <el-form-item :label="$('designer.setting.textContent')">
    <el-input v-model="optionModel.textContent" type="textarea" :rows="3"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "textContent-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>