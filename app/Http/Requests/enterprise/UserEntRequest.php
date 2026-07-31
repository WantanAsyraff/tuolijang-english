<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise;

use App\Http\Requests\ApiValidate;
use crmeb\utils\Regex;

class UserEntRequest extends ApiValidate
{
    /**
     * 提醒.
     * @var string[]
     */
    protected $message = [
        'phone.required' => '请填写手机号',
        'phone.size'     => '请填写正确的手机号',
        'phone.regex'    => '请填写正确的手机号',
    ];

    /**
     * 规则.
     * @var array
     */
    protected function rules()
    {
        return [
            'phone' => ['required', 'size:11', 'regex:' . Regex::PHONE_NUMBER],
        ];
    }
}
