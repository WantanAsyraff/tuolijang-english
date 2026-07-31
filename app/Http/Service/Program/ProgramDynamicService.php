<?php

declare(strict_types=1);


namespace App\Http\Service\Program;

use App\Constants\ProgramEnum\DynamicEnum;
use App\Http\Dao\Program\ProgramDynamicDao;
use App\Http\Service\Admin\AdminService;
use crmeb\basic\BaseService;
use crmeb\traits\service\ResourceServiceTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 项目动态
 * Class ProgramDynamicService.
 */
class ProgramDynamicService extends BaseService
{
    use ResourceServiceTrait;

    public $dao;

    public function __construct(ProgramDynamicDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 添加动态
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addLog(int $types, int $actionType, array $data): void
    {
        $this->dao->create(array_merge($data, [
            'types'       => $types,
            'action_type' => $actionType,
            'operator'    => app()->get(AdminService::class)->value($data['uid'], 'name'),
        ]));
    }

    /**
     * 列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getList(array $where, array $field = ['*'], $sort = 'created_at', array $with = []): array
    {
        [$page, $limit] = $this->getPageValue();
        $list           = $this->dao->getList($where, $field, $page, $limit, $sort, $where['types'] == DynamicEnum::TASK ? array_merge($with, ['task']) : $with);
        $count          = $this->dao->count($where);

        return $where['types'] == DynamicEnum::TASK && $where['relation_id'] > 0 ?
            $this->listData($list, $count, ['total_count' => $this->dao->count(['relation_id' => $where['relation_id'], 'types' => $where['types']])]) :
            $this->listData($list, $count);
    }
}
