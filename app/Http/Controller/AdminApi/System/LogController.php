<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\System;

use App\Http\Contract\System\LogInterface;
use App\Http\Controller\AdminApi\AuthController;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 日志.
 */
#[Prefix('ent/system/log')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class LogController extends AuthController
{
    public function __construct(LogInterface $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 获取列表.
     * @return mixed
     */
    #[Get('/', '系统日志列表')]
    public function index()
    {
        $where = $this->request->getMore([
            ['user_name', ''],
            ['path', ''],
            ['time', ''],
            ['event_name', ''],
            ['entid', 1],
        ]);
        return $this->success($this->service->getLogPageList($where));
    }
}
