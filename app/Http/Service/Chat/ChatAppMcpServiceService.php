<?php

declare(strict_types=1);


namespace App\Http\Service\Chat;

use App\Http\Dao\Chat\ChatAppMcpServiceDao;
use crmeb\basic\BaseService;

/**
 * 外部 MCP 服务管理.
 */
class ChatAppMcpServiceService extends BaseService
{
    /** 工具名称前缀 */
    public const TOOL_PREFIX = 'ext_mcp_';

    public function __construct(ChatAppMcpServiceDao $dao)
    {
        $this->dao = $dao;
    }

    public function resourceSave(array $data)
    {
        if (!$data['config_json']) {
            throw $this->exception('配置不能为空');
        }
        if (empty($data['config_json']['url'])) {
            throw $this->exception('配置地址不能为空');
        }
        if (empty($data['config_json']['transport'])) {
            throw $this->exception('配置传输方式不能为空');
        }

        return $this->dao->create($data);
    }

    public function resourceUpdate($id, array $data)
    {
        return $this->dao->update((int)$id, $data);
    }

    public function resourceDelete($id, ?string $key = null)
    {
        if ($this->dao->value($id, 'is_default')) {
            throw $this->exception('当前为默认的mcp无法删除');
        }
        return $this->dao->delete((int)$id);
    }

    /**
     * 获取服务列表（分页）.
     */
    public function getList(array $where, array $field = ['*'], $sort = 'sort', array $with = []): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, ['*'], $page, $limit, $sort);
        $count = $this->dao->count($where);
        return $this->listData($list, $count);
    }
}
