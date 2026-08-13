<template>
	<el-form-item :label='$("legacy.39e0705286c07753")'>
		<el-switch v-model="optionModel.showIcon"></el-switch>
	</el-form-item>
</template>

<script>
export default {
	name: "showIcon-editor",
	props: {
		designer: Object,
		selectedWidget: Object,
		optionModel: Object,
	},

}
</script>

<style scoped>

</style>
