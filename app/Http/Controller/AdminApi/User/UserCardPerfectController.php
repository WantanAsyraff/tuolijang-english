<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\User;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Service\User\UserCardPerfectService;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 完善个人信息.
 */
class UserCardPerfectController extends AuthController
{
    public function __construct(UserCardPerfectService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    public function list(): mixed
    {
        $where = $this->request->getMore([
            ['uid', $this->uuid],
            ['status', [1, 2]],
            ['total', [0, 1, -1]],
        ]);
        $data = $this->service->getList($where, ['*'], 'status', [
            'enterprise' => fn ($query) => $query->select(['id', 'enterprise_name', 'logo']),
        ]);
        return $this->success($data);
    }

    /**
     * 拒绝操作.
     * @throws BindingResolutionException
     */
    public function refuse($id): mixed
    {
        if (! $id) {
            return $this->fail('缺少邀请记录ID');
        }
        $this->service->refusePerfect($id, $this->uuid);
        return $this->success('操作成功');
    }

    public function destory() {}
}
