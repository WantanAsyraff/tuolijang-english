<?php

declare(strict_types=1);


namespace App\Http\Dao\Other;

use App\Http\Model\Company\Assist;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 辅助Dao
 * Class AssistDao.
 */
class AssistDao extends BaseDao
{
    use ListSearchTrait;

    /**
     * 插入数据.
     * @return bool
     * @throws BindingResolutionException
     */
    public function insert(array $data)
    {
        return $this->getModel(false)->insert($data);
    }

    /**
     * 设置模型.
     */
    protected function setModel(): string
    {
        return Assist::class;
    }
}
