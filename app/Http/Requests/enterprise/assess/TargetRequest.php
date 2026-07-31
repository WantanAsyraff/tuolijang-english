<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\assess;

use App\Http\Requests\ApiValidate;

/**
 * 企业资金流水
 * Class RankRequest.
 */
class TargetRequest extends ApiValidate
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
    public function message()
    {
        return [
            'cate_id.required' => '请选择模板分类',
            'cate_id.integer'  => '请选择正确的模板分类',
            'name.required'    => '请填写模板名称',
            'content.required' => '请填写模板内容',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'cate_id' => 'required|integer',
            'name'    => 'required',
            'content' => 'required',
        ];
    }
}
