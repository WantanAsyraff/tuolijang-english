<?php

declare(strict_types=1);


namespace App\Http\Requests\Customer;

use App\Http\Requests\ApiValidate;

/**
 * 客户标签
 * Class LabelRequest.
 */
class LabelRequest extends ApiValidate
{
    /**
     * 自动.
     * @var bool
     */
    public $authValidate = true;

    /**
     * @return array|string[]
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写名称',
        ];
    }
}
