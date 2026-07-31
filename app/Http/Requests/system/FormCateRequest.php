<?php

declare(strict_types=1);


namespace App\Http\Requests\system;

use App\Http\Requests\ApiValidate;

/**
 * 表单分组验证器.
 */
class FormCateRequest extends ApiValidate
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
        'title' => 'required|max:64',
        'sort'  => 'integer|max:99999',
    ];

    /**
     * 提示.
     * @var string[]
     */
    protected $message = [
        'title.required' => '请填写分组名称',
        'title.max'      => '空间名称长度不能大于64个字符',
        'sort.integer'   => '排序只能为整数',
        'sort.max'       => '排序最大不能超过99999',
    ];
}
