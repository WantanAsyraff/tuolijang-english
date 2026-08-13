<template>
	<el-form-item :label='$("legacy.ea333f3119567b1f")'>
		<el-switch v-model="optionModel.showHeader"></el-switch>
	</el-form-item>
</template>

<script>
export default {
	name: "showHeader-editor",
	props: {
		designer: Object,
		selectedWidget: Object,
		optionModel: Object,
	},

}
</script>

<style scoped>

</style>
