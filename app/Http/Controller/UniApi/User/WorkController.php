<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\User;

use App\Http\Contract\Schedule\ScheduleInterface;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Approve\ApproveApplyService;
use App\Http\Service\Assess\UserAssessService;
use App\Http\Service\Attendance\AttendanceStatisticsService;
use App\Http\Service\Customer\CustomerService;
use App\Http\Service\News\NewsService;
use App\Http\Service\Report\ReportService;
use App\Http\Service\System\RolesService;
use App\Http\Service\User\UserQuickService;
use crmeb\traits\SearchTrait;
use crmeb\utils\Regex;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 工作台
 * Class WorkController.
 */
#[Prefix('uni/user/work')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class WorkController extends AuthController
{
    use SearchTrait;

    /**
     * 工作台主页.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    #[Get('index', '工作台主页')]
    public function index(
        ScheduleInterface $scheduleServices,
        ApproveApplyService $approveApplyServices,
        NewsService $noticeServices,
        ReportService $dailyServices,
        CustomerService $clientServices,
        UserAssessService $assessServices,
        RolesService $rolesService,
    ): mixed {
        $entId              = $this->entId;
        $uuid               = $this->uuid;
        $work_background    = sys_config('work_background', '');
        $enterprise_culture = sys_config('enterprise_culture', '');
        $schedule           = $scheduleServices->scheduleList(
            auth('admin')->id(),
            $entId,
            now()->startOfDay()->toDateTimeString(),
            now()->endOfDay()->toDateTimeString(),
            $scheduleServices->typeList(auth('admin')->id(), 'id')
        );
        $approveSearch = ['types' => 1, 'verify_status' => 1, 'entid' => $entId, 'name' => ''];
        $approve       = $approveApplyServices->setLimit(1, 5)->getList($approveSearch);
        if ($rolesService->checkAuthStatus(auth('admin')->user(), '/admin/user/notice/index', 'GET')) {
            $notice = $noticeServices->setLimit(1, 5)->getUserNoticeCount($uuid, $entId);
        } else {
            $notice = ['count' => 0, 'list' => []];
        }
        $daily  = $dailyServices->getUserDailyCount($uuid, $entId);
        $client = $clientServices->getUserClientData($uuid);
        $assess = $assessServices->getUserAssessData($uuid);
        $this->withScopeFrame();
        $frame             = $clientServices->getUserFrameData($uuid, (array) $this->request->get('uid', []));
        $statisticsService = app()->get(AttendanceStatisticsService::class);
        $team_statistics   = $statisticsService->getHomeTeamStatistics($uuid);
        $person_statistics = $statisticsService->getHomePersonStatistics($uuid);
        return $this->success(compact('work_background', 'enterprise_culture', 'schedule', 'approve', 'notice', 'daily', 'client', 'assess', 'frame', 'team_statistics', 'person_statistics'));
    }

    /**
     * 获取某月计划填写列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function daily(ReportService $services): mixed
    {
        $time = $this->request->get('time');
        if (! $time) {
            $timeDate = now()->toArray();
            $time     = $timeDate['year'] . '-' . $timeDate['month'];
        }
        if (! preg_match(Regex::MONTH_TIME_RULE, $time)) {
            return $this->fail('时间段格式错误');
        }
        [$year, $month] = explode('-', $time);
        $month          = str_pad($month, 2, '', STR_PAD_LEFT);
        $time           = $year . '-' . $month;
        return $this->success($services->getMonthDailyList($this->uuid, $this->entId, $time));
    }

    #[Get('menus', '获取工作台快捷菜单')]
    public function getFastEntry(UserQuickService $service): mixed
    {
        return $this->success($service->getFastEntry(auth('admin')->user(), false));
    }

    /**
     * 工作台快捷入口.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[Put('menus', '自定义工作台快捷入口菜单')]
    public function setFastEntry(UserQuickService $service): mixed
    {
        [$data] = $this->request->postMore([
            ['data', []],
        ], true);
        if ($service->setFastEntry($this->uuid, $data, $this->entId, false)) {
            return $this->success('修改成功');
        }
        return $this->fail('修改失败');
    }
}
