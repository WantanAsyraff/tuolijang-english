<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 考勤管理工具枚举
 */
final class McpAttendanceEnum extends McpBaseEnum
{
    // ========== 考勤查询 ==========

    /**
     * 打卡记录列表
     */
    public const ATTENDANCE_CLOCK_LIST = 'attendance_clock_list';

    /**
     * 考勤统计
     */
    public const ATTENDANCE_STATISTICS = 'attendance_statistics';

    /**
     * 考勤汇总报表
     */
    public const ATTENDANCE_SUMMARY = 'attendance_summary';

    // ========== 申请审批 ==========

    /**
     * 请假/补卡申请列表
     */
    public const ATTENDANCE_APPLY_LIST = 'attendance_apply_list';

    /**
     * 请假/补卡申请详情
     */
    public const ATTENDANCE_APPLY_DETAIL = 'attendance_apply_detail';

    // ========== 排班管理 ==========

    /**
     * 排班列表
     */
    public const ATTENDANCE_SCHEDULE_LIST = 'attendance_schedule_list';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::ATTENDANCE_CLOCK_LIST => '打卡记录列表',
        self::ATTENDANCE_STATISTICS => '考勤统计',
        self::ATTENDANCE_SUMMARY => '考勤汇总报表',
        self::ATTENDANCE_APPLY_LIST => '请假/补卡申请列表',
        self::ATTENDANCE_APPLY_DETAIL => '请假/补卡申请详情',
        self::ATTENDANCE_SCHEDULE_LIST => '排班列表',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '考勤管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'attendance';
    }
}
