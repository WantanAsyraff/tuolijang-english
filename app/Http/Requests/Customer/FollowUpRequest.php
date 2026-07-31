<?php

declare(strict_types=1);


namespace App\Http\Requests\Customer;

use App\Http\Requests\ApiValidate;

/**
 * 客户跟踪记录
 * Class ClientRequest.
 */
class FollowUpRequest extends ApiValidate
{
    /**
     * 验证场景.
     * @var string[][]
     */
    protected $scene = [
        'store'  => ['eid', 'content', 'types'],
        'update' => ['content', 'types'],
    ];

    /**
     * 错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'eid.required'     => '请填写关联ID',
            'content.required' => '请填写内容',
            'types.required'   => '请选择跟进类型',
            'types.integer'    => '跟进类型错误',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'eid'     => 'required',
            'content' => 'required',
            'types'   => 'required|integer',
        ];
    }
}
