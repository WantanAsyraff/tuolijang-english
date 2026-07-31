<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Constants\CustomEnum\CustomEnum;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Customer\RecordService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 客户记录
 * Class RecordController.
 */
#[Prefix('uni/client/record')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class RecordController extends AuthController
{
    public function __construct(RecordService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 列表数据.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('/', '客户记录列表')]
    public function index(): mixed
    {
        $eid = $this->request->get('eid', 0);
        if (! $eid) {
            return $this->fail('common.empty.attrs');
        }
        $where = $this->request->getMore([
            ['eid', 0],
            ['link_type', CustomEnum::CUSTOMER],
        ]);
        return $this->success($this->service->getList($where));
    }
}
