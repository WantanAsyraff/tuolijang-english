<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\WorkExternalContact;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Requests\WorkExternalContact\GroupRequest;
use App\Http\Service\WorkExternalContact\WorkMassMessagingTempGroupService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 企微群发素材分组.
 */
#[Prefix('ent/work/mass_messaging_temp_group')]
#[Resource('/', false, except: ['show'], names: [
    'index'   => '获取群发素材分组列表接口',
    'create'  => '获取新增群发素材分组表单',
    'store'   => '保存群发素材分组接口',
    'edit'    => '获取群发素材分组信息接口',
    'update'  => '修改群发素材分组接口',
    'destroy' => '删除群发素材分组接口',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class WorkMassMessagingTempGroupController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(WorkMassMessagingTempGroupService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    protected function getRequestClassName(): string
    {
        return GroupRequest::class;
    }

    protected function getSearchField(): array
    {
        return [
            ['name', '', 'name_like'],
        ];
    }

    protected function getRequestFields(): array
    {
        return [
            ['name', ''],
            ['sort', 0],
        ];
    }
}
