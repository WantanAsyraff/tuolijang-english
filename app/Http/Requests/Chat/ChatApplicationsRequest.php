<?php

declare(strict_types=1);


namespace App\Http\Requests\Chat;

use App\Http\Requests\ApiValidate;
use Illuminate\Validation\Rule;

class ChatApplicationsRequest extends ApiValidate
{
    /**
     * 验证场景.
     * @var \string[][]
     */
    protected $scene = [
        'store'  => ['name', 'info'],
        'update' => [
                'name','info', 'edit', 'status', 'member_id', 'use_limit', 'sort', 'models_id',
                'count_number', 'tables', 'content', 'tooltip_text', 'prologue_text', 'prologue_list',
                'json'
            ],
        ];

    /**
     * 错误提醒.
     * @return string[]
     */
    public function message()
    {
        return ['name.required'      => '名称必填', 'name.unique' => '名称不能重复', 'use_limit.required' => '使用频次必填',
                'models_id.required' => '模型ID必填', 'count_number.required' => '对话轮数必填',
                'tables.array'       => '数据库表名必填', 'tooltip_text.required' => '提示词必填',
                'json.required'      => '高级设置必填','info.required' => '简介不能为空'
            ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        $id = $this->route('id');
        $ru = [
            'name' => ['required', Rule::unique('chat_applications','name')->ignore($this->route('id'))->whereNull('deleted_at')],
            'content' =>['max:10000'],
        ];
        //if ($id) {
        //    $ru = array_merge($ru,[
        //        'edit' => 'required',
        //        'status' => 'required',
        //        'member_id' => 'required',
        //        'use_limit' => 'required',
        //        'sort' => 'required',
        //        'models_id' => 'required',
        //        'count_number' => 'required',
        //        'tables' => 'array',
        //        'content' => 'required',
        //        'tooltip_text' => 'required',
        //        'prologue_text' => 'required',
        //        'prologue_list' => 'required',
        //        'json' => 'required',
        //    ]);
        //}
        return $ru;
    }
}
