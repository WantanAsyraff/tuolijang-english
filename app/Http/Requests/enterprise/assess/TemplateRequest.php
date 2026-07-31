<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\assess;

use App\Http\Requests\ApiValidate;

/**
 * 企业资金流水
 * Class RankRequest.
 */
class TemplateRequest extends ApiValidate
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
            'cate_id.integer' => '请选择正确的模板分类',
            'name.required'   => '请填写模板名称',
            'info.required'   => '请填写模板内容',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'cate_id' => 'integer',
            'name'    => 'required',
            'info'    => 'required',
        ];
    }
}
