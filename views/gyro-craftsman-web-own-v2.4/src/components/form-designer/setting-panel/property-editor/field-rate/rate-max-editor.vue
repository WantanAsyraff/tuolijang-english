<template>
  <el-form-item :label="$('designer.setting.maxStars')">
    <el-input-number v-model="optionModel.max" :min="1" :max="10"
                     class="hide-spin-button" style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "rate-max-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },

  }
</script>

<style scoped>

</style>