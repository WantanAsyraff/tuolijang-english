<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Report;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Requests\enterprise\user\EnterpriseUserDailyReplyRequest;
use App\Http\Requests\enterprise\user\EnterpriseUserDailyRequest;
use App\Http\Service\Report\ReportReplyService;
use App\Http\Service\Report\ReportService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 汇报
 * Class DailyController.
 */
#[Prefix('uni/daily')]
#[Resource('/', false, except: ['create', 'show', 'destroy'], names: [
    'index'  => '获取汇报列表接口',
    'store'  => '保存汇报接口',
    'edit'   => '获取查看汇报接口',
    'update' => '修改汇报接口',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ReportController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(ReportService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 获取汇报列表.
     */
    public function index(): mixed
    {
        $where = $this->request->getMore($this->getSearchField());

        return $this->success($this->service->getGroupList($where, uid: auth('admin')->id()));
    }

    /**
     * 获取汇报人员.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('users', '汇报人员')]
    public function users()
    {
        [$viewer] = $this->request->getMore([
            ['viewer', 'user'],
        ], true);
        $users = $this->service->getDailyUser(auth('admin')->id(), $viewer);

        return $this->success($users);
    }

    /**
     * 回复评论.
     *
     * @return mixed
     */
    #[Post('reply', '汇报回复')]
    public function reply(EnterpriseUserDailyReplyRequest $request, ReportReplyService $services)
    {
        $data = $request->postMore([
            ['content', ''],
            ['daily_id', 0],
            ['pid', 0],
            ['uid', $this->uuid],
        ]);
        if ($services->create($data)) {
            return $this->success('回复成功');
        }
        return $this->fail('回复失败');
    }

    /**
     * 删除回复.
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    #[Delete('reply/{id}/{daily_id}', '汇报回复删除')]
    public function deleteReply(ReportReplyService $services, $id, $dailyId)
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        if ($services->deleteReply((int) $id, (int) $dailyId, $this->uuid, $this->entId)) {
            return $this->success('删除成功');
        }
        return $this->fail('删除失败');
    }

    /**
     * 汇报统计
     */
    #[Get('statistics', '汇报统计')]
    public function statistics()
    {
        [$types, $frameId, $time] = $this->request->getMore([
            ['types', ''],
            ['frame_id', ''],
            ['time', ''],
        ], true);
        return $this->success($this->service->statisticsByFrameId($this->uuid, $this->entId, (int) $frameId, (int) $types, $time));
    }

    /**
     * 提交统计列表.
     */
    #[Get('submit/list', '汇报提交统计')]
    public function submitList(): mixed
    {
        [$types, $time, $frameId] = $this->request->getMore([
            ['types', ''],
            ['time', ''],
            ['frame_id', ''],
        ], true);
        return $this->success($this->service->getSubmitListByFrameId($this->uuid, (int) $frameId, (int) $types, $time));
    }

    /**
     * 未提交汇报人员列表.
     */
    #[Get('no/submit/list', '汇报未提交人员列表')]
    public function noSubmitUserList(): mixed
    {
        [$types, $time, $frameId] = $this->request->getMore([
            ['types', ''],
            ['time', ''],
            ['frame_id', ''],
        ], true);
        return $this->success($this->service->getNoSubmitUserListByFrameId($this->uuid, (int) $frameId, (int) $types, $time));
    }

    /**
     * 汇报表单回显数据.
     * @param mixed $type
     * @throws BindingResolutionException
     */
    #[Get('schedule/record/{type}', '汇报待办回显')]
    public function scheduleRecord($type = 0): mixed
    {
        return $this->success($this->service->dailyScheduleRecord($this->uuid, (int) $type, $this->origin));
    }

    /**
     * 默认汇报人.
     * @throws BindingResolutionException
     */
    #[Get('report/member', '汇报默认汇报人')]
    public function reportMember(): mixed
    {
        return $this->success($this->service->getReportMember(auth('admin')->id()), tips: 0);
    }

    /**
     * 获取保存和修改字段.
     *
     * @return array|\string[][]
     */
    protected function getRequestFields(): array
    {
        return [
            ['finish', []],
            ['plan', []],
            ['mark', ''],
            ['status', 0],
            ['types', 1],
            ['uid', $this->uuid],
            ['entid', 1],
            ['attach_ids', ''],
            ['members', []],
        ];
    }

    /**
     * 字段验证
     */
    protected function getRequestClassName(): string
    {
        return EnterpriseUserDailyRequest::class;
    }

    /**
     * 搜索字段.
     */
    protected function getSearchField(): array
    {
        return [
            ['type', 0, 'dep_report'],
            ['types', ''],
            ['time', ''],
            ['user_id', ''],
            ['viewer', 'user'],
            ['name', '', 'finish_like'],
        ];
    }
}
