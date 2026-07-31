<?php

declare(strict_types=1);

namespace App\Http\Dao\Open;

use App\Http\Model\Open\OpenApiKey;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

class OpenApiKeyDao extends BaseDao
{
    use ListSearchTrait;
    /**
     * 设置模型.
     * @return string
     */
    protected function setModel()
    {
        return OpenApiKey::class;
    }

}
