<?php

declare(strict_types=1);


namespace App\Http\Requests\system;

use App\Http\Requests\ApiValidate;

/**
 * Class SystemRoleRequest.
 */
class SystemRoleRequest extends ApiValidate
{
    /**
     * 验证规则.
     * @var string[]
     */
    protected $rules = [
        'role_name'          => 'required',
        'rules'               => 'required',
        'status'              => 'integer',
        'module_permission'   => 'array',
    ];

    /**
     * 设置错误提示.
     * @var string[]
     */
    protected $message = [
        'role_name.required' => '角色名称必须填写',
        'rules.required'     => '至少选择一个权限',
        'status.integer'     => '状态值必须为数字',
    ];
}
