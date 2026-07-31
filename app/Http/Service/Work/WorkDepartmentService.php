<?php

declare(strict_types=1);


namespace App\Http\Service\Work;

use App\Http\Dao\Work\WorkDepartmentDao;
use crmeb\basic\BaseModel;
use crmeb\basic\BaseService;
use crmeb\services\wechat\config\WorkConfig;
use crmeb\services\wechat\Work;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 *  部门
 * Class WorkDepartmentService.
 */
class WorkDepartmentService extends BaseService
{
    public function __construct(WorkDepartmentDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 同步企业微信部门和成员信息.
     */
    public function authDepartment()
    {
        $res        = app(Work::class)->getDepartment();
        $department = $res['department'] ?? [];
        $config     = app(WorkConfig::class);
        $corpId     = $config->getCorpId();
        if (! $corpId) {
            throw $this->exception('请先配置企业微信ID');
        }
        foreach ($department as $item) {
            $item['sort']              = $item['order'] ?? '';
            $item['name_en']           = $item['name_en'] ?? '';
            $item['department_leader'] = json_encode($item['department_leader'] ?? []);
            $item['department_id']     = $item['id'] ?? '';
            unset($item['order'], $item['id']);
            $this->dao->updateOrCreate(['department_id' => $item['department_id'], 'corp_id' => $corpId], $item + ['corp_id' => $corpId]);
        }
    }

    /**
     * 创建部门.
     * @return BaseModel|mixed|Model
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function createDepartment(array $payload)
    {
        $corpId = $payload['ToUserName'];
        $where  = ['corp_id' => $corpId, 'department_id' => $payload['Id']];

        $departmentInfo = app()->get(Work::class)->getDepartmentInfo($payload['Id']);

        if ($this->dao->exists($where)) {
            return $this->updateDepartment($corpId, (int) $payload['Id'], $departmentInfo['department']['name']);
        }
        return $this->dao->create([
            'corp_id'       => $corpId,
            'department_id' => $payload['Id'] ?? '',
            'name'          => $departmentInfo['department']['name'] ?? '',
            'parentid'      => $departmentInfo['department']['parentid'] ?? '',
            'sort'          => $payload['order'] ?? '',
            'create_time'   => time(),
        ]);
    }

    /**
     * 更新部门.
     * @return mixed
     */
    public function updateDepartment(string $corpId, int $departmentId, string $name)
    {
        if (! $name) {
            $departmentInfo = app()->get(Work::class)->getDepartmentInfo($departmentId);
            $name           = $departmentInfo['department']['name'] ?? '';
        }

        return $this->dao->update(['corp_id' => $corpId, 'department_id' => $departmentId], ['name' => $name]);
    }

    /**
     * 删除部门.
     * @return mixed
     */
    public function deleteDepartment(string $corpId, int $departmentId)
    {
        return $this->dao->delete(['corp_id' => $corpId, 'department_id' => $departmentId]);
    }
}
