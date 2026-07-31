<?php

declare(strict_types=1);


namespace App\Http\Dao\Customer;

use App\Http\Model\Customer\Record;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\BatchSearchTrait;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 动态记录.
 */
class RecordDao extends BaseDao
{
    use ListSearchTrait;
    use BatchSearchTrait;

    /**
     * 获取最新客户记录.
     * @return Collection
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getLastRecord(array $where)
    {
        return $this->search($where)->with('latest')->groupBy('eid')->get()->pluck('latest');
    }

    protected function setModel(): string
    {
        return Record::class;
    }
}
