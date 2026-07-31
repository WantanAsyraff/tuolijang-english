<?php

declare(strict_types=1);


namespace App\Http\Dao\Approve;

use App\Http\Model\Approve\ApproveApply;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 申请记录表
 * Class ApproveApplyDao.
 */
class ApproveApplyDao extends BaseDao
{
    use ListSearchTrait;

    public function getApplyList(array $where = [], array $field = ['*'], int $page = 0, int $limit = 0, $sort = null, array $with = [])
    {
        return $this->search($where)->select($field)
            ->when($page && $limit, function ($query) use ($page, $limit) {
                $query->forPage($page, $limit);
            })->when($sort = sort_mode($sort), function ($query) use ($sort) {
                if (is_array($sort)) {
                    foreach ($sort as $k => $v) {
                        if (is_numeric($k)) {
                            $query->orderByDesc($v);
                        } else {
                            $query->orderBy($k, $v);
                        }
                    }
                } else {
                    $query->orderByDesc($sort);
                }
            })->with($with)->get();
    }

    /**
     * 设置模型.
     * @return mixed|string
     */
    protected function setModel()
    {
        return ApproveApply::class;
    }
}
