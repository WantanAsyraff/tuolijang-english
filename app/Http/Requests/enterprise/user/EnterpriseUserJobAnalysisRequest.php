<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\user;

use App\Http\Requests\ApiValidate;

/**
 * 工作分析
 * Class EnterpriseUserJobAnalysisRequest.
 */
class EnterpriseUserJobAnalysisRequest extends ApiValidate
{
    /**
     * 场景.
     * @var \string[][]
     */
    protected $scene = [
        'data' => 'require',
    ];

    /**
     * 规则.
     * @return array
     */
    public function rules()
    {
        return [
            'data' => 'required',
        ];
    }

    /**
     * 提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'data.required' => '请填写分析内容',
        ];
    }
}
