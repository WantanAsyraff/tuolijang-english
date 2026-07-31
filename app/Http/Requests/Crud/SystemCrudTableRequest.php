<?php

declare(strict_types=1);


namespace App\Http\Requests\Crud;

use App\Http\Requests\ApiValidate;

class SystemCrudTableRequest extends ApiValidate
{
    /**
     * @var bool
     */
    public $authValidate = true;

    /**
     * @return string[]
     * @email 136327134@qq.com
     * @date 2024/2/27
     */
    public function rules()
    {
        return [
            'view_search'                      => 'array',
            'view_search.rule.*.field_name'    => 'required',
            'view_search.rule.*.field_name_en' => 'required',
            'view_search.rule.*.operator'      => 'required',
        ];
    }

    /**
     * @return string[]
     * @email 136327134@qq.com
     * @date 2024/2/27
     */
    public function message()
    {
        return [
            'view_search.array'                         => '视图数据类型应为数组',
            'view_search.rule.*.field_name.required'    => '缺少字段名',
            'view_search.rule.*.field_name_en.required' => '缺少字段',
            'view_search.rule.*.operator.required'      => '缺少搜索条件',
        ];
    }
}
