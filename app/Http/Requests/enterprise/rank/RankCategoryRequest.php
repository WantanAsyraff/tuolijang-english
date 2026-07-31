<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\rank;

use App\Http\Requests\ApiValidate;

/**
 * Class RankCategoryRequest.
 */
class RankCategoryRequest extends ApiValidate
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
            'name' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写职级类型名称',
        ];
    }
}
