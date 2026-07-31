<?php

declare(strict_types=1);


namespace App\Http\Requests\Crud;

use App\Http\Requests\ApiValidate;

/**
 * 统计看板验证
 * Class SystemCrudDashboardRequest.
 */
class SystemCrudDashboardRequest extends ApiValidate
{
    /**
     * 自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 错误提醒.
     * @return string[]
     */
    public function message(): array
    {
        return [
            'name.required' => '请填写名称',
            'name.max'      => '名称长度超出限制',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|max:100',
        ];
    }
}
