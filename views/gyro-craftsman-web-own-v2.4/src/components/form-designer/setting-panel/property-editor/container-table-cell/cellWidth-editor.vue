<template>
  <el-form-item :label="$('designer.setting.cellWidth')">
    <el-input type="text" v-model="optionModel.cellWidth"></el-input>
  </el-form-item>
</template>

<script>
  export default {
    name: "cellWidth-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
  }
</script>

<style scoped>

</style>