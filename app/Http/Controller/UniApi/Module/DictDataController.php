<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Module;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Config\DictDataService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 字典数据.
 */
#[Prefix('uni/dict/data')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class DictDataController extends AuthController
{
    public function __construct(DictDataService $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    /**
     * 字典树形数据.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('tree', '字典树形数据')]
    public function tree()
    {
        $where = $this->request->getMore([
            ['name', '', 'name_like'],
            ['types', '', 'type_name'],
            ['type_id', ''],
            ['level', ''],
            ['pid', ''],
            ['status', ''],
            ['isCityShow', ''],
        ]);
        return $this->success($this->service->getTreeData($where));
    }

    /**
     * 字典数据 id name 格式.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('select', '字典树形数据')]
    public function select()
    {
        $where = $this->request->getMore([
            ['types', '', 'type_name'],
        ]);
        return $this->success($this->service->getIdNameByTypeName($where));
    }
}
