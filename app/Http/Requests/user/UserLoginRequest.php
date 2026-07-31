<?php

declare(strict_types=1);


namespace App\Http\Requests\user;

use App\Http\Requests\ApiValidate;
use crmeb\utils\Regex;

/**
 * Class UserLoginRequest.
 */
class UserLoginRequest extends ApiValidate
{
    /**
     * 场景.
     * @var string[][]
     */
    protected $scene = [
        'login'                => ['account', 'password', 'captcha'],
        'phoneLogin'           => ['phone', 'verification_code'],
        'updatePassword'       => ['phone', 'password', 'password_confirm'],
        'commonUpdatePassword' => ['phone', 'password', 'verification_code'],
        'phone'                => ['phone'],
        'registerUser'         => ['phone', 'verification_code', 'password', 'password_confirm'],
    ];

    private const PHONE_NUMBER = '/^\+?[1-9]\d{6,14}$/';

    /**
     * Normalize phone input before validation and before verification_code uses request('phone').
     */
    public function check(array $data = [], array $rules = [])
    {
        $this->normalizeRequestPhone();

        return parent::check($data, $rules);
    }

    private function normalizeRequestPhone(): void
    {
        $phone = request()->input('phone');
        if (is_string($phone) || is_numeric($phone)) {
            request()->merge(['phone' => preg_replace('/[\s()\-]/', '', (string) $phone)]);
        }
    }

    /**
     * 验证规则.
     * @return array
     */
    public function rules()
    {
        return [
            'account'  => 'required|min:5|max:50|alpha_num',
            'password' => [
                'required',
                'min:' . sys_config('login_password_length', 5),
                'regex:' . Regex::loginRegex(),
            ],
            'password_confirm' => [
                'required',
                'regex:' . Regex::loginRegex(),
                'password_confirm_api:' . request('password'),
            ],
            'captcha'           => 'required|captcha_api:' . request('key') . ',user',
            'phone'             => ['regex:' . self::PHONE_NUMBER],
            'verification_code' => 'required|numeric|verification_api:' . request('phone'),
        ];
    }

    /**
     * 错误提示.
     * @return array
     */
    public function message()
    {
        return [
            'account.required'                      => '账号必须填写',
            'account.min'                           => '账号长度不正确',
            'account.max'                           => '账号长度超出限制',
            'account.alpha_num'                     => '账号不正确',
            'password.required'                     => '密码必须填写',
            'password.min'                          => '密码长度不正确,最少' . sys_config('login_password_length', 5) . '个字符',
            'password.regex'                        => '输入的密码不符合规则,请输入' . get_password_message() . '的密码组合',
            'password_confirm.required'             => '确认密码必须填写',
            'password_confirm.regex'                => '确认密码不符合规则,请输入' . get_password_message() . '的密码组合',
            'password_confirm.password_confirm_api' => '两次输入的密码不一致',
            'captcha.required'                      => '验证码必须填写',
            'captcha.captcha_api'                   => '验证码不正确',
            'phone.regex'                           => '请输入正确的手机号码',
            'verification_code.required'            => '短信验证码必须填写',
            'verification_code.numeric'             => '短信验证码必须为数字',
            'verification_code.size'                => '短信验证码必须为6位',
            'verification_code.verification_api'    => '请输入正确的短信验证码',
        ];
    }
}
