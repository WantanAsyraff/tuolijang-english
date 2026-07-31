<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Work;

use App\Constants\Work\MediaEnum;
use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\WorkExternalContact\UrlMetadataService;
use App\Http\Service\WorkExternalContact\WorkReplyTempGroupService;
use App\Http\Service\WorkExternalContact\WorkReplyTempService;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 企微快捷回复.
 */
#[Prefix('uni/work/reply_temp')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class WorkReplyTempController extends AuthController
{
    public function __construct(WorkReplyTempService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * 获取快捷回复分组.
     */
    #[Get('index', '获取快捷回复')]
    public function index(): mixed
    {
        $where = $this->request->getMore([
            ['group_id', ''],
            ['name', '', 'name_like'],
            ['types', ''],
        ]);
        return $this->success($this->service->getList($where, sort: ['sort', 'id'], with: ['file']));
    }

    /**
     * 获取快捷回复分组.
     */
    #[Get('group', '获取快捷回复分组')]
    public function group(WorkReplyTempGroupService $service): mixed
    {
        $where = $this->request->getMore([
            ['id', ''],
            ['name', '', 'name_like'],
        ]);
        return $this->success($service->getList($where));
    }

    /**
     * 获取个人库列表.
     */
    #[Get('personal', '获取个人库列表')]
    public function personal(): mixed
    {
        $where = $this->request->getMore([
            ['name', '', 'name_like'],
            ['is_personal', 1],
            ['uid', auth('admin')->id()],
            ['types', ''],
        ]);
        $result = $this->service->getPersonalList($where);
        return $this->success($result);
    }

    /**
     * 创建个人库内容.
     */
    #[Post('personal', '创建个人库内容')]
    public function createPersonal(): mixed
    {
        $uid      = auth('admin')->id();
        $material = $this->request->postMore([
            ['content', ''],
            ['sort', 1],
        ]);
        $data = [
            'types'       => MediaEnum::TEMP_TEXT,
            'title'       => $material['title'] ?? auth('admin')->user()->name . '的模版' . time(),
            'content'     => $material['content'] ?? '',
            'info'        => $material['info'] ?? '',
            'link'        => $material['link'] ?? '',
            'app_id'      => $material['app_id'] ?? '',
            'file_id'     => $material['file_id'] ?? 0,
            'sort'        => $material['sort'] ?? 1,
            'uid'         => $uid,
            'is_personal' => 1,
        ];
        if (! $data['content']) {
            return $this->fail('请输入回复内容');
        }
        $result = $this->service->resourceSave($data);
        return $this->success(['id' => $result->id]);
    }

    /**
     * 更新个人库内容.
     */
    #[Put('personal/{id}', '更新个人库内容')]
    public function updatePersonal(int $id): mixed
    {
        $uid  = auth('admin')->id();
        $data = $this->request->postMore([
            ['content', ''],
            ['sort', 1],
        ]);

        $this->service->updatePersonal($id, $data, $uid);
        return $this->success('更新成功');
    }

    /**
     * 删除个人库内容.
     */
    #[Delete('personal/{id}', '删除个人库内容')]
    public function deletePersonal(int $id): mixed
    {
        $uid = auth('admin')->id();
        $this->service->deletePersonal($id, $uid);
        return $this->success('删除成功');
    }

    /**
     * 获取URL元数据(标题、描述、封面图).
     */
    #[Post('url_metadata', '获取URL元数据')]
    public function getUrlMetadata(UrlMetadataService $urlMetadataService): mixed
    {
        [$url] = $this->request->postMore([
            ['url', ''],
        ], true);

        if (! $url) {
            return $this->fail('请填写网址链接');
        }

        try {
            $metadata = $urlMetadataService->getMetadata($url);
            return $this->success($metadata);
        } catch (\Exception $e) {
            \Log::error('URL metadata fetch failed: ' . $e->getMessage(), [
                'url'  => $url,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->fail('获取链接信息失败，请稍后重试');
        }
    }

    /**
     * 获取分页参数.
     */
    protected function getPageValue(): array
    {
        $page  = (int) $this->request->get('page', 1);
        $limit = (int) $this->request->get('limit', 20);
        $limit = max(1, min(100, $limit));
        return [$page, $limit];
    }
}
