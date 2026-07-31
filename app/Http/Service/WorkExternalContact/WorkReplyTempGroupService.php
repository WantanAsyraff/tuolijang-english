<?php

declare(strict_types=1);


namespace App\Http\Service\WorkExternalContact;

use App\Http\Dao\WorkExternalContact\WorkReplyTempGroupDao;
use crmeb\basic\BaseService;
use crmeb\interfaces\ResourceServicesInterface;
use crmeb\services\FormService;
use crmeb\traits\service\ResourceServiceTrait;
use Illuminate\Support\Collection;

/**
 * 快捷回复分组.
 */
class WorkReplyTempGroupService extends BaseService implements ResourceServicesInterface
{
    use ResourceServiceTrait;

    protected string $saveUrl = '/ent/work/reply_temp_group';

    public function __construct(WorkReplyTempGroupDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $where, array $field = ['*'], $sort = ['sort', 'id'], array $with = []): array
    {
        return parent::getList($where, $field, $sort, $with);
    }

    public function resourceCreate(array $other = []): array
    {
        return $this->createElementForm('添加分组', $this->getFormRule(collect($other)), $this->saveUrl);
    }

    public function resourceEdit(int $id, array $other = []): array
    {
        $data = $this->dao->get($id);
        if (! $data) {
            throw $this->exception('编辑的分组不存在');
        }
        return $this->createElementForm('编辑分组', $this->getFormRule(collect($data)), $this->saveUrl . '/' . $id, 'PUT');
    }

    private function getFormRule(Collection $collect)
    {
        return [
            FormService::input('name', '分组名称', $collect->get('name', ''))->required(),
            FormService::number('sort', '排序', $collect->get('sort', 0))->min(0)->max(9999999)->required(),
        ];
    }
}
