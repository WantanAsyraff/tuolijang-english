<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\approve;

use App\Http\Requests\ApiValidate;

/**
 * 审批配置验证
 * Class ApproveRequest.
 */
class ApproveReplyRequest extends ApiValidate
{
    /**
     * 自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'apply_id.required' => '缺少审核申请ID',
            'content.required'  => '缺少评价内容',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'apply_id' => 'required',
            'content'  => 'required',
        ];
    }
}
