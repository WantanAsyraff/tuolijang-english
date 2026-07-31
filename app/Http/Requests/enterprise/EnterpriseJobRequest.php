<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise;

use App\Http\Requests\ApiValidate;

/**
 * 企业岗位
 * Class EnterpriseJobRequest.
 */
class EnterpriseJobRequest extends ApiValidate
{
    /**
     * 自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required'    => '请填写岗位名称',
            'cate_id.required' => '请选择职级类别',
            'rank_id.required' => '请选择职级',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'name'    => 'required',
            'cate_id' => 'required',
            'rank_id' => 'required',
        ];
    }
}
