<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Schedule;

use App\Constants\ScheduleEnum;
use App\Http\Contract\Schedule\ScheduleInterface;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\user\ScheduleRequest;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 待办日程.
 */
#[Prefix('uni/schedule')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ScheduleController extends AuthController
{
    public function __construct(ScheduleInterface $schedule)
    {
        parent::__construct();
        $this->service = $schedule;
    }

    /**
     * 日程类型列表.
     */
    #[Get('types', '获取日程类型列表')]
    public function typeList(): mixed
    {
        return $this->success($this->service->typeList(auth('admin')->id(), ['id', 'name', 'color', 'info', 'is_public']));
    }

    /**
     * 新建日程类型表单.
     */
    #[Get('type/create', '新建日程类型表单')]
    public function createType(): mixed
    {
        return $this->success($this->service->typeCreateForm());
    }

    /**
     * 新建日程类型.
     */
    #[Post('type/save', '新建日程类型')]
    public function saveType(): mixed
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['color', ''],
            ['info', ''],
        ]);
        $res = $this->service->saveType(auth('admin')->id(), $data);
        return $res ? $this->success('添加成功') : $this->fail('添加失败');
    }

    /**
     * 修改日程类型表单.
     * @return mixed
     */
    #[Get('type/edit/{id}', '修改日程类型表单')]
    public function editType($id)
    {
        if (! $id) {
            return $this->fail('缺少日程类型ID');
        }
        return $this->success($this->service->typeEditForm((int) $id, auth('admin')->id()));
    }

    /**
     * 修改日程类型.
     * @return mixed
     */
    #[Put('type/update/{id}', '修改日程类型')]
    public function updateType($id)
    {
        if (! $id) {
            return $this->fail('缺少日程类型ID');
        }
        $data = $this->request->postMore([
            ['name', ''],
            ['color', ''],
            ['info', ''],
        ]);
        $res = $this->service->updateType((int) $id, auth('admin')->id(), $data);
        return $res ? $this->success('修改成功') : $this->fail('修改失败');
    }

    /**
     * 删除日程类型.
     * @return mixed
     */
    #[Delete('type/delete/{id}', '删除日程类型')]
    public function deleteType($id)
    {
        if (! $id) {
            return $this->fail('缺少日程类型ID');
        }
        $res = $this->service->deleteType((int) $id, auth('admin')->id());
        return $res ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 获取日程列表.
     * @return mixed
     */
    #[Post('index', '获取日程列表')]
    public function index()
    {
        [$start, $end, $cid, $period] = $this->request->postMore([
            ['start_time', ''],
            ['end_time', ''],
            ['cid', []],
            ['period', 1],
        ], true);
        return $this->success($this->service->scheduleList(auth('admin')->id(), $this->entId, $start, $end, $cid, (int) $period));
    }

    /**
     * 新建日程保存.
     */
    #[Post('store', '新建日程保存')]
    public function store(ScheduleRequest $request): mixed
    {
        $request->scene('create')->check();
        $data = $this->request->postMore([
            ['title', ''],
            ['member', []], // 参与人
            ['content', ''],
            ['cid', 0],
            ['color', ''],
            ['remind', 0],
            ['remind_time', ''],
            ['repeat', 0],
            ['period', 0],
            ['rate', 0],
            ['days', []],
            ['all_day', 0],
            ['start_time', ''],
            ['end_time', ''],
            ['fail_time', null],
        ]);
        $this->service->saveSchedule(auth('admin')->id(), $this->entId, $data);
        return $this->success('添加成功');
    }

    /**
     * 修改日程内容.
     * @return mixed
     * @throws ValidationException
     */
    #[Put('update/{id}', '修改日程内容')]
    public function update(ScheduleRequest $request, $id)
    {
        if (! $id) {
            return $this->fail('缺少日程ID');
        }
        $request->scene('update')->check();
        $data = $this->request->postMore([
            ['title', ''],
            ['member', []], // 参与人
            ['content', ''],
            ['cid', 0],
            ['color', ''],
            ['remind', 0],
            ['remind_time', ''],
            ['repeat', 0],
            ['period', 0],
            ['rate', 0],
            ['days', []],
            ['all_day', 0],
            ['start_time', ''],
            ['end_time', ''],
            ['fail_time', null],
            ['type', ScheduleEnum::CHANGE_ALL],
            ['start', ''],
            ['end', ''],
        ]);
        $this->service->updateSchedule($this->entId, (int) $id, $data, auth('admin')->id());
        return $this->success('修改成功');
    }

    /**
     * 修改日程状态
     */
    #[Put('status/{id}', '修改日程状态')]
    public function status(ScheduleRequest $request, $id)
    {
        if (! $id) {
            return $this->fail('缺少日程ID');
        }
        $request->scene('status')->check();
        [$status, $start_time, $end_time] = $this->request->postMore([
            ['status', 0],
            ['start', 0],
            ['end', 0],
        ], true);
        $res = $this->service->updateStatus((int) $id, auth('admin')->id(), $this->entId, (int) $status, [$start_time, $end_time]);
        return $res ? $this->success('操作成功') : $this->fail('操作失败');
    }

    /**
     * 日程详情.
     */
    #[Get('info/{id}', '获取日程信息')]
    public function info($id)
    {
        if (! $id) {
            return $this->fail('缺少日程ID');
        }
        $where = $this->request->getMore([
            ['start_time', ''],
            ['end_time', ''],
        ]);
        $field = ['id', 'uid', 'cid', 'color', 'title', 'content', 'all_day', 'start_time', 'end_time', 'period', 'days', 'rate', 'remind as is_remind', 'link_id', 'fail_time'];
        $info  = $this->service->scheduleInfo((int) $id, auth('admin')->id(), $field, $where);
        return $this->success($info);
    }

    /**
     * 删除日程.
     * @throws ValidationException
     */
    #[Delete('delete/{id}', '删除日程')]
    public function delete(ScheduleRequest $request, $id): mixed
    {
        if (! $id) {
            return $this->fail('缺少日程ID');
        }
        $request->scene('delete')->check();
        $data = $this->request->postMore([
            ['type', ScheduleEnum::CHANGE_ALL],
            ['start', ''],
            ['end', ''],
        ]);
        $this->service->deleteSchedule(auth('admin')->id(), $this->entId, (int) $id, $data);
        return $this->success('删除成功');
    }

    #[Post('count', '获取日程数量')]
    public function count()
    {
        [$start, $end, $cid, $period] = $this->request->postMore([
            ['start_time', ''],
            ['end_time', ''],
            ['cid', []],
            ['period', 1],
        ], true);
        $data = $this->service->scheduleCount(auth('admin')->id(), $this->entId, $start, $end, $cid, (int) $period);
        return $this->success($data);
    }

    /**
     * 评价列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('replys', '日程评价列表')]
    public function replays()
    {
        $where = $this->request->getMore([
            ['schedule_id', 0, 'pid'],
            ['time', '', 'time_zone'],
        ]);
        $data = $this->service->replays($where);
        return $this->success($data);
    }

    /**
     * 保存评价.
     * @return mixed
     * @throws BindingResolutionException
     */
    #[Post('reply/save', '保存日程评价')]
    public function save_reply()
    {
        $data = $this->request->postMore([
            ['schedule_id', 0, 'pid'],
            ['reply_id', 0],
            ['content', ''],
            ['start', '', 'start_time'],
            ['end', '', 'end_time'],
            ['to_uid', ''],
            ['files', []],
        ]);
        $this->service->saveReply($this->uuid, $this->entId, $data);
        return $this->success('保存成功');
    }

    /**
     * 删除评价.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Delete('reply/del/{id}', '删除日程评价')]
    public function del_reply($id)
    {
        $this->service->delReply((int) $id, $this->uuid, $this->entId);
        return $this->success('删除成功');
    }

    /**
     * 获取日程列表(按日期).
     * @return mixed
     */
    #[Post('date_lst', '获取日程列表')]
    public function date_list()
    {
        [$start, $end, $cid, $period] = $this->request->postMore([
            ['start_time', ''],
            ['end_time', ''],
            ['cid', []],
            ['period', 1],
        ], true);
        return $this->success($this->service->scheduleDateList(auth('admin')->id(), $this->entId, $start, $end, $cid, (int) $period));
    }
}
