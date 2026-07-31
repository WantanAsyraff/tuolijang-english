<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise;

use App\Http\Requests\ApiValidate;

class EntFrameRequest extends ApiValidate
{
    /**
     * 自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 提醒.
     * @var string[]
     */
    protected $message = [
        'name.required' => '组织架构名称必须填写',
    ];

    /**
     * 验证规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
