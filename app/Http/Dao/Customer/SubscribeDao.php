<?php

declare(strict_types=1);


namespace App\Http\Dao\Customer;

use App\Http\Model\Customer\Subscribe;
use App\Http\Model\Customer\Customer;
use crmeb\basic\BaseDao;
use crmeb\basic\BaseModel;
use crmeb\traits\dao\JoinSearchTrait;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\HigherOrderWhenProxy;

/**
 * 关注Dao.
 */
class SubscribeDao extends BaseDao
{
    use ListSearchTrait;
    use JoinSearchTrait;

    /**
     * 设置模型.
     *
     * @return BaseModel
     * @throws BindingResolutionException
     */
    public function getModel(bool $need = false)
    {
        if ($need) {
            return $this->getJoinModel('cid', 'cid');
        }
        return parent::getModel($need);
    }

    /**
     * 关联客户查询.
     * @param mixed $where
     * @return BaseModel|HigherOrderWhenProxy|mixed
     * @throws BindingResolutionException
     */
    public function clientSearch($where, ?bool $authWhere = null)
    {
        return $this->getJoinModel('eid', 'id')->where($this->getFiled('subscribe_status'), $where['subscribe_status'])
            ->where(function ($query) use ($where) {
                $uidField          = $this->getFiled('uid', $this->aliasB);
                $subscribeUidField = $this->getFiled('uid');
                if (is_array($where['uid'])) {
                    $query->whereIn($subscribeUidField, $where['uid']);
                } else {
                    $query->where($uidField, $where['uid']);
                }
                $query->where($subscribeUidField, $where['subscribe_uid']);
                $query->whereNull($this->getFiled('deleted_at', $this->aliasB));
            })->distinct($this->getFiled('id'));
    }

    /**
     * @return string
     */
    protected function setModel()
    {
        return Subscribe::class;
    }

    protected function setModelB(): string
    {
        return Customer::class;
    }
}
