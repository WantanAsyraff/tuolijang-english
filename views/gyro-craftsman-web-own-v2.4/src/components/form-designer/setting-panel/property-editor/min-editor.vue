<template>
  <el-form-item :label="$('designer.setting.minValue')">
    <el-input-number v-model="minValue" class="hide-spin-button" style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "min-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
    computed: {
      minValue: {
        get() {
          return this.optionModel['min']
        },

        set(newValue) {
          //if ((!newValue && (newValue !== 0)) || isNaN(newValue)) {
          if ((newValue === undefined) || (newValue === null) || isNaN(newValue)) {
            this.optionModel.min = null
          } else {
            this.optionModel.min = Number(newValue)
          }
        }
      },

    },
  }
</script>

<style scoped>

</style>