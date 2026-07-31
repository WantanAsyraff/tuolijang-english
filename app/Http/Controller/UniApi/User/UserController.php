<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\User;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\user\UserRequest;
use App\Http\Service\Admin\AdminInfoService;
use App\Http\Service\Admin\AdminService;
use App\Http\Service\System\MenusService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 个人办公-用户管理
 * Class UserController.
 */
#[Prefix('uni/user')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class UserController extends AuthController
{
    public function __construct(AdminService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 获取个人用户信息.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('userInfo', '获取当前用户信息')]
    public function userInfo()
    {
        $info = $this->service->getInfo(auth('admin')->id(), ['id', 'uid', 'password', 'phone', 'name', 'avatar'], [
            'info' => fn ($query) => $query->select(['email', 'uid']),
        ]);
        if ($info['info']) {
            $info['email'] = $info['info']['email'];
        }
        return $this->success($info);
    }

    /**
     * 修改个人用户信息.
     * @return mixed
     * @throws ValidationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Put('userInfo', '修改当前用户信息')]
    public function update(UserRequest $request)
    {
        [$avatar, $realName, $email, $phone, $password, $passwordConfirm, $verificationCode] = $this->request->postMore([
            ['avatar', ''],
            ['real_name', ''],
            ['email', ''],
            ['phone', ''],
            ['password', ''],
            ['password_confirm', ''],
            ['verification_code', ''],
        ], true);
        $data = $infoData = [];
        if ($email) {
            $request->scene('update_email')->check();
            $infoData['email'] = $email;
        }
        if ($phone && $verificationCode) {
            $request->scene('update_phone')->check();
            $data['phone'] = $phone;

            if ($this->service->exists(['phone' => $phone])) {
                return $this->fail('手机号已注册,请更换没有注册的手机号');
            }
        }
        if ($password && $passwordConfirm) {
            $request->scene('update_password')->check();
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            $data['is_init']  = 0;
        }
        if ($realName) {
            $data['name'] = $realName;
        }
        if ($avatar) {
            $data['avatar'] = $avatar;
        }
        if ($data) {
            $this->service->update(auth('admin')->id(), $data);
        }
        if ($infoData) {
            app()->get(AdminInfoService::class)->update(auth('admin')->id(), $infoData);
        }
        return $this->success('common.update.succ', $data);
    }

    /**
     * 获取个人菜单.
     * @return mixed
     * @throws BindingResolutionException
     */
    #[Get('menus', '获取当前用户基础信息')]
    public function menus(MenusService $menusService)
    {
        $userId = auth('admin')->user()->is_admin ? 'all' : auth('admin')->id();
        $menu   = $menusService->getUserMenusForUni((string) $userId);
        $roles  = $menusService->getUserButtons($userId);
        return $this->success(compact('menu', 'roles'));
    }

    /**
     * 获取登录用户信息.
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Get('info', '获取当前用户基础信息')]
    public function info(AdminService $service)
    {
        return $this->success($service->loginInfo());
    }

    /**
     * 组织架构字母索引数据.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Get('tree', '组织架构字母索引数据')]
    public function cardLang(): mixed
    {
        return $this->success($this->service->getLangUser(['types' => [1, 2, 3]], [
            'job' => fn ($query) => $query->select(['id', 'name', 'describe']),
        ]));
    }
}
