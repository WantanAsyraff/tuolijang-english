<template>
	<el-form-item :label='$("legacy.ddc135979a39b3ac")' v-if="optionModel.showHeader">
		<el-switch v-model="optionModel.showRefresh"></el-switch>
	</el-form-item>
</template>

<script>
export default {
	name: "showRefresh-editor",
	props: {
		designer: Object,
		selectedWidget: Object,
		optionModel: Object,
	},

}
</script>

<style scoped>

</style>
