<template>
  <el-form-item :label="$('designer.setting.maxValue')">
    <el-input-number v-model="maxValue" class="hide-spin-button" style="width: 100%"></el-input-number>
  </el-form-item>
</template>

<script>
  export default {
    name: "max-editor",
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
    computed: {
      maxValue: {
        get() {
          return this.optionModel['max']
        },

        set(newValue) {
          if (!newValue || isNaN(newValue)) {
            this.optionModel.max = null
          } else {
            this.optionModel.max = Number(newValue)
          }
        }
      },

    }
  }
</script>

<style scoped>

</style>