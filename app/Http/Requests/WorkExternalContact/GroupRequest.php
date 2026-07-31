<?php

declare(strict_types=1);


namespace App\Http\Requests\WorkExternalContact;

use App\Http\Requests\ApiRequest;

/**
 * 分组请求验证.
 */
class GroupRequest extends ApiRequest
{
    /**
     * 验证规则.
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '请填写分组名称',
        ];
    }
}
