<?php

declare(strict_types=1);


namespace App\Http\Service\Program;

use App\Http\Dao\Program\ProgramTaskMemberDao;
use crmeb\basic\BaseService;
use crmeb\traits\service\ResourceServiceTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 项目任务成员
 * Class ProgramTaskMemberService.
 */
class ProgramTaskMemberService extends BaseService
{
    use ResourceServiceTrait;

    public $dao;

    public function __construct(ProgramTaskMemberDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 处理成员.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function handleMember(array $members, int $taskId): void
    {
        $data = [];
        if ($taskId) {
            foreach ($this->dao->column(['task_id' => $taskId], 'uid', 'id') as $key => $item) {
                $data[$item] = $key;
            }
        }

        foreach ($members as $member) {
            if (isset($data[$member])) {
                unset($data[$member]);
                continue;
            }
            $this->dao->create(['task_id' => $taskId, 'uid' => $member]);
        }

        if ($data) {
            $this->dao->delete(['task_id' => $taskId, 'id' => $data]);
        }
    }
}
