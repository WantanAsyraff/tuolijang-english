<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\storage;

use App\Http\Requests\ApiValidate;
use Illuminate\Validation\Rule;

class StorageRecordRequest extends ApiValidate
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
            'types' => [
                'required', Rule::in([0, 1, 2, 3, 4, 5]),
            ],
            'storage' => 'required',
        ];
    }

    /**
     * 提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'types.required'   => '请填写操作类型',
            'types.in'         => '操作类型不正确',
            'storage.required' => '缺少物资参数',
        ];
    }
}
