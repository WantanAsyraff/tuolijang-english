<?php

declare(strict_types=1);


namespace App\Constants\CustomEnum;

use MyCLabs\Enum\Enum;

/**
 * 发票相关枚举.
 */
final class InvoiceEnum extends Enum
{
    /**
     * 发票状态：待审核.
     */
    public const STATUS_AUDIT = 0;

    /**
     * 发票状态：开票审核通过(待开票).
     */
    public const STATUS_APPROVED = 1;

    /**
     * 发票状态：开票审核拒绝.
     */
    public const STATUS_REJECT = 2;

    /**
     * 发票状态：撤回开票审核.
     */
    public const STATUS_REVOKE = 3;

    /**
     * 发票状态：发票已作废.
     */
    public const STATUS_CANCEL = -1;

    /**
     * 发票状态：申请作废中.
     */
    public const STATUS_APPLY_CANCEL = 4;

    /**
     * 发票状态：已开票.
     */
    public const STATUS_INVOICED = 5;
}
