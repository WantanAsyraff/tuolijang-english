<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\bill;

use App\Http\Requests\ApiValidate;

/**
 * 资金流水分类
 * Class RankCategoryRequest.
 */
class BillCategoryRequest extends ApiValidate
{
    /**
     * 自动.
     * @var bool
     */
    public $authValidate = true;

    /**
     * @return array|string[]
     */
    public function rules()
    {
        return [
            'name'  => 'required',
            'types' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required'  => '请填写分类名称',
            'types.required' => '请选择分类类型',
        ];
    }
}
