<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\News;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Requests\category\CategoryRequest;
use App\Http\Service\News\NewsCateService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\CateControllerTrait;
use crmeb\traits\ResourceControllerTrait;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 通知公告分类.
 */
#[Prefix('ent/notice')]
#[Resource('category', false, names: [
    'index'   => '通知公告分类列表',
    'create'  => '通知公告分类创建表单',
    'store'   => '保存通知公告分类接口',
    'show'    => '显示隐藏公告分类接口',
    'edit'    => '通知公告分类修改表单',
    'update'  => '修改通知公告分类接口',
    'destroy' => '删除通知公告分类接口',
], parameters: ['category' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class NewsCateController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;
    use CateControllerTrait;

    public function __construct(NewsCateService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 设置Request类名.
     */
    protected function getRequestClassName(): string
    {
        return CategoryRequest::class;
    }
}
