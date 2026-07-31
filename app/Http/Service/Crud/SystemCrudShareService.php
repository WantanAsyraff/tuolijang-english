<?php

declare(strict_types=1);


namespace App\Http\Service\Crud;

use App\Http\Dao\Crud\SystemCrudShareDao;
use crmeb\basic\BaseService;

/**
 * 数据共享记录.
 */
class SystemCrudShareService extends BaseService
{
    public function __construct(SystemCrudShareDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 分享列表.
     * @return array|mixed
     */
    public function shareList(int $crudId, int $dataId)
    {
        $ids = app()->get(SystemCrudDataShareService::class)->column(['crud_id' => $crudId, 'data_id' => $dataId], 'share_id');
        if (! $ids) {
            $ids = [0];
        }
        return $this->getList(
            where: ['crud_id' => $crudId, 'ids' => $ids],
            sort: 'id',
            with: [
                'user'    => fn ($q) => $q->select(['id', 'name']),
                'operate' => fn ($q) => $q->select(['id', 'name']),
            ]
        );
    }
}
