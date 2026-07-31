<?php

declare(strict_types=1);


namespace App\Task\message;

use App\Constants\AssessEnum;
use App\Constants\NoticeEnum;
use App\Events\SystemMessageEvent;
use App\Http\Service\Admin\AdminService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 考核目标发布提醒
 * Class UserAssessReleaseRemind.
 */
class UserAssessReleaseRemind extends Task
{
    public function __construct(protected $entId, protected $testUid, protected $data) {}

    public function handle()
    {
        try {
            $userInfo = app()->get(AdminService::class)->get($this->testUid, with: ['frame'])?->toArray();
            if (! $userInfo) {
                return true;
            }
            event(new SystemMessageEvent(
                type: NoticeEnum::ASSESS_PUBLISH_TYPE,
                params: [
                    '考核名称'     => $this->data['name'],
                    '考核开始时间' => $this->data['start_time'],
                    '考核结束时间' => $this->data['end_time'],
                    '考核人'       => $userInfo['name'] ?? '',
                    '考核人部门'   => $userInfo['frame']['name'] ?? '',
                ],
                receiverIds: (int) $this->testUid,
                other: ['id' => $this->data['id']],
                linkId: $this->data['id'],
                linkStatus: $this->data['status']
            ));
            Task::deliver(new StatusChangeTask(AssessEnum::LINK_NOTICE, AssessEnum::ASSESS_SELF_APPRAISAL, $this->entId, $this->data['id']));
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
