<?php

declare(strict_types=1);


namespace App\Http\Requests\system;

use App\Http\Requests\ApiValidate;

/**
 * Class SystemRoleRequest.
 */
class FolderShareRequest extends ApiValidate
{
    /**
     * 验证规则.
     * @var string[]
     */
    protected $rules = [
        'rule'            => 'required|array|min:1',
        'rule.*.uid'      => 'required',
        'rule.*.download' => 'in:0,1',
        'rule.*.update'   => 'in:0,1',
    ];

    /**
     * 设置错误提示.
     * @var string[]
     */
    protected $message = [
        'rule'             => '请选择用户',
        'rules.*.uid'      => '请选择分享用户',
        'rules.*.download' => '下载权限有误',
        'rules.*.update'   => '更新权限有误',
    ];
}
