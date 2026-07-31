<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\client;

use App\Http\Requests\ApiValidate;

class ContractResourceRequest extends ApiValidate
{
    /**
     * 验证场景.
     * @var string[][]
     */
    protected $scene = [
        'store'  => ['cid', 'content'],
        'update' => ['content'],
    ];

    /**
     * 错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'cid.required'     => '请填写订单ID',
            'cid.numeric'      => '订单ID必须为数字',
            'cid.gt'           => '请填写订单ID',
            'content.required' => '请填写资料描述',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'cid'     => 'required|numeric|gt:0',
            'content' => 'required|max:5000',
        ];
    }
}
