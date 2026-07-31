<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\user;

use App\Http\Requests\ApiValidate;

/**
 * 工作报告
 * Class EnterpriseUserDailyRequest.
 */
class EnterpriseUserDailyRequest extends ApiValidate
{
    /**
     * 验证规则.
     * @return array|string[]
     */
    public function rules()
    {
        return [
            'finish' => 'required',
            // 'plan'    => 'required|array',
            'members' => 'required|array',
        ];
    }

    /**
     * 错误提醒.
     * @return array
     */
    public function message()
    {
        return [
            'finish.required'  => '请填写工作总结',
            'plan.required'    => '请填写工作计划',
            'members.required' => '请选择汇报人',
            'members.array'    => '请选择正确的汇报人',
        ];
    }
}
