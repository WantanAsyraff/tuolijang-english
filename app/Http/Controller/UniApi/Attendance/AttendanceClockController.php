<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Attendance;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Attendance\AttendanceApplyRecordService;
use App\Http\Service\Attendance\AttendanceClockService;
use App\Http\Service\Attendance\AttendanceStatisticsService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 考勤打卡
 * Class AttendanceClockController.
 */
#[Prefix('uni/attendance')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class AttendanceClockController extends AuthController
{
    public function __construct(AttendanceClockService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 基础数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('basic', '打卡基础数据')]
    public function basic(): mixed
    {
        [$date, $userId] = $this->request->getMore([
            ['date', ''],
            ['user_id', 0],
        ], true);

        $data = $this->service->getBasic($this->uuid, $date, (int) $userId);
        return $this->success($data);
    }

    /**
     * 打卡
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Post('clock_in', '考勤打卡')]
    public function clockIn(): mixed
    {
        $data = $this->request->postMore([
            ['remark', ''],
            ['image', ''],
            ['lat', ''],
            ['lng', ''],
            ['address', ''],
            ['is_external', 0],
            ['update_number', ''],
            ['date', ''],
            ['shift_id', 0],
            ['number', ''],
            ['mac', ''],
        ]);
        $res = $this->service->saveClock(auth('admin')->id(), $data);
        return $this->success($res);
    }

    /**
     * 打卡记录.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('clock_record', '打卡记录')]
    public function clockRecord(AttendanceStatisticsService $service): mixed
    {
        $data = $service->getStatisticsByUid(auth('admin')->id());
        return $this->success($data);
    }

    /**
     * 月度打卡统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('month_report', '月度打卡统计')]
    public function monthReport(AttendanceStatisticsService $service): mixed
    {
        [$date, $type, $userId] = $this->request->getMore([
            ['date', ''],
            ['type', ''],
            ['user_id', ''],
        ], true);
        $data = $service->getMonthReport($this->uuid, $date, (int) $type, (int) $userId);
        return $this->success($data);
    }

    /**
     * 打卡详情.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('clock/info', '打卡详情')]
    public function clockInfo(AttendanceStatisticsService $service): mixed
    {
        [$date, $userId] = $this->request->getMore([
            ['date', ''],
            ['user_id', 0],
        ], true);

        $data = $service->getStatisticsDetail($this->uuid, $date, (int) $userId);
        return $this->success($data);
    }

    /**
     * 团队统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('team/statistics', '团队统计')]
    public function teamStatistics(AttendanceStatisticsService $service): mixed
    {
        [$date] = $this->request->getMore([
            ['date', ''],
        ], true);

        $data = $service->getTeamStatistics($this->uuid, $date);
        return $this->success($data);
    }

    /**
     * 团队外勤统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('team/external_statistics', '团队外勤统计')]
    public function teamExternalStatistics(AttendanceStatisticsService $service): mixed
    {
        [$date] = $this->request->getMore([
            ['date', ''],
        ], true);

        $data = $service->getTeamExternalStatistics($this->uuid, $date);
        return $this->success($data);
    }

    /**
     * 团队明细.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('team/commute_details', '团队明细')]
    public function teamCommuteDetails(AttendanceStatisticsService $service): mixed
    {
        [$date, $status] = $this->request->getMore([
            ['date', ''],
            ['status', []],
        ], true);

        $data = $service->getTeamCommuteDetails($this->uuid, $date, (array) $status);
        return $this->success($data);
    }

    /**
     * 团队外勤统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('team/attendance_statistics', '团队外勤统计')]
    public function teamAttendanceStatistics(AttendanceStatisticsService $service): mixed
    {
        [$date] = $this->request->getMore([
            ['date', ''],
        ], true);

        $data = $service->getTeamAttendanceStatistics($this->uuid, $date);
        return $this->success($data);
    }

    /**
     * 团队加班统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('team/overtime_statistics', '团队加班统计')]
    public function teamOvertimeStatistics(AttendanceStatisticsService $service): mixed
    {
        $where = $this->request->getMore([
            ['date', '', 'month'],
            ['status', '', 'date_type'],
        ]);

        $data = $service->getTeamOvertimeStatistics($this->uuid, $where);
        return $this->success($data);
    }

    /**
     * 团队假勤统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('team/leave_statistics', '团队假勤统计')]
    public function teamLeaveStatistics(AttendanceStatisticsService $service): mixed
    {
        $where = $this->request->getMore([
            ['date', '', 'month'],
            ['status', []],
        ]);

        $data = $service->getTeamLeaveStatistics($this->uuid, $where);
        return $this->success($data);
    }

    /**
     * 月统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('month_statistics', '考勤月统计')]
    public function monthStatistics(AttendanceStatisticsService $service): mixed
    {
        [$date, $type, $userId] = $this->request->getMore([
            ['date', ''],
            ['type', ''],
            ['user_id', ''],
        ], true);

        $data = $service->getMonthStatistics($this->uuid, $date, (int) $type, (int) $userId);
        return $this->success($data);
    }

    /**
     * 个人加班统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('person/overtime_statistics', '个人加班统计')]
    public function personOvertimeStatistics(AttendanceApplyRecordService $service): mixed
    {
        $where = $this->request->getMore([
            ['date', '', 'month'],
            ['user_id', '', 'uid'],
            // ['date_type', ''],
            // ['calc_type', '']
        ]);

        $data = $service->getPersonOvertimeStatistics($this->uuid, $where);
        return $this->success($data);
    }

    /**
     * 个人考勤统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('person/attendance_statistics', '个人考勤统计')]
    public function personAttendanceStatistics(AttendanceStatisticsService $service): mixed
    {
        [$date, $status, $userId] = $this->request->getMore([
            ['date', ''],
            ['status', []],
            ['user_id', ''],
        ], true);

        $data = $service->getPersonAttendanceStatistics($this->uuid, $date, (array) $status, (int) $userId);
        return $this->success($data);
    }

    /**
     * 个人假勤统计
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('person/leave_statistics', '个人假勤统计')]
    public function personLeaveStatistics(AttendanceApplyRecordService $service): mixed
    {
        $where = $this->request->getMore([
            ['date', '', 'month'],
            ['status', []],
            ['user_id', '', 'uid'],
        ]);

        $data = $service->getPersonLeaveStatistics($this->uuid, $where);
        return $this->success($data);
    }

    /**
     * 考勤人员数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('user/{id}', '考勤人员数据')]
    public function attendanceUser($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        $data = $this->service->getAttendanceUser($this->uuid, (int) $id);
        return $this->success($data);
    }

    /**
     * 考勤审批类型.
     * @throws BindingResolutionException
     */
    #[Get('approve', '考勤审批类型')]
    public function approveList(AttendanceStatisticsService $service): mixed
    {
        [$date] = $this->request->getMore([
            ['date', ''],
        ], true);
        $data = $service->getApproveList($this->uuid, $date);
        return $this->success($data);
    }

    /**
     * 异常日期列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('abnormal_date', '异常日期列表')]
    public function abnormalDateList(AttendanceStatisticsService $service): mixed
    {
        $data = $service->getAbnormalDateList(auth('admin')->id());
        return $this->success($data);
    }

    /**
     * 异常记录列表.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Get('abnormal_record/{id}', '异常记录列表')]
    public function abnormalRecordList($id, AttendanceStatisticsService $service): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        $data = $service->getAbnormalRecordList($this->uuid, (int) $id);
        return $this->success($data);
    }
}
