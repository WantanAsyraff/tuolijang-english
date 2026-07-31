<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Attach\AttachService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 客户文件
 * Class FileController.
 */
#[Prefix('uni/client/file')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class FileController extends AuthController
{
    public function __construct(AttachService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 展示数据.
     */
    #[Get('/', '获取客户文件列表')]
    public function index(): mixed
    {
        $where = $this->request->getMore([
            ['eid', ''],
            ['entid', 1],
        ]);
        $where['relation_type'] = [2, 3, 4, 5, 6];
        return $this->success($this->service->getRelationList($where));
    }

    /**
     * 删除数据.
     * @param mixed $id
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    #[Delete('delete/{id}', '删除客户文件记录')]
    public function destroy($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        $this->service->delImg([$id], $this->entId);
        return $this->success('common.delete.succ');
    }

    /**
     * 上传.
     */
    #[Post('upload', '上传客户文件')]
    public function upload(): mixed
    {
        [$pid, $file, $relationType, $relationId, $eid, $md5, $chunkIndex, $chunkTotal] = $this->request->postMore([
            ['cid', 0],
            ['file', 'file'],
            ['relation_type', ''],
            ['relation_id', 0],
            ['eid', ''],
            ['md5', ''],        // 文件md5
            ['chunk_index', 0], // 分片索引
            ['chunk_total', 0], // 总分片数
        ], true);

        try {
            $fileInfo = $this->service->setOption(['chunk_index' => (int)$chunkIndex, 'chunk_total' => (int)$chunkTotal, 'md5' => $md5])
                ->setRelationType($relationType)->setRelationId((int) $relationId, (int) $eid)
                ->upload((int) $pid, $file, 1, 0, 2, $this->entId, $this->uuid);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }

        if ($fileInfo === true) {
            return $this->success('ok');
        }
        $fileInfo['url'] = link_file($fileInfo['url']);
        return $this->success('common.upload.succ', $fileInfo);
    }

    /**
     * 重命名.
     * @param mixed $id
     * @throws BindingResolutionException|\ReflectionException
     */
    #[Put('real_name/{id}', '文件重命名')]
    public function realName($id): mixed
    {
        if (! $id) {
            return $this->fail(__('common.empty.attrs'));
        }
        [$realName] = $this->request->postMore([
            ['real_name', ''],
        ], true);
        $this->service->setRealName((int) $id, $this->entId, $realName);
        return $this->success('common.operation.succ');
    }
}
