import request from "../utils/request"

/**
 * @description 打卡规则
 */
export function attendanceBasic(data: object) {
    return request.get('attendance/basic', data);
}

/**
 * @description 获取打卡记录接口
 */
export function attendanceClockRecord(data: object) {
    return request.get('attendance/clock_record', data);
}


/**
 * @description  打卡
 */
export function attendanceClockIn(data: object) {
    return request.post('attendance/clock_in', data);
}

/**
 * @description 月报统计
 */
export function monthReport(data: object) {
    return request.get('attendance/month_report', data);
}

/**
 * @description 我的统计
 */
export function clockInfo(data: object) {
    return request.get('attendance/clock/info', data);
}

/**
 * @description 团队统计
 */
export function teamStatistics(data: object) {
    return request.get('attendance/team/statistics', data);
}
/**
 * @description 获取外勤列表接口
 */
export function externalStatistics(data: object) {
    return request.get('attendance/team/external_statistics', data);
}
/**
 * @description 团队上下班明细
 */
export function teamCommuteDetails(data: object) {
    return request.get('attendance/team/commute_details', data);
}
/**
 * @description 获取团队考勤明细接口
 */
export function teamAttendanceStatistics(data: object) {
    return request.get('attendance/team/attendance_statistics', data);
}
/**
 * @description 获取团队假勤明细接口
 */
export function teamAttendanceLeaveStatistics(data: object) {
    return request.get('attendance/team/leave_statistics', data);
}
/**
 * @description 团队加班明细接口
 */
export function teamAttendanceOvertimeStatistics(data: object) {
    return request.get('attendance/team/overtime_statistics', data);
}
/**
 * @description 获取月报统计
 */
export function monthStatistics(data: object) {
    return request.get('attendance/month_statistics', data);
}

/**
 * @description 获取个人加班明细
 */
export function overtimeStatistics(data: object) {
    return request.get('attendance/person/overtime_statistics', data);
}

/**
 * @description 获取个人假勤明细
 */
export function leaveStatistics(data: object) {
    return request.get('attendance/person/leave_statistics', data);
}

/**
 * @description 获取个人考勤明细
 */
export function personAttendanceStatistics(data: object) {
    return request.get('attendance/person/attendance_statistics', data);
}

/**
 * @description 获取个人信息
 */
export function attendanceUserMsg(id: string) {
    return request.get(`attendance/user/${id}`);
}

/**
 * @description 考勤审批
 */
export function attendanceApproveApi(data: object) {
    return request.get(`attendance/approve`, data);
}

/**
 * 
 * @param data 查询参数
 * @returns 排版列表
 */
export function attendanceScheduleListApi(data: object) {
    return request.get(`attendance/arrange`, data);
}

/**
 * @description 获取考勤组列表
 */
export function attendanceGroupListApi(data: object) {
    return request.get(`attendance/group`, data);
}

/**
 * @description 添加考勤排班
 */
export function attendanceScheduleAddApi(data: object) {
    return request.post(`attendance/arrange`, data);
}

/**
 * @description 获取考勤排班信息
 */
export function attendanceScheduleInfoApi(groupId: string, data: object) {
    return request.get(`attendance/arrange/info/${groupId}`, data);
}

/**
 * @description 获取排班班次
 */
export function attendanceScheduleShiftApi(data: object) {
    return request.get(`attendance/shift`, data);
}

/**
 * @description 获取排班班次信息
 */
export function attendanceScheduleShiftInfoApi(shiftId: string) {
    return request.get(`attendance/shift/info/${shiftId}`);
}

/**
 * @description 获取排班周期列表
 */
export function attendanceScheduleCycleListApi(groupId: string) {
    return request.get(`attendance/cycle/list/${groupId}`);
}

/**
 * @description 保存排班
 */
export function attendanceScheduleSaveApi(groupId: string, data: object) {
    return request.put(`attendance/arrange/${groupId}`, data);
}

/**
 * @description 删除排班班次
 */
export function attendanceScheduleShiftDelApi(shiftId: string) {
    return request.delete(`attendance/shift/${shiftId}`);
}

/**
 * @description 新增排班班次
 */
export function attendanceScheduleShiftAddApi(data: object) {
    return request.post(`attendance/shift`, data);
}

/**
 * @description 修改排班班次
 */
export function attendanceScheduleShiftEditApi(shiftId: string, data: object) {
    return request.put(`attendance/shift/${shiftId}`, data);
}

/**
 * @description 保存考勤组关联的排班班次
 */
export function saveAttendanceGroupShiftApi(groupId: string, data: object) {
    return request.post(`attendance/group/shifts/${groupId}`, data);
}

/**
 * @description 获取排班菜单权限
 */
export function scheduleMenuPermissionApi() {
    return request.get(`attendance/arrange/is_admin`);
}

