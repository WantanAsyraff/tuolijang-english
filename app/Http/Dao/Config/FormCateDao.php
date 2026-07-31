<?php

declare(strict_types=1);


namespace App\Http\Dao\Config;

use App\Http\Model\Config\FormCate;
use crmeb\basic\BaseDao;
use crmeb\interfaces\ResourceDaoInterface;
use crmeb\traits\dao\ListSearchTrait;

/**
 * 自定义表单分类 Dao
 * Class FormCateDao.
 */
class FormCateDao extends BaseDao implements ResourceDaoInterface
{
    use ListSearchTrait;

    protected function setModel(): string
    {
        return FormCate::class;
    }
}
