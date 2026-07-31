/** 打卡阶段类型：on 表示上班卡，off 表示下班卡。 */
export type AttendanceWorkType = "on" | "off";

/** 打卡按钮视觉状态：普通、成功/异常可打卡、错误禁用、外勤/拍照打卡。 */
export type ClockButtonVariant = "normal" | "suc" | "err" | "upload";

/** 单条打卡记录，来自 attendance/clock_record 或新版 attendance/basic 的 list。 */
export interface AttendanceClockRecord {
  /** 当前记录在后端班次中的序号；跨天打卡提交时用于告诉后端补哪个打卡点。 */
  number?: number | string;
  /** 实际打卡时间，通常是 HH:mm 或 HH:mm:ss。为空时代表该打卡点还未打卡。 */
  clock_time?: string;
  /** 考勤状态：0/1 正常类状态，2 迟到，4/6 早退，5/7 缺卡等，具体值由后端定义。 */
  status?: number;
  /** 位置状态：0 正常，1 外勤，2 地点异常。 */
  location_status?: number;
  /** 是否允许更新打卡：1 表示当前记录可触发“更新打卡”。 */
  update_status?: number;
  /** 外勤状态：1 表示外勤打卡，2 通常表示异常外勤，需要异常处理。 */
  is_external?: number;
  /** 打卡 Wi-Fi 的 MAC/BSSID，APP 端 Wi-Fi 打卡时展示。 */
  mac?: string;
  /** 打卡地址；普通范围内打卡会优先展示考勤组地址，外勤/异常展示记录地址。 */
  address?: string;
  /** 打卡备注，主要来自外勤或拍照打卡弹窗。 */
  remark?: string;
  /** 打卡图片列表，外勤/拍照打卡时展示。 */
  image?: string[];
  /** 兼容后端可能新增的字段，避免页面因接口扩展产生类型阻塞。 */
  [key: string]: unknown;
}

/** 页面渲染用的单段班次规则，已合并上班/下班打卡记录和跨天上下文。 */
export interface AttendanceShiftRule {
  /** 该规则在班次中的第几段；一天两次上下班时用于定位第 1/2 段。 */
  number?: number | string;
  /** 规则上班时间，例如 09:00。 */
  work_hours?: string;
  /** 规则下班时间，例如 18:00；跨天班次可能属于第二天。 */
  off_hours?: string;
  /** 上班卡所属自然日期；新版 basic 用它判断是否展示在今天页面。 */
  work_date?: string;
  /** 下班卡所属自然日期；跨天班次的下班卡会落在次日。 */
  off_date?: string;
  /** 页面是否展示上班节点；跨天时昨日上班卡不应出现在今天页面。 */
  showOn?: boolean;
  /** 页面是否展示下班节点；跨天时昨日下班卡需要出现在今天页面。 */
  showOff?: boolean;
  /** 上班节点右侧的辅助标签，例如“次日”。 */
  onLabel?: string;
  /** 下班节点右侧的辅助标签，例如“昨日班次”或“次日”。 */
  offLabel?: string;
  /** 上班节点对应的当前可打卡状态，用于按钮颜色和文案计算。 */
  onClockStatus?: number;
  /** 下班节点对应的当前可打卡状态，用于按钮颜色和文案计算。 */
  offClockStatus?: number;
  /** 上班节点状态过期时间戳，到点后可触发状态刷新逻辑。 */
  onClockTimestamp?: number;
  /** 下班节点状态过期时间戳，到点后可触发状态刷新逻辑。 */
  offClockTimestamp?: number;
  /** 上班节点实际归属日期，打卡提交时随请求带给后端。 */
  onClockDate?: string;
  /** 下班节点实际归属日期，跨天补打昨天下班卡时随请求带给后端。 */
  offClockDate?: string;
  /** 上班节点归属的班次 ID，跨天场景避免后端误判当前日期班次。 */
  onShiftId?: number | string;
  /** 下班节点归属的班次 ID，跨天场景用于定位昨日完整班次。 */
  offShiftId?: number | string;
  /** 上班节点更新打卡编号，兼容旧版 update_number 约定：0/2。 */
  onUpdateNumber?: number | "";
  /** 下班节点更新打卡编号，兼容旧版 update_number 约定：1/3。 */
  offUpdateNumber?: number | "";
  /** 已合并的上班打卡记录；为空表示规则存在但暂无记录。 */
  on?: AttendanceClockRecord;
  /** 已合并的下班打卡记录；为空表示规则存在但暂无记录。 */
  off?: AttendanceClockRecord;
  /** 兼容后端班次规则新增字段。 */
  [key: string]: unknown;
}

/** 旧版 attendance/basic 返回的班次对象，或新版 shift_data 内部的班次对象。 */
export interface AttendanceShift {
  /** 班次 ID。 */
  id?: number | string;
  /** 班次名称，例如“默认班次”。 */
  name?: string;
  /** 一天上下班次数，1 表示一段，2 表示两段。 */
  number?: number | string;
  /** 班次规则列表，每条规则包含一组上班/下班时间。 */
  rules?: AttendanceShiftRule[];
  /** 兼容班次颜色、休息时间、工时等其他字段。 */
  [key: string]: unknown;
}

/** 新版 attendance/basic 中 prev/now 的单日班次上下文。 */
export interface AttendanceShiftDay {
  /** 该上下文代表的自然日期，例如 prev.date 是昨天，now.date 是今天。 */
  date?: string;
  /** 该日排班对应的班次 ID，打卡提交时用于后端精确定位。 */
  shift_id?: number | string;
  /** 该日班次详情；真正的 rules 位于 shift_data.rules。 */
  shift_data?: AttendanceShift;
  /** 该日当前可打卡状态；跨天时 prev 和 now 可以各自独立。 */
  clock_status?: number;
  /** 该日当前状态的过期时间戳。 */
  clock_timestamp?: number;
  /** 该日已生成的打卡槽位/记录列表，顺序与展示出来的上班/下班节点对应。 */
  list?: AttendanceClockRecord[];
  /** 兼容新版接口后续补充字段。 */
  [key: string]: unknown;
}

/** 新版 attendance/basic 的班次容器：同时返回昨天和今天的输入。 */
export interface AttendanceShiftDays {
  /** 昨天的班次上下文；跨天班次的下班卡通常从这里取。 */
  prev?: AttendanceShiftDay | null;
  /** 今天的班次上下文；普通当天打卡主要从这里取。 */
  now?: AttendanceShiftDay | null;
  /** 兼容后端扩展字段。 */
  [key: string]: unknown;
}

/** 考勤组配置中的一条 Wi-Fi 白名单。 */
export interface AttendanceGroupWifi {
  /** 公司 Wi-Fi MAC/BSSID。 */
  mac?: string;
  /** 兼容 Wi-Fi 名称等后端扩展字段。 */
  [key: string]: unknown;
}

/** 考勤组配置，控制定位、Wi-Fi、外勤、拍照等打卡能力。 */
export interface AttendanceGroup {
  /** 考勤地址。 */
  address?: string;
  /** 有效打卡范围，单位通常为米。 */
  effective_range?: number | string;
  /** 考勤点纬度。 */
  lat?: number | string;
  /** 考勤点经度。 */
  lng?: number | string;
  /** 是否开启地图/定位打卡。 */
  is_map?: number | boolean;
  /** 是否开启 Wi-Fi 打卡。 */
  is_wifi?: number | boolean;
  /** 是否允许外勤打卡。 */
  is_external?: number;
  /** 外勤打卡是否必填备注。 */
  is_external_note?: number;
  /** 外勤打卡是否必传图片。 */
  is_external_photo?: number;
  /** 是否需要拍照打卡。 */
  is_photo?: number;
  /** 公司 Wi-Fi 白名单。 */
  wifi?: AttendanceGroupWifi[];
  /** 兼容管理员、补卡规则等其他考勤组字段。 */
  [key: string]: unknown;
}

/** attendance/basic 顶层数据。 */
export interface AttendanceData {
  /** 考勤组配置；为空表示当前用户可能不属于考勤组或白名单场景。 */
  group?: AttendanceGroup | null;
  /** 班次数据：旧版是 AttendanceShift，新版是 AttendanceShiftDays，休息日可能为空字符串。 */
  shift?: AttendanceShift | AttendanceShiftDays | "";
  /** 是否白名单用户；白名单用户不受普通规则约束。 */
  whitelist?: boolean | number;
  /** 兼容异常统计、提示等后端扩展字段。 */
  [key: string]: unknown;
}

/** 当前登录用户在考勤页展示所需的最小信息。 */
export interface AttendanceUserInfo {
  /** 用户头像。 */
  avatar?: string;
  /** 用户姓名。 */
  name?: string;
  /** 用户职位信息。 */
  job?: {
    /** 职位名称。 */
    name?: string;
  };
  /** 用户所属组织架构。 */
  frames?: Array<{
    /** 部门节点。 */
    frame?: {
      /** 部门名称。 */
      name?: string;
    };
  }>;
}

/** 当前设备连接的 Wi-Fi 信息。 */
export interface WifiInfo {
  /** 当前 Wi-Fi 的 BSSID/MAC。 */
  BSSID?: string;
  /** 当前 Wi-Fi 名称。 */
  SSID?: string;
}

/** 地图坐标，统一使用 latitude/longitude 命名便于 uni 地图组件消费。 */
export interface AttendanceCoordinate {
  /** 纬度。 */
  latitude: number;
  /** 经度。 */
  longitude: number;
}

/** 打卡记录状态标签。 */
export interface AttendanceStatusTag {
  /** 展示文案，例如“缺卡”“迟到”。 */
  text: string;
  /** 标签样式类名。 */
  className: "lack" | "out" | "be-late" | "be-add";
}

/** 单条打卡记录右侧操作入口。 */
export interface AttendanceRecordAction {
  /** renew 表示更新打卡，apply 表示异常处理。 */
  type: "renew" | "apply";
  /** 操作文案。 */
  text: string;
}

/** 扁平化后的可打卡节点，用于替代旧版 recordData.length 的奇偶推断。 */
export interface AttendanceClockStage {
  /** 节点唯一键，格式为 `${规则索引}-${on/off}`。 */
  key: string;
  /** 班次规则索引，从 0 开始。 */
  index: number;
  /** 页面可见节点的顺序，从 1 开始；成功弹窗沿用它判断第几次打卡。 */
  sequence: number;
  /** 当前节点是上班卡还是下班卡。 */
  workType: AttendanceWorkType;
  /** 当前节点对应的打卡记录/槽位。 */
  record?: AttendanceClockRecord;
  /** 更新打卡编号，提交 attendance/clock_in 时使用。 */
  updateNumber: number | "";
  /** 当前节点的可打卡状态。 */
  clockStatus?: number;
  /** 当前节点状态过期时间戳。 */
  clockTimestamp?: number;
  /** 当前节点归属日期。 */
  clockDate?: string;
  /** 当前节点归属班次 ID。 */
  shiftId?: number | string;
  /** 当前节点后端记录编号。 */
  recordNumber?: number | string;
}

/** 单个上班/下班节点的视图模型，供 AttendanceStageRecord 渲染。 */
export interface AttendanceStageViewModel {
  /** 节点唯一键。 */
  key: string;
  /** 班次规则索引。 */
  index: number;
  /** 上班/下班类型。 */
  workType: AttendanceWorkType;
  /** 节点标题，例如“上班打卡”。 */
  title: string;
  /** 规则时间，例如 09:00。 */
  time: string;
  /** 节点辅助标签，例如“昨日班次”。 */
  label: string;
  /** 是否隐藏该节点；跨天时用于过滤不属于当天页面的上班/下班点。 */
  hidden: boolean;
  /** 该节点绑定的打卡记录。 */
  record?: AttendanceClockRecord;
  /** 状态标签列表。 */
  tags: AttendanceStatusTag[];
  /** 地址展示文案。 */
  addressText: string;
  /** Wi-Fi MAC 展示文案。 */
  macText: string;
  /** 备注展示文案。 */
  remark: string;
  /** 图片列表。 */
  images: string[];
  /** 节点操作入口。 */
  action?: AttendanceRecordAction;
  /** 是否展示连接线。 */
  showLine: boolean;
  /** 是否在该节点下方展示圆形打卡按钮。 */
  showClockButton: boolean;
}

/** 一条班次规则对应的上班节点和下班节点视图模型。 */
export interface AttendanceShiftViewModel {
  /** 班次规则唯一键。 */
  key: string;
  /** 班次规则索引。 */
  index: number;
  /** 上班节点。 */
  on: AttendanceStageViewModel;
  /** 下班节点。 */
  off: AttendanceStageViewModel;
}

/** 圆形打卡按钮视图模型。 */
export interface ClockButtonViewModel {
  /** 按钮主文案。 */
  text: string;
  /** 按钮视觉状态。 */
  variant: ClockButtonVariant;
  /** 是否禁用点击。 */
  disabled: boolean;
  /** 考勤范围提示文案。 */
  rangeText: string;
  /** 是否展示当前地址。 */
  showAddress: boolean;
  /** 当前地址文本。 */
  addressText: string;
  /** 是否展示 Wi-Fi 信息。 */
  showWifiInfo: boolean;
  /** Wi-Fi 名称。 */
  wifiSsid: string;
  /** Wi-Fi BSSID/MAC。 */
  wifiBssid: string;
  /** 当前 Wi-Fi 是否命中考勤组白名单。 */
  isWifiRange: boolean;
}
