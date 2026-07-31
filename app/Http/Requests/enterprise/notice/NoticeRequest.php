<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\notice;

use App\Http\Requests\ApiValidate;

/**
 * 企业通知
 * Class RankRequest.
 */
class NoticeRequest extends ApiValidate
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
            'title.required'   => '请填写通知标题',
            'cate_id.required' => '请选择通知分类',
            'cate_id.integer'  => '分类只能为数字',
            'content.required' => '请填写通知内容',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'title'   => 'required',
            'cate_id' => 'required|integer',
            'content' => 'required',
        ];
    }
}
