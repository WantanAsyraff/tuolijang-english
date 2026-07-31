<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\storage;

use App\Http\Requests\ApiValidate;

class StorageRequest extends ApiValidate
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
            'cid'  => 'required',
            'name' => 'required',
        ];
    }

    /**
     * 提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'cid.required'  => '请选择物资分类',
            'name.required' => '请填写物资名称',
        ];
    }
}
