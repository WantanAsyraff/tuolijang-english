<template>
	<view>

	
	<view class="contract-tab-info">
		<template v-if="contractData.length > 0">
			<view class="contract-info-item" v-for="(item, index) in contractData" :key="index"
				@click="examineList(item)">
				<view class="contract-header">
					<view class="contract-title">{{ item.doc_name }}</view>
					<view v-if="item.status" class="status-badge" :style="{
						color: statusList[item.status].color ? statusList[item.status].color : '#1890ff',
						background: statusList[item.status].color
							? getColor(statusList[item.status].color, '0.1')
							: getColor('#1890ff', '0.1')
					}">{{ statusList[item.status].name }}</view>
				</view>
				<view class="contract-body">
					<uni-row class="contract-item" style="margin-bottom: 8px;">
						<uni-col :span="5" class="left">合同编号</uni-col>
						<uni-col :span="19">{{ item.doc_no||'--' }}</uni-col>
					</uni-row>
					<uni-row class="contract-item" style="margin-bottom: 8px;">
						<uni-col :span="5" class="left">合同金额</uni-col>
						<uni-col :span="19">{{ getContractPrice(item) }}</uni-col>
					</uni-row>
					<uni-row class="contract-item" style="margin-bottom: 8px;">
						<uni-col :span="5" class="left">签约方式</uni-col>
						<uni-col :span="19">{{ item.sign_type == 1 ? '线下签约' : '电子签' }}</uni-col>
					</uni-row>
					<uni-row class="contract-item" style="margin-bottom: 8px;">
						<uni-col :span="5" class="left">起止时间</uni-col>
						<uni-col :span="19">{{ item.start_date }}-{{ item.end_date }}</uni-col>
					</uni-row>
				</view>
			</view>
		</template>
		<empty v-else :index="9" :title="emptyTitle" style="height: 950rpx;"></empty>
			
	</view>
	<view class="footer-text" v-if="contractData.length > 0 && count == contractData.length">-没有更多了-</view>
	</view>

</template>

<script setup>
import empty from "@/components/empty/index.vue";
import { uploadImage, formatBytes } from "@/utils/file";
import { getColor } from "@/utils/helper";
import deanPopover from "@/components/deanPopover/index.vue";
import { followDeleteApi } from "@/api/customer";
import message from "@/utils/message";
import { clickNavigateTo } from "@/utils/helper";
import { reactive, toRefs } from "vue";
import followRecord from "./followRecord.vue";
const props = defineProps({
	contractData: {
		type: Array,
		default: () => {
			return [];
		}
	},
	count: {
		type: Number,
		default: 0
	},
	emptyTitle: {
		type: String,
		default: "暂无合同，快去添加吧！"
	},
});
const emit = defineEmits(["editFollow"]);

const { contractData, emptyTitle } = toRefs(props);
const statusList = ref({
	'-1': {
		name: '审批驳回',
		color: '#ED4014',
	},
	'0': {
		name: '待处理',
		color: '#FFC107',
	},
	'1': {
		name: '待审核',
		color: '#409EFF',
	},
	'2': {
		name: '待签约',
		color: '#19BE6B',
	},
	'3': {
		name: '已签约',
		color: '#409EFF',
	},
	'4': {
		name: '已拒绝',
		color: '#909399',
	},
	'5': {
		name: '已过期',
		color: '#909399',
	},
	'6': {
		name: '已撤销',
		color: '#909399',
	},
}
);

const examineList = (item) => {
	clickNavigateTo(
		`/pages/customer/signing/details?id=${item.id}`,
	);
};

const getContractPrice = (item) => {
	const price = item.contract_price ?? item.total_amount ?? item.price ?? item.amount
	if (price !== undefined && price !== null && price !== '') return price
	const orders = item.orders || item.contracts || []
	if (Array.isArray(orders) && orders.length > 0) {
		return orders.reduce((sum, order) => sum + Number(order.contract_price || order.total_amount || 0), 0).toFixed(2)
	}
	return '--'
}



</script>

<style scoped lang="scss">
.contract-tab-info {
	background: #fff;
	/* 触发BFC（块级格式化上下文） */
	overflow: hidden;
	/* 或 auto, scroll */
	/* 或者 */
	display: flow-root;

	/* 现代解决方案 */
	.contract-info-item {
		padding: 30rpx 30rpx 14rpx;
		font-size: $uni-font-size-default;
		color: $uni-text-color;
		background: #fff;
		border-bottom: 1px solid #EEEEEE;
		// &:last-child {
		// 	border: none;
		// }
	}
}

.contract-header {
	display: flex;
	justify-content: space-between;

	.contract-title {
		font-weight: 500;
		font-size: 26rpx;
	}

	.status-badge {
		min-width: 88rpx;
		height: 42rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 4px;
		font-size: 24rpx;
		padding: 0 10rpx;
	}
}

.contract-body {
	margin-top: 22rpx;

	.contract-item {
		font-size: 26rpx;
		color: $uni-text-color;
		display: flex;
		align-items: center;
		.left {
			color: #606266;
		}

	}
}
</style>
