<template>
  <el-form-item :label="$('designer.setting.maxLength')">
    <el-input type="number" @input.native="inputNumberHandler" class="hide-spin-button"
              min="1" v-model="maxLength" style="width: 100%"></el-input>
  </el-form-item>
</template>

<script>
  import propertyMixin from "@/components/form-designer/setting-panel/property-editor/propertyMixin"

  export default {
    name: "maxLength-editor",
    mixins: [propertyMixin],
    props: {
      designer: Object,
      selectedWidget: Object,
      optionModel: Object,
    },
    computed: {
      maxLength: {
        get() {
          return this.optionModel['maxLength']
        },

        set(newValue) {
          if (!newValue || isNaN(newValue)) {
            this.optionModel.maxLength = null
          } else {
            this.optionModel.maxLength = Number(newValue)
          }
        }
      },
    }
  }
</script>

<style scoped>

</style>