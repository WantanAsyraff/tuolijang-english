<?php

declare(strict_types=1);


namespace App\Constants;

use MyCLabs\Enum\Enum;

final class CompanyEnum extends Enum
{
    /**
     * 企业状态：正常.
     */
    private const COMPANY_STATUS_NORMAL = 1;

    /**
     * 企业状态：待审核.
     */
    private const COMPANY_STATUS_EXAMINE = 0;

    /**
     * 企业状态：锁定.
     */
    private const COMPANY_STATUS_LOCKING = -1;
}
