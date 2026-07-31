<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\storage;

use App\Http\Requests\ApiValidate;
use Illuminate\Validation\Rule;

class StorageCategoryRequest extends ApiValidate
{
    /**
     * 自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 规则.
     * @return array
     */
    public function rules()
    {
        return [
            'cate_name' => 'required',
            'type'      => [
                'required', Rule::in([0, 1]),
            ],
        ];
    }

    /**
     * 提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'cate_name.required' => '请填写分类名称',
            'type.required'      => '请选择分类类型',
            'type.in'            => '分类类型不正确',
        ];
    }
}
