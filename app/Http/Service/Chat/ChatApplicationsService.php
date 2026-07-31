<?php

declare(strict_types=1);


namespace App\Http\Service\Chat;

use App\Http\Dao\Chat\ChatApplicationsDao;
use App\Http\Service\Admin\AdminService;
use crmeb\basic\BaseService;
use crmeb\interfaces\ResourceServicesInterface;
use crmeb\traits\service\ResourceServiceTrait;
use Illuminate\Support\Facades\DB;

/**
 *  chat应用管理.
 */
class ChatApplicationsService extends BaseService implements ResourceServicesInterface
{
    use ResourceServiceTrait;

    public function __construct(ChatApplicationsDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $where, array $field = ['id', 'uid', 'info', 'name', 'pic', 'status', 'edit'], $sort = ['sort', 'id'], array $with = ['user']): array
    {
        [$page, $limit] = $this->getPageValue();
        if (isset($where['cate_id']) && is_array($where['cate_id'])) {
            $where['cate_id'] = array_map(function ($item) {
                return str_replace(['[', ']'], '', $item);
            }, $where['cate_id']);
        }
        $list = $this->dao->getList($where, $field, $page, $limit, $sort, $with);
        $uid  = auth('admin')->id();
        foreach ($list as &$item) {
            $item['auth'] = $uid == $item['uid'] || in_array($uid, $item['edit']);
        }
        $count = $this->dao->count($where);
        return $this->listData($list, $count);
    }

    public function resourceSave(array $data)
    {
        $data['tooltip_text'] = <<<'MD'
#角色规范
你是一个 XXXX 小助手，你的任务是 XXXX。

#思考规范

- 在回答问题时，你需要分析用户的问题，确保理解需求和上下文。
- 当用户的需求不明确时，你应该主动优先明确用户需求。
- 对于超出 本角色 小助手服务范围的需求，你需要按如下话术委婉拒答：抱歉，并引导用户提出关于 本角色 相关的问题。

#回复规范

- 你需要以 简洁高效的语气风格 回复用户。
- 在每次结束对话时你可以向用户提问并引导相关话题深入。
MD;
  
        return DB::transaction(function () use ($data) {
            $create = $this->dao->create($data);
            app()->get(ChatAppAuthService::class)->save($create->id, $data['edit']);
            return $create;
        });
    }

    public function resourceUpdate($id, array $data)
    {
        $id = (int) $id;
        unset($data['uid']);
        $updated = DB::transaction(function () use ($id, $data) {
            app()->get(ChatAppAuthService::class)->clear($id);
            app()->get(ChatAppAuthService::class)->save($id, $data['edit']);
            return $this->dao->update($id, $data);
        });

        app()->get(ChatHistoryService::class)->clearMcpToolsCache();

        return $updated;
    }

    public function resourceEdit(int $id, array $other = []): array
    {
        if (! $id) {
            throw $this->exception('缺少必要参数');
        }
        $data = $this->dao->get($id)?->toArray();
        if (! $data) {
            throw $this->exception('数据不存在');
        }
        $data['prologue_list'] = is_string($data['prologue_list']) ? json_decode($data['prologue_list'], true) : $data['prologue_list'];
        if (! is_array($data['prologue_list'])) {
            $data['prologue_list'] = [];
        }
        $data['keyword'] = is_string($data['keyword']) ? json_decode($data['keyword'], true) : $data['keyword'];
        if (! is_array($data['keyword'])) {
            $data['keyword'] = [];
        }
        $adminService     = app()->get(AdminService::class);
        $data['auth_ids'] = $adminService->search([])->whereIn('id', $data['auth_ids'])
            ->select(['id', 'name', 'avatar', 'uid', 'phone'])->get()?->toArray();
        $data['edit'] = $adminService->search([])->whereIn('id', $data['edit'])
            ->select(['id', 'name', 'avatar', 'uid', 'phone'])->get()?->toArray();
        return $data;
    }

    public function resourceCreate(array $other = []): array {}

    /**
     * @return string
     */
    public function getDatabaseTooltipText(array $tables = [])
    {
        $prefix = config('database.connections.mysql.prefix');
        // 表结构获取
        $tableContent = '';
        // 表之间引用关系
        $refContent = '';
        foreach ($tables as $table) {
            if (! is_scalar($table)) {
                throw $this->exception('表名不符合规范');
            }
            $tableName = $this->normalizeTableName((string) $table, $prefix);
            $this->ensureValidDatabaseTable($tableName);
            $sql = (array) DB::select("SHOW CREATE TABLE `{$tableName}`");
            $tableContent .= $sql[0]->{'Create Table'} . "\n\n";
            $refContent .= '- ' . $tableName . " 表的 uid 字段关联了 " . $this->normalizeTableName('admin', $prefix) . " 表的 id 字段；\n";
        }
        // 替换掉换行符和制表符
        $tableContent = str_replace('COLLATE utf8mb4_unicode_ci', '', $tableContent);
        // 替换掉SET utf8mb4
        $tableContent = str_replace('SET utf8mb4', '', $tableContent);
        // 补充提示词模版
        $tooltipText = <<<MD
# MySQL查询语句生成提示词
---
用户使用注意事项：
1.  本提示词仅用于生成SELECT查询语句，不支持生成增删改语句；
2.  如果多个表记得手动修改下面的表之间引用关系；
3.  选择数据库只是为了快速生成提示词，您可以自行修改本提示词内容，可以粘贴到AI工具中帮你优化这块提示词；
4.  这块内容是提示词注释模块，您可以删除；
---

# 数据库表结构
{$tableContent}
# 表之间引用关系
{$refContent}
### 记得手动修改上面表的引用关系哦！

## 查询规则
返回字段：[逗号分隔的字段列表，如 id, name AS 姓名]
筛选条件：[如 is_delete = 1 AND id > 1]
排序规则：[如 ORDER BY updated_at DESC, id ASC]
分组与聚合：[如 GROUP BY num HAVING COUNT(*) > 5]
连接操作：[如 LEFT JOIN a ON a.id = b.id]
限制结果：[如 LIMIT 10 OFFSET 20]
其他操作：[如 DISTINCT, 子查询等]

## 补充说明
{$this->normalizeTableName('client_bill', $prefix)}.status = 1 才算业绩

MD;

        // 截取字符串超出1w字节
        if (strlen($tooltipText) > 10000) {
            $tooltipText = substr($tooltipText, 0, 10000);
        }
        return $tooltipText;
    }

    protected function normalizeTableName(string $table, string $prefix = ''): string
    {
        $prefix = $prefix !== '' ? $prefix : (string) config('database.connections.mysql.prefix');
        $table = trim($table);
        $table = preg_replace('/^(?:' . preg_quote($prefix, '/') . '|eb_)/i', '', $table);
        return $prefix . ltrim((string) $table, '_');
    }

    protected function ensureValidDatabaseTable(string $tableName): void
    {
        if (! preg_match('/^[A-Za-z0-9_]{1,64}$/', $tableName)) {
            throw $this->exception('表名不符合规范');
        }

        $exists = DB::selectOne(
            'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1',
            [$tableName]
        );
        if (! $exists) {
            throw $this->exception('数据表不存在');
        }
    }
}
