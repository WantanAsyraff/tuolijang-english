<?php

declare(strict_types=1);


namespace App\Http\Requests\category;

use App\Http\Requests\ApiRequest;

/**
 * Class SystemAttachCateRequest.
 */
class CategoryRequest extends ApiRequest
{
    /**
     * 验证规则.
     * @return array
     */
    public function rules()
    {
        return [
            'cate_name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'cate_name.required' => '请填写分类名称',
        ];
    }
}
