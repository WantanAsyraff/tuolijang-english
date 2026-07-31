<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\trait;

use App\Http\Requests\ApiValidate;

class HayGroupRequest extends ApiValidate
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
            'name' => 'required|max:50',
            'list' => 'required|array',
        ];
    }

    /**
     * 提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写评估表名称',
            'name.max'      => '评估表数据过长',
            'list.required' => '请设置评估数据',
            'list.array'    => '请设置评估数据',
        ];
    }
}
