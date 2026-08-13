<template>
  <div>
    <el-form-item :label="$('designer.setting.widgetColumnWidth')" v-show="!!isSubFormChildWidget()">
      <el-input type="text" v-model="optionModel.columnWidth"></el-input>
    </el-form-item>
  </div>
</template>

<script>
  export default {
    name: "columnWidth-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
    inject: ['isSubFormChildWidget'],
  }
</script>

<style scoped>

</style>