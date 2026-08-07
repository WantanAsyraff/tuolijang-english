import appI18n from '@/locale';
import { computed, ref, watch, type ComputedRef, type Ref } from "vue";
import { attendanceClockIn } from "@/api/attendance";
import type {
  AttendanceClockStage,
  AttendanceClockRecord,
  AttendanceCoordinate,
  AttendanceData,
  AttendanceGroup,
  AttendanceShiftRule,
  AttendanceWorkType,
  WifiInfo,
} from "./attendanceTypes";
import {
  createClockStages,
  createTimelineViewModel,
  getClockButtonState,
  getCurrentClockStage,
  getCurrentWorkType,
  getUpdateNumber,
} from "./attendanceViewModel";

/** 外勤/拍照打卡弹窗提交的数据。 */
interface ExternalFormData {
  /** 外勤备注。 */
  text?: string;
  /** 外勤或拍照打卡图片。 */
  imgs?: string[];
  /** 是否外勤打卡：0 范围内，1 外勤。 */
  is_external?: number;
}

/** 打卡流程 composable 的依赖项，由 index.vue 汇总数据层、定位层和 Wi-Fi 层后传入。 */
interface UseAttendanceClockFlowOptions {
  /** attendance/basic 顶层数据，用于判断是否有考勤组。 */
  attendanceData: Ref<AttendanceData | null>;
  /** 当前考勤组配置。 */
  group: ComputedRef<AttendanceGroup | null>;
  /** 已归一化的时间线规则。 */
  basicData: Ref<AttendanceShiftRule[]>;
  /** 已归一化的打卡记录。 */
  recordData: Ref<AttendanceClockRecord[]>;
  /** 当前可打卡状态。 */
  clockStatus: Ref<number>;
  /** 当前打卡类型，上班或下班，成功弹窗依赖它。 */
  onWork: Ref<AttendanceWorkType>;
  /** 是否白名单用户。 */
  isWhite: Ref<boolean>;
  /** 第一段下班时间是否已过。 */
  isAfterOffHours1: Ref<boolean>;
  /** 第二段下班时间是否已过。 */
  isAfterOffHours2: Ref<boolean>;
  /** 是否开启定位打卡。 */
  isLocationEnable: ComputedRef<boolean>;
  /** 是否开启 Wi-Fi 打卡。 */
  isWifiSignEnable: ComputedRef<boolean>;
  /** 当前 Wi-Fi 是否在公司 Wi-Fi 范围。 */
  isCompanyWifiRange: ComputedRef<boolean>;
  /** 当前定位是否在考勤范围。 */
  isLocationRange: Ref<boolean>;
  /** 当前定位坐标。 */
  nowXy: Ref<AttendanceCoordinate | undefined>;
  /** 当前定位反解析地址。 */
  address: Ref<string>;
  /** 当前 Wi-Fi 信息。 */
  wifiInfo: Ref<WifiInfo | null>;
  /** 打开外勤/拍照弹窗。 */
  openExternalPopup: () => void;
  /** 打开打卡成功弹窗。 */
  openSuccessPopup: () => void;
}

/**
 * 考勤首页打卡流程控制。
 *
 * 该 composable 负责把“当前可打卡节点”转换成按钮状态，并在点击后完成：
 * - 定位/Wi-Fi/外勤权限判断。
 * - 外勤或拍照弹窗调起。
 * - attendance/clock_in 参数组装。
 * - 成功弹窗状态同步。
 */
export function useAttendanceClockFlow(options: UseAttendanceClockFlowOptions) {
  /** 成功弹窗展示的实际打卡时间。 */
  const clockTime = ref("");
  /** 成功弹窗用来判断第几次打卡的序号。 */
  const signInType = ref(1);
  /** 外勤备注。 */
  const remark = ref("");
  /** 外勤/拍照图片。 */
  const imgs = ref<string[]>([]);
  /** 是否外勤打卡。 */
  const isExternal = ref(0);
  /** 当前提交的 update_number；普通打卡为空，更新打卡时为 0/1/2/3。 */
  const updateNumber = ref<number | "">("");
  /** 用户点击时锁定的目标节点，防止外勤弹窗期间响应式数据变化导致提交到错误节点。 */
  const clockTargetStage = ref<AttendanceClockStage | null>(null);

  /** 当前应该承载打卡按钮的节点。 */
  const currentClockStage = computed(() => getCurrentClockStage(options.basicData.value));
  /** 当前节点在可见节点中的顺序；旧版弹窗仍依赖该值。 */
  const recordLength = computed(() => currentClockStage.value?.sequence || options.recordData.value.length);
  /** 当前节点对应的记录。 */
  const currentRecord = computed(() => currentClockStage.value?.record || options.recordData.value[recordLength.value - 1] || {});
  /** 当前节点是上班卡还是下班卡。 */
  const currentWorkType = computed(() => currentClockStage.value?.workType || getCurrentWorkType(recordLength.value));
  /** 当前节点的打卡状态；新版取节点状态，旧版取全局 clockStatus。 */
  const currentClockStatus = computed(() => currentClockStage.value?.clockStatus ?? options.clockStatus.value);
  /** 是否属于考勤组。 */
  const isGroup = computed(() => !!options.group.value);
  /** 考勤组是否允许外勤打卡。 */
  const groupExternal = computed(() => (!options.isWhite.value && options.group.value ? options.group.value.is_external || 0 : 0));

  /** 定位或 Wi-Fi 任一命中，即认为在有效打卡范围。 */
  const isEffectiveRange = computed(() => options.isCompanyWifiRange.value || options.isLocationRange.value);

  /** 是否完全不允许打卡；定位、Wi-Fi、外勤三者都不可用时为 true。 */
  const notAllowSign = computed(() => {
    const permission1 = options.isLocationEnable.value && options.nowXy.value && options.isLocationRange.value;
    const permission2 = options.isWifiSignEnable.value && options.isCompanyWifiRange.value;
    const permission3 = options.group.value?.is_external === 1;

    return !(permission1 || permission2 || permission3);
  });

  /** 时间线展示模型，activeStageKey 控制按钮出现在哪个跨天节点下方。 */
  const timelineItems = computed(() => createTimelineViewModel({
    basicData: options.basicData.value,
    recordLength: recordLength.value,
    activeStageKey: currentClockStage.value?.key,
    group: options.group.value,
    isWhite: options.isWhite.value,
    isAfterOffHours1: options.isAfterOffHours1.value,
    isAfterOffHours2: options.isAfterOffHours2.value,
  }));

  /** 圆形打卡按钮的文案、颜色、禁用态和范围提示。 */
  const clockButton = computed(() => getClockButtonState({
    notAllowSign: notAllowSign.value,
    clockStatus: currentClockStatus.value,
    recordLength: recordLength.value,
    currentWorkType: currentWorkType.value,
    isWhite: options.isWhite.value,
    isGroup: isGroup.value,
    isExternal: groupExternal.value,
    isEffectiveRange: isEffectiveRange.value,
    isLocationEnable: options.isLocationEnable.value,
    isWifiSignEnable: options.isWifiSignEnable.value,
    isWifiRange: options.isCompanyWifiRange.value,
    address: options.address.value,
    wifiInfo: options.wifiInfo.value,
    updateStatus: currentRecord.value.update_status,
  }));

  /** 当前节点变化时，同步给成功弹窗和外勤弹窗使用。 */
  watch(currentWorkType, (value) => {
    options.onWork.value = value;
  }, { immediate: true });

  /** 外勤打卡备注是否必填。 */
  const externalTextRequired = computed(() => {
    return !options.isWhite.value && options.group.value ? options.group.value.is_external_note || 0 : 0;
  });

  /** 外勤打卡图片是否必填。 */
  const externalPicRequired = computed(() => {
    return !options.isWhite.value && options.group.value ? options.group.value.is_external_photo || 0 : 0;
  });

  /** 圆形打卡按钮点击入口。 */
  function handleClockButtonClick() {
    if (clockButton.value.disabled) return;

    if (currentRecord.value.update_status) {
      // 更新打卡必须锁定当前节点，否则跨天时可能更新到当天上班卡。
      startClock(currentClockStage.value?.index, currentClockStage.value?.workType);
      return;
    }

    startClock();
  }

  /** 时间线记录右侧“更新打卡”点击入口。 */
  function handleStageRenew(index: number, type: AttendanceWorkType) {
    startClock(index, type);
  }

  /** 外勤/拍照弹窗确认后继续提交打卡。 */
  function handleExternalOk(formData: ExternalFormData) {
    isExternal.value = formData.is_external || 0;
    imgs.value = formData.imgs || [];
    remark.value = formData.text || "";
    submitClock(updateNumber.value);
  }

  /**
   * 启动一次打卡。
   *
   * @param index 指定班次段索引；从时间线“更新打卡”进入时传入。
   * @param type 指定上班/下班；不传时使用当前可打卡节点。
   */
  function startClock(index?: number, type?: AttendanceWorkType) {
    if (isMissingAttendancePosition()) {
      uni.showToast({
        title: appI18n.global.t('ui.attendanceComposablesUseAttendanceClockFlowTsUnableToClockIn'),
        icon: "none",
      });
      return;
    }

    const targetStage = index !== undefined && type
      ? findClockStage(index, type)
      : currentClockStage.value;

    // 锁定目标节点，外勤弹窗确认后 submitClock 仍使用同一个节点上下文。
    clockTargetStage.value = targetStage;

    options.onWork.value = type || targetStage?.workType || options.onWork.value || getCurrentWorkType(recordLength.value);
    updateNumber.value = index !== undefined && type
      ? getUpdateNumber(index, type)
      : targetStage?.updateNumber ?? "";

    const group = options.group.value;
    const allowExternalSignIn = group?.is_external;
    const needPhotoSignIn = group?.is_photo;
    // 未进入考勤范围但允许外勤、早退状态范围内拍照、或考勤组要求拍照时，需要先弹确认/上传弹窗。
    const permission1 = currentClockStatus.value === 1 && !isEffectiveRange.value && allowExternalSignIn;
    const permission2 = [4, 6].includes(currentClockStatus.value) && isEffectiveRange.value;
    const permission3 = (allowExternalSignIn && !isEffectiveRange.value) || needPhotoSignIn;

    if (permission1 || permission2 || permission3 || options.isWhite.value || !group) {
      options.openExternalPopup();
      return;
    }

    submitClock(updateNumber.value);
  }

  /**
   * 提交 attendance/clock_in。
   *
   * 新版跨天接口会额外带上 date/shift_id/number，用于明确“打的是昨天班次的下班卡”
   * 还是“今天班次的上班卡”。旧版接口没有这些字段时仍按原参数提交。
   */
  function submitClock(isUpdate: number | "" = updateNumber.value) {
    if (isMissingAttendancePosition()) {
      uni.showToast({
        title: appI18n.global.t('ui.attendanceComposablesUseAttendanceClockFlowTsYouCannotClockInOutsideTheAttendanceArea'),
        icon: "none",
      });
      return;
    }

    if (options.attendanceData.value?.group == null) {
      isExternal.value = 0;
    }

    const data: Record<string, unknown> = {
      // 坐标和地址始终传当前设备信息，外勤/范围内打卡都需要。
      lat: options.nowXy.value?.latitude || "",
      lng: options.nowXy.value?.longitude || "",
      address: options.address.value,
      remark: remark.value,
      image: imgs.value,
      is_external: isExternal.value,
      update_number: isUpdate,
    };

    const targetStage = clockTargetStage.value || currentClockStage.value;

    if (targetStage?.clockDate) {
      // 跨天打卡的归属日期，例如补打昨天开始班次的今天下班卡。
      data.date = targetStage.clockDate;
    }
    if (targetStage?.shiftId !== undefined) {
      // 后端根据班次 ID 精准定位跨天班次。
      data.shift_id = targetStage.shiftId;
    }
    if (targetStage?.recordNumber !== undefined) {
      // 后端打卡槽位编号，避免仅靠 update_number 产生歧义。
      data.number = targetStage.recordNumber;
    }

    if (options.isCompanyWifiRange.value && options.wifiInfo.value?.BSSID) {
      data.mac = options.wifiInfo.value.BSSID;
    }

    uni.showLoading({ mask: true });
    attendanceClockIn(data)
      .then((res: { data: { clock_time: string } }) => {
        uni.hideLoading();
        clockTime.value = res.data.clock_time;
        signInType.value = recordLength.value;
        options.openSuccessPopup();
      })
      .catch((err: { message?: string }) => {
        uni.hideLoading();
        setTimeout(() => {
          uni.showToast({
            title: err.message || "打卡失败",
            icon: "none",
          });
        });
      });
  }

  /** 判断是否缺少必要的位置来源。 */
  function isMissingAttendancePosition() {
    const p1 = options.isLocationEnable.value && !options.nowXy.value;
    const p2 = !options.isLocationEnable.value && !options.isCompanyWifiRange.value;
    return p1 && p2;
  }

  /** 根据班次段和上/下班类型查找指定节点，主要服务更新时间线记录。 */
  function findClockStage(index: number, type: AttendanceWorkType) {
    return createClockStages(options.basicData.value).find(stage => stage.index === index && stage.workType === type) || null;
  }

  return {
    clockTime,
    signInType,
    notAllowSign,
    timelineItems,
    clockButton,
    clockRecordLength: recordLength,
    isEffectiveRange,
    externalTextRequired,
    externalPicRequired,
    handleClockButtonClick,
    handleStageRenew,
    handleExternalOk,
  };
}
