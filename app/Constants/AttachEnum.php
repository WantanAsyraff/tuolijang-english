<?php

declare(strict_types=1);


namespace App\Constants;

/**
 * 附件关联类型.
 */
final class AttachEnum
{
    // 默认模块
    public const RELATION_TYPE_DEFAULT = '';

    // 汇报模块
    public const RELATION_TYPE_DAILY = 'daily';

    // 回款/续费模块
    public const RELATION_TYPE_BILL = 'bill';

    // 订单附件
    public const RELATION_TYPE_CONTRACT = 'contract';

    // 客户附件
    public const RELATION_TYPE_CUSTOMER = 'customer';

    // 客户跟进附件
    public const RELATION_TYPE_FOLLOW = 'follow';

    // 发票附件
    public const RELATION_TYPE_INVOICE = 'invoice';

    // 打卡附件
    public const RELATION_TYPE_ATTENDANCE_CLOCK = 'attendance_clock';

    // 联系人
    public const RELATION_TYPE_LIAiSON = 'liaison';

    // 项目
    public const RELATION_TYPE_PROGRAM = 'program';

    // 财务模块
    public const RELATION_TYPE_FINANCE = 'finance';

    // 线索模块
    public const RELATION_TYPE_CLUE = 'clue';

    // 商机模块
    public const RELATION_TYPE_ODDS = 'odds';

    // 产品模块
    public const RELATION_TYPE_PRODUCT = 'product';

    // 审批评价
    public const RELATION_TYPE_APPROVE_REPLY = 'approve_reply';

    // 日程评价
    public const RELATION_TYPE_SCHEDULE_REPLY = 'schedule_reply';

    // 合同签约文件
    public const RELATION_TYPE_SIGN = 'contract_sign';

    // 合同签约结果
    public const RELATION_TYPE_SIGN_RESULT = 'contract_sign_result';

    // 客户导入
    public const RELATION_TYPE_CUSTOMER_IMPORT = 'customer_import';

    // 模块类型
    public const RELATION_TYPE = [
        self::RELATION_TYPE_DEFAULT          => 0,
        self::RELATION_TYPE_DAILY            => 1,
        self::RELATION_TYPE_BILL             => 2,
        self::RELATION_TYPE_CONTRACT         => 3,
        self::RELATION_TYPE_CUSTOMER         => 4,
        self::RELATION_TYPE_FOLLOW           => 5,
        self::RELATION_TYPE_INVOICE          => 6,
        self::RELATION_TYPE_ATTENDANCE_CLOCK => 7,
        self::RELATION_TYPE_LIAiSON          => 8,
        self::RELATION_TYPE_PROGRAM          => 9,
        self::RELATION_TYPE_FINANCE          => 10,
        self::RELATION_TYPE_CLUE             => 11,
        self::RELATION_TYPE_ODDS             => 12,
        self::RELATION_TYPE_PRODUCT          => 13,
        self::RELATION_TYPE_APPROVE_REPLY    => 14,
        self::RELATION_TYPE_SCHEDULE_REPLY   => 15,
        self::RELATION_TYPE_SIGN             => 16,
        self::RELATION_TYPE_SIGN_RESULT      => 17,
        self::RELATION_TYPE_CUSTOMER_IMPORT  => 18,
    ];
}
