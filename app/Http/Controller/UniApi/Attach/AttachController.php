<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Attach;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\Attach\AttachService;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 附件管理
 * Class AttachController.
 */
#[Prefix('uni/attach')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class AttachController extends AuthController
{
    public function __construct(AttachService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 图片上传.
     * @param mixed $attach_type
     * @param mixed $type
     */
    #[Post('imgs/{attach_type?}', '附件图片上传')]
    public function upload($attach_type = 1, $type = 0): mixed
    {
        [$pid, $file, $way, $relationType, $relationId, $md5, $chunkIndex, $chunkTotal] = $this->request->postMore([
            ['cid', 0],
            ['file', 'file'],
            ['way', 2],
            ['relation_type', ''],
            ['relation_id', 0],
            ['md5', ''],        // 文件md5
            ['chunk_index', 0], // 分片索引
            ['chunk_total', 0], // 总分片数
        ], true);
        $res = $this->service->setOption(['chunk_index' => (int) $chunkIndex, 'chunk_total' => (int) $chunkTotal, 'md5' => $md5])
            ->setRelationType($relationType)->setRelationId((int) $relationId)->upload((int) $pid, $file, $attach_type, $type, (int) $way, $this->entId, $this->uuid);
        return $this->success($res === true ? 'ok' : '上传成功', $res === true ? [] : $res);
    }
}
