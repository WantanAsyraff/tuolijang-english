<?php

declare(strict_types=1);


namespace App\Http\Service\User;

use App\Http\Dao\Company\CompanyUserChangeDao;
use crmeb\basic\BaseService;
use crmeb\interfaces\ResourceServicesInterface;
use crmeb\traits\service\ResourceServiceTrait;

/**
 * 人事异动.
 */
class UserChangeService extends BaseService implements ResourceServicesInterface
{
    use ResourceServiceTrait;

    public $dao;

    public function __construct(CompanyUserChangeDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $where, array $field = ['*'], $sort = 'updated_at', array $with = ['oFrame', 'nFrame', 'oPosition', 'nPosition', 'card']): array
    {
        return parent::getList($where, $field, $sort, $with);
    }

    public function resourceCreate(array $other = []): array
    {
        return [];
    }

    public function resourceSave(array $data) {}

    public function resourceEdit(int $id, array $other = []): array
    {
        return [];
    }

    public function resourceUpdate($id, array $data)
    {
        return [];
    }

    public function resourceDelete($id, ?string $key = null)
    {
        return [];
    }
}
