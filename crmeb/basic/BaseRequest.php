<?php

declare(strict_types=1);


namespace crmeb\basic;

use crmeb\exceptions\ApiRequestException;
use crmeb\traits\RequestHelpTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request基类
 * Class BaseRequest.
 */
abstract class BaseRequest extends FormRequest
{
    use RequestHelpTrait;

    /**
     * 程序自定义业务错误码
     *
     * @var int
     */
    protected $code = 400;

    /**
     * http状态码
     *
     * @var int
     */
    protected $statusCode = 200;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    public function messages()
    {
        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ApiRequestException(
            $validator->errors()->first(),
            $this->code,
            null,
            $this->statusCode
        );
    }

    /**
     * 设置request.
     * @return $this
     */
    protected function request()
    {
        return $this;
    }
}
