<?php

declare(strict_types=1);


namespace App\Constants\Crud;

use MyCLabs\Enum\Enum;

/**
 * 低代码：触发器.
 */
final class CrudTriggerEnum extends Enum
{
    /**
     * 触发动作：创建时.
     */
    public const TRIGGER_CREATED = 'create';

    /**
     * 触发动作：更新时.
     */
    public const TRIGGER_UPDATED = 'update';

    /**
     * 触发动作：删除时.
     */
    public const TRIGGER_DELETED = 'delete';

    /**
     * 触发动作：审批通过时.
     */
    public const TRIGGER_APPROVED = 'approve_success';

    /**
     * 触发动作：审批撤回时.
     */
    public const TRIGGER_REVOKE = 'approve_revoke';

    /**
     * 触发动作：审批提交时.
     */
    public const TRIGGER_SAVED = 'approve_create';

    /**
     * 触发动作：审批驳回时.
     */
    public const TRIGGER_REJECT = 'approve_reject';

    /**
     * 触发动作：定时任务
     */
    public const TRIGGER_TIMER = 'timer';
}
