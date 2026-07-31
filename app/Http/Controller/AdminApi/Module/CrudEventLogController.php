<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Module;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Middleware\AuthAdmin;
use App\Http\Middleware\AuthEnterprise;
use App\Http\Middleware\LogEnterprise;
use App\Http\Service\Crud\SystemCrudEventLogService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('ent/crud')]
#[Middleware([AuthAdmin::class, AuthEnterprise::class, LogEnterprise::class])]
class CrudEventLogController extends AuthController
{
    public function __construct(SystemCrudEventLogService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * @return mixed
     * @throws BindingResolutionException
     */
    #[Get('event/log')]
    public function index()
    {
        $where = $this->request->getMore([
            ['name', ''],
            ['crud_id', ''],
        ]);

        return $this->success($this->service->getLogList($where));
    }
}
