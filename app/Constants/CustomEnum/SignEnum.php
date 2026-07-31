<?php

declare(strict_types=1);


namespace App\Constants\CustomEnum;

/**
 * 合同签约业务
 */
final class SignEnum extends CustomEnum
{
    /**
     * 合同签约审批拒绝.
     */
    public const OPERATE_APPROVE_REJECT = -1;

    /**
     * 新增合同签约.
     */
    public const OPERATE_CREATE = 1;

    /**
     * 合同签约审批通过.
     */
    public const OPERATE_APPROVED = 2;

    /**
     * 合同签约完成.
     */
    public const OPERATE_COMPLETE = 3;

    /**
     * 拒绝合同签约.
     */
    public const OPERATE_REJECT = 4;

    /**
     * 签约已过期
     */
    public const OPERATE_EXPIRED = 5;

    /**
     * 签约已撤销
     */
    public const OPERATE_REVOKE = 6;

    /**
     * 合同状态:-1、审批驳回;0、待处理;1、待审核;2、待签约;3、已签约;4、已拒绝;5、已过期;6、已撤销;.
     */
    public const STATUS_APPROVE_REJECT = -1;

    public const STATUS_WAIT_HANDLE = 0;

    public const STATUS_WAIT_APPROVE = 1;

    public const STATUS_WAIT_SIGN = 2;

    public const STATUS_WAIT_SIGN_COMPLETE = 3;

    public const STATUS_WAIT_SIGN_COMPLETE_REJECT = 4;

    public const STATUS_WAIT_SIGN_EXPIRED = 5;

    public const STATUS_WAIT_SIGN_REVOKE = 6;

    /**
     * 状态：合同状态：INIT=合同创建，PART=合同签署中，ALL=合同签署完成，REJECT=合同拒签，CANCEL=合同撤回，WILLEXPIRE=合同即将过期，DEADLINE=合同流签(合同过期)，RELIEVED=已解除，INVALID=合同已失效，EXCEPTION=合同异常.
     */
    public const FLOW_STATUS_INIT = 'INIT';

    public const FLOW_STATUS_PART = 'PART';

    public const FLOW_STATUS_ALL = 'ALL';

    public const FLOW_STATUS_REJECT = 'REJECT';

    public const FLOW_STATUS_CANCEL = 'CANCEL';

    public const FLOW_STATUS_WILLEXPIRE = 'WILLEXPIRE';

    public const FLOW_STATUS_DEADLINE = 'DEADLINE';

    public const FLOW_STATUS_RELIEVED = 'RELIEVED';

    public const FLOW_STATUS_INVALID = 'INVALID';

    public const FLOW_STATUS_EXCEPTION = 'EXCEPTION';

    /**
     * 签署状态：PENDING= 待签署；ACCEPT=已签署；REJECT=拒绝；DEADLINE=过期没人处理；CANCEL=流程已撤回；STOP=流程已终止；WAITPICKUP=待领取；FILLPENDING=待填写；FILLACCEPT=填写完成；FORWARD=已转他人处理；RELIEVED=已解除；FILLREJECT=拒绝填写；EXCEPTION=异常.
     */
    public const APPROVE_STATUS_PENDING = 'PENDING';

    public const APPROVE_STATUS_ACCEPT = 'ACCEPT';

    public const APPROVE_STATUS_REJECT = 'REJECT';

    public const APPROVE_STATUS_DEADLINE = 'DEADLINE';

    public const APPROVE_STATUS_CANCEL = 'CANCEL';

    public const APPROVE_STATUS_STOP = 'STOP';

    public const APPROVE_STATUS_WAITPICKUP = 'WAITPICKUP';

    public const APPROVE_STATUS_FILLPENDING = 'FILLPENDING';

    public const APPROVE_STATUS_FILLACCEPT = 'FILLACCEPT';

    public const APPROVE_STATUS_FORWARD = 'FORWARD';

    public const APPROVE_STATUS_RELIEVED = 'RELIEVED';

    public const APPROVE_STATUS_FILLREJECT = 'FILLREJECT';

    public const APPROVE_STATUS_EXCEPTION = 'EXCEPTION';
}
