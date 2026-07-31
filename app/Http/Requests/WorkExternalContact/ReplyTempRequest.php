<?php

declare(strict_types=1);


namespace App\Http\Requests\WorkExternalContact;

use App\Http\Requests\ApiRequest;

/**
 * 分组请求验证.
 */
class ReplyTempRequest extends ApiRequest
{
    /**
     * 验证规则.
     * @return array
     */
    public function rules()
    {
        return [
            'group_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'group_id.required' => '请选择内容分组',
        ];
    }
}
