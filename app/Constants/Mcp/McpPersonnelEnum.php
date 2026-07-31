<?php

declare(strict_types=1);


namespace App\Constants\Mcp;

/**
 * MCP 人员管理工具枚举
 */
final class McpPersonnelEnum extends McpBaseEnum
{
    /**
     * ==================== 人员查询工具 ====================
     */

    /**
     * 查询人员列表
     */
    public const PERSONNEL_LIST = 'personnel_list';

    /**
     * 获取人员详情
     */
    public const PERSONNEL_DETAIL = 'personnel_detail';

    /**
     * 搜索人员
     */
    public const PERSONNEL_SEARCH = 'personnel_search';

    /**
     * 当前登录用户身份
     */
    public const WHO_AM_I = 'who_am_i';

    /**
     * 获取我的直属下级
     */
    public const MY_SUBORDINATES = 'my_subordinates';

    /**
     * ==================== 组织架构工具 ====================
     */

    /**
     * 获取组织架构树
     */
    public const ORG_TREE = 'org_tree';

    /**
     * 获取部门详情
     */
    public const ORG_DEPARTMENT_DETAIL = 'org_department_detail';

    /**
     * 获取部门下的人员
     */
    public const ORG_DEPARTMENT_USERS = 'org_department_users';

    /**
     * 获取部门列表
     */
    public const ORG_DEPARTMENT_LIST = 'org_department_list';

    /**
     * ==================== 统计工具 ====================
     */

    /**
     * 人员统计概览
     */
    public const STATS_OVERVIEW = 'personnel_stats_overview';

    /**
     * 部门人员分布统计
     */
    public const STATS_DEPARTMENT_DISTRIBUTION = 'personnel_stats_department_distribution';

    /**
     * ==================== 权限工具 ====================
     */

    /**
     * 检查数据权限
     */
    public const CHECK_DATA_PERMISSION = 'check_data_permission';

    /**
     * 获取我的数据权限范围
     */
    public const MY_DATA_SCOPE = 'my_data_scope';

    /**
     * 工具名称映射
     */
    public const TOOL_NAMES = [
        self::PERSONNEL_LIST => '查询人员列表',
        self::PERSONNEL_DETAIL => '获取人员详情',
        self::PERSONNEL_SEARCH => '搜索人员',
        self::WHO_AM_I => '当前登录用户身份',
        self::MY_SUBORDINATES => '获取我的直属下级',
        self::ORG_TREE => '获取组织架构树',
        self::ORG_DEPARTMENT_DETAIL => '获取部门详情',
        self::ORG_DEPARTMENT_USERS => '获取部门人员',
        self::ORG_DEPARTMENT_LIST => '获取部门列表',
        self::STATS_OVERVIEW => '人员统计概览',
        self::STATS_DEPARTMENT_DISTRIBUTION => '部门人员分布统计',
        self::CHECK_DATA_PERMISSION => '检查数据权限',
        self::MY_DATA_SCOPE => '获取我的数据权限范围',
    ];

    /**
     * 获取模块名称
     */
    public static function getModuleName(): string
    {
        return '人员管理';
    }

    /**
     * 获取模块标识
     */
    public static function getModuleKey(): string
    {
        return 'personnel';
    }
}
