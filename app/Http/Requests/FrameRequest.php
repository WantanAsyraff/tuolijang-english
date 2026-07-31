<?php

declare(strict_types=1);


namespace App\Http\Requests;

/**
 * 组织架构数据验证
 */
class FrameRequest extends ApiValidate
{
    protected $scene = [
        'create' => ['path', 'name', 'role_id'],
        'update' => ['name', 'role_id'],
    ];

    /**
     * 提醒.
     * @var string[]
     */
    protected $message = [
        'path.required'    => '请选择父级部门',
        'name.required'    => '请填写部门名称',
        'role_id.required' => '请选择角色',
        'role_id.integer'  => '请选择正确的角色',
        'role_id.gt'       => '请选择角色',
    ];

    /**
     * 验证规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'path'    => 'required',
            'name'    => 'required',
            'role_id' => 'required|integer|gt:0',
        ];
    }
}
