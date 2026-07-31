<?php

declare(strict_types=1);


namespace App\Http\Requests\system;

use App\Http\Requests\ApiValidate;

/**
 * 协议
 * Class SystemAgreementRequest.
 */
class SystemAgreementRequest extends ApiValidate
{
    /**
     * 设置规则.
     * @var array
     */
    protected $rules = [
        'content' => 'required',
    ];

    /**
     * 错误提示.
     * @var string[]
     */
    protected $message = [
        'content.required' => '请填写协议内容',
    ];
}
