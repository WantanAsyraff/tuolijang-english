<?php

declare(strict_types=1);


namespace App\Http\Requests\system;

use App\Http\Requests\ApiValidate;

/**
 * Class SystemRoleRequest.
 */
class EnterpriseFolderShareRequest extends ApiValidate
{
    /**
     * 验证规则.
     * @var string[]
     */
    protected $rules = [
        'name' => 'required|max:32',
    ];

    /**
     * 设置错误提示.
     * @var string[]
     */
    protected $message = [
        'name.required' => '请填写空间名称',
        'name.max'      => '空间名称长度不能大于32位',
    ];

    protected $scene = [
        'add'    => ['name'],
        'update' => [],
    ];
}
