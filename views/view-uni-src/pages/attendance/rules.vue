<template>
	<view class="main" v-if="attendanceData">
		<uni-nav-bar :fixed="true" background-color="#fff" :border="false" status-bar left-icon="left" :title="$t('ui.attendanceAttendanceUserCardAttendanceRules')"
			@clickLeft="back" />
		<view class="header mb10">
			<view class="user card">
				<view class="user-msg">
					<image class="avatar" :src="userInfo.avatar" mode=""></image>
					<view class="">
						<view class="name">{{ userInfo.real_name }}</view>
						<view class="position">{{ userInfo?.job?.name || "-" }}</view>
					</view>
				</view>
				<view class="rule"> </view>
			</view>
		</view>
		<view class="card">
			<view class="header">
				<view class="title"> {{ $t('ui.attendanceRulesAttendanceTime') }} </view>
				<view class="jump"> </view>
			</view>
			<view class="content">
				<view v-for="(item, index) in attendanceTimeList" :key="index">
					<view class="name">
						<view class="line"></view>
						<view class="att-name">{{ item.name }}</view>
					</view>
					<view v-for="(c, index) in item.rules" :key="index">
						<view class="time-range">
							<view class="att-name">{{ c.first_day_after ? $t('ui.attendanceRulesNextDay') : $t('ui.attendanceRulesToday')
                }}{{ c.work_hours }}</view>{{ $t('ui.attendanceRulesMon') }}
							<view class="att-name">{{ c.second_day_after ? $t('ui.attendanceRulesNextDay') : $t('ui.attendanceRulesToday')
                }}{{ c.off_hours }}</view>
						</view>
						<view class="text">{{ $t('ui.attendanceRulesArrivingMoreThan') }}{{ formatTime(c.late) }}{{ $t('ui.attendanceRulesLateIsConsideredLateLeaving') }}{{
                formatTime(c.early_leave)
              }}{{ $t('ui.attendanceRulesEarlyIsConsideredEarlyLeaveArrivingMoreThan') }}{{
                formatTime(c.extreme_late)
              }}{{ $t('ui.attendanceRulesLateIsConsideredSeverelyLate') }}</view>
						<view class="text">{{ $t('ui.attendanceRulesArrivingMoreThan') }}{{ formatTime(c.late_lack_card) }}{{ $t('ui.attendanceRulesLeaving') }}{{
                formatTime(c.early_lack_card)
              }}{{ $t('ui.attendanceRulesEarlyIsConsideredAHalfDayMissingClockRecord') }}</view>
						<view class="text">{{ $t('ui.attendanceRulesEarliestClockIn') }}{{ formatTime(c.early_card) }} {{ $t('ui.attendanceRulesBeforeStartLatestClockOut') }}
							{{ c.second_day_after ? $t('ui.attendanceRulesNextDay') : $t('ui.attendanceRulesToday')
              }}{{ $t('ui.attendanceRulesMayBeDelayedBy') }}{{ formatTime(c.delay_card) }}{{ $t('ui.attendanceRules') }}{{c.free_clock==1? $t('ui.attendanceRulesClockOutIsOptional') : ''}}
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="card">
			<view class="header">
				<view class="title">{{ $t('ui.attendanceRulesAttendanceArea') }}</view>
				<view class="jump"></view>
			</view>
			<view class="address" v-if="attendanceData.group?.is_map">
				<view class="range">{{ $t('ui.attendanceRulesOfficeLocation') }}{{ attendanceData.group.effective_range }}{{ $t('ui.attendanceRulesM') }}</view>
				<navigator class="adr"
					:url="`/pages/attendance/map?lat=${attendanceData.group.lat}&lng=${attendanceData.group.lng}&radius=${attendanceData.group.effective_range}`"
					open-type="navigate" hover-class="navigator-hover">
					<text class="iconfont icon-kaoqin-dingwei"></text>
					{{ attendanceData.group.location_name }}
				</navigator>
			</view>
			<view v-if="attendanceData.group?.is_wifi" class="address">
				<view class="range">{{ $t('ui.attendanceRulesWiFiInformation') }}</view>
				<view v-for="item in attendanceData.group.wifi" :key="item.id" class="wifi-list">
					<view class="wifi-name over-text">
						{{ $t('ui.attendanceRulesWiFiName') }}{{ item.name }}
					</view>
					<view class="wifi-mac over-text">
						{{ $t('ui.attendanceRulesMacAddress') }}{{ item.mac }}
					</view>
				</view>
			</view>
		</view>
		<view class="card">
			<view class="header">
				<view class="title"> {{ $t('ui.attendanceAttendanceUserCardAttendanceRules') }} </view>
				<view class="jump"> </view>
			</view>
			<view class="content">
				<view v-if="attendanceData.group && attendanceData.group.repair_allowed">
					<view class="time-range">
						<view class="att-name">{{ $t('ui.attendanceRulesClockCorrection') }}</view>
					</view>
					<view class="text">{{ $t('ui.attendanceRulesAllowClockCorrections') }}</view>
					<view class="text">{{ publishedBooksMessage }}{{ $t('ui.attendanceRulesAllowClockCorrections') }}</view>
					<view class="text" v-if="attendanceData.group.is_limit_time">
						{{ $t('ui.attendanceRulesCorrectionsCanBeRequestedForThePast') }}{{ attendanceData.group.limit_time }}{{ $t('ui.attendanceRulesDays') }}</view>
					<view class="text" v-if="attendanceData.group.is_limit_number">
						{{ $t('ui.attendanceRulesMonthlyCorrectionLimitPerPerson') }}{{ attendanceData.group.limit_number }}{{ $t('ui.attendanceRulesSecond') }}</view>
				</view>
				<view v-if="attendanceData.group && attendanceData.group.is_photo">
					<view class="time-range">
						<view class="att-name">{{ $t('ui.attendanceRulesPhotoRequiredForClockIn') }}</view>
					</view>
					<view class="text">{{ $t('ui.attendanceRulesEmployeesMustTakePhotosWhenClockingInAndOut') }}</view>
				</view>
				<view v-if="attendanceData.group && attendanceData.group.is_external">
					<view class="time-range">
						<view class="att-name">{{ $t('ui.attendanceRulesOffSiteClockIn') }}</view>
					</view>
					<view class="text">{{ $t('ui.attendanceRulesAllowOffSiteClockIn') }}</view>
					<view class="text" v-if="attendanceData.group.is_external_note">{{ $t('ui.attendanceRulesRemarksAreRequiredForOffSiteClockIns') }}</view>
					<view class="text" v-if="attendanceData.group.is_external_photo">{{ $t('ui.attendanceRulesPhotosAreRequiredForOffSiteClockIns') }}</view>
				</view>
			</view>
		</view>
		<view class="card" v-if="attendanceData && attendanceData.group && attendanceData.group.admins.length">
			<view class="header">
				<view class="title">{{ $t('ui.attendanceRulesAttendanceAdministrator') }}</view>
				<view class="jump"></view>
			</view>
			<view class="admin">
				<view class="tip">{{ $t('ui.attendanceRulesContactYourAttendanceGroupAdministratorIfYouHaveQuestions') }}</view>
				<view class="user">
					<view class="user-msg" v-for="item in attendanceData.group.admins" :key="item.id">
						<image class="avatar" :src="item.avatar" mode=""></image>
						<view class="">
							<view class="name">{{ item.name }}</view>
							<view class="position">{{ item.job?.name || $t('ui.attendanceRulesNoPosition') }}</view>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script setup>
	import {
		attendanceBasic,
		attendanceUserMsg
	} from "@/api/attendance";
	import {
		useStore
	} from "vuex";
	import {
		formatTime
	} from "@/utils/helper";
	const store = useStore();
	const userInfo = ref(store.state.app.userInfo);
	let attendanceTimeList = ref([]);
	let attendanceData = ref();
	const userId = ref(0);
	const date = ref();
	onLoad((options) => {
		if (options.user_id) {
			userId.value = options.user_id;
			date.value = options.date || '';
			getAttendanceUserMsg();
		}
		getAttendanceBasic();
	});
	const getAttendanceBasic = () => {
		let d = {
			user_id: userId.value,
		};
		date.value ? d.date = date.value : ''

		attendanceBasic(d).then((res) => {
			attendanceData.value = res.data;
			if (res.data.shift?.now) {
				attendanceTimeList.value.push(res.data.shift.now.shift_data);
			}
		});
	};
	const getAttendanceUserMsg = () => {
		attendanceUserMsg(userId.value).then((res) => {
			userInfo.value = res.data;
		});
	};
	const publishedBooksMessage = computed(() => {
		let text = [];
		const repairType = attendanceData.value.group.repair_type;
		if (repairType.length > 0) {
			if (repairType.includes(1)) {
				text.push("缺卡");
			}
			if (repairType.includes(2)) {
				text.push("迟到");
			}
			if (repairType.includes(3)) {
				text.push("早退");
			}
			if (repairType.includes(4)) {
				text.push("正常");
			}
		}
		return text.toString();
	});
	const back = () => {
		uni.navigateBack({
			delta: 1
		})
	}
	onBeforeUnmount(() => {});
</script>

<style lang="scss" scoped>
	.header {
		padding-top: 20rpx;

		.user {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin: 0 20rpx;
			padding: 30rpx 24rpx;

			.user-msg {
				display: flex;
				align-items: center;

				.avatar {
					width: 80rpx;
					height: 80rpx;
					border-radius: 8rpx;
					margin-right: 20rpx;
				}

				.name {
					font-size: 30rpx;
					font-weight: 500;
					color: #303133;
					line-height: 30rpx;
					margin-bottom: 18rpx;
				}

				.position {
					font-size: 24rpx;
					font-weight: 400;
					color: #909399;
					line-height: 24rpx;
				}
			}

			.rule {
				font-size: 12rpx;
				font-weight: 400;
				color: #308bf8;
				line-height: 12rpx;
			}
		}
	}

	.card {
		margin: 20rpx 20rpx 0rpx;
		background-color: #fff;
		border-radius: 12rpx;

		.header {
			display: flex;
			justify-content: space-between;
			padding: 30rpx 24rpx;
			border-bottom: 1rpx solid #ebeef5;

			.title {
				font-size: 30rpx;
				font-weight: 500;
				color: #303133;
				line-height: 30rpx;

				.gard {
					font-size: 26rpx;
					font-weight: 500;
					color: #909399;
					line-height: 30rpx;
				}
			}

			.jump {
				display: flex;
				align-items: center;
				font-size: 24rpx;
				font-family: PingFang SC-Regular, PingFang SC;
				font-weight: 400;
				color: #308bf8;
				line-height: 24rpx;

				.icon-jinru-copy {
					font-size: 20rpx;
					color: #c0c4cc;
					margin-left: 12rpx;
				}
			}
		}

		.address {
			padding: 30rpx 24rpx;

			.range {
				font-size: 28rpx;
				font-weight: 500;
				color: #303133;
				line-height: 28rpx;
				margin-bottom: 20rpx;
			}

			.adr {
				font-size: 26rpx;
				font-weight: 400;
				color: #308bf8;
				line-height: 26rpx;
				padding: 14rpx 18rpx;
				background: #f2f6fc;
				border-radius: 4rpx;
				width: max-content;

				.icon-kaoqin-dingwei {
					font-size: 20rpx;
				}
			}
		}

		.content {
			padding: 30rpx 24rpx;

			.name {
				display: flex;
				align-items: center;
				font-size: 28rpx;
				font-weight: 500;
				color: #303133;
				line-height: 28rpx;

				.line {
					width: 4rpx;
					height: 28rpx;
					background: #308bf8;
					margin-right: 16rpx;
				}
			}

			.time-range {
				display: flex;
				align-items: center;
				margin-top: 28rpx;
				font-size: 28rpx;
				font-weight: 500;
				color: #303133;
				line-height: 28rpx;
			}

			.text {
				margin-top: 30rpx;
				font-size: 28rpx;
				font-weight: 400;
				color: #606266;
			}
		}

		.admin {
			padding: 0rpx 24rpx 40rpx;

			.tip {
				font-size: 24rpx;
				font-weight: 500;
				color: #909399;
				line-height: 24rpx;
				margin-top: 36rpx;
			}

			.user {
				display: flex;
				flex-wrap: wrap;

				.user-msg {
					display: flex;
					align-items: center;
					width: 50%;
					margin-top: 30rpx;

					.avatar {
						width: 80rpx;
						height: 80rpx;
						border-radius: 8rpx;
						margin-right: 20rpx;
					}

					.name {
						font-size: 30rpx;
						font-weight: 500;
						color: #303133;
						line-height: 30rpx;
						margin-bottom: 18rpx;
					}

					.position {
						font-size: 24rpx;
						font-weight: 400;
						color: #909399;
						line-height: 24rpx;
					}
				}
			}
		}
	}

	.wifi-list {
		display: flex;
		font-size: 24rpx;
		color: #303133;
		line-height: 24rpx;
		margin-top: 20rpx;

		.wifi-name {
			flex: 1;
			min-width: 0rpx;
		}

		.wifi-mac {
			flex: 1;
			min-width: 0rpx;
		}
		
	}
</style>