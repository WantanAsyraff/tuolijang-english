<?php

declare(strict_types=1);


namespace App\Http\Service\Other;

use App\Http\Dao\Other\TaskRunRecordDao;
use crmeb\basic\BaseModel;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

/**
 * 任务执行记录
 * Class TaskRunRecordService.
 */
class TaskRunRecordService extends BaseService
{
    /**
     * TaskRunRecordServices constructor.
     */
    public function __construct(TaskRunRecordDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 保存任务运行日志.
     * @return BaseModel|Model
     * @throws BindingResolutionException
     */
    public function saveRunLog(int $taskId, string $message, string $files = '', int $line = 0, int $status = 1)
    {
        return $this->dao->create([
            'task_id' => $taskId,
            'message' => $message,
            'line'    => $line,
            'files'   => $files,
            'status'  => $status,
        ]);
    }
}
