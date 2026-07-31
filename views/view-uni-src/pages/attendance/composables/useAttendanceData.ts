import { computed, ref } from "vue";
import { useStore } from "vuex";
import { attendanceBasic, attendanceClockRecord } from "@/api/attendance";
import { toLogin } from "@/libs/login";
import type {
  AttendanceClockRecord,
  AttendanceData,
  AttendanceShift,
  AttendanceShiftDay,
  AttendanceShiftDays,
  AttendanceShiftRule,
  AttendanceUserInfo,
  AttendanceWorkType,
} from "./attendanceTypes";
import { createClockStages, getCurrentClockStage, getCurrentWorkType, getUpdateNumber } from "./attendanceViewModel";

/**
 * 生成当前自然日期。
 *
 * 注意：这里统一补零输出 yyyy-MM-dd，避免和新版接口中的 work_date/off_date
 * 做字符串比较时出现 `2026-5-7` 与 `2026-05-07` 不一致的问题。
 */
function createNowDate() {
  const now = new Date();
  return formatDate(now);
}

/** 将月/日补齐为两位数字，供日期格式化使用。 */
function padDateValue(value: number) {
  return value < 10 ? `0${value}` : String(value);
}

/** 将 Date 转成接口和页面统一使用的 yyyy-MM-dd 格式。 */
function formatDate(date: Date) {
  return `${date.getFullYear()}-${padDateValue(date.getMonth() + 1)}-${padDateValue(date.getDate())}`;
}

/**
 * 归一化后端日期文本。
 *
 * 新旧接口可能返回未补零日期，页面跨天逻辑需要通过日期判断节点是否属于今天，
 * 因此所有日期先经过这里统一格式。
 */
function normalizeDateText(date?: string) {
  if (!date) return "";

  const parsedDate = new Date(date.replace(/-/g, "/"));
  if (Number.isNaN(parsedDate.getTime())) return date;

  return formatDate(parsedDate);
}

/**
 * 根据规则时间和可选日期生成可比较的 Date。
 *
 * 旧逻辑只拿当天时间比较，跨天下班卡会把“10:49”误认为今天上午。
 * 新逻辑允许传入 off_date/work_date，确保“昨日上班、今日下班”的结束时间
 * 按真实日期参与异常处理入口判断。
 */
function createClockDate(time?: string, dateText?: string) {
  if (!time) return null;

  const [hour, minute] = time.split(":");
  const parsedHour = Number.parseInt(hour, 10);
  const parsedMinute = Number.parseInt(minute, 10);

  if (Number.isNaN(parsedHour) || Number.isNaN(parsedMinute)) return null;

  const date = dateText ? new Date(dateText.replace(/-/g, "/")) : new Date();
  if (Number.isNaN(date.getTime())) return null;

  date.setHours(parsedHour);
  date.setMinutes(parsedMinute);
  date.setSeconds(0);
  return date;
}

/** 将后端打卡槽位编号统一转成 number，便于判断当前可打卡节点。 */
function normalizeRecordNumber(value: unknown) {
  const numberValue = Number(value);
  return Number.isNaN(numberValue) ? undefined : numberValue;
}

/**
 * 休息日或白名单场景没有完整规则时，用占位规则维持旧版时间线结构。
 *
 * @param isWhite 是否白名单；白名单不展示 `-`，避免误导用户。
 * @param count 需要创建的规则数量。
 */
function createEmptyRules(isWhite: boolean, count: number) {
  const timeText = isWhite ? "" : "-";

  return Array.from({ length: count }, () => ({
    work_hours: timeText,
    off_hours: timeText,
  }));
}

/**
 * 旧版接口数据合并：attendance/basic 只给规则，attendance/clock_record 再给记录。
 *
 * 旧版记录顺序约定为：第 1 段上班、第 1 段下班、第 2 段上班、第 2 段下班。
 * 这里按该顺序把记录挂到规则的 on/off 字段上，保持原页面行为。
 */
function mergeRulesWithRecords(rules: AttendanceShiftRule[], records: AttendanceClockRecord[], isWhite: boolean) {
  const sourceRules = rules.length ? rules : createEmptyRules(isWhite, records.length > 2 ? 2 : 1);

  return sourceRules.map((item, index) => ({
    ...item,
    on: records[index * 2],
    off: records[index * 2 + 1],
  }));
}

/**
 * 判断 attendance/basic 的 shift 是否为新版结构。
 *
 * 旧版：shift.rules 直接是当天班次规则。
 * 新版：shift.prev / shift.now 分别代表昨天、今天的班次上下文。
 */
function isShiftDays(shift: AttendanceData["shift"]): shift is AttendanceShiftDays {
  return !!shift
    && typeof shift === "object"
    && !Array.isArray(shift)
    && ("prev" in shift || "now" in shift);
}

/** 安全读取班次 rules，避免休息日或空班次导致运行时报错。 */
function getShiftRules(shift?: AttendanceShift) {
  return shift && Array.isArray(shift.rules) ? shift.rules : [];
}

/**
 * 判断新版 prev/now 中的某个上班/下班节点是否应该展示在当前页面。
 *
 * 关键规则：
 * - 如果后端给了 work_date/off_date，就严格按节点日期是否等于当前页面日期判断。
 * - 如果没有日期字段，则兜底认为 prev 只展示下班卡，now 展示完整当天班次。
 */
function shouldShowEmbeddedStage(
  entryKey: "prev" | "now",
  stageType: AttendanceWorkType,
  stageDate: string | undefined,
  activeDate: string
) {
  const normalizedStageDate = normalizeDateText(stageDate);

  if (normalizedStageDate && activeDate) {
    return normalizedStageDate === activeDate;
  }

  return entryKey === "prev" ? stageType === "off" : true;
}

/**
 * 为跨天节点生成辅助标签。
 *
 * `昨日班次` 用于告诉用户这个下班卡属于昨天开始的完整班次；
 * `次日` 用于标识当前班次下班时间跨到了第二天。
 */
function getStageLabel(entryKey: "prev" | "now", rule: AttendanceShiftRule, stageType: AttendanceWorkType) {
  if (entryKey === "prev" && stageType === "off") {
    return "昨日班次";
  }

  if (stageType === "off" && rule.work_date && rule.off_date && normalizeDateText(rule.work_date) !== normalizeDateText(rule.off_date)) {
    return "次日";
  }

  return "";
}

/**
 * 将新版 attendance/basic 的 prev/now 结构转换成页面原来能消费的 basicData。
 *
 * 归一化后的结果同时满足三个目标：
 * - 时间线仍然按 `rule.on / rule.off` 渲染，不大改组件结构。
 * - 跨天班次只显示落在今天的节点，例如昨天上班、今天下班时只显示“昨天下班卡”。
 * - 每个节点携带自己的 clock_status/date/shift_id，打卡提交不再依赖 recordData.length 猜测。
 */
function normalizeEmbeddedShiftData(shiftDays: AttendanceShiftDays, isWhite: boolean): {
  rules: AttendanceShiftRule[];
  records: AttendanceClockRecord[];
  activeShift: AttendanceShift | "";
} {
  const activeDate = normalizeDateText(shiftDays.now?.date || createNowDate());
  const entries: Array<{ key: "prev" | "now"; data: AttendanceShiftDay }> = [];

  if (shiftDays.prev?.shift_data) {
    entries.push({ key: "prev", data: shiftDays.prev });
  }
  if (shiftDays.now?.shift_data) {
    entries.push({ key: "now", data: shiftDays.now });
  }

  const rules: AttendanceShiftRule[] = [];

  entries.forEach((entry) => {
    const sourceRules = getShiftRules(entry.data.shift_data);
    const records = Array.isArray(entry.data.list) ? entry.data.list : [];
    const activeRecordNumber = normalizeRecordNumber(records[records.length - 1]?.number);
    const getStageClockState = (record?: AttendanceClockRecord) => {
      const recordNumber = normalizeRecordNumber(record?.number);
      const isActiveRecord = activeRecordNumber !== undefined && recordNumber === activeRecordNumber;
      const shouldUseCurrentState = isActiveRecord || !!record?.update_status;

      return {
        status: shouldUseCurrentState ? entry.data.clock_status || 0 : 0,
        timestamp: shouldUseCurrentState ? entry.data.clock_timestamp || 0 : 0,
      };
    };
    // 新版 list 只包含当前上下文需要输入的打卡槽位，按可展示节点顺序逐个取用。
    let recordIndex = 0;

    sourceRules.forEach((sourceRule, ruleIndex) => {
      const showOn = shouldShowEmbeddedStage(entry.key, "on", sourceRule.work_date, activeDate);
      const showOff = shouldShowEmbeddedStage(entry.key, "off", sourceRule.off_date, activeDate);

      if (!showOn && !showOff) return;

      // 在原始规则上补充页面控制字段，避免破坏后续依赖 work_hours/off_hours 的旧逻辑。
      const rule: AttendanceShiftRule = {
        ...sourceRule,
        showOn,
        showOff,
      };

      if (showOn) {
        const record = records[recordIndex++];
        const clockState = getStageClockState(record);

        // 上班节点归属于当前 entry，后续按钮状态和提交参数都从这里读取。
        rule.on = record;
        rule.onLabel = getStageLabel(entry.key, sourceRule, "on");
        rule.onClockStatus = clockState.status;
        rule.onClockTimestamp = clockState.timestamp;
        rule.onClockDate = entry.data.date;
        rule.onShiftId = entry.data.shift_id;
        rule.onUpdateNumber = getUpdateNumber(ruleIndex, "on");
      }

      if (showOff) {
        const record = records[recordIndex++];
        const clockState = getStageClockState(record);

        // 下班节点同样保留所属日期和班次，跨天补打昨天下班卡时会使用这些字段。
        rule.off = record;
        rule.offLabel = getStageLabel(entry.key, sourceRule, "off");
        rule.offClockStatus = clockState.status;
        rule.offClockTimestamp = clockState.timestamp;
        rule.offClockDate = entry.data.date;
        rule.offShiftId = entry.data.shift_id;
        rule.offUpdateNumber = getUpdateNumber(ruleIndex, "off");
      }

      rules.push(rule);
    });
  });

  if (!rules.length && shiftDays.now?.shift_data) {
    // 有今日班次但没有需要展示的节点时，保留 activeShift 以便顶部“考勤规则”入口可用。
    return {
      rules: createEmptyRules(isWhite, 0),
      records: [] as AttendanceClockRecord[],
      activeShift: shiftDays.now.shift_data,
    };
  }

  return {
    rules,
    records: createClockStages(rules).map(stage => stage.record).filter(Boolean) as AttendanceClockRecord[],
    activeShift: (shiftDays.now?.shift_data || shiftDays.prev?.shift_data || "") as AttendanceShift | "",
  };
}

/** 判断页面日期是否已经早于今天；用于历史日期异常处理入口展示。 */
function isPastDate(date: string) {
  const selectedDate = new Date(date.replace(/-/g, "/"));
  if (Number.isNaN(selectedDate.getTime())) return false;

  const currentDate = new Date();
  selectedDate.setHours(0, 0, 0, 0);
  currentDate.setHours(0, 0, 0, 0);

  return currentDate > selectedDate;
}

/**
 * 考勤首页数据聚合层。
 *
 * 该 composable 负责：
 * - 读取登录用户、考勤组、班次规则、打卡记录和异常统计。
 * - 兼容旧版 basic + clock_record 两段式接口。
 * - 兼容新版 basic 直接返回 prev/now 跨天打卡输入。
 * - 向页面和打卡流程暴露统一后的 basicData/recordData/clockStatus。
 */
export function useAttendanceData() {
  const store = useStore();
  /** 当前登录用户，顶部用户卡片使用。 */
  const userInfo = computed<AttendanceUserInfo | null>(() => store.state.app.userInfo || null);
  /** 登录状态，不登录时进入登录流程。 */
  const isLogin = computed<boolean>(() => store.state.app.isLogin);

  /** 页面首屏 loading，控制时间线延迟渲染。 */
  const loading = ref(true);
  /** attendance/basic 顶层数据，定位、Wi-Fi、规则入口都会读取。 */
  const attendanceData = ref<AttendanceData | null>(null);
  /** 已归一化的当前页面可见打卡记录。 */
  const recordData = ref<AttendanceClockRecord[]>([]);
  /** 已归一化的班次规则，每条规则可包含 on/off 记录和跨天上下文。 */
  const basicData = ref<AttendanceShiftRule[]>([]);
  /** 本月待处理异常数量。 */
  const abnormal = ref<number | string>("");
  /** 当前可打卡状态，旧版来自 clock_record，新版来自当前可打卡节点。 */
  const clockStatus = ref(0);
  /** 当前状态失效时间戳，计时器到点后可触发状态更新。 */
  const clockUpdateTime = ref(0);
  /** 当前按钮对应上班卡还是下班卡，成功弹窗和外勤弹窗使用。 */
  const onWork = ref<AttendanceWorkType>("on");
  /** 当前用户是否白名单。 */
  const isWhite = ref(false);
  /** 顶部规则入口使用的当前班次对象。 */
  const shift = ref<AttendanceShift | "">("");
  /** 页面日期；考勤首页默认今天。 */
  const nowDate = ref(createNowDate());
  /** 第一段下班时间是否已过，用于控制异常处理入口。 */
  const isAfterOffHours1 = ref(false);
  /** 第二段下班时间是否已过，用于控制异常处理入口。 */
  const isAfterOffHours2 = ref(false);
  /** 是否休息日；当前首页暂未展示休息卡，但保留该状态给后续扩展。 */
  const isRestDay = ref(false);
  /** 是否命中新版 basic 内嵌打卡记录，用于决定是否再合并 clock_record。 */
  const hasEmbeddedClockData = ref(false);

  /** 顶部用户卡片展示的部门 + 职位文案。 */
  const userJobInfo = computed(() => {
    if (!userInfo.value?.job) return "";

    const jobName = userInfo.value.job?.name;
    const departmentName = userInfo.value.frames?.[0]?.frame?.name;

    if (!departmentName) return "";
    return jobName ? `${departmentName} (${jobName})` : departmentName;
  });

  /** 当前考勤组配置。 */
  const group = computed(() => attendanceData.value?.group || null);
  /** 是否开启定位打卡。 */
  const isLocationEnable = computed(() => !!group.value?.is_map);
  /** 是否开启 Wi-Fi 打卡。 */
  const isWifiSignEnable = computed(() => !!group.value?.is_wifi);

  /** 初始化入口：未登录先跳登录，已登录则拉取考勤首页数据。 */
  async function init(afterBasicLoaded?: () => void | Promise<void>) {
    if (!isLogin.value) {
      toLogin();
      return;
    }

    await refresh(afterBasicLoaded);
  }

  /**
   * 刷新首页数据。
   *
   * 先请求 basic 是因为定位范围、Wi-Fi 规则、跨天打卡槽位都依赖 basic。
   * 旧版接口随后再请求 clock_record 合并记录；新版接口只从 clock_record 补异常数，
   * 避免旧接口的当天记录覆盖 basic 中的“昨天下班卡”。
   */
  async function refresh(afterBasicLoaded?: () => void | Promise<void>) {
    loading.value = true;

    try {
      await fetchBasic();
      await afterBasicLoaded?.();
      await fetchClockRecord({ mergeRecords: !hasEmbeddedClockData.value });
    } finally {
      loading.value = false;
    }
  }

  /** 拉取并解析 attendance/basic。 */
  async function fetchBasic() {
    const res = await attendanceBasic({});
    const data = (res.data || {}) as AttendanceData;

    attendanceData.value = data;
    isWhite.value = Boolean(data.whitelist);
    hasEmbeddedClockData.value = isShiftDays(data.shift);

    if (hasEmbeddedClockData.value && isShiftDays(data.shift)) {
      // 新版接口：basic 已经带 prev/now 以及打卡槽位，直接归一化成时间线数据。
      const normalized = normalizeEmbeddedShiftData(data.shift, isWhite.value);
      shift.value = normalized.activeShift;
      basicData.value = normalized.rules;
      recordData.value = normalized.records;
      isRestDay.value = !basicData.value.length && !recordData.value.length;
      syncClockStateFromBasicData();
    } else {
      // 旧版接口：basic 只提供当天班次规则，记录稍后由 clock_record 合并。
      shift.value = data.shift && !isShiftDays(data.shift) ? data.shift : "";
      basicData.value = data.shift && !isShiftDays(data.shift) && Array.isArray(data.shift.rules)
        ? data.shift.rules.map(item => ({ ...item }))
        : [];
      recordData.value = [];
      clockUpdateTime.value = 0;
      clockStatus.value = 0;
    }

    updateAfterOffHours();
  }

  /**
   * 拉取 attendance/clock_record。
   *
   * @param options.mergeRecords 是否把 clock_record.list 合并进 basicData。
   * 新版 basic 已经包含跨天槽位时不能合并，否则会丢掉 prev 下班卡。
   */
  async function fetchClockRecord(options: { mergeRecords?: boolean } = {}) {
    const res = await attendanceClockRecord({});
    const data = res.data || {};
    const records = Array.isArray(data.list) ? data.list : [];

    abnormal.value = data.abnormal;

    if (options.mergeRecords === false) {
      return;
    }

    // 旧版接口按原逻辑合并记录，并通过记录数量推断当前上班/下班阶段。
    recordData.value = records;
    onWork.value = getCurrentWorkType(records.length);
    basicData.value = records.length || basicData.value.length
      ? mergeRulesWithRecords(basicData.value, records, isWhite.value)
      : [];
    isRestDay.value = !basicData.value.length && !records.length;
    clockUpdateTime.value = data.clock_timestamp || 0;
    clockStatus.value = data.clock_status || 0;
  }

  /** 新版接口下，从当前可打卡节点同步按钮状态和成功弹窗的上下班类型。 */
  function syncClockStateFromBasicData() {
    const currentStage = getCurrentClockStage(basicData.value);

    if (!currentStage) {
      clockUpdateTime.value = 0;
      clockStatus.value = 0;
      return;
    }

    onWork.value = currentStage.workType;
    clockUpdateTime.value = currentStage.clockTimestamp || 0;
    clockStatus.value = currentStage.clockStatus || 0;
  }

  /** 根据规则下班时间判断异常处理入口是否可展示。 */
  function updateAfterOffHours() {
    const currentTime = new Date();
    const rules = basicData.value;
    const offHours1 = createClockDate(rules[0]?.off_hours, rules[0]?.off_date);
    const offHours2 = createClockDate(rules[1]?.off_hours, rules[1]?.off_date);

    isAfterOffHours1.value = false;
    isAfterOffHours2.value = false;

    if (isPastDate(nowDate.value)) {
      isAfterOffHours1.value = true;
      isAfterOffHours2.value = true;
      return;
    }

    isAfterOffHours1.value = offHours1 ? currentTime > offHours1 : false;
    isAfterOffHours2.value = offHours2 ? currentTime > offHours2 : false;
  }

  return {
    userInfo,
    userJobInfo,
    loading,
    attendanceData,
    group,
    recordData,
    basicData,
    abnormal,
    clockStatus,
    clockUpdateTime,
    onWork,
    isWhite,
    shift,
    nowDate,
    isAfterOffHours1,
    isAfterOffHours2,
    isRestDay,
    isLocationEnable,
    isWifiSignEnable,
    init,
    refresh,
  };
}
