<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\trait;

use App\Http\Requests\ApiValidate;

class PromotionRequest extends ApiValidate
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
            'sort' => 'integer',
        ];
    }

    /**
     * 提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写晋升表名称',
            'name.max'      => '晋升表数据过长',
            'sort.integer'  => '排序值只能为数字',
        ];
    }
}
