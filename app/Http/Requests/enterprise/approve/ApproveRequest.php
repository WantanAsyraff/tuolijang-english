<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\approve;

use App\Http\Requests\ApiValidate;

/**
 * 审批配置验证
 * Class ApproveRequest.
 */
class ApproveRequest extends ApiValidate
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
            'baseConfig.required'    => '缺少基础配置信息',
            'formConfig.required'    => '缺少表单配置信息',
            'processConfig.required' => '缺少流程配置信息',
            'ruleConfig.required'    => '缺少规则配置信息',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'baseConfig'    => 'required',
            'formConfig'    => 'required',
            'processConfig' => 'required',
            'ruleConfig'    => 'required',
        ];
    }
}
