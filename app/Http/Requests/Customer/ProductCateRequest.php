<?php

declare(strict_types=1);


namespace App\Http\Requests\Customer;

use App\Http\Requests\ApiValidate;

/**
 * 分类验证器.
 */
class ProductCateRequest extends ApiValidate
{
    /**
     * 开启自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 规则.
     * @var string[]
     */
    protected $rules = [
        'name' => 'required|max:255',
    ];

    /**
     * 提示.
     * @var string[]
     */
    protected $message = [
        'name.required' => '请填写分类名称',
        'name.max'      => '分类名称长度不能大于255个字符',
    ];
}
