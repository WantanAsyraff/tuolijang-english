<?php

declare(strict_types=1);


namespace App\Http\Requests\system;

use App\Http\Requests\ApiValidate;

/**
 * 字典验证器.
 */
class DIctTypeRequest extends ApiValidate
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
        'name'  => 'required|max:64',
        'ident' => 'required|max:64|alpha_dash',
    ];

    /**
     * 提示.
     * @var string[]
     */
    protected $message = [
        'name.required'    => '请填写空间名称',
        'name.max'         => '空间名称长度不能大于64个字符',
        'ident.required'   => '请填写字典标识',
        'ident.max'        => '字典标识长度不能大于64个字符',
        'ident.alpha_dash' => '字典标识只能由字母下划线组合',
    ];
}
