<?php

declare(strict_types=1);


namespace App\Http\Service\Crud;

use App\Http\Dao\Crud\SystemCrudRoleDao;
use crmeb\basic\BaseService;

/**
 * 实体数据权限.
 */
class SystemCrudRoleService extends BaseService
{
    public function __construct(SystemCrudRoleDao $dao)
    {
        $this->dao = $dao;
    }

    public function saveRoles($roleId, $data)
    {
        if (!$roleId) {
            throw $this->exception('缺少权限ID');
        }
        foreach ($data as $value) {
            $this->dao->updateOrCreate(['role_id' => $roleId, 'crud_id' => $value['crud_id']], [
                'role_id'        => $roleId,
                'crud_id'        => $value['crud_id'],
                'created'        => (int)$value['created'],
                'reade'          => (int)$value['reade'],
                'reade_frame'    => json_encode($value['reade_frame']),
                'updated'        => (int)$value['updated'],
                'updated_frame'  => json_encode($value['updated_frame']),
                'deleted'        => (int)$value['deleted'],
                'deleted_frame'  => json_encode($value['deleted_frame']),
                'transfer'       => (int)($value['transfer'] ?? 0),
                'transfer_frame' => json_encode($value['transfer_frame'] ?? []),
                'share'          => (int)($value['share'] ?? 0),
                'share_frame'    => json_encode($value['share_frame'] ?? []),
            ]);
        }
        return true;
    }
}
