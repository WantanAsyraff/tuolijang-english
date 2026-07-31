<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Work;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Service\Work\WorkMemberService;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 企业微信成员管理.
 */
#[Prefix('ent/work')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class WorkMemberController extends AuthController
{
    public function __construct(WorkMemberService $services)
    {
        parent::__construct();
        $this->service = $services;
    }
}
