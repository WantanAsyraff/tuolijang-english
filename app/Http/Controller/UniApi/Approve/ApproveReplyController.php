<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Approve;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\enterprise\approve\ApproveReplyRequest;
use App\Http\Service\Approve\ApproveReplyService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 审核评价留言
 */
#[Prefix('uni/approve/reply')]
#[Resource('/', false, except: ['index', 'show', 'edit', 'create', 'update'], names: [
    'store'   => '保存审批评价接口',
    'destroy' => '删除审批评价接口',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ApproveReplyController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(ApproveReplyService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    protected function getRequestFields(): array
    {
        return [
            ['apply_id', ''],
            ['content', ''],
            ['files', []],
        ];
    }

    protected function getRequestClassName(): string
    {
        return ApproveReplyRequest::class;
    }

    /**
     * @return array|string[][]
     */
    protected function getSearchField(): array
    {
        return [
            ['name', '', 'name_like'],
            ['types', 0],
        ];
    }
}
