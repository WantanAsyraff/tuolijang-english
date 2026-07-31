<?php

declare(strict_types=1);


namespace App\Http\Dao\Crud;

use App\Http\Model\Crud\SystemCrudSeniorSearch;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\TogetherSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

class SystemCrudSeniorSearchDao extends BaseDao
{
    use TogetherSearchTrait;

    /**
     * @return int
     * @throws BindingResolutionException
     */
    public function destroy($id)
    {
        return $this->getModel(false)::destroy($id);
    }

    protected function setModel()
    {
        return SystemCrudSeniorSearch::class;
    }
}
