<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Notice;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Admin\AdminService;
use App\Http\Service\Notice\MessageCateService;
use App\Http\Service\Notice\NoticeRecordService;
use crmeb\services\uniPush\PushMessage;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 系统通知控制器.
 */
#[Prefix('uni/message')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class NoticeController extends AuthController
{
    public function __construct(NoticeRecordService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 通知分类.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('cate', '通知分类')]
    public function cate(MessageCateService $service)
    {
        return $this->success($service->getUniNoticeCate(auth('admin')->id(), $this->entId));
    }

    /**
     * 消息列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('list', '消息列表')]
    public function index()
    {
        [$cateId,$title,$isRead] = $this->request->getMore([
            ['cate_id', 0],
            ['title', ''],
            ['is_read', ''],
            ['cate_id', 0],
        ], true);
        return $this->success($this->service->getMessageList($this->uuid, $this->entId, (int) $cateId, $title, $isRead, reverse: true));
    }

    /**
     * 获取消息详情并标记已读.
     * @param int $id 消息ID
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('detail/{id}', '获取消息详情并标记已读')]
    public function detail(int $id)
    {
        $userId = auth('admin')->id();
        return $this->success($this->service->getInfoAndMarkRead($id, $userId));
    }

    /**
     * 修改处理状态
     * @return mixed
     */
    #[Put('update/{id}/{isHandle}', '修改处理状态')]
    public function updateHandle($id, $isHandle)
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $messageInfo = $this->service->get($id);
        if (! $messageInfo) {
            return $this->fail('消息不存在');
        }
        $messageInfo->is_handle = $isHandle;
        if ($messageInfo->save()) {
            return $this->success('common.update.succ');
        }
        return $this->fail('common.update.fail');
    }

    /**
     * 按照分类修改处理状态
     * @throws BindingResolutionException
     */
    #[Put('handle/{cate_id}/{isHandle}', '按照分类修改处理状态')]
    public function updateCateHandle($cate_id, $isHandle): mixed
    {
        if (! $cate_id) {
            return $this->fail('common.empty.attrs');
        }
        $userId = auth('admin')->id();
        $uuid   = $this->uuid;

        // 使用batchUpdate方法，它会自动处理角标更新
        $affectedRows = $this->service->batchUpdate($uuid, $this->entId, (int) $isHandle, (int) $cate_id, []);

        return $this->success('common.update.succ');
    }

    /**
     * 批量修改.
     * @throws BindingResolutionException
     */
    #[Put('batch/{isRead}', '批量修改')]
    public function batchUpdate($isRead): mixed
    {
        [$ids, $cateId] = $this->request->postMore([
            ['ids', []],
            ['cate_id', 0],
        ], true);

        if (! $ids && ! $cateId) {
            return $this->fail('缺少参数');
        }
        if ($this->service->batchUpdate($this->uuid, $this->entId, (int) $isRead, (int) $cateId, $ids)) {
            return $this->success('修改成功');
        }
        return $this->fail('修改失败');
    }

    /**
     * 批量删除.
     */
    #[Delete('batch/delete', '批量删除')]
    public function batchDelete(): mixed
    {
        [$ids, $cateId] = $this->request->postMore([
            ['ids', []],
            ['cate_id', 0],
        ], true);

        if (! $ids && ! $cateId) {
            return $this->fail('缺少参数');
        }
        if ($this->service->batchDelete($this->uuid, $this->entId, (int) $cateId, $ids)) {
            return $this->success('删除成功');
        }

        return $this->fail('删除失败');
    }

    /**
     * 获取待处理消息数量.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('count', '获取待处理消息数量')]
    public function pendingCount()
    {
        return $this->success($this->service->getCount($this->uuid, $this->entId));
    }

    /**
     * 清除未读消息角标.
     * @return mixed
     */
    #[Post('clear_badge', '清除未读消息角标')]
    public function clearBadge()
    {
        try {
            $adminService = app()->get(AdminService::class);
            $user         = $adminService->get((int) auth('admin')->id());

            if (! $user || empty($user['client_id'])) {
                return $this->fail('用户客户端ID不存在');
            }

            $clientId = $user['client_id'];

            // 计算当前用户的未读消息数量
            $unreadCount = $this->service->getUnreadCountByUserId((int) auth('admin')->id());

            // 使用个推服务设置正确的角标数
            $uniPush = app(PushMessage::class);
            $result  = $uniPush->userBadge([$clientId], (string) $unreadCount);

            return $this->success([
                'message'      => '角标已更新',
                'unread_count' => $unreadCount,
                'result'       => $result,
            ]);
        } catch (\Exception $e) {
            return $this->fail('清除角标失败: ' . $e->getMessage());
        }
    }
}
