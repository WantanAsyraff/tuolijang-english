<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\user;

use App\Http\Requests\ApiValidate;

/**
 * Class EnterpriseUserDailyReplyRequest.
 */
class EnterpriseUserDailyReplyRequest extends ApiValidate
{
    /**
     * @var bool
     */
    public $authValidate = true;

    /**
     * @return array
     */
    public function message()
    {
        return [
            'content.required' => '请填写评论内容',
        ];
    }

    /**
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'content' => 'required',
        ];
    }
}
