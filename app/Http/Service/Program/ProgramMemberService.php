<?php

declare(strict_types=1);


namespace App\Http\Service\Program;

use App\Http\Dao\Program\ProgramMemberDao;
use crmeb\basic\BaseService;
use crmeb\traits\service\ResourceServiceTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 项目成员
 * Class ProgramMemberService.
 */
class ProgramMemberService extends BaseService
{
    use ResourceServiceTrait;

    public $dao;

    public function __construct(ProgramMemberDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 处理成员.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function handleMember(array $members, int $programId): void
    {
        $data = [];
        if ($programId) {
            foreach ($this->dao->column(['program_id' => $programId], 'uid', 'id') as $key => $item) {
                $data[$item] = $key;
            }
        }

        foreach ($members as $member) {
            if (isset($data[$member])) {
                unset($data[$member]);
                continue;
            }
            $this->dao->create(['program_id' => $programId, 'uid' => $member]);
        }

        if ($data) {
            $this->dao->delete(['program_id' => $programId, 'id' => $data]);
        }
    }

    /**
     * 获取参与项目的所有人员.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getMemberIdsByUid(int $uid): array
    {
        $programService = app()->get(ProgramService::class);
        $programIds     = array_merge(
            $programService->column(['uid_or_creator_uid' => $uid], 'id'),
            $this->dao->column(['uid' => $uid], 'program_id')
        );
        if (! $programIds) {
            return [];
        }
        return $this->dao->column(['program_id' => array_unique($programIds)], 'uid');
    }
}
