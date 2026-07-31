<template>
	<view class="nav">
		<view class="date-list">
			<view class="date">
				<view class="time">{{ yearValue ? yearValue : nowYear }}{{ $t('ui.examineFormTimeFromPickerYear') }}{{monthValue ? monthValue : nowMonth }}{{ $t('ui.examineFormTimeFromPickerMonth') }}</view>
				<view class="jump" @click="jump">
					{{ $t('ui.attendanceUserAttendanceMyMonthlyReport') }}
					<text class="iconfont icon-jinru-copy"></text>
				</view>
			</view>
			<calendar :dateList="dateList" @getMonthReport="getMonthReport" @enter="changeDate"
				@changeMonth="changeMonth"></calendar>
		</view>
		<view class="notice">
			<text v-if="isWhite"> {{ $t('ui.attendanceUserAttendanceAllowlist') }} </text>
			<text v-else-if="noTing"> {{ $t('ui.attendanceUserAttendanceBreak') }} </text>
			<text v-else>{{shiftName}} {{ $t('ui.attendanceUserAttendanceHours') }}{{ formatTime(workHouse) }})</text>
			<text class="iconfont icon-jinru-copy" v-if="!noTing" @click="jumpRule"></text>
		</view>
		<view class="attendance-card card" v-if="!loading && !noTing">
			<view v-for="(item, index) in basicData" :key="index">
				<view class="stage mb10">
					<view class="time">{{ item.work_hours }}</view>
					<view class="status">
						<view class="status-time">
							<view class="title">
								<view>{{ $t('ui.attendanceUserAttendanceClockIn') }} <text v-if="item.on">{{ item.on.clock_time }}</text></view>
								<view v-if="item.on && isLackCardStatus(item.on.status)" class="tag lack">{{ $t('ui.attendanceUserAttendanceMissingClockIn') }}
								</view>
								<view v-if="item.on && item.on.is_external == 1" class="tag out">{{ $t('ui.attendanceUserAttendanceOffSite') }}</view>
								<view v-if="item.on && item.on.status == 2" class="tag be-late">{{ $t('ui.attendanceUserAttendanceLate') }}</view>
								<view v-if="item.on && item.on.location_status == 2" class="tag be-add">{{ $t('ui.attendanceUserAttendanceLocationException') }}</view>
							</view>
							<view
								v-if="((item.on && item.on.status > 1) || (item.on && item.on.is_external == 2)) && (index==0 ? isAfterOffHours1 : isAfterOffHours2)"
								class="renew" @click="applyModal()">{{ $t('ui.attendanceUserAttendanceExceptionHandling') }}</view>
						</view>
						<view v-if="item.on" class="address"><text v-if="item.on.address"
								class="iconfont icon-kaoqin-dingwei"></text>{{ item.on.address }}</view>
					</view>
				</view>
				<view class="stage content-box mb10">
					<view class="line"></view>
					<view class="content">
						<view class="adr" v-if="item.on">
							<text v-if="item.on.remark" class="iconfont icon-kaoqin-beizhu1"></text>
							{{ item.on.remark }}
						</view>
					</view>
				</view>
				<view class="stage mb10">
					<view class="time">{{ item.off_hours }}</view>
					<view class="status">
						<view class="status-time">
							<view class="title">{{ $t('ui.attendanceUserAttendanceClockOut') }} <text v-if="item.off">{{ item.off.clock_time || "" }}</text>
								<view v-if="item.off && isLackCardStatus(item.off.status)"
									class="tag lack">{{ $t('ui.attendanceUserAttendanceMissingClockIn') }}</view>
								<view v-if="item.off && item.off.is_external == 1" class="tag out">{{ $t('ui.attendanceUserAttendanceOffSite') }}</view>
								<view v-if="item.off && item.off.status == 2" class="tag be-late">{{ $t('ui.attendanceUserAttendanceLate') }}</view>
								<view v-if="item.off && item.off.status == 4"
									class="tag be-late">{{ $t('ui.attendanceUserAttendanceEarlyLeave') }}</view>
								<view v-if="item.off && item.off.location_status == 2" class="tag be-add">{{ $t('ui.attendanceUserAttendanceLocationException') }}</view>
							</view>
							<view
								v-if="((item.off && item.off.status > 1) || (item.off && item.off.is_external) == 2) && (index==0 ? isAfterOffHours1 : isAfterOffHours2)"
								class="renew" @click="applyModal">{{ $t('ui.attendanceUserAttendanceExceptionHandling') }}</view>
						</view>
						<view class="address mt10" v-if="item.off && item.off.address"><text
								class="iconfont icon-kaoqin-dingwei"></text>
							{{ item.off.address }}
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="rest card" v-if="!loading && noTing">
			<image src="/static/image/attendance/rest.png" alt="" srcset="" />
			<view>{{ $t('ui.attendanceUserAttendanceTodayIsARestDayNoClockInIs') }}</view>
		</view>
		<!-- 申请单 -->
		<uni-popup ref="refsMenuPopup" type="bottom" background-color="#fff" :is-mask-click="false">
			<applyForMenuModal :dateVal="nowDate" @cancel="applyCancel" @selectType="selectType"></applyForMenuModal>
		</uni-popup>
	</view>
</template>

<script setup>
	import {
		ref,
		reactive,
		toRefs,
		onMounted
	} from "vue";
	import progressBox from "./progress.vue";
	import cardList from "./cardList.vue";
	import calendar from "./calendar.vue";
	import {
		monthReport,
		clockInfo,
		attendanceBasic
	} from "@/api/attendance";
	import {
		formatTime
	} from "@/utils/helper";
	import applyForMenuModal from "../components/applyForMenuModal.vue";
	import { useStore } from 'vuex';
	const store = useStore();

	const dateList = ref([]);
	const basicData = ref([]);
	const shiftName = ref('');
	const recordData = ref([
		[]
	]);
	const workHouse = ref();
	const loading = ref(true);
	const noTing = ref(false);
	const isWhite = ref(false);
	const userIdByOptions = ref(0);
	const userId = computed(() => {
		return userIdByOptions.value || store.state.app.userInfo.id;
	});
	onLoad((options) => {
		if (options.user_id) {
			userIdByOptions.value = options.user_id;
		}
	});
	onMounted(() => {
		getMonthReport();
		getBasic();
	});
	let monthTime = ref(`${new Date().getFullYear()}-${new Date().getMonth() + 1}`);
	let nowYear = ref(new Date().getFullYear());
	let nowMonth = ref(new Date().getMonth() + 1);
	let dateVal = ref('');
	let nowDate = ref(
		`${new Date().getFullYear()}-${
    new Date().getMonth() + 1
  }-${new Date().getDate()}`
	);
	let isAfterOffHours1 = ref(false);
	let isAfterOffHours2 = ref(false);
	let yearValue = ref();
	let monthValue = ref();

	const changeMonth = (year, month) => {
		yearValue.value = year;
		monthValue.value = month;
		nowYear.value = year;
		nowMonth.value = month;
		nowDate.value = `${year}-${month}-${getAvailableDay(year, month)}`;
		getBasic();
	}

	const getAvailableDay = (year, month) => {
		const targetDay = new Date(nowDate.value).getDate() || new Date().getDate();
		const monthLastDay = new Date(year, month, 0).getDate();
		return Math.min(targetDay, monthLastDay);
	};
	const isLackCardStatus = (status) => {
		return [5, 6, 7].includes(Number(status));
	};
	const normalizeShiftInfo = (shift) => {
		if (shift && typeof shift === "object" && ("now" in shift || "prev" in shift || "next" in shift)) {
			const activeShift = shift.now || shift.prev || shift.next || {};
			const shiftData = activeShift.shift_data || {};
			return {
				hasShift: !!activeShift.shift_data,
				name: shiftData.name || "默认班次",
				number: Number(shiftData.number) || 0,
				rules: Array.isArray(shiftData.rules) ? shiftData.rules : [],
			};
		}

		return {
			hasShift: !!shift,
			name: shift?.name || "默认班次",
			number: Number(shift?.number) || 0,
			rules: Array.isArray(shift?.rules) ? shift.rules : [],
		};
	};
	const applyRecordsToBasicData = (records) => {
		if (!basicData.value.length) {
			basicData.value = records.length > 2 ? [
				{
					work_hours: "上班",
					off_hours: "下班"
				},
				{
					work_hours: "上班",
					off_hours: "下班"
				},
			] : [
				{
					work_hours: "上班",
					off_hours: "下班"
				}
			];
		}

		basicData.value.forEach((item) => {
			item.on = undefined;
			item.off = undefined;
		});

		records.forEach((record, index) => {
			const recordNumber = Number(record?.number);
			const slotNumber = Number.isNaN(recordNumber) ? index : recordNumber;
			const ruleIndex = Math.floor(slotNumber / 2);
			if (!basicData.value[ruleIndex]) {
				return;
			}

			if (slotNumber % 2 === 0) {
				basicData.value[ruleIndex].on = record;
			} else {
				basicData.value[ruleIndex].off = record;
			}
		});
	};
	const getMonthReport = (val) => {
		let data = {
			date: val ? val : monthTime.value,
			type: 1,
			user_id: userId.value,
		};
		monthReport(data).then((res) => {
			dateList.value = res.data;
		});
	};
	const changeDate = (date, d) => {
		nowDate.value = `${d.fullYear}-${d.month}-${d.day}`;
		getBasic();
	};
	const getBasic = () => {
		loading.value = true;
		noTing.value = false;
		isWhite.value = false;
		let data = {
			date: nowDate.value,
			user_id: userId.value,
		};
		attendanceBasic(data).then((res) => {
			const shiftInfo = normalizeShiftInfo(res.data.shift);
			basicData.value = shiftInfo.rules;
			shiftName.value = shiftInfo.name;
			const currentTime = new Date();
			let offHoursString1 = '';
			let offHoursString2 = '';
			if (shiftInfo.number && shiftInfo.rules.length) {
				offHoursString1 = shiftInfo.rules[0].off_hours || '';
				if (shiftInfo.number == 2 && shiftInfo.rules[1]) {
					offHoursString2 = shiftInfo.rules[1].off_hours || '';
				}
			}
			const offHoursArray1 = offHoursString1.split(":");
			const offHoursArray2 = offHoursString2.split(":");
			const offHours1 = new Date();
			const offHours2 = new Date();
			offHours1.setHours(parseInt(offHoursArray1[0], 10));
			offHours1.setMinutes(parseInt(offHoursArray1[1], 10));
			offHours1.setSeconds(0);
			offHours2.setHours(parseInt(offHoursArray2[0], 10));
			offHours2.setMinutes(parseInt(offHoursArray2[1], 10));
			offHours2.setSeconds(0);
			let selectedDate = new Date(nowDate.value).getDate()
			let currentDate = currentTime.getDate()
			if(currentDate > selectedDate) {
				isAfterOffHours1.value = true
			} else {
				isAfterOffHours1.value = currentTime > offHours1;
				isAfterOffHours2.value = currentTime > offHours2;
			}
			if (res.data.whitelist) {
				isWhite.value = true
			} else if (!shiftInfo.hasShift) {
				noTing.value = true
			} else {
				noTing.value = false
				isWhite.value = false
			}
			getClockInfo();
		});
	};
	const getClockInfo = () => {
		let data = {
			date: nowDate.value,
			user_id: userId.value,
		};
		clockInfo(data).then((res) => {
			recordData.value = Array.isArray(res.data.list) ? res.data.list : [];
			if (basicData.value.length || recordData.value.length) {
				applyRecordsToBasicData(recordData.value);
			} else {
				noTing.value = true;
			}

			workHouse.value = res.data.work_hours;
			loading.value = false;
		});
	};
	const jump = () => {
		uni.navigateTo({
			url: `/pages/attendance/personalReport?user_id=${userId.value || ""
    }&yearValue=${yearValue.value || nowYear.value}&monthValue=${monthValue.value || nowMonth.value}`,
		});
	};
	const jumpRule = () => {
		uni.navigateTo({
			url: `/pages/attendance/rules?user_id=${userId.value || 0}&date=${
      nowDate.value
    }`,
		});
	};
	// 异常处理 申请
	const refsMenuPopup = ref(null); // 申请
	const applyModal = () => {
		refsMenuPopup.value.open();
	};
	const applyCancel = () => {
		refsMenuPopup.value.close();
	}
	const selectType = () => {
		refsMenuPopup.value.close();
	};
</script>

<style lang="scss" scoped>
	.date-list {
		background-color: #308bf8;
		padding: 30rpx;

		.date {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 30rpx;

			.time {
				font-size: 28rpx;
				font-weight: 400;
				color: #ffffff;
			}

			.jump {
				font-size: 24rpx;
				font-weight: 400;
				color: #ffffff;
				line-height: 24rpx;
				background: rgba(255, 255, 255, 0.2);
				border-radius: 22rpx 22rpx 22rpx 22rpx;
				padding: 10rpx 18rpx;
				display: flex;
				align-items: center;

				.icon-jinru-copy {
					font-size: 20rpx;
					line-height: 24rpx;
					transform: scale(0.8);
				}
			}
		}
	}

	.notice {
		display: flex;
		align-items: center;
		justify-content: space-between;
		color: #606266;
		font-size: 24rpx;
		font-weight: 400;
		line-height: 36rpx;
		padding: 30rpx 10rpx 30rpx 42rpx;

		.icon-jinru-copy {
			font-size: 20rpx;
			margin-right: 12rpx;
		}
	}

	.card.attendance-card.onthing {
		color: #c0c4cc !important;
	}

	.card.rest {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 26rpx;
		font-weight: 400;
		color: #909399;
		line-height: 32rpx;

		img {
			width: 400rpx;
			height: 300rpx;
			margin-bottom: 28rpx;
		}
	}

	.card.attendance-card {
		padding: 40rpx 24rpx 38rpx 24rpx;

		.stage {
			display: flex;
			align-items: center;

			.time {
				font-size: 32rpx;
				font-weight: 500;
				color: #303133;
				line-height: 32rpx;
				margin-right: 24rpx;
				width: 88rpx;
				text-align: center;
			}

			.status {
				flex: 1;

				.status-time {
					display: flex;
					align-items: center;
					justify-content: space-between;

					.title {
						font-size: 30rpx;
						font-weight: 500;
						color: #303133;
						display: flex;
						align-items: center;

						.tag {
							font-size: 20rpx;
							font-weight: 400;
							line-height: 20rpx;
							padding: 6rpx;
							border-radius: 2px;
							opacity: 1;
							margin-left: 10rpx;
							transform: scale(0.9);
						}

						.lack {
							color: #ed4014;
							border: 1rpx solid #ed4014;
						}

						.out {
							color: #19be6b;
							border: 1rpx solid #19be6b;
						}

						.be-late {
							color: #ff9900;
							border: 1rpx solid #ff9900;
						}

						.be-add {
							color: #19be6b;
							border: 1rpx solid #19be6b;
						}
					}

					.renew {
						font-size: 24rpx;
						font-weight: 400;
						color: #308bf8;
						line-height: 24rpx;
					}
				}

				.address {
					font-size: 24rpx;
					font-weight: 400;
					color: #909399;
					line-height: 26rpx;
					margin-top: 16rpx;

					.icon-kaoqin-dingwei {
						font-size: 24rpx;
						margin-right: 2rpx;
					}
				}
			}

			.line {
				width: 4rpx;
				background-color: #eeeeee;
			}

			.none {
				display: none;
			}

			.content {
				flex: 1;
				width: auto;
				min-height: 74rpx;
				padding-left: 62rpx;
			}
		}

		.content-box {
			align-items: stretch;
			padding-left: 38rpx;

			.content {
				.adr {
					font-size: 24rpx;
					font-weight: 400;
					color: #909399;
					line-height: 24rpx;

					.icon-kaoqin-beizhu1 {
						font-size: 20rpx;
					}
				}

				.clock {
					margin-left: -104rpx;
					padding: 82rpx 0 60rpx;
				}
			}
		}

		.clock-end {
			padding: 82rpx 0 60rpx;
		}
	}

	.card {
		background-color: #fff;
		padding: 28rpx 24rpx;
		border-radius: 12rpx;
		margin: 0 20rpx;
	}
</style>
