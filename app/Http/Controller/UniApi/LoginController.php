<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi;

use App\Constants\CacheEnum;
use App\Constants\CommonEnum;
use App\Constants\UserEnum;
use App\Http\Contract\Common\CommonInterface;
use App\Http\Requests\common\AccountLoginRequest;
use App\Http\Requests\user\UserLoginRequest;
use App\Http\Service\Admin\AdminService;
use crmeb\services\CaptchaService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Mews\Captcha\Captcha;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 工作台登录控制器
 * Class LoginController.
 */
#[Prefix('uni/user')]
class LoginController extends AuthController
{
    public function __construct(AdminService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 图像验证码
     * @return array|mixed
     * @throws \Exception
     */
    public function captcha(Captcha $captcha)
    {
        return $this->success($captcha->create('user', true));
    }

    /**
     * 滑块图像验证码
     * @return array|mixed
     */
    public function sliderCaptcha(CaptchaService $captcha): mixed
    {
        try {
            $service = $captcha->getCaptchaService();
            $data    = $service->get();
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
        return $this->success($data);
    }

    /**
     * 一次验证
     * @return array
     */
    public function check(CaptchaService $captcha)
    {
        try {
            $data    = $captcha->validate();
            $service = $captcha->getCaptchaService();
            $service->check($data['token'], $data['pointJson']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
        return $this->success([]);
    }

    /**
     * 二次验证
     */
    public function verification(CaptchaService $captcha)
    {
        try {
            $data    = $captcha->validate();
            $service = $captcha->getCaptchaService();
            $service->verification($data['token'], $data['pointJson']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
        return $this->success([]);
    }

    /**
     * 账号登录.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ValidationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('login', '账号密码登录')]
    public function login(AccountLoginRequest $request)
    {
        $request->scene('login_uni')->check();

        [$account, $password, $client, $mac, $clientId] = $request->postMore([
            ['account', ''],
            ['password', ''],
            ['client', $request->header('Form-type')],
            ['mac', ''],
            ['client_id', ''],
        ], true);
        $this->service->checkUserAuth($account);
        return $this->success($this->service->login($account, $password, $client, $mac, $clientId, origin: CommonEnum::ORIGIN_UNI, isSingle: UserEnum::SINGLE_LOGIN));
    }

    /**
     * 短信验证码登录.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ValidationException
     * @throws \ReflectionException
     */
    #[Post('login/phone', '手机号登录')]
    public function phoneLogin(UserLoginRequest $request): mixed
    {
        $request->scene('phoneLogin')->check();
        [$phone, $client, $mac, $clientId] = $request->postMore([
            ['phone', ''],
            ['client', $request->header('Form-type')],
            ['mac', ''],
            ['client_id', ''],
        ], true);
        $this->service->checkUserAuth($phone);
        if (! $this->service->exists(['phone' => $phone])) {
            $this->service->register($phone, md5(microtime() . $phone));
        }
        return $this->success($this->service->phoneLogin($phone, $client, $mac, $clientId, origin: CommonEnum::ORIGIN_UNI, isSingle: UserEnum::SINGLE_LOGIN));
    }

    /**
     * 修改密码
     * @return mixed
     * @throws BindingResolutionException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    #[Put('update/password', '修改密码')]
    public function savePassword(UserLoginRequest $request)
    {
        $request->scene('updatePassword')->check();

        [$phone, $password] = $request->postMore([
            ['phone', ''],
            ['password', ''],
        ], true);

        $this->service->password(auth('admin')->id(), $phone, $password);

        return $this->success('common.update.succ');
    }

    /**
     * 绑定用户扫码关联.
     */
    #[Post('scan/user', '扫码关联用户', ['auth.admin', 'ent.auth', 'ent.log'])]
    public function scan_user(): mixed
    {
        [$key] = $this->request->postMore([
            ['key', ''],
        ], true);
        $res = $this->service->scanWithUser($key, auth('admin')->id());
        if ($res) {
            return $this->success('扫码成功');
        }
        return $this->fail('扫码失败');
    }

    /**
     * 确认扫码登录.
     */
    #[Post('scan/login', '确认扫码登录', ['auth.admin', 'ent.auth', 'ent.log'])]
    public function scan_login(): mixed
    {
        [$key, $status] = $this->request->postMore([
            ['key', ''],
            ['status', ''],
        ], true);
        $this->service->scanLogin(auth('admin')->id(), $key, $status);
        return $this->success('操作成功');
    }

    /**
     * 退出登录.
     * @return mixed
     */
    #[Get('logout', '退出登录', ['auth.admin', 'ent.auth', 'ent.log'])]
    public function logout()
    {
        $this->service->logout();
        return $this->success('退出成功');
    }

    /**
     * 刷新登录凭证.
     */
    #[Post('token/refresh', '刷新登录凭证')]
    public function refreshToken(): mixed
    {
        $refreshToken = (string) $this->request->post('refresh_token', '');
        if (! $refreshToken) {
            return $this->fail('缺少刷新TOKEN');
        }

        return $this->success($this->service->refreshToken($refreshToken), tips: 0);
    }

    /**
     * 获得短信发送key.
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Get('verify/code', '获得短信发送key')]
    public function verifyCode()
    {
        $unique = password_hash(uniqid(more_entropy: true), PASSWORD_BCRYPT);
        Cache::tags([CacheEnum::TAG_OTHER])->add('sms.key.' . $unique, 0, 300);
        $time = sys_config('verify_expire_time', 1);
        return $this->success(['key' => $unique, 'expire_time' => $time]);
    }

    /**
     * 验证码短信发送
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ValidationException
     */
    #[Post('verify/send', '发送验证码')]
    public function verify(UserLoginRequest $request, CommonInterface $common)
    {
        $request->scene('phone')->check();
        [$phone, $key, $types, $from] = $request->postMore([
            ['phone', ''],
            ['key', ''],
            ['types', 0],
            ['from', 0], // 来源：默认为登录操作
        ], true);
        if ($from) {
            if ($this->service->exists(['phone' => $phone])) {
                if ($this->service->value(['phone' => $phone], 'status') == UserEnum::USER_LOCKING) {
                    return $this->fail('该手机号已被锁定');
                }
            } elseif (! sys_config('registration_open', 0)) {
                return $this->fail('短信发送失败，未注册的手机号');
            }
        }
        if ($common->smsVerifyCode($phone, $key, $types)) {
            return $this->success('短信发送成功');
        }
        return $this->fail('短信发送失败');
    }
}
