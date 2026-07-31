<?php

declare(strict_types=1);


namespace App\Http\Requests\system;

use App\Http\Requests\ApiValidate;

class SystemPaytypeRequest extends ApiValidate
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  => 'required',
            'ident' => 'required',
        ];
    }

    /**
     * 设置错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required'  => '请填写支付方式名称',
            'ident.required' => '请填写支付方式标识',
        ];
    }
}
