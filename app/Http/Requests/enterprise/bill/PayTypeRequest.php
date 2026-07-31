<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\bill;

use App\Http\Requests\ApiValidate;

/**
 * 支付方式
 * Class PayTypeRequest.
 */
class PayTypeRequest extends ApiValidate
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:30',
            // 'ident' => 'required|max:50',
            'sort' => 'integer',
        ];
    }

    /**
     * 设置错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写支付方式名称',
            'name.max'      => '支付方式名称长度超出限制',
            // 'ident.required' => '请填写支付方式标识',
            // 'ident.max'      => '支付方式标识长度超出限制',
            'sort.integer' => '排序值只能为数字',
        ];
    }
}
