<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise;

use App\Http\Requests\ApiValidate;
use App\Http\Requests\Traits\NormalizesPhoneInput;
use crmeb\utils\Regex;

class UserEntRequest extends ApiValidate
{
    use NormalizesPhoneInput;

    public function check(array $data = [], array $rules = [])
    {
        $this->normalizePhoneInputs(['phone']);

        return parent::check($data, $rules);
    }

    /**
     * 提醒.
     * @var string[]
     */
    protected $message = [
        'phone.required' => '请填写手机号',
        'phone.max'     => '请填写正确的手机号',
        'phone.regex'    => '请填写正确的手机号',
    ];

    /**
     * 规则.
     * @var array
     */
    protected function rules()
    {
        return [
            'phone' => ['required', 'max:32', 'regex:' . Regex::PHONE_NUMBER],
        ];
    }
}
