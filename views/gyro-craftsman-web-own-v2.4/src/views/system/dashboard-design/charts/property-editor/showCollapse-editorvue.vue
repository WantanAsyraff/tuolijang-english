<template>
	<el-form-item :label='$("legacy.f8132c7ca733c805")' v-if="optionModel.showHeader">
		<el-switch v-model="optionModel.showCollapse"></el-switch>
	</el-form-item>
</template>

<script>
export default {
	name: "showCollapse-editor",
	props: {
		designer: Object,
		selectedWidget: Object,
		optionModel: Object,
	},

}
</script>

<style scoped>

</style>
