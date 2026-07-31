<?php

declare(strict_types=1);


namespace App\Http\Requests\WorkExternalContact;

use App\Http\Requests\ApiRequest;

/**
 * 群发请求验证.
 */
class MassMessagingRequest extends ApiRequest
{
    /**
     * 验证规则.
     * @return array
     */
    public function rules()
    {
        return [
            'send_uid'  => 'required',
            'is_all'    => 'required',
            'is_modify' => 'required',
            'is_timed'  => 'required',
        ];
    }

    public function messages()
    {
        return [
            'send_uid.required'  => '请选择群发员工',
            'is_all.required'    => '请选择客户范围',
            'is_modify.required' => '请选择是否可调整发送范围',
            'is_timed.required'  => '请选择群发时间',
        ];
    }
}
