<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Customer;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Requests\Customer\FollowUpRequest;
use App\Http\Service\Customer\FollowUpService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 客户跟进记录
 * Class FollowUpController.
 */
#[Prefix('ent/client/follow')]
#[Resource('/', false, except: ['create', 'show', 'edit'], names: [
    'index'   => '获取客户跟进列表',
    'store'   => '保存客户跟进接口',
    'update'  => '修改客户跟进接口',
    'destroy' => '删除客户跟进接口',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class FollowUpController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(FollowUpService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 搜索字段.
     * @return array|string[][]
     */
    protected function getSearchField(): array
    {
        return [
            ['eid', ''],
            ['status', ''],
            ['name', '', 'name_like'],
            ['time', ''],
            ['view_search', ''],
            ['link_type', ''],
        ];
    }

    /**
     * 设置.
     */
    protected function getRequestClassName(): string
    {
        return FollowUpRequest::class;
    }

    /**
     * 提取字段.
     */
    protected function getRequestFields(): array
    {
        return [
            ['eid', ''],
            ['content', ''],
            ['attach_ids', []],
            ['types', 0],
            ['time', ''],
            ['follow_id', 0],
            ['link_type', ''],
        ];
    }
}
