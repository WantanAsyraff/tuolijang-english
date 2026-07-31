<?php

declare(strict_types=1);


namespace App\Http\Requests\Customer;

use App\Http\Requests\ApiValidate;

/**
 * 产品请求验证
 * Class ProductRequest.
 */
class ProductRequest extends ApiValidate
{
    /**
     * 错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写产品名称',
            'name.max'     => '产品名称长度超出限制',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'name' => 'required|max:255',
        ];
    }
}
