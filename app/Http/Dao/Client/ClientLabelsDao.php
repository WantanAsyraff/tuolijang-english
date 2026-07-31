<?php

declare(strict_types=1);


namespace App\Http\Dao\Client;

use App\Http\Model\Client\ClientLabels;
use crmeb\basic\BaseDao;

/**
 * 客户标签关联.
 */
class ClientLabelsDao extends BaseDao
{
    public function countByLabelIds(array $labelIds): int
    {
        if (! $labelIds) {
            return 0;
        }

        return $this->getModel()->whereIn('label_id', $labelIds)->count();
    }

    public function deleteByLabelIds(array $labelIds): int
    {
        if (! $labelIds) {
            return 0;
        }

        return $this->getModel()->whereIn('label_id', $labelIds)->delete();
    }

    protected function setModel()
    {
        return ClientLabels::class;
    }
}
