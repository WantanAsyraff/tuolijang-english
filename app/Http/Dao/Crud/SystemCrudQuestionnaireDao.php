<?php

namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudQuestionnaire;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;

/**
 * Class SystemCrudQuestionnaireDao.
 */
class SystemCrudQuestionnaireDao extends BaseDao
{

    use ListSearchTrait;

    /**
     * @return string
     */
    protected function setModel()
    {
        return SystemCrudQuestionnaire::class;
    }
}
