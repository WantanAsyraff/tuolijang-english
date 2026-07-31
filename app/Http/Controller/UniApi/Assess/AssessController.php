<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Assess;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Assess\UserAssessService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 考核记录
 * Class AssessController.
 */
#[Prefix('uni/assess')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class AssessController extends AuthController
{
    public function __construct(UserAssessService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 当前考核.
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('now', '当前绩效考核列表')]
    public function now()
    {
        return $this->success($this->service->getNowAssess($this->uuid));
    }

    /**
     * 我的考核列表.
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('mine', '我的绩效考核列表')]
    public function mine()
    {
        $where = $this->request->getMore([
            ['period', ''],
        ]);

        return $this->success($this->service->getMineAssess($where, auth('admin')->id()));
    }

    /**
     * 获取考核详情.
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('info/{id}', '获取考核详情')]
    public function info($id)
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }

        return $this->success($this->service->getAssessInfo((int) $id, (int) $this->entId));
    }

    /**
     * 修改绩效考核（启用后）.
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Put('update/{id}', '修改绩效考核（启用后）')]
    public function update($id)
    {
        [$data, $types, $mark, $hide_mark, $space, $target] = $this->request->postMore([
            ['data', []],
            ['types', 0],
            ['mark', ''],
            ['hide_mark', ''],
            ['space', []],
            ['target', []],
        ], true);
        $this->service->save((int) $id, $this->uuid, $this->entId, $data, (int) $types, $mark, $hide_mark, $space, $target, true);
        return $this->success('common.update.succ');
    }
}
