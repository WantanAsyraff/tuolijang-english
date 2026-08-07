import appI18n from '@/locale';
import type {
  AttendanceClockRecord,
  AttendanceClockStage,
  AttendanceGroup,
  AttendanceShiftRule,
  AttendanceShiftViewModel,
  AttendanceStageViewModel,
  AttendanceStatusTag,
  AttendanceWorkType,
  ClockButtonVariant,
  ClockButtonViewModel,
  WifiInfo,
} from "./attendanceTypes";

const EMPTY_IMAGE_LIST = JSON.stringify([]);

/** 圆形打卡按钮状态计算所需的输入，来自数据层、定位层和 Wi-Fi 层。 */
interface ClockButtonStateInput {
  /** 是否完全不满足定位/Wi-Fi/外勤任一打卡条件。 */
  notAllowSign: boolean;
  /** 当前可打卡节点状态。 */
  clockStatus: number;
  /** 当前可见打卡节点顺序，兼容旧版按记录数量判断成功弹窗类型的逻辑。 */
  recordLength: number;
  /** 是否白名单用户。 */
  isWhite: boolean;
  /** 当前用户是否有考勤组。 */
  isGroup: boolean;
  /** 考勤组是否允许外勤打卡。 */
  isExternal: number;
  /** 当前定位或 Wi-Fi 是否在有效考勤范围内。 */
  isEffectiveRange: boolean;
  /** 是否开启定位打卡。 */
  isLocationEnable: boolean;
  /** 是否开启 Wi-Fi 打卡。 */
  isWifiSignEnable: boolean;
  /** 当前 Wi-Fi 是否命中考勤组白名单。 */
  isWifiRange: boolean;
  /** 当前定位反解析地址。 */
  address: string;
  /** 当前 Wi-Fi 信息。 */
  wifiInfo?: WifiInfo | null;
  /** 当前记录是否允许更新打卡。 */
  updateStatus?: number;
  /** 当前按钮绑定的真实节点类型；跨天时优先使用它，而不是 recordLength 奇偶。 */
  currentWorkType?: AttendanceWorkType;
}

/** 时间线视图模型计算所需输入。 */
interface TimelineInput {
  /** 已归一化的班次规则。 */
  basicData: AttendanceShiftRule[];
  /** 当前可见节点顺序，用于旧版更新打卡和异常入口兼容。 */
  recordLength: number;
  /** 当前应显示打卡按钮的节点 key。 */
  activeStageKey?: string;
  /** 考勤组配置，用于地址展示兜底。 */
  group?: AttendanceGroup | null;
  /** 是否白名单。 */
  isWhite: boolean;
  /** 第一段下班时间是否已过。 */
  isAfterOffHours1: boolean;
  /** 第二段下班时间是否已过。 */
  isAfterOffHours2: boolean;
}

/**
 * 旧版当前上下班推断规则。
 *
 * 旧版接口只有当天 recordData，因此用记录数量奇偶判断当前按钮文案。
 * 新版跨天场景会优先使用 AttendanceClockStage.workType，这个函数仅作为兜底。
 */
export function getCurrentWorkType(recordLength: number): AttendanceWorkType {
  return recordLength === 1 || recordLength === 3 ? "on" : "off";
}

/**
 * 根据班次段索引和上/下班类型计算旧版 update_number。
 *
 * 后端约定：
 * - 第 1 段上班 0，下班 1。
 * - 第 2 段上班 2，下班 3。
 */
export function getUpdateNumber(index?: number, type?: AttendanceWorkType) {
  if (index === undefined || !type) return "";
  if (index === 0) return type === "on" ? 0 : 1;
  if (index === 1) return type === "on" ? 2 : 3;
  return "";
}

/**
 * 将二维的 `rules -> on/off` 拉平成可顺序处理的打卡节点列表。
 *
 * 这样按钮和提交逻辑可以直接绑定到“当前节点”，不再依赖 recordData.length。
 * 跨天班次只会把 showOn/showOff 未隐藏的节点纳入顺序。
 */
export function createClockStages(rules: AttendanceShiftRule[]): AttendanceClockStage[] {
  const stages: AttendanceClockStage[] = [];

  rules.forEach((rule, index) => {
    if (rule.showOn !== false) {
      // 上班节点保留自己的状态、日期和班次 ID，打卡提交时直接取用。
      stages.push({
        key: getStageKey(index, "on"),
        index,
        sequence: 0,
        workType: "on",
        record: rule.on,
        updateNumber: rule.onUpdateNumber ?? getUpdateNumber(index, "on"),
        clockStatus: rule.onClockStatus,
        clockTimestamp: rule.onClockTimestamp,
        clockDate: rule.onClockDate,
        shiftId: rule.onShiftId,
        recordNumber: rule.on?.number,
      });
    }

    if (rule.showOff !== false) {
      // 下班节点同样独立建模，跨天的“昨天下班卡”会作为一个普通 off 节点处理。
      stages.push({
        key: getStageKey(index, "off"),
        index,
        sequence: 0,
        workType: "off",
        record: rule.off,
        updateNumber: rule.offUpdateNumber ?? getUpdateNumber(index, "off"),
        clockStatus: rule.offClockStatus,
        clockTimestamp: rule.offClockTimestamp,
        clockDate: rule.offClockDate,
        shiftId: rule.offShiftId,
        recordNumber: rule.off?.number,
      });
    }
  });

  return stages.map((stage, index) => ({
    ...stage,
    sequence: index + 1,
  }));
}

/**
 * 获取当前应该承载打卡按钮的节点。
 *
 * 优先级：
 * 1. 还未打卡或允许更新打卡的节点。
 * 2. 在待处理节点中优先选择 clockStatus 非 0 的节点，避免按钮停在“未到打卡时间”的槽位。
 * 3. 如果都已完成，则返回最后一个节点，用于展示最终状态。
 */
export function getCurrentClockStage(rules: AttendanceShiftRule[]) {
  const stages = createClockStages(rules).filter(stage => stage.record);
  const pendingStages = stages.filter(stage => {
    if (stage.record?.update_status) return true;
    return !stage.record?.clock_time;
  });

  return pendingStages.find(stage => stage.clockStatus !== undefined && stage.clockStatus !== 0)
    || pendingStages[0]
    || stages[stages.length - 1]
    || null;
}

/** 生成圆形打卡按钮的完整视图模型。 */
export function getClockButtonState(input: ClockButtonStateInput): ClockButtonViewModel {
  const variant = getClockButtonVariant(input);
  const disabled = (input.clockStatus === 0 && !input.isWhite) || input.notAllowSign || variant === "err";

  return {
    text: getClockButtonText(input),
    variant,
    disabled,
    rangeText: getRangeText(input),
    showAddress: input.isLocationEnable && !!input.address && !input.isWhite,
    addressText: input.address,
    showWifiInfo: input.isWifiSignEnable,
    wifiSsid: input.wifiInfo?.SSID || "--",
    wifiBssid: input.wifiInfo?.BSSID || "--",
    isWifiRange: input.isWifiRange,
  };
}

/**
 * 将归一化后的 rules 转为时间线组件使用的视图模型。
 *
 * 注意：这里仍返回 on/off 两个节点，是为了复用原组件结构；
 * 真实是否展示由 stage.hidden 控制。
 */
export function createTimelineViewModel(input: TimelineInput): AttendanceShiftViewModel[] {
  return input.basicData.map((rule, index) => ({
    key: `shift-${index}`,
    index,
    on: createStageViewModel({
      stageKey: getStageKey(index, "on"),
      index,
      workType: "on",
      time: rule.work_hours || "",
      label: rule.onLabel || "",
      hidden: rule.showOn === false,
      record: rule.on,
      group: input.group,
      isWhite: input.isWhite,
      recordLength: input.recordLength,
      isAfterOffHours: index === 0 ? input.isAfterOffHours1 : input.isAfterOffHours2,
      activeStageKey: input.activeStageKey,
    }),
    off: createStageViewModel({
      stageKey: getStageKey(index, "off"),
      index,
      workType: "off",
      time: rule.off_hours || "",
      label: rule.offLabel || "",
      hidden: rule.showOff === false,
      record: rule.off,
      group: input.group,
      isWhite: input.isWhite,
      recordLength: input.recordLength,
      isAfterOffHours: index === 0 ? input.isAfterOffHours1 : input.isAfterOffHours2,
      activeStageKey: input.activeStageKey,
    }),
  }));
}

/** 创建单个上班/下班节点的展示模型。 */
function createStageViewModel(options: {
  stageKey: string;
  index: number;
  workType: AttendanceWorkType;
  time: string;
  label: string;
  hidden: boolean;
  record?: AttendanceClockRecord;
  group?: AttendanceGroup | null;
  isWhite: boolean;
  recordLength: number;
  isAfterOffHours: boolean;
  activeStageKey?: string;
}): AttendanceStageViewModel {
  const { record, group, workType, index, recordLength } = options;
  const images = Array.isArray(record?.image) && record.image[0] !== EMPTY_IMAGE_LIST ? record.image : [];

  return {
    key: options.stageKey,
    index,
    workType,
    title: workType === "on" ? "上班打卡" : "下班打卡",
    time: options.time,
    label: options.label,
    hidden: options.hidden,
    record,
    tags: createStatusTags(record, workType),
    addressText: getRecordAddress(record, group),
    macText: record?.mac ? `MAC地址: ${record.mac}` : "",
    remark: record?.remark || "",
    images,
    action: createRecordAction(record, workType, recordLength, options.isWhite, options.isAfterOffHours),
    showLine: workType === "on" || !!record,
    // 新版有 activeStageKey 时精准控制按钮位置；旧版继续使用原先 recordLength 规则。
    showClockButton: !options.hidden && (
      options.activeStageKey
        ? options.stageKey === options.activeStageKey
        : isClockButtonVisible(record, workType, index, recordLength)
    ),
  };
}

/** 生成节点 key，保证数据层、时间线和按钮定位使用同一套标识。 */
function getStageKey(index: number, workType: AttendanceWorkType) {
  return `${index}-${workType}`;
}

/** 根据打卡状态、范围状态和外勤配置决定按钮颜色。 */
function getClockButtonVariant(input: ClockButtonStateInput): ClockButtonVariant {
  if (input.notAllowSign) return "err";
  if (input.clockStatus === 0) return input.updateStatus ? "normal" : "err";
  if (input.clockStatus === 1) return input.isExternal ? "upload" : "normal";
  if ([2, 3, 5].includes(input.clockStatus)) {
    if (!input.isExternal && !input.isEffectiveRange && input.isGroup) return "err";
    return "suc";
  }
  if ([4, 6].includes(input.clockStatus)) {
    if (input.updateStatus) return "normal";
    if (!input.isExternal && input.isGroup && !input.isEffectiveRange) return "err";
    return "suc";
  }

  return "err";
}

/** 根据打卡状态生成按钮主文案。 */
function getClockButtonText(input: ClockButtonStateInput) {
  if (input.notAllowSign) return "无法打卡";
  if (input.clockStatus === 0) return input.updateStatus ? "更新打卡" : "无法打卡";
  // 跨天时 currentWorkType 来自当前节点；旧版没有节点上下文时再按 recordLength 兜底。
  const currentWorkType = input.currentWorkType || getCurrentWorkType(input.recordLength);

  if (input.clockStatus === 1) {
    if (currentWorkType === "on") {
      if (!input.isEffectiveRange && input.isExternal) return "外勤打卡";
      if (input.isEffectiveRange && input.updateStatus) return "更新打卡";
      return "上班打卡";
    }
    if (currentWorkType === "off") {
      if (!input.isEffectiveRange && input.isExternal) return "外勤打卡";
      if (input.isEffectiveRange && input.updateStatus) return "更新打卡";
      return "下班打卡";
    }
  }

  if ([2, 3, 5].includes(input.clockStatus)) {
    if (input.isExternal && !input.isEffectiveRange) return "迟到外勤";
    if (!input.isExternal && !input.isEffectiveRange && !input.isWhite && input.isGroup) return "无法打卡";
    return "迟到打卡";
  }

  if ([4, 6].includes(input.clockStatus)) {
    if (input.updateStatus) return "更新打卡";
    if (!input.isExternal && input.isGroup && !input.isEffectiveRange) return "无法打卡";
    if (!input.isEffectiveRange && input.isGroup) return "早退外勤";
    return "早退打卡";
  }

  return "无法打卡";
}

/** 生成按钮下方的范围提示文案。 */
function getRangeText(input: ClockButtonStateInput) {
  if (input.isEffectiveRange && !input.isWhite && input.isGroup && input.clockStatus !== 0) {
    return "您已进入考勤范围";
  }
  if (!input.isEffectiveRange && !input.isWhite && input.isGroup && input.clockStatus !== 0) {
    return "您当前不在考勤范围";
  }
  if (input.clockStatus === 0 && input.isExternal) {
    return "未到打卡时间，稍候再试";
  }
  return "";
}

/** 将后端状态码转换成时间线标签。 */
function createStatusTags(record: AttendanceClockRecord | undefined, workType: AttendanceWorkType) {
  if (!record) return [];

  const tags: AttendanceStatusTag[] = [];

  if (record.status === 5 || record.status === 7) {
    tags.push({ text: appI18n.global.t('ui.attendanceUserAttendanceMissingClockIn'), className: "lack" });
  }
  if (record.location_status === 1) {
    tags.push({ text: appI18n.global.t('ui.attendanceUserAttendanceOffSite'), className: "out" });
  }
  if (record.status === 2) {
    tags.push({ text: appI18n.global.t('ui.attendanceUserAttendanceLate'), className: "be-late" });
  }
  if (workType === "off" && (record.status === 4 || record.status === 6)) {
    tags.push({ text: appI18n.global.t('ui.attendanceUserAttendanceEarlyLeave'), className: "be-late" });
  }
  if (record.location_status === 2) {
    tags.push({ text: appI18n.global.t('ui.attendanceUserAttendanceLocationException'), className: "be-add" });
  }

  return tags;
}

/** 获取打卡记录地址展示文本。 */
function getRecordAddress(record?: AttendanceClockRecord, group?: AttendanceGroup | null) {
  if (!record?.address) return "";
  // 普通范围内打卡展示考勤组地址；外勤或异常地点展示记录自身地址。
  if (!group) return record.address;
  return record.location_status && record.location_status > 0 ? record.address : group.address || "";
}

/** 根据记录状态决定是否展示“更新打卡”或“异常处理”。 */
function createRecordAction(
  record: AttendanceClockRecord | undefined,
  workType: AttendanceWorkType,
  recordLength: number,
  isWhite: boolean,
  isAfterOffHours: boolean
) {
  if (!record) return undefined;

  // 上班卡允许更新时，只有下一张下班卡已经生成后才展示更新入口，沿用旧版交互。
  if (workType === "on" && record.update_status === 1 && (recordLength === 2 || recordLength === 4)) {
    return { type: "renew" as const, text: isWhite ? "" : "更新打卡" };
  }

  // 下班卡允许更新时，只有当前记录数量停在上班卡之后才展示更新入口。
  if (workType === "off" && record.update_status === 1 && (recordLength === 1 || recordLength === 3)) {
    return { type: "renew" as const, text: appI18n.global.t('ui.attendanceComposablesAttendanceViewModelTsUpdateClockRecord') };
  }

  // 异常处理需要等下班时间过后再开放，避免用户在规则未结束前处理异常。
  if ((Number(record.status) > 1 || record.is_external === 2) && isAfterOffHours) {
    return { type: "apply" as const, text: appI18n.global.t('ui.attendanceUserAttendanceExceptionHandling') };
  }

  return undefined;
}

/**
 * 旧版按钮位置判断。
 *
 * 新版跨天已由 activeStageKey 精准定位，这里仅用于没有 activeStageKey 的旧版数据。
 */
function isClockButtonVisible(
  record: AttendanceClockRecord | undefined,
  workType: AttendanceWorkType,
  index: number,
  recordLength: number
) {
  if (!record) return false;

  if (workType === "on") {
    return (index === 0 && recordLength === 1) || (index === 1 && recordLength === 3);
  }

  return (index === 0 && recordLength === 2) || (index === 1 && recordLength === 4);
}
