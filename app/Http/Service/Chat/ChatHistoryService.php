<?php

declare(strict_types=1);


namespace App\Http\Service\Chat;


use App\Http\Dao\Chat\ChatHistoryDao;
use App\Http\Model\Chat\ChatApplications;
use App\Http\Model\Chat\ChatModels;
use App\Http\Service\Admin\AdminService;
use App\Http\Service\Crud\SystemCrudFieldService;
use App\Http\Service\Crud\SystemCrudService;
use App\Mcp\ExternalMcpClient;
use App\Mcp\Context\McpUserContextResolver;
use App\Mcp\ToolExecutor;
use App\Mcp\ToolRegistry;
use crmeb\basic\BaseModel;
use crmeb\basic\BaseService;
use crmeb\services\ai\BaidubceOption;
use crmeb\services\ai\BaseCurl;
use crmeb\services\ai\BaseOption;
use crmeb\services\ai\DeepseekOption;
use crmeb\services\CoreBusinessService;
use crmeb\services\SmsService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Swoole\Http\Response as SwooleResponse;
use App\Http\Service\Chat\EmbeddingService;

use function Ramsey\Uuid\v4;

/**
 * 聊天历史服务类
 * 处理聊天对话相关的业务逻辑，包括对话创建、消息管理、SQL查询执行等功能
 *
 * 主要功能：
 * - 聊天对话的创建和管理
 * - AI 对话的流式和非流式请求处理
 * - SQL 语句的生成和执行
 * - 向量数据库的查询和存储
 * - 对话历史的管理和清理
 *
 * @author 陀螺匠团队
 * @version 2.0.0
 * @package App\Http\Service\Chat
 *
 */
class ChatHistoryService extends BaseService
{
    public const CHAT_HISTORY_TABLE = 'chat_history_t';

    private const MESSAGE_BODY_COMPRESS_THRESHOLD = 60000;

    private const MESSAGE_BODY_TARGET_LENGTH = 40000;

    private const MESSAGE_CONTENT_MAX_LENGTH = 12000;

    private const MESSAGE_SUMMARY_MAX_LENGTH = 8000;

    private const TOOL_RESULT_EVENT_MAX_LENGTH = 8000;

    private const TOOL_RESULT_VISIBLE_MAX_LENGTH = 1200;

    private const TOOL_ARGUMENT_EVENT_MAX_LENGTH = 4000;

    private const TOOL_OBSERVATION_SUMMARY_MAX_LENGTH = 1600;

    private const MAX_TOOL_CALL_ROUNDS = 5;

    private const MAX_TOOL_REPAIR_ATTEMPTS = 2;

    private const SQL_PREVIEW_LIMIT = 10;

    private const SQL_LIMIT_DETECT_LIMIT = 11;

    private const THINKING_EVENT_TYPES = ['thinking', 'reasoning', 'tool', 'info', 'data'];

    private const MCP_TOOLS_CACHE_KEY = 'chat:mcp_tools_meta:v1';

    /**
     * 外部MCP工具名 → 服务配置映射.
     * @var array<string, array{url: string, headers: array, timeout: int}>
     */
    private array $externalMcpToolMap = [];

    /**
     * 当前对话的 MCP 工具调用计划.
     * @var string[]
     */
    private array $mcpToolPlan = [];

    /**
     * 当前对话 MCP 工具调用计划原因.
     * @var array<string, string>
     */
    private array $mcpToolPlanReasons = [];

    /**
     * 最近一次 AI 工具筛选返回的原因.
     * @var array<string, string>
     */
    private array $selectedMcpToolReasons = [];

    /**
     * 当前对话可用 MCP 工具元信息.
     * @var array<string, array<string, mixed>>
     */
    private array $mcpToolsMetaByName = [];

    /**
     * 核心业务服务
     */
    protected CoreBusinessService $coreService;

    public function __construct(ChatHistoryDao $dao, CoreBusinessService $coreService)
    {
        $this->dao = $dao;
        $this->coreService = $coreService;
    }

    protected function appendRuntimeContextPrompt(string $systemMessage): string
    {
        $systemMessage = $this->normalizeDatabasePrompt($systemMessage);
        $timezone = config('app.timezone') ?: date_default_timezone_get();
        $now = date('Y-m-d H:i:s');
        $runtimePrompt = <<<TXT

# 运行时上下文

- 当前服务端时间：{$now}
- 当前服务端时区：{$timezone}
- 你的模型知识库可能不是最新的；涉及“当前、今天、最新、实时、现在、最近”、价格、政策、人员状态、权限范围、业务数据等时效性信息时，不能只凭模型知识库下结论。
- 如果可用工具、MCP 或数据库能够查询，应优先使用它们获取当前数据；如果没有可用实时数据来源，需要明确说明“无法实时核验”，并区分已知信息与推测。
- 不要声称已经核验实时信息，除非该信息来自本次工具、MCP、数据库结果或上述当前服务端时间。
TXT;

        $systemMessage = trim($systemMessage);
        return $systemMessage === '' ? trim($runtimePrompt) : $systemMessage . "\n" . trim($runtimePrompt);
    }

    protected function databaseTablePrefix(): string
    {
        return DB::getTablePrefix() ?: (string) config('database.connections.mysql.prefix');
    }

    protected function prefixedTableName(string $table): string
    {
        $prefix = $this->databaseTablePrefix();
        $table = preg_replace('/^(?:' . preg_quote($prefix, '/') . '|eb_)/i', '', $table);
        return $prefix . ltrim((string) $table, '_');
    }

    protected function normalizeDatabasePrompt(string $content): string
    {
        $prefix = $this->databaseTablePrefix();
        if ($content === '' || $prefix === '' || $prefix === 'eb_') {
            return $content;
        }
        return preg_replace('/\beb_([A-Za-z0-9_]+)/', $prefix . '$1', $content);
    }

    protected function normalizeSqlTablePrefix(string $sql): string
    {
        // 委托给核心服务处理
        return $this->coreService->normalizeTablePrefix($sql);
    }

    /**
     * 将模型配置中的基础模型写入请求参数.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @return void
     */
    protected function configureModelOption(BaseOption $option, ChatModels $modelsInfo): void
    {
        $model = trim((string)($modelsInfo->is_model ?? ''));
        if ($model !== '') {
            $option->model = $model;
        }
    }

    /**
     * 应用自定义高级参数，并过滤会覆盖模型配置的字段.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param mixed $json 应用配置中的高级参数列表
     * @return void
     */
    protected function applyApplicationOptions(BaseOption $option, $json): void
    {
        if (!$json) {
            return;
        }

        $other = [];
        foreach ((array)$json as $item) {
            if (!isset($item['field'], $item['value'])) {
                continue;
            }

            $field = trim((string)$item['field']);
            if ($field === '') {
                continue;
            }

            if (in_array(strtolower($field), ['model'], true)) {
                $this->sendMessage('应用高级参数中的 model 已忽略，实际请求模型以模型配置为准', 'info');
                continue;
            }

            $other[$field] = $item['value'];
        }

        if ($other) {
            $option->options($other);
        }
    }

    /**
     * 重置单次对话中的 MCP 运行时状态，避免服务复用时串话.
     *
     * @return void
     */
    protected function resetMcpRuntimeState(): void
    {
        $this->externalMcpToolMap = [];
        $this->mcpToolPlan = [];
        $this->mcpToolPlanReasons = [];
        $this->selectedMcpToolReasons = [];
        $this->mcpToolsMetaByName = [];
    }

    /**
     * 获取本次请求实际使用的模型名称.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @return string 模型名称
     */
    protected function getOptionModelName(BaseOption $option, ChatModels $modelsInfo): string
    {
        return trim((string)($option->model ?: ($modelsInfo->is_model ?? '')));
    }

    /**
     * 判断当前请求是否走 DeepSeek 供应商.
     *
     * @param BaseOption $option AI 请求参数对象
     * @return bool 是否为 DeepSeek 配置
     */
    protected function isDeepseekOption(BaseOption $option): bool
    {
        return $option instanceof DeepseekOption;
    }

    /**
     * 判断当前模型是否属于思考/推理模型.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @return bool 是否为思考模型
     */
    protected function isReasoningModel(BaseOption $option, ChatModels $modelsInfo): bool
    {
        $model = strtolower($this->getOptionModelName($option, $modelsInfo));
        $body = $option->toArray();
        $thinkingType = strtolower((string)($body['thinking']['type'] ?? ''));

        return $thinkingType === 'enabled'
            || str_contains($model, 'reasoner')
            || str_contains($model, 'thinking')
            || str_contains($model, 'r1');
    }

    /**
     * 判断当前模型请求是否支持工具调用.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @return bool 是否支持工具调用
     */
    protected function supportsToolCalling(BaseOption $option, ChatModels $modelsInfo): bool
    {
        if (!$option->tools) {
            return false;
        }

        $model = strtolower($this->getOptionModelName($option, $modelsInfo));
        $body = $option->toArray();
        $thinkingType = strtolower((string)($body['thinking']['type'] ?? ''));

        if ($this->isDeepseekOption($option) && $model === 'deepseek-reasoner' && $thinkingType !== 'enabled') {
            return false;
        }

        return true;
    }

    /**
     * 判断当前请求是否需要兼容 reasoning_content + tool_calls 回填.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @return bool 是否需要思考工具调用兼容
     */
    protected function supportsReasoningToolCalls(BaseOption $option, ChatModels $modelsInfo): bool
    {
        return $this->isDeepseekOption($option) && $this->isReasoningModel($option, $modelsInfo);
    }

    /**
     * 向前端声明当前模型能力，避免用户误以为普通 chat 模型已开启原生思考模式.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @return void
     */
    protected function sendModelCapabilityThinkingMessage(BaseOption $option, ChatModels $modelsInfo): void
    {
        $model = $this->getOptionModelName($option, $modelsInfo);
        $isDeepseek = $this->isDeepseekOption($option);
        $isReasoning = $this->isReasoningModel($option, $modelsInfo);
        $timezone = config('app.timezone') ?: date_default_timezone_get();
        $now = date('Y-m-d H:i:s');

        if ($isDeepseek && strtolower($model) === 'deepseek-chat') {
            $content = '当前模型 deepseek-chat 不属于正式思考模型，将使用后端 Agent 调度展示思考和工具过程';
        } elseif ($isDeepseek && $isReasoning) {
            $content = '当前 DeepSeek 模型已按思考模式处理，将流式返回 reasoning 内容并兼容工具调用上下文';
        } elseif ($isReasoning) {
            $content = '当前模型被识别为思考模型，将尝试流式返回 reasoning 内容';
        } else {
            $content = '当前模型未识别为正式思考模型，将使用后端 Agent 调度展示思考和工具过程';
        }

        $this->sendThinkingMessage($content, 'model_capability', [
            'model' => $model,
            'provider' => (int)($modelsInfo->provider ?? 0),
            'is_deepseek' => $isDeepseek,
            'is_reasoning_model' => $isReasoning,
            'tool_calling_supported' => $this->supportsToolCalling($option, $modelsInfo),
            'reasoning_tool_calls_supported' => $this->supportsReasoningToolCalls($option, $modelsInfo),
            'service_time' => $now,
            'timezone' => $timezone,
        ]);
    }

    /**
     * 验证SQL是否为只读的SELECT语句.
     *
     * @param string $sql SQL语句
     * @return bool true表示安全，false表示危险
     */
    protected function validateSelectOnly(string $sql): bool
    {
        // 委托给核心服务处理
        return $this->coreService->validateSelectSql($sql);
    }

    protected function trimSqlTerminator(string $sql): string
    {
        return rtrim(rtrim(trim($sql)), ';');
    }

    protected function wrapSqlForLimit(string $sql, int $limit): string
    {
        $sql = $this->trimSqlTerminator($sql);

        return "SELECT * FROM ({$sql}) AS chat_sql_preview LIMIT {$limit}";
    }

    protected function wrapSqlForPage(string $sql): string
    {
        $sql = $this->trimSqlTerminator($sql);

        return "SELECT * FROM ({$sql}) AS chat_sql_page LIMIT \${page},\${limit}";
    }

    protected function wrapSqlForCount(string $sql): string
    {
        $sql = $this->trimSqlTerminator($sql);

        return "SELECT COUNT(*) AS aggregate FROM ({$sql}) AS chat_sql_count";
    }

    protected function getSqlResultCount(string $sql, int $fallbackCount): int
    {
        try {
            $countResult = Db::select($this->wrapSqlForCount($sql));
            $countRow = $countResult[0] ?? null;
            if (is_array($countRow)) {
                return (int) ($countRow['aggregate'] ?? $countRow['count'] ?? $fallbackCount);
            }
            if (is_object($countRow)) {
                return (int) ($countRow->aggregate ?? $countRow->count ?? $fallbackCount);
            }
        } catch (\Throwable $e) {
            return $fallbackCount;
        }

        return $fallbackCount;
    }

    /**
     * 获取置顶对话列表.
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getTopUpList(int $userId, array $field = ['*'])
    {
        return $this->dao->topUpModel($userId)->select($field)->orderBy('top_up', 'asc')->get()->toArray();
    }

    /**
     * 创建历史对话.
     * @return BaseModel|Model
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function saveHistory(int $applicationId, string $title, int $userId)
    {
        return $this->dao->create([
            'user_id' => $userId,
            'chat_application_id' => $applicationId,
            'title' => $title,
        ]);
    }

    /**
     * 执行SQL语句.
     * @return array
     * @throws BindingResolutionException
     */
    public function runSql(ChatApplications $appInfo, string $message, array $recordData = [], bool $interrupt = true, array $saveData = [])
    {
        $listData = [];
        $run = false;
        // 进行数据库的查询
        $this->sendMessage('执行SQL语句：' . $message, 'info');
        try {
            $res = (new SmsService())->dialog([
                [
                    'content' => $appInfo->content,
                    'role' => 'system',
                ],
                [
                    'content' => $message,
                    'role' => 'user',
                ],
            ]);

            $this->sendMessage('输出res：' . json_encode($res), 'info');

            $sql = $res['data']['data']['choices'][0]['message']['content'] ?? '';
            $sql = str_replace(['```sql', '```', 'json'], '', $sql);
            $sql = trim(str_replace("\n", '', $sql));
            $recordData['sql_text'] = $sql;

            $this->sendMessage('输出sql：' . $sql, 'info');

            $saveData['recordData'] = $recordData;
            $sql = $sql ? json_decode($sql, true) : [];
            if (!empty($sql['list_sql'])) {
                $sql['list_sql'] = $this->normalizeSqlTablePrefix($sql['list_sql']);
                if (!empty($sql['page_sql'])) {
                    $sql['page_sql'] = $this->normalizeSqlTablePrefix($sql['page_sql']);
                }
                // 严格验证SQL是否为只读SELECT
                if (!$this->validateSelectOnly($sql['list_sql'])) {
                    $interrupt && $this->sendMessage('SQL验证失败，仅允许执行SELECT语句', 'error', $interrupt ? $saveData : []);
                    return [$recordData, false, []];
                }
                // 验证page_sql
                if (!empty($sql['page_sql']) && !$this->validateSelectOnly($sql['page_sql'])) {
                    $interrupt && $this->sendMessage('SQL验证失败，仅允许执行SELECT语句', 'error', $interrupt ? $saveData : []);
                    return [$recordData, false, []];
                }
                try {
                    if (str_contains($sql['list_sql'], '${page}')) {
                        $this->sendMessage($recordData + ['is_page' => 1], 'data');
                    }

                    $sql['list_sql'] = str_replace(['${page}', '${limit}'], [0, 10], $sql['list_sql']);
                    $listData['list'] = DB::select($sql['list_sql']);

                    if ($sql['page_sql']) {
                        $listData['totalNum'] = DB::select($sql['page_sql']);
                    }

                    $this->sendMessage($listData, 'info');

                    $run = true;
                } catch (\Throwable $e) {
                    $interrupt && $this->sendMessage($e->getMessage() . '|' . $e->getLine() . '|' . $e->getFile(), 'error', $interrupt ? $saveData : []);
                }
            } else {
                $interrupt && $this->sendMessage('没有查询到数据', 'error', $interrupt ? $saveData : []);
            }
        } catch (\Throwable $e) {
            $interrupt && $this->sendMessage($e->getMessage(), 'error', $interrupt ? $saveData : []);
        }

        return [$recordData, $run, $listData];
    }

    /**
     * 请求AI非流式请求获取内容
     * @param BaseOption $option
     * @param string $key
     * @param string $nl2sqlPrompt
     * @param string $message
     * @return array
     * @throws BindingResolutionException
     */
    public function noStreamRequest(BaseOption $option, string $key, string $nl2sqlPrompt, string $message): string
    {
        $option = clone $option;
        $option->messages = [
            [
                'role' => BaseOption::RULE_SYSTEM,
                'content' => $nl2sqlPrompt,
            ],
            [
                'content' => $message,
                'role' => BaseOption::RULE_USER,
            ],
        ];
        // 设置非流式返回
        $option->stream = false;
        $option->streamOptions = [
            'include_usage' => false,
        ];
        $this->compressOptionMessages($option, 'no_stream');
        // 调试输出请求参数
        $this->sendMessage($option->messages, 'info');
        $curl = new BaseCurl($key);
        $res = $curl->setBody($option)->send(url: $option->url);
        // 输出请求结果
        $this->sendMessage('输出res：' . json_encode($res, JSON_UNESCAPED_UNICODE), 'info');
        // 获取AI返回的内容
        $content = $res['choices'][0]['message']['content'] ?? '';
        // 输出AI返回的内容
        $this->sendMessage('输出AI返回的内容：' . $content, 'info');
        return $content;
    }

    /**
     * 从消息中提取sql语句.
     * @return string
     */
    public function getSqlFromMessage(ChatApplications $appInfo, BaseOption $option, string $key, string $message, int $userId)
    {
        $sqlContent = '';
        $this->sendMessage('检查缓存是否有：' . $message, 'info');
        // 根据问题生成向量在redis中搜索如果有直接获取sql
        $sqlContent = app()->get(EmbeddingService::class)->searchSqlInVectorDB($message);
        $this->sendMessage('检查结果' . (string)$sqlContent, 'info');
        if ($sqlContent) {
            $sqlContent = $this->normalizeSqlTablePrefix($sqlContent);
            $this->sendMessage('从向量数据库中获取到sql：' . $sqlContent, 'info');
        } else {
            $this->sendMessage('开始向AI获取sql语句', 'info');
            // 正常返回获取sql的提示词
            $nl2sqlPrompt = $this->CreateSqlPromptGetString($appInfo, $userId);
            // 调用AI模型获取sql语句
            $sqlContent = $this->getSqlFromAi($option, $key, $nl2sqlPrompt, $message);
        }
        return $sqlContent;
    }

    /**
     * 从AI模型中获取sql语句.
     * @param BaseOption $option
     * @param string $key
     * @param string $nl2sqlPrompt
     * @param string $message
     * @return string
     */
    public function getSqlFromAi(BaseOption $option, string $key, string $nl2sqlPrompt, string $message)
    {
        $sqlContent = '';
        $this->sendMessage('请求中。。。', 'info');
        // 获取AI返回的内容
        $aiContent = $this->noStreamRequest($option, $key, $nl2sqlPrompt, $message);

        $this->sendMessage('输出AIcontent：' . $aiContent, 'info');
        // 正则提取 content 中的Ai输出的内容可能是文本也可能是json
        if (!empty($aiContent)) {
            // 提取content中的sql语句
            $pattern = '/\bSELECT\b\s+.*?(?:;|(?=\s*```)|(?="\s*(?:,|\}|\]))|$)/is';
            if (preg_match($pattern, (string)$aiContent, $matches)) {
                $sqlContent = trim($matches[0]);
                $sqlContent = preg_replace('/\\n+/', ' ', $sqlContent);
                $sqlContent = preg_replace('/\s+/', ' ', $sqlContent);
                $this->sendMessage('直接提取SQL：' . $sqlContent, 'info');
            } else {
                $this->sendMessage('没有匹配到SQL语句', 'error');
            }

        }
        $this->sendMessage('提取后SQL' . $sqlContent, 'info');

        return $sqlContent;
    }

    /**
     * 执行sql用当前模型执行. 停用
     * @return array
     * @throws BindingResolutionException
     */
    public function runSqlV2(ChatApplications $appInfo, BaseOption $option, string $key, string $message, array $recordData = [], bool $interrupt = true, array $saveData = [], array $userInfo = [])
    {

        $listData = [
            'list' => [],
            'totalNum' => [],
        ];
        $run = false;
        // 调用sql查询提示词
        $nl2sqlPrompt = $this->CreateSqlPrompt($appInfo, $userInfo);

        // 进行数据库的查询
        try {
            // 根据问题生成向量在redis中搜索如果有直接获取sql
            $sql = app()->get(EmbeddingService::class)->searchSqlInVectorDB($message);
            if ($sql) {
                $this->sendMessage('从向量数据库中获取到sql：' . $sql, 'info');
                $sql = $this->normalizeSqlTablePrefix($sql);

                // 严格验证SQL是否为只读SELECT
                if (!$this->validateSelectOnly($sql)) {
                    $this->sendMessage('SQL验证失败，仅允许执行SELECT语句', 'error');
                    return [];
                }
                $this->sendMessage('sql语句符合规范，开始执行', 'info');
                $recordData['sql_text'] = $sql;
                $listData['list'] = DB::select($sql);
                $run = true;
            } else {
                $this->sendMessage('从向量数据库中没有获取到sql, 开始向AI模型发送请求获取sql语句', 'info');

                // 向AI模型发送请求获取sql语句
                $content = $this->noStreamRequest($option, $key, $nl2sqlPrompt, $message);

                // 提取并清理 SQL 内容
                $sqlContent = str_replace(['```sql', '```', 'json'], '', $content);
                $sqlContent = trim(str_replace("\n", '', $sqlContent));

                // 准备保存的数据
                $recordData['sql_text'] = $sqlContent;
                $saveData['recordData'] = $recordData;

                // 输出sql 信息
                $this->sendMessage('输出sql：' . $sqlContent, 'info');

                // 解析sql
                $sql = $sqlContent ? json_decode($sqlContent, true) : [];
                // 严格验证SQL是否为只读SELECT
                if (isset($sql['list_sql']) && !empty($sql['list_sql']) && $this->validateSelectOnly($sql['list_sql'])) {
                    $listSql = $this->normalizeSqlTablePrefix($sql['list_sql']);
                    if (isset($sql['page_sql']) && !empty($sql['page_sql'])) {
                        $sql['page_sql'] = $this->normalizeSqlTablePrefix($sql['page_sql']);
                    }
                    // 移除换行符和多余空格
                    $listSql = trim(str_replace("\n", ' ', $listSql));
                    try {
                        // 判断是否包含分页
                        $isPage = str_contains($listSql, '${page}');
                        // 如果没有分页，则添加分页
                        if (!$isPage && !str_contains($listSql, 'LIMIT')) {
                            // 如果最后一位字符串是；号则切割掉
                            if (str_ends_with($listSql, ';')) {
                                $listSql = substr($listSql, 0, -1);
                            }
                            $listSql .= ' LIMIT ${page},${limit}';
                            $isPage = true;
                            $recordData['sql_text'] = json_encode($sql);
                        }
                        // 替换占位符为实际值
                        $listSql = str_replace(['${page}', '${limit}'], [0, 10], $listSql);
                        $this->sendMessage('查询sql：' . $listSql, 'info');
                        // 执行参sql查询
                        $listData['list'] = DB::select($listSql);

                        // 如果包含page_sql，则执行总条数查询
                        if (isset($sql['page_sql']) && !empty($sql['page_sql']) && $this->validateSelectOnly($sql['page_sql'])) {
                            // 执行总条数查询
                            $this->sendMessage('查询条数sql：' . $sql['page_sql'], 'info');
                            $listData['totalNum'] = DB::select($sql['page_sql']);
                        }
                        // 如果查询结果大于等于10条，则标记为分页
                        if ($isPage && count($listData['list']) >= 10) {
                            // 标记为分页
                            $this->sendMessage($recordData + ['is_page' => 1], 'data');
                        }
                        if (empty($listData['list'])) {
                            $listData['totalNum'] = 0;
                            $listData['list'] = [];
                            $this->sendMessage('没有查询到数据', 'error');

                        } else {
                            // 输出查询结果
                            $this->sendMessage($listData, 'info');
                            $run = true;
                            /* 查询成功，保存查询语句和返回的sql语句到向量数据库中
                            * @param string $userMessage 用户输入的消息
                            * @param string $listSql 返回的sql语句
                            */
                            $this->sendMessage('saveSqlToVectorDB', 'info');
                            $this->sendMessage($message, 'info');
                            $this->sendMessage($listSql, 'info');
                            $result = app()->get(EmbeddingService::class)->saveSqlToVectorDB($message, $listSql, 'sql');
                            if ($result) {
                                $this->sendMessage('向量存储成功', 'info');
                            } else {
                                $this->sendMessage('向量存储失败', 'error');
                            }
                        }

                    } catch (\Throwable $e) {
                        $this->sendMessage($e->getMessage() . '|' . $e->getLine() . '|' . $e->getFile(), $interrupt ? 'error' : 'info', $interrupt ? $saveData : []);
                        $this->sendMessage('没有生成sql查询', 'error');
                    }
                } else {
                    // 没有获取到sql语句
                    $this->sendMessage('没有生成sql查询', $interrupt ? 'error' : 'info', $interrupt ? $saveData : []);
                }
            }
        } catch (\Throwable $e) {
            $this->sendMessage($e->getMessage(), $interrupt ? 'error' : 'info', $interrupt ? $saveData : []);
        }

        return [$recordData, $run, $listData];
    }

    /**
     * 中断对话.
     * @return bool
     * @throws InvalidArgumentException
     */
    public function interrupt(string $chatRecordUuid)
    {
        return Cache::tags([self::CHAT_HISTORY_TABLE])->set($chatRecordUuid, 'stop', 8400);
    }

    /**
     * 清理对话记录.
     * @return mixed
     * @throws BindingResolutionException
     */
    public function cleanUpDialog(int $historyId)
    {
        $chatRecordService = app()->get(ChatRecordService::class);

        return $chatRecordService->delete(['chat_history_id' => $historyId]);
    }


    /**
     * 对话 优点执行sql快；增加向量缓存
     * @return bool
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function dialogV2(string $message, int $historyId, int $userId, ?string $chatRecordUuid = null, int $isShow = 1)
    {
        $chatRecordService = app()->get(ChatRecordService::class);
        $startTime = microtime(true);

        if ($chatRecordUuid) {
            $record['chat_record_uuid'] = $chatRecordUuid;
        } else {
            $record = [];
        }

        $saveData = [
            'userId' => $userId,
            'historyId' => $historyId,
            'chatRecordUuid' => $chatRecordUuid,
            'recordData' => $record,
            'startTime' => $startTime,
            'message' => $message,
        ];

        $chatApplicationId = $this->dao->value(['id' => $historyId], 'chat_application_id');
        if (!$chatApplicationId) {
            return $this->sendMessage('没有查询到应用ID', 'error', $saveData);
        }

        $appInfo = app()->get(ChatApplicationsService::class)->get($chatApplicationId);
        if (!$appInfo) {
            return $this->sendMessage('没有查询到应用信息', 'error', $saveData);
        }
        if ($appInfo->member_id && !in_array($userId, $appInfo->member_id)) {
            return $this->sendMessage('暂无权限访问此应用', 'error', $saveData);
        }
        if ($appInfo->use_limit && $chatRecordService->getDialogNum($userId, $appInfo->id, $appInfo->use_limit)) {
            return $this->sendMessage('今日使用已达到上限', 'error', $saveData);
        }

        $modelsInfo = app()->get(ChatModelsService::class)->get($appInfo['models_id']);
        if (!$modelsInfo) {
            return $this->sendMessage('没有查询到模型信息', 'error', $saveData);
        }
        if (!$modelsInfo->key) {
            return $this->sendMessage('模型配置有问题，请检查模型配置', 'error', $saveData);
        }

        $this->resetMcpRuntimeState();
        $option = $this->option($modelsInfo->provider);
        $this->configureModelOption($option, $modelsInfo);
        // 清空历史对话
        $option->messages = [];

        // 放入其他参数，model 始终以模型配置为准，避免应用高级参数覆盖.
        $this->applyApplicationOptions($option, $appInfo->json);
        $this->configureModelOption($option, $modelsInfo);
        // 系统提示词
        $systemMessage = $this->appendRuntimeContextPrompt((string)$appInfo->tooltip_text);

        if ($systemMessage) {
            $option->setMessage($systemMessage, BaseOption::RULE_SYSTEM);
        }

        // 加入历史对话内容
        if ($appInfo->count_number) {

            $chatRecordList = $chatRecordService->getDialogRecord($historyId, $appInfo->count_number);
            if ($chatRecordList) {
                // 历史对话倒序
                $chatRecordList = array_reverse($chatRecordList);
                foreach ($chatRecordList as $item) {
                    $this->sendMessage($item['problem_text'], 'info');
                    $this->sendMessage($item['answer_text'], 'info');
                    // 完整对话才会加入历史回话
                    if (!empty($item['problem_text']) && !empty($item['answer_text'])) {
                        $option->setMessage($item['problem_text'], BaseOption::RULE_USER);
                        $option->setMessage($item['answer_text'], BaseOption::RULE_ASSISTANT);
                    }
                }
            }
        }
        // 重新查询 重新生成
        if ($chatRecordUuid) {
            try {
                $this->sendMessage('重新生成', 'info');
                // 删除旧的embedding
                app()->get(EmbeddingService::class)->deleteEmbedding($message, 0.8, 'sql');
                $this->sendMessage('删除旧的embedding成功', 'info');
            } catch (\Exception $e) {
                return $this->sendMessage('删除旧的embedding失败：' . $e->getMessage(), 'error');
            }
            $recordData = $chatRecordService->get(['chat_record_uuid' => $chatRecordUuid])?->toArray();
            if (!$recordData) {
                return $this->sendMessage('没查询到记录，无法从新生成', 'error', $saveData);
            }
        } else {
            $recordData = [
                'chat_record_uuid' => v4(),
                'is_show' => $isShow,
                'chat_applications_id' => $appInfo->id,
            ];
            $this->sendMessage($recordData, 'data');
        }

        $usedDatabaseQuery = false;

        if ($appInfo->source_type == 1) {
            // MCP数据源模式：跳过NL2SQL，按用户问题筛选并注入外部MCP工具
            $this->sendMessage('MCP数据源模式，筛选并注入外部MCP工具', 'info', $saveData);
            $option->setMessage($message);
            $this->prepareExternalMcpTools($option, $modelsInfo->key, $message, $appInfo);
        } elseif ($appInfo->is_table && array_filter($appInfo->keyword, function ($keyword) use ($message) {
                return str_contains($message, $keyword);
            })) {
            $usedDatabaseQuery = true;
            $this->sendMessage('触发关键词开启数据库查询', 'info');
            // 根据message获取sql语句
            $sqlContent = $this->getSqlFromMessage($appInfo, $option, $modelsInfo->key, $message, $userId);
            // 加入用户问题到提示词
            $option->setMessage($message);
            /* 执行sql查询数据
             * @param string $message 用户问题
             * @param string $sqlContent 生成的sql语句
             * @param array $recordData 记录数据
             * @return array 查询数据信息和记录数据
             */
            $this->sendMessage('执行SQL查询' . $sqlContent, 'info');
            [$datainfo, $recordData] = $this->runSqlV3($message, $sqlContent, $recordData);
            // 加入查询数据作为提示词
            $option->setMessage($datainfo, BaseOption::RULE_ASSISTANT);
            // 加入查询后台用户设置的提示词,用户处理返回数据的格式
            $option->setMessage($appInfo->data_arrange_text);
        } else {
            $this->sendMessage('没出发关键词', 'info', $saveData);
            $option->setMessage($message);
        }

        $this->compressOptionMessages($option, 'dialog');
        // 输出系统提示词+历史对话+用户问题+数据整理提示词
        $this->sendMessage($option->messages, 'info');
        try {
            // 根据查询结果和用户提示词，生成最终的回答
            $this->sendMessage('根据查询结果生成再次请求AI获得最终回答', 'info');
            [$response, $recordData] = $this->streamRequest($option, $appInfo, $modelsInfo, $message, $recordData, $saveData);
        } catch (\Exception $e) {
            $embeddingService = app()->get(EmbeddingService::class);
            // 删除当前问题的embedding
            $embeddingService->deleteEmbedding($message);
            $this->sendMessage($e->getMessage(), 'error', $saveData);
        }
        // 保存对话记录
        $this->sendMessage('保存对话记录', 'info');
        return $this->saveRecord(userId: $userId, historyId: $historyId, message: $message, chatRecordUuid: $chatRecordUuid, recordData: $recordData, startTime: $startTime, response: (string)$response);
    }

    /*
     * 数据库查询功能
     * @param string $message 用户问题
     * @param string $sqlContent 生成的sql语句
     * @param array $recordData 记录数据
     * @return array 查询数据信息和记录数据
     */
    public function runSqlV3(string $message, string $sqlContent, array $recordData): array
    {
        // 查询数据
        $datainfo = '';
        $sqlContent = $this->normalizeSqlTablePrefix($sqlContent);
        $this->sendMessage('SQL:' . $sqlContent, 'info');
        // 验证一下是不是select语句，并不含危险的sql语句，例如：delete、update、drop、insert等
        if (!$this->validateSelectOnly($sqlContent)) {
            $this->sendMessage('SQL验证失败，仅允许执行SELECT语句', 'error');
            return [];
        }
        if ($sqlContent) {
            try {
                try {
                    $previewSql = $this->wrapSqlForLimit($sqlContent, self::SQL_LIMIT_DETECT_LIMIT);
                    $listData = Db::select($previewSql);
                } catch (\Exception $e) {
                    $this->sendMessage('抱歉没查到数据！', 'error');
                    $listData = [];
                }
                $hasMore = count($listData) > self::SQL_PREVIEW_LIMIT;
                $listData = array_slice($listData, 0, self::SQL_PREVIEW_LIMIT);
                $count = $hasMore ? $this->getSqlResultCount($sqlContent, self::SQL_LIMIT_DETECT_LIMIT) : count($listData);
                // 加入查询数据条数提示词
                if ($count > 0) {
                    // 只查询前10条数据,如果大于10进行分页处理
                    if ($count > self::SQL_PREVIEW_LIMIT) {
                        $sqlContent = $this->trimSqlTerminator($sqlContent);

                        // 分页查询用
                        $sql['totalNum'] = $count;
                        $sql['list_sql'] = $this->wrapSqlForPage($sqlContent);
                        $sql['page_sql'] = 'new';
                        $sql['table_fields'] = [];
                        foreach ($listData as $key => $value) {
                            if ($key !== 'list') {
                                $sql['table_fields'][$key] = $value;
                            }
                        }
                        $recordData['sql_text'] = json_encode($sql, JSON_UNESCAPED_UNICODE);

                        // 标记为分页,前端需要根据is_page判断是否需要分页展示更多数据按钮
                        $this->sendMessage($recordData + ['is_page' => 1], 'data');
                        // 前10条数据进行整理方便AI识别，减小字符长度
                        $resStr = "数据条数为：{$count},前10条数据为：";
                        $datainfo = $resStr . json_encode($listData, JSON_UNESCAPED_UNICODE);

                    } else {
                        $recordData['sql_text'] = $sqlContent;
                        // 加入查询数据条数提示词
                        $datainfo = '查询到的数据为：' . json_encode($listData, JSON_UNESCAPED_UNICODE);

                    }
                    // 查询数据并保存到向量数据库
                    $result = app()->get(EmbeddingService::class)->saveSqlToVectorDB($message, $sqlContent, 'sql', 86400 * 365);
                    if ($result) {
                        $this->sendMessage('向量存储成功', 'info');
                    } else {
                        $this->sendMessage('向量存储失败', 'error');
                    }
                } else {
                    $datainfo = '查询到的数据为空';
                }

            } catch (\Exception $e) {
                $this->sendMessage('查询数据失败！', 'error');
            }

        }
        return [$datainfo, $recordData];
    }

    /**
     * 对话 优点一次性直接请求模型，不会过多消耗一号通的token，缺点用户提示词较多
     * 流程：用户提问的内容直接交给模型-》模型函数决定是否需要执行函数-》执行函数-》一号通模型返回结果-》是否存在数据 -》是 -》查询的数据结果二次请求模型-》模型返回结果.
     * @return bool
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function dialog(string $message, int $historyId, int $userId, ?string $chatRecordUuid = null, int $isShow = 1)
    {
        $chatRecordService = app()->get(ChatRecordService::class);
        $startTime = microtime(true);

        if ($chatRecordUuid) {
            $record['chat_record_uuid'] = $chatRecordUuid;
        } else {
            $record = [];
        }

        $saveData = [
            'userId' => $userId,
            'historyId' => $historyId,
            'chatRecordUuid' => $chatRecordUuid,
            'recordData' => $record,
            'startTime' => $startTime,
            'message' => $message,
        ];

        $chatApplicationId = $this->dao->value(['id' => $historyId], 'chat_application_id');
        if (!$chatApplicationId) {
            return $this->sendMessage('没有查询到应用ID', 'error', $saveData);
        }

        $appInfo = app()->get(ChatApplicationsService::class)->get($chatApplicationId);
        if (!$appInfo) {
            return $this->sendMessage('没有查询到应用信息', 'error', $saveData);
        }
        if ($appInfo->member_id && !in_array($userId, $appInfo->member_id)) {
            return $this->sendMessage('暂无权限访问此应用', 'error', $saveData);
        }
        if ($chatRecordService->getDialogNum($userId, $appInfo->id, $appInfo->use_limit)) {
            return $this->sendMessage('今日使用已达到上限', 'error', $saveData);
        }

        $modelsInfo = app()->get(ChatModelsService::class)->get($appInfo['models_id']);
        if (!$modelsInfo) {
            return $this->sendMessage('没有查询到模型信息', 'error', $saveData);
        }
        if (!$modelsInfo->key) {
            return $this->sendMessage('模型配置有问题，请检查模型配置', 'error', $saveData);
        }

        $this->resetMcpRuntimeState();
        $option = $this->option($modelsInfo->provider);
        $this->configureModelOption($option, $modelsInfo);

        // 放入其他参数，model 始终以模型配置为准，避免应用高级参数覆盖.
        $this->applyApplicationOptions($option, $appInfo->json);
        $this->configureModelOption($option, $modelsInfo);

        $systemMessage = (string)$appInfo->tooltip_text;

        // 数据库查询功能，获取用户提示词
        if ($appInfo->tables) {
            $option->setTool([
                'name' => 'run_sql',
                'description' => '用来执行数据库查询',
                'parameters' => [
                    'data' => [
                        'type' => 'array',
                        'description' => '查询后的数据，二维数组，键值对形式',
                    ],
                ],
                'required' => ['data'],
            ]);

            $systemMessage = ($systemMessage ? $systemMessage . "\n" : '') . implode(',', $appInfo->tables) . "\n" . $appInfo->content;
        }
        $systemMessage = $this->appendRuntimeContextPrompt($systemMessage);
        $this->sendMessage($systemMessage, 'info');
        // 加入用户提示词
        if ($systemMessage) {
            $option->setMessage($systemMessage, 'system');
        }

        // 加入历史对话内容
        if ($appInfo->count_number) {
            $chatRecordList = $chatRecordService->getDialogRecord($historyId, $appInfo->count_number);
            if ($chatRecordList) {
                foreach ($chatRecordList as $item) {
                    if (!$item['problem_text'] || !$item['answer_text']) {
                        continue;
                    }
                    $option->setMessage($item['problem_text']);
                    $option->setMessage($item['answer_text'], BaseOption::RULE_ASSISTANT);
                }
            }
        }

        $option->setMessage($message);

        if ($chatRecordUuid) {
            $recordData = $chatRecordService->get(['chat_record_uuid' => $chatRecordUuid])?->toArray();
            if (!$recordData) {
                $recordData['chat_applications_id'] = $appInfo->id;
                $saveData['recordData'] = $recordData;
                return $this->sendMessage('没查询到记录，无法从新生成', 'error', $saveData);
            }
        } else {
            $recordData = [
                'chat_record_uuid' => v4(),
                'is_show' => $isShow,
                'chat_applications_id' => $appInfo->id,
            ];
            $this->sendMessage($recordData, 'data');
        }

        $this->sendMessage($option->messages, 'info');

        [$response, $recordData] = $this->streamRequest($option, $appInfo, $modelsInfo, $message, $recordData, $saveData);

        return $this->saveRecord(userId: $userId, historyId: $historyId, message: $message, chatRecordUuid: $chatRecordUuid, recordData: $recordData, startTime: $startTime, response: (string)$response);
    }

    /**
     * 保存记录
     * @return true
     * @throws BindingResolutionException
     */
    public function saveRecord(int $userId, int $historyId, string $message, string $chatRecordUuid, array $recordData, float|int $startTime = 0, string $response = '')
    {
        $chatRecordService = app()->get(ChatRecordService::class);
        $array = explode('data:', str_replace(["\r\n", "\n", "\r", '[DONE]'], '', $response));

        $content = '';
        foreach ($array as $value) {
            if (!trim($value)) {
                continue;
            }
            $res = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (!empty($res['type']) && in_array($res['type'], self::THINKING_EVENT_TYPES, true)) {
                    if (!empty($res['usage'])) {
                        $recordData['tokens'] = $res['usage']['total_tokens'] ?? 0;
                        $recordData['prompt_tokens'] = $res['usage']['prompt_tokens'] ?? 0;
                        $recordData['completion_tokens'] = $res['usage']['completion_tokens'] ?? 0;
                    }
                    continue;
                }
                if (!empty($res['choices'][0]['delta']['content'])) {
                    $content .= $res['choices'][0]['delta']['content'];
                }
                if (!empty($res['error']['message'])) {
                    $content .= $res['error']['message'];
                    continue;
                }
                if (!empty($res['type']) && !empty($res['message']) && $res['type'] == 'error') {
                    $content .= $res['message'];
                    continue;
                }
                if (empty($res['usage'])) {
                    continue;
                }
                $recordData['tokens'] = $res['usage']['total_tokens'] ?? 0;
                $recordData['prompt_tokens'] = $res['usage']['prompt_tokens'] ?? 0;
                $recordData['completion_tokens'] = $res['usage']['completion_tokens'] ?? 0;
            } else {
                $content .= $value;
            }
        }
        $recordData['answer_text'] = $content;
        $recordData['chat_history_id'] = $historyId;
        $recordData['problem_text'] = $message;
        $recordData['details'] = $response;
        $endTime = microtime(true);
        $executionTime = $startTime ? $endTime - $startTime : 0;
        $recordData['run_time'] = $executionTime;

        if ($chatRecordUuid) {
            unset($recordData['id'], $recordData['created_at'], $recordData['updated_at']);
            $chatRecordService->update(['chat_record_uuid' => $chatRecordUuid], $recordData);
        } else {
            $recordData['uid'] = $userId;
            try {
                $chatRecordService->create($recordData);
            } catch (\Exception $e) {
                $this->sendMessage($e->getMessage(), 'error');
            }
        }
        return true;
    }

    /**
     * 构建当前服务的内置 MCP 访问地址.
     *
     * @return string 内置 MCP 服务地址
     */
    public function buildInternalMcpUrl(?string $module = null): string
    {
        $url = request()->getSchemeAndHttpHost() . '/mcp';
        $module = trim((string) $module);

        return $module !== '' ? $url . '/' . $module : $url;
    }

    /**
     * 获取当前登录用户的 MCP Key，内置 MCP 自调用时用于定位用户身份.
     *
     * @return string 当前用户 MCP Key
     */
    protected function getCurrentMcpKey(): string
    {
        $user = auth('admin')->user();
        if (!$user) {
            return '';
        }

        $mcpKey = (string)($user->mcp_key ?? '');
        if ($mcpKey !== '') {
            return $mcpKey;
        }

        try {
            return app(AdminService::class)->getMcpKey((int)$user->id);
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * 注入外部 MCP 工具（MCP 数据源模式）.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param string $key 模型 API Key
     * @param mixed $appInfo 当前聊天应用配置
     * @return void
     */
    protected function injectExternalMcpTools(BaseOption $option, string $key, $appInfo): void
    {
        $mcpKey = $this->getCurrentMcpKey();

        $services = $this->normalizeMcpServices($appInfo->mcp_json ?? []);
        if (empty($services)) {
            $this->sendMessage('没有配置MCP服务', 'info');
            return;
        }

        $injectedTools = [];
        foreach ($services as $service) {
            $serviceId = (int)($service['id'] ?? 0);
            if (!$serviceId) {
                $this->sendMessage('MCP服务缺少ID，已跳过：' . ($service['name'] ?? ''), 'info');
                continue;
            }

            if ((int)($service['status'] ?? 1) !== 1) {
                continue;
            }

            $config = $this->resolveMcpJsonServiceConfig($service, $mcpKey);
            if (!$config) {
                continue;
            }

            try {
                $client = new ExternalMcpClient($config['url'], $config['headers'], $config['timeout']);
                $tools = $client->listTools();
                $this->sendMessage('MCP工具：' . json_encode($tools, JSON_UNESCAPED_UNICODE), 'info');
                if (!empty($tools['error']) || !is_array($tools)) {
                    $this->sendMessage('MCP工具发现失败：' . ($service['name'] ?? $serviceId) . '，' . ($tools['message'] ?? '未知错误'), 'error');
                    continue;
                }

                foreach ($tools as $tool) {
                    $originalName = (string)($tool['name'] ?? '');
                    if ($originalName === '') {
                        continue;
                    }

                    $schema = $this->normalizeToolInputSchema($tool['inputSchema'] ?? ['type' => 'object', 'properties' => []]);

                    // 使用原始工具名，并记录到映射中用于后续调用分发
                    $this->externalMcpToolMap[$originalName] = $config;

                    $option->setTool([
                        'name' => $originalName,
                        'description' => $tool['description'] ?? '',
                        'parameters' => $schema,
                    ]);
                    $injectedTools[] = $originalName;
                }
            } catch (\Throwable $e) {
                $this->sendMessage('MCP服务连接失败：' . ($service['name'] ?? $serviceId) . '，' . $e->getMessage(), 'error');
            }
        }

        if (empty($injectedTools)) {
            $this->sendMessage('没有发现可用MCP工具', 'info');
            return;
        }

        $this->sendMessage('已注入外部MCP工具：' . implode(', ', $injectedTools), 'info');
    }

    /**
     * MCP 数据源模式：根据用户问题筛选并注入外部 MCP 工具.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param string $key 模型 API Key
     * @param string $message 用户问题
     * @param mixed $appInfo 当前聊天应用配置
     * @return void
     */
    protected function prepareExternalMcpTools(BaseOption $option, string $key, string $message, $appInfo): void
    {
        $this->sendThinkingMessage('正在发现外部MCP工具', 'tool_select');
        $toolsMeta = $this->discoverExternalMcpTools($appInfo);
        if (!$toolsMeta) {
            $this->sendThinkingMessage('没有发现可用外部MCP工具', 'tool_error');
            return;
        }

        $this->sendThinkingMessage('正在本地检索可用外部MCP工具', 'tool_select');
        $toolNames = $this->selectMcpToolsLocally($message, $toolsMeta);
        if (!$toolNames) {
            $this->sendThinkingMessage('本地检索未命中，正在使用AI筛选外部MCP工具', 'tool_select');
            $toolNames = $this->selectMcpTools($option, $key, $message, $toolsMeta);
        }

        $injectedTools = $this->injectSelectedMcpTools($option, $toolsMeta, $toolNames);
        if (!$injectedTools) {
            $this->sendThinkingMessage('外部MCP工具注入失败', 'tool_error');
            return;
        }

        $this->sendThinkingMessage('已准备外部MCP工具数量：' . count($injectedTools), 'tool_select');
        $this->setMcpToolPlan($option, $injectedTools, $this->selectedMcpToolReasons);
        $this->sendMessage('已注入外部MCP工具数量：' . count($injectedTools), 'info');
    }

    /**
     * 发现外部 MCP 工具并附加调用配置.
     *
     * @param mixed $appInfo 当前聊天应用配置
     * @return array<int, array<string, mixed>> 可用工具元数据列表
     */
    protected function discoverExternalMcpTools($appInfo): array
    {
        $mcpKey = $this->getCurrentMcpKey();
        $services = $this->normalizeMcpServices($appInfo->mcp_json ?? []);
        if (empty($services)) {
            $this->sendMessage('没有配置MCP服务', 'info');
            return [];
        }

        $toolsMeta = [];
        foreach ($services as $service) {
            $serviceId = (int)($service['id'] ?? 0);
            if (!$serviceId) {
                $this->sendMessage('MCP服务缺少ID，已跳过：' . ($service['name'] ?? ''), 'info');
                continue;
            }

            if ((int)($service['status'] ?? 1) !== 1) {
                continue;
            }

            $config = $this->resolveMcpJsonServiceConfig($service, $mcpKey);
            if (!$config) {
                continue;
            }

            try {
                $client = new ExternalMcpClient($config['url'], $config['headers'], $config['timeout']);
                $tools = $client->listTools();
                if (!empty($tools['error']) || !is_array($tools)) {
                    $this->sendThinkingMessage('MCP工具发现失败：' . ($service['name'] ?? $serviceId) . '，' . ($tools['message'] ?? '未知错误'), 'tool_error');
                    continue;
                }

                foreach ($tools as $tool) {
                    $toolName = (string)($tool['name'] ?? '');
                    if ($toolName === '') {
                        continue;
                    }

                    $toolsMeta[$toolName] = [
                        'name' => $toolName,
                        'description' => $tool['description'] ?? '',
                        'inputSchema' => $this->normalizeToolInputSchema($tool['inputSchema'] ?? ['type' => 'object', 'properties' => []]),
                        'mcpConfig' => $config,
                    ];
                }
            } catch (\Throwable $e) {
                $this->sendThinkingMessage('MCP服务连接失败：' . ($service['name'] ?? $serviceId) . '，' . $e->getMessage(), 'tool_error');
            }
        }

        return array_values($toolsMeta);
    }

    /**
     * 判断 MCP 服务是否为系统默认内置服务.
     *
     * @param array<string, mixed> $service MCP 服务配置
     * @return bool 是否为默认内置服务
     */
    protected function isDefaultMcpService(array $service): bool
    {
        return (int)($service['is_default'] ?? 0) === 1;
    }

    /**
     * 清理 MCP 工具元数据缓存.
     */
    public function clearMcpToolsCache(): void
    {
        ToolRegistry::clearCache();
        Cache::tags([self::CHAT_HISTORY_TABLE])->forget(self::MCP_TOOLS_CACHE_KEY);
    }

    /**
     * 统一 MCP 服务配置结构，兼容单服务、服务列表和 JSON 字符串.
     *
     * @param mixed $mcpJson 应用中保存的 MCP 配置
     * @return array<int, array<string, mixed>> 标准化后的服务列表
     */
    protected function normalizeMcpServices($mcpJson): array
    {
        if (is_string($mcpJson)) {
            $mcpJson = json_decode($mcpJson, true) ?: [];
        }

        if (!is_array($mcpJson) || $mcpJson === []) {
            return [];
        }

        if (isset($mcpJson['id']) || isset($mcpJson['is_default']) || isset($mcpJson['service_url']) || isset($mcpJson['config_json'])) {
            return [$mcpJson];
        }

        return array_values(array_filter($mcpJson, fn($service) => is_array($service)));
    }

    /**
     * 解析单个 MCP 服务的可调用配置.
     *
     * @param array<string, mixed> $service MCP 服务配置
     * @param string|null $mcpKey 当前用户 MCP Key
     * @return array{url: string, headers: array<string, string>, timeout: int, module?: string}|null 可调用配置，解析失败返回 null
     */
    protected function resolveMcpJsonServiceConfig(array $service, ?string $mcpKey = null): ?array
    {
        $configJson = $this->normalizeMcpConfigJson($service['config_json'] ?? []);
        $rawUrl = trim((string)($configJson['url'] ?? ($service['service_url'] ?? '')));
        $module = $this->resolveMcpModule($configJson, $service, $rawUrl);

        $headers = $this->parseMcpHeaders($configJson['headers'] ?? ($service['headers'] ?? []));
        $timeout = (int)($configJson['timeout'] ?? ($service['timeout'] ?? 30));
        $timeout = $timeout > 0 ? $timeout : 30;

        if ((int)($service['is_default'] ?? 0) === 1) {
            if ($mcpKey) {
                $headers['X-Mcp-Key'] = $mcpKey;
            }

            return [
                'url' => $this->buildInternalMcpUrl($module ?: null),
                'headers' => $headers,
                'timeout' => $timeout,
                'module' => $module,
            ];
        }

        $transport = (string)($configJson['transport'] ?? ($service['type'] ?? 'sse'));
        if (!in_array($transport, ['sse', 'http'], true)) {
            $this->sendMessage('MCP服务仅支持HTTP/SSE：' . ($service['name'] ?? ''), 'info');
            return null;
        }

        $url = $rawUrl;
        if (!$this->isHttpMcpUrl($url)) {
            $this->sendMessage('MCP服务地址必须是http或https：' . ($service['name'] ?? ''), 'info');
            return null;
        }

        return [
            'url' => $url,
            'headers' => $headers,
            'timeout' => $timeout,
            'module' => $module,
        ];
    }

    /**
     * 解析 MCP 模块名，兼容 config_json.module、service.module 和 /mcp/{module} 相对路径.
     *
     * @param array<string, mixed> $configJson MCP JSON配置
     * @param array<string, mixed> $service MCP 服务配置
     * @param string $url MCP 服务地址
     * @return string 模块名
     */
    protected function resolveMcpModule(array $configJson, array $service, string $url = ''): string
    {
        $module = trim((string)($configJson['module'] ?? ($service['module'] ?? '')));
        if ($module !== '') {
            return $module;
        }

        if ($url === '') {
            return '';
        }

        $path = (string)(parse_url($url, PHP_URL_PATH) ?: $url);
        if (preg_match('#(?:^|/)mcp/([^/?#]+)#', $path, $matches)) {
            return (string)$matches[1];
        }

        return '';
    }

    /**
     * 兼容 config_json 为对象、JSON字符串、配置列表三种形态.
     *
     * @param mixed $configJson
     * @return array<string, mixed>
     */
    protected function normalizeMcpConfigJson($configJson): array
    {
        if (is_string($configJson)) {
            $configJson = json_decode($configJson, true) ?: [];
        }

        if (!is_array($configJson) || $configJson === []) {
            return [];
        }

        if (array_keys($configJson) === range(0, count($configJson) - 1)) {
            $firstConfig = $configJson[0] ?? [];
            return is_array($firstConfig) ? $firstConfig : [];
        }

        return $configJson;
    }

    /**
     * 标准化 MCP 请求头配置.
     *
     * @param mixed $headers 请求头配置，支持 JSON 字符串、键值对象或 key/value 列表
     * @return array<string, string> 请求头键值表
     */
    protected function parseMcpHeaders($headers): array
    {
        if (is_string($headers)) {
            $headers = json_decode($headers, true) ?: [];
        }

        if (!is_array($headers)) {
            return [];
        }

        if (isset($headers[0]) && is_array($headers[0]) && isset($headers[0]['key'])) {
            $map = [];
            foreach ($headers as $header) {
                $key = (string)($header['key'] ?? '');
                if ($key !== '') {
                    $map[$key] = (string)($header['value'] ?? '');
                }
            }
            return $map;
        }

        $map = [];
        foreach ($headers as $key => $value) {
            if (is_string($key) && $key !== '') {
                $map[$key] = is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE);
            }
        }

        return $map;
    }

    /**
     * 判断 MCP 地址是否为 HTTP 或 HTTPS.
     *
     * @param string $url MCP 服务地址
     * @return bool 是否为合法 HTTP 地址
     */
    protected function isHttpMcpUrl(string $url): bool
    {
        return (bool)preg_match('/^https?:\/\//i', $url);
    }

    /**
     * 标准化工具入参 schema，保证模型侧收到 object schema.
     *
     * @param mixed $schema 原始工具入参 schema
     * @return array<string, mixed> 标准化后的 schema
     */
    protected function normalizeToolInputSchema($schema): array
    {
        if (!is_array($schema)) {
            $schema = [];
        }
        if (($schema['type'] ?? '') !== 'object') {
            $schema['type'] = 'object';
        }
        if (!isset($schema['properties']) || $schema['properties'] === null) {
            $schema['properties'] = new \stdClass();
        } elseif (is_array($schema['properties']) && empty($schema['properties'])) {
            $schema['properties'] = new \stdClass();
        } elseif (!is_array($schema['properties']) && !is_object($schema['properties'])) {
            $schema['properties'] = new \stdClass();
        }

        return $schema;
    }

    /**
     * 调用外部 MCP JSON 工具，并统一异常返回结构.
     *
     * @param mixed $appInfo 当前聊天应用配置
     * @param string $toolName 工具名称
     * @param array<string, mixed> $arguments 工具调用参数
     * @return array<string, mixed> MCP 工具执行结果
     */
    protected function callMcpJsonTool($appInfo, string $toolName, array $arguments): array
    {
        $config = $this->externalMcpToolMap[$toolName] ?? null;
        if (!$config) {
            return [
                'content' => [['type' => 'text', 'text' => 'External MCP tool not found: ' . $toolName]],
                'isError' => true,
            ];
        }

        try {
            $client = new ExternalMcpClient($config['url'], $config['headers'], $config['timeout']);
            return $client->callTool($toolName, $arguments);
        } catch (\Throwable $e) {
            return [
                'content' => [['type' => 'text', 'text' => 'MCP tool error: ' . $e->getMessage()]],
                'isError' => true,
            ];
        }
    }

    /**
     * 根据用户问题筛选并注入 MCP 工具.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param string $key 模型 API Key
     * @param string $message 用户问题
     * @return void
     */
    protected function prepareMcpTools(BaseOption $option, string $key, string $message): void
    {
        if (!$this->shouldPrepareMcpTools($message)) {
            return;
        }

        $this->sendThinkingMessage('正在筛选可用MCP工具', 'tool_select');
        $this->injectMcpUserContext();

        $toolsMeta = ToolRegistry::getToolsMeta();
        if (!$toolsMeta) {
            $this->sendThinkingMessage('没有可用MCP工具', 'tool_error');
            return;
        }

        $toolNames = $this->selectMcpTools($option, $key, $message, $toolsMeta);
        if (!$toolNames) {
            $toolNames = $this->fallbackSelectMcpTools($message, $toolsMeta);
            $this->selectedMcpToolReasons = $this->buildFallbackMcpToolReasons($toolNames, $toolsMeta);
            $this->sendThinkingMessage('AI筛选未命中，已启用MCP工具兜底筛选', 'tool_select');
        }

        $injectedTools = $this->injectSelectedMcpTools($option, $toolsMeta, $toolNames);
        if (!$injectedTools) {
            $this->sendThinkingMessage('MCP工具注入失败', 'tool_error');
            return;
        }

        $this->sendThinkingMessage('已准备MCP工具数量：' . count($injectedTools), 'tool_select');
        $this->setMcpToolPlan($option, $injectedTools, $this->selectedMcpToolReasons);
        $this->sendMessage('已注入MCP工具数量：' . count($injectedTools), 'info');
    }

    /**
     * 判断是否需要尝试准备 MCP 工具.
     *
     * @param string $message 用户问题
     * @return bool 是否需要准备 MCP 工具
     */
    protected function shouldPrepareMcpTools(string $message): bool
    {
        return $this->matchesMcpKeyword($message) || $this->looksLikeBusinessDataQuestion($message);
    }

    /**
     * 按筛选结果注入工具.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param array<int, array<string, mixed>> $toolsMeta 可用工具元数据列表
     * @param string[] $toolNames 需要注入的工具名称
     * @return string[] 实际注入成功的工具名称
     */
    protected function injectSelectedMcpTools(BaseOption $option, array $toolsMeta, array $toolNames): array
    {
        $toolsByName = [];
        foreach ($toolsMeta as $tool) {
            $toolName = (string)($tool['name'] ?? '');
            if ($toolName !== '') {
                $toolsByName[$toolName] = $tool;
            }
        }

        $injectedTools = [];
        foreach ($toolNames as $toolName) {
            if (!isset($toolsByName[$toolName])) {
                continue;
            }

            $schema = $this->normalizeToolInputSchema($toolsByName[$toolName]['inputSchema'] ?? ['type' => 'object', 'properties' => []]);
            $this->mcpToolsMetaByName[$toolName] = [
                'name' => $toolName,
                'description' => $toolsByName[$toolName]['description'] ?? '',
                'inputSchema' => $schema,
            ];

            if (!empty($toolsByName[$toolName]['mcpConfig']) && is_array($toolsByName[$toolName]['mcpConfig'])) {
                $this->externalMcpToolMap[$toolName] = $toolsByName[$toolName]['mcpConfig'];
            }

            $option->setTool([
                'name' => $toolName,
                'description' => $toolsByName[$toolName]['description'] ?? '',
                'parameters' => $schema,
            ]);
            $injectedTools[] = $toolName;
        }

        return $injectedTools;
    }

    /**
     * MCP 采用服务端内置关键词触发，避免改表和前端配置.
     *
     * @param string $message 用户问题
     * @return bool 是否命中 MCP 触发关键词
     */
    protected function matchesMcpKeyword(string $message): bool
    {
        foreach ($this->getMcpKeywords() as $keyword) {
            if ($keyword !== '' && stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取 MCP 工具触发和兜底相关性匹配关键词.
     *
     * @return string[] 关键词列表
     */
    protected function getMcpKeywords(): array
    {
        return [
            'MCP', 'mcp', 'MCp', 'Mcp', 'mCp', 'mcP', '工具',
            '客户', '线索', '订单', '商机', '合同', '发票', '跟进', '联系人',
            '账目', '财务', '流水', '收支', '绩效', '汇报', '人员', '员工', '组织', '部门',
            '考勤', '日程', 'customer', 'lead', 'order', 'opportunity', 'contract',
            'invoice', 'finance', 'assess', 'report', 'personnel', 'attendance', 'schedule',
        ];
    }

    /**
     * 识别没有显式命中关键词、但明显像业务数据查询的问题.
     *
     * @param string $message 用户问题
     * @return bool 是否像业务数据查询
     */
    protected function looksLikeBusinessDataQuestion(string $message): bool
    {
        foreach ([
            '查询', '查一下', '查看', '列表', '统计', '汇总', '明细', '详情', '趋势',
            '排名', '分析', '数量', '多少', '有哪些', '谁', '我的', '下属', '权限',
            '数据范围', '本月', '上月', '今天', '昨天', '最近', '部门',
            'list', 'detail', 'stat', 'statistics', 'search', 'permission', 'scope',
        ] as $keyword) {
            if ($keyword !== '' && stripos($message, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 使用当前模型从 MCP 工具列表中筛选相关工具.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param string $key 模型 API Key
     * @param string $message 用户问题
     * @param array<int, array<string, mixed>> $toolsMeta 可用工具元数据列表
     * @return string[] 模型筛选出的工具名称
     */
    protected function selectMcpTools(BaseOption $option, string $key, string $message, array $toolsMeta): array
    {
        $this->selectedMcpToolReasons = [];

        $availableTools = array_map(function (array $tool) {
            return [
                'name' => $tool['name'] ?? '',
                'description' => $tool['description'] ?? '',
                'inputSchema' => $tool['inputSchema'] ?? ['type' => 'object', 'properties' => new \stdClass()],
            ];
        }, $toolsMeta);

        $prompt = "你是MCP工具调用规划器。请根据用户问题，从工具列表中选择最相关且需要实际调用的工具，最多5个。"
            . "如果问题需要多个业务维度，必须规划多个工具；可以在同一轮调用多个工具。"
            . "只返回JSON，格式为：{\"tools\":[\"tool_name\"],\"plans\":[{\"name\":\"tool_name\",\"reason\":\"为什么必须调用该工具\"}]}；"
            . "如果不需要工具，返回：{\"tools\":[],\"plans\":[]}。"
            . "不要返回解释、Markdown或额外文本。\n工具列表："
            . json_encode($availableTools, JSON_UNESCAPED_UNICODE);

        $filterOption = clone $option;
        $filterOption->messages = [
            ['role' => BaseOption::RULE_SYSTEM, 'content' => $prompt],
            ['role' => BaseOption::RULE_USER, 'content' => $message],
        ];
        $filterOption->tools = [];
        $filterOption->stream = false;

        try {
            $res = (new BaseCurl($key))->setBody($filterOption)->send(url: $filterOption->url);
            $content = trim((string)($res['choices'][0]['message']['content'] ?? ''));
        } catch (\Throwable $e) {
            $this->sendThinkingMessage('MCP工具筛选失败：' . $e->getMessage(), 'tool_error');
            return [];
        }

        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $content = $matches[0];
        }

        $data = json_decode($content, true);
        if (!is_array($data) || !isset($data['tools']) || !is_array($data['tools'])) {
            $this->sendMessage('MCP工具筛选结果解析失败', 'info');
            return [];
        }

        $validNames = array_column($toolsMeta, 'name');
        $toolNames = [];
        foreach ($data['tools'] as $tool) {
            $toolName = is_array($tool) ? ($tool['name'] ?? '') : $tool;
            if (is_string($toolName) && in_array($toolName, $validNames, true)) {
                $toolNames[] = $toolName;
            }
        }

        foreach (($data['plans'] ?? $data['tool_plan'] ?? []) as $plan) {
            if (!is_array($plan)) {
                continue;
            }

            $toolName = (string)($plan['name'] ?? $plan['tool'] ?? '');
            if ($toolName !== '' && in_array($toolName, $validNames, true)) {
                $toolNames[] = $toolName;
                $reason = trim((string)($plan['reason'] ?? ''));
                if ($reason !== '') {
                    $this->selectedMcpToolReasons[$toolName] = $reason;
                }
            }
        }

        $toolNames = array_values(array_unique($toolNames));

        $toolNames = array_slice($toolNames, 0, 5);
        $this->selectedMcpToolReasons = array_intersect_key($this->selectedMcpToolReasons, array_flip($toolNames));
        $this->sendMessage('MCP工具筛选完成，命中数量：' . count($toolNames), 'info');

        return $toolNames;
    }

    /**
     * 本地轻量检索 MCP 工具，避免每轮对话都额外调用 AI 做筛选.
     *
     * @param string $message 用户问题
     * @param array<int, array<string, mixed>> $toolsMeta 可用工具元数据列表
     * @return string[] 本地检索出的工具名称
     */
    protected function selectMcpToolsLocally(string $message, array $toolsMeta): array
    {
        $toolNames = $this->fallbackSelectMcpTools($message, $toolsMeta, 8);
        if (!$toolNames) {
            $this->selectedMcpToolReasons = [];
            return [];
        }

        $toolNames = $this->addMcpDependencyTools($message, $toolNames, $toolsMeta, 10);
        $this->selectedMcpToolReasons = $this->buildFallbackMcpToolReasons($toolNames, $toolsMeta);
        $this->sendMessage('MCP本地工具检索完成，命中数量：' . count($toolNames), 'info');

        return $toolNames;
    }

    /**
     * 建立并写入本轮 MCP 工具调用计划.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param string[] $toolNames 计划调用的工具名称
     * @param array<string, string> $reasons 工具调用原因，key 为工具名
     * @return void
     */
    protected function setMcpToolPlan(BaseOption $option, array $toolNames, array $reasons = []): void
    {
        $toolNames = array_values(array_unique(array_filter($toolNames, fn($toolName) => is_string($toolName) && $toolName !== '')));
        $this->mcpToolPlan = $toolNames;
        $this->mcpToolPlanReasons = array_intersect_key($reasons, array_flip($toolNames));

        if (!$toolNames) {
            return;
        }

        $option->setMessage($this->buildMcpToolPlanPrompt(), BaseOption::RULE_SYSTEM);
        $this->sendThinkingMessage('已生成MCP工具调用计划：' . implode('、', $toolNames), 'tool_select', [
            'tools' => $toolNames,
        ]);
    }

    /**
     * 构建 MCP 计划提示词，用于约束模型优先完成计划工具.
     *
     * @return string 系统提示词
     */
    protected function buildMcpToolPlanPrompt(): string
    {
        $plans = [];
        foreach ($this->mcpToolPlan as $toolName) {
            $plans[] = [
                'name' => $toolName,
                'reason' => $this->mcpToolPlanReasons[$toolName] ?? '该工具与用户问题相关，需要调用后再判断答案是否完整',
            ];
        }

        return "MCP工具调用计划如下："
            . json_encode($plans, JSON_UNESCAPED_UNICODE)
            . "\n执行要求："
            . "1. 计划中的工具都属于本次回答所需信息，优先调用这些工具；"
            . "2. 如果有多个工具，可以在同一轮并行发起多个tool_calls；"
            . "3. 在计划工具未完成前，不要直接输出最终回答；"
            . "4. 工具执行失败时，可以基于失败结果继续判断，但不要忽略其他未完成工具；"
            . "5. 所有必要工具完成后，再基于工具结果生成最终回答。";
    }

    /**
     * 判断 MCP 工具计划中是否仍有未执行工具.
     *
     * @param string[] $executedTools 已执行工具名称
     * @return bool 是否存在未完成计划工具
     */
    protected function hasPendingMcpPlanTools(array $executedTools): bool
    {
        return !empty($this->getPendingMcpPlanTools($executedTools));
    }

    /**
     * 获取 MCP 计划中尚未执行的工具.
     *
     * @param string[] $executedTools 已执行工具名称
     * @return string[] 未执行工具名称
     */
    protected function getPendingMcpPlanTools(array $executedTools): array
    {
        if (!$this->mcpToolPlan) {
            return [];
        }

        return array_values(array_diff($this->mcpToolPlan, array_values(array_unique($executedTools))));
    }

    /**
     * 构建缺失工具提示词，推动模型继续调用尚未完成的工具.
     *
     * @param string[] $missingTools 未执行工具名称
     * @return string 系统提示词
     */
    protected function buildMissingMcpToolsPrompt(array $missingTools): string
    {
        $plans = [];
        foreach ($missingTools as $toolName) {
            $plans[] = [
                'name' => $toolName,
                'reason' => $this->mcpToolPlanReasons[$toolName] ?? '该计划工具尚未调用，当前信息不足以直接回答',
            ];
        }

        return "当前还没有完成MCP工具调用计划，缺失工具如下："
            . json_encode($plans, JSON_UNESCAPED_UNICODE)
            . "\n请继续调用这些缺失工具。不要输出最终回答；如果需要多个工具，请在同一轮发起多个tool_calls。";
    }

    /**
     * 从请求对象中同步已注入工具的元信息，供计划、验证和参数修复使用.
     *
     * @param BaseOption $option AI 请求参数对象
     * @return void
     */
    protected function syncToolMetasFromOption(BaseOption $option): void
    {
        foreach ($option->tools as $tool) {
            $function = $tool['function'] ?? [];
            if (!is_array($function)) {
                continue;
            }

            $toolName = (string)($function['name'] ?? '');
            if ($toolName === '') {
                continue;
            }

            $this->mcpToolsMetaByName[$toolName] = [
                'name' => $toolName,
                'description' => $function['description'] ?? '',
                'inputSchema' => $this->normalizeToolInputSchema($function['parameters'] ?? ['type' => 'object', 'properties' => []]),
            ];
        }
    }

    /**
     * 获取当前对话可用工具元信息.
     *
     * @param BaseOption $option AI 请求参数对象
     * @return array<int, array<string, mixed>>
     */
    protected function getAvailableToolMetas(BaseOption $option): array
    {
        $this->syncToolMetasFromOption($option);

        return array_values($this->mcpToolsMetaByName);
    }

    /**
     * 生成 Agent 工具调用计划.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @param string $message 用户问题
     * @return array{required_tools: string[], tool_order: string[], success_criteria: string, assumptions: array}
     */
    protected function buildAgentToolPlan(BaseOption $option, ChatModels $modelsInfo, string $message): array
    {
        $toolsMeta = $this->getAvailableToolMetas($option);
        if (!$toolsMeta) {
            return $this->buildFallbackAgentToolPlan([]);
        }

        $validNames = array_values(array_filter(array_map(fn(array $tool) => (string)($tool['name'] ?? ''), $toolsMeta)));
        $fallbackPlan = $this->buildFallbackAgentToolPlan($this->mcpToolPlan ?: $validNames);

        $this->sendThinkingMessage('正在规划需要调用的工具', 'tool_plan', [
            'available_tools' => $validNames,
            'planned_tools' => $fallbackPlan['required_tools'],
        ]);

        if (count($validNames) <= 1) {
            $this->sendAgentToolPlanThinkingMessage($fallbackPlan);
            $this->appendAgentToolPlanPrompt($option, $fallbackPlan);
            return $fallbackPlan;
        }

        $plannerOption = clone $option;
        $plannerOption->messages = [
            [
                'role' => BaseOption::RULE_SYSTEM,
                'content' => "你是Agent工具规划器。根据用户问题和工具列表，规划完成回答所需工具。"
                    . "只返回JSON：{\"goal\":\"目标\",\"required_tools\":[\"tool\"],\"tool_order\":[\"tool\"],\"success_criteria\":\"成功标准\",\"assumptions\":[]}"
                    . "。不要返回Markdown或解释。工具列表："
                    . json_encode($toolsMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ],
            [
                'role' => BaseOption::RULE_USER,
                'content' => $message,
            ],
        ];
        $plannerOption->tools = [];
        $plannerOption->stream = false;

        try {
            $res = (new BaseCurl($modelsInfo->key))->setBody($plannerOption)->send(url: $plannerOption->url);
            $data = $this->decodeJsonObject((string)($res['choices'][0]['message']['content'] ?? ''));
        } catch (\Throwable $e) {
            $this->sendThinkingMessage('工具规划失败，使用已筛选工具计划：' . $e->getMessage(), 'tool_plan');
            $this->sendAgentToolPlanThinkingMessage($fallbackPlan);
            $this->appendAgentToolPlanPrompt($option, $fallbackPlan);
            return $fallbackPlan;
        }

        if (!$data) {
            $this->sendThinkingMessage('工具规划结果解析失败，使用已筛选工具计划', 'tool_plan');
            $this->sendAgentToolPlanThinkingMessage($fallbackPlan);
            $this->appendAgentToolPlanPrompt($option, $fallbackPlan);
            return $fallbackPlan;
        }

        $requiredTools = $this->filterValidToolNames((array)($data['required_tools'] ?? $data['tools'] ?? []), $validNames);
        $toolOrder = $this->filterValidToolNames((array)($data['tool_order'] ?? $requiredTools), $validNames);
        $requiredTools = $requiredTools ?: $toolOrder;

        if (!$requiredTools) {
            $this->sendAgentToolPlanThinkingMessage($fallbackPlan);
            $this->appendAgentToolPlanPrompt($option, $fallbackPlan);
            return $fallbackPlan;
        }

        $plan = [
            'required_tools' => $requiredTools,
            'tool_order' => $toolOrder ?: $requiredTools,
            'success_criteria' => trim((string)($data['success_criteria'] ?? '完成必要工具查询，并基于工具结果回答')),
            'assumptions' => is_array($data['assumptions'] ?? null) ? $data['assumptions'] : [],
        ];

        $this->mcpToolPlan = $plan['required_tools'];
        $this->sendAgentToolPlanThinkingMessage($plan);
        $this->appendAgentToolPlanPrompt($option, $plan);

        return $plan;
    }

    /**
     * 将 Agent 工具计划写回模型上下文，约束后续工具调用顺序和行为.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param array $plan Agent 工具计划
     * @return void
     */
    protected function appendAgentToolPlanPrompt(BaseOption $option, array $plan): void
    {
        $requiredTools = array_values(array_unique(array_filter(
            (array)($plan['required_tools'] ?? []),
            fn($toolName) => is_string($toolName) && $toolName !== ''
        )));

        if (!$requiredTools) {
            return;
        }

        $this->mcpToolPlan = $requiredTools;

        $option->setMessage(
            "Agent工具计划已更新："
            . json_encode($plan, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            . "\n执行规则：先调用计划内缺失工具；工具空结果或失败时允许修复参数后重试；信息不足时不要假装已查询到数据。",
            BaseOption::RULE_SYSTEM
        );
    }

    /**
     * 构造本地兜底工具计划.
     *
     * @param string[] $toolNames
     * @return array{required_tools: string[], tool_order: string[], success_criteria: string, assumptions: array}
     */
    protected function buildFallbackAgentToolPlan(array $toolNames): array
    {
        $toolNames = array_values(array_unique(array_filter($toolNames, fn($toolName) => is_string($toolName) && $toolName !== '')));

        return [
            'required_tools' => $toolNames,
            'tool_order' => $toolNames,
            'success_criteria' => $toolNames ? '完成计划工具查询，并基于工具结果回答' : '不需要工具或当前无可用工具，直接回答',
            'assumptions' => [],
        ];
    }

    /**
     * 过滤模型返回的工具名，只保留当前请求可用的工具.
     *
     * @param array<int, mixed> $toolNames
     * @param string[] $validNames
     * @return string[]
     */
    protected function filterValidToolNames(array $toolNames, array $validNames): array
    {
        $result = [];
        foreach ($toolNames as $tool) {
            $toolName = is_array($tool) ? (string)($tool['name'] ?? $tool['tool'] ?? '') : (string)$tool;
            if ($toolName !== '' && in_array($toolName, $validNames, true)) {
                $result[] = $toolName;
            }
        }

        return array_values(array_unique($result));
    }

    /**
     * 将 Agent 工具计划作为 thinking 事件返回前端.
     *
     * @param array $plan Agent 工具计划
     * @return void
     */
    protected function sendAgentToolPlanThinkingMessage(array $plan): void
    {
        $tools = $plan['required_tools'] ?? [];
        $content = $tools
            ? '已生成Agent工具计划：' . implode('、', $tools)
            : 'Agent判断当前没有必须调用的工具';

        $this->sendThinkingMessage($content, 'tool_plan', [
            'required_tools' => $tools,
            'tool_order' => $plan['tool_order'] ?? $tools,
            'success_criteria' => $plan['success_criteria'] ?? '',
            'assumptions' => $plan['assumptions'] ?? [],
        ]);
    }

    /**
     * 从模型输出中提取 JSON 对象.
     *
     * @param string $content 模型输出文本
     * @return array<string, mixed>
     */
    protected function decodeJsonObject(string $content): array
    {
        $content = trim($content);
        if ($content === '') {
            return [];
        }

        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $content = $matches[0];
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * 根据兜底筛选结果生成工具调用原因.
     *
     * @param string[] $toolNames
     * @param array<int, array<string, mixed>> $toolsMeta 可用工具元数据列表
     * @return array<string, string> 工具名到调用原因的映射
     */
    protected function buildFallbackMcpToolReasons(array $toolNames, array $toolsMeta): array
    {
        $toolsByName = [];
        foreach ($toolsMeta as $tool) {
            $name = (string)($tool['name'] ?? '');
            if ($name !== '') {
                $toolsByName[$name] = $tool;
            }
        }

        $reasons = [];
        foreach ($toolNames as $toolName) {
            $description = trim((string)($toolsByName[$toolName]['description'] ?? ''));
            $reasons[$toolName] = $description !== '' ? $description : '本地兜底筛选认为该工具与用户问题相关';
        }

        return $reasons;
    }

    /**
     * 根据问题句式补充工具依赖，例如“某人的客户”需要先查人员再查客户.
     *
     * @param string $message 用户问题
     * @param string[] $toolNames 已选工具名称
     * @param array<int, array<string, mixed>> $toolsMeta 可用工具元数据列表
     * @param int $limit 最多返回工具数量
     * @return string[] 补充依赖后的工具名称
     */
    protected function addMcpDependencyTools(string $message, array $toolNames, array $toolsMeta, int $limit = 10): array
    {
        if (!$this->messageMayReferencePerson($message) || !$this->selectedToolsNeedTargetUser($toolNames, $toolsMeta)) {
            return array_slice(array_values(array_unique($toolNames)), 0, $limit);
        }

        $dependencyTools = $this->findPersonnelLookupTools($toolsMeta);
        if (!$dependencyTools) {
            return array_slice(array_values(array_unique($toolNames)), 0, $limit);
        }

        return array_slice(array_values(array_unique(array_merge($dependencyTools, $toolNames))), 0, $limit);
    }

    /**
     * 判断问题中是否可能出现人员指代.
     */
    protected function messageMayReferencePerson(string $message): bool
    {
        if (preg_match('/[\x{4e00}-\x{9fa5}A-Za-z0-9_]{2,20}的/u', $message)) {
            return true;
        }

        foreach (['员工', '人员', '同事', '下属', '负责人', '销售', '业务员'] as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 判断已选工具是否需要目标用户参数.
     *
     * @param string[] $toolNames
     * @param array<int, array<string, mixed>> $toolsMeta
     */
    protected function selectedToolsNeedTargetUser(array $toolNames, array $toolsMeta): bool
    {
        $selected = array_flip($toolNames);
        foreach ($toolsMeta as $tool) {
            $toolName = (string)($tool['name'] ?? '');
            if ($toolName === '' || !isset($selected[$toolName])) {
                continue;
            }

            $schema = $tool['inputSchema'] ?? [];
            $properties = is_array($schema) && is_array($schema['properties'] ?? null) ? $schema['properties'] : [];
            foreach (array_keys($properties) as $field) {
                if (in_array((string)$field, ['target_user', 'target_user_id', 'user_id', 'uid', 'owner_id', 'salesman_id'], true)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 查找人员检索类工具，优先选择 search 工具.
     *
     * @param array<int, array<string, mixed>> $toolsMeta
     * @return string[]
     */
    protected function findPersonnelLookupTools(array $toolsMeta): array
    {
        $candidates = [];
        foreach ($toolsMeta as $tool) {
            $toolName = (string)($tool['name'] ?? '');
            if ($toolName === '') {
                continue;
            }

            $text = mb_strtolower($toolName . ' ' . (string)($tool['description'] ?? ''), 'UTF-8');
            if (!str_contains($text, 'personnel') && !str_contains($text, '人员') && !str_contains($text, '员工')) {
                continue;
            }

            $score = 1;
            if (str_contains($text, 'search') || str_contains($text, '搜索')) {
                $score += 5;
            }
            if (str_contains($text, 'list') || str_contains($text, '列表')) {
                $score += 2;
            }

            $candidates[] = ['name' => $toolName, 'score' => $score];
        }

        usort($candidates, fn(array $a, array $b) => $b['score'] <=> $a['score']);

        return array_values(array_unique(array_column(array_slice($candidates, 0, 2), 'name')));
    }

    /**
     * AI 筛选失败时的本地兜底筛选.
     *
     * @param string $message 用户问题
     * @param array<int, array<string, mixed>> $toolsMeta 可用工具元数据列表
     * @param int $limit 最多返回工具数量
     * @return string[] 兜底筛选出的工具名称
     */
    protected function fallbackSelectMcpTools(string $message, array $toolsMeta, int $limit = 5): array
    {
        $scoredTools = [];
        foreach ($toolsMeta as $tool) {
            $toolName = (string)($tool['name'] ?? '');
            if ($toolName === '') {
                continue;
            }

            $score = $this->scoreMcpToolRelevance($message, $tool);
            if ($score > 0) {
                $scoredTools[] = ['name' => $toolName, 'score' => $score];
            }
        }

        usort($scoredTools, fn(array $a, array $b) => $b['score'] <=> $a['score']);
        $toolNames = array_column(array_slice($scoredTools, 0, $limit), 'name');

        if (!$toolNames && $this->looksLikeBusinessDataQuestion($message)) {
            $toolNames = array_values(array_filter(array_map(
                fn(array $tool) => (string)($tool['name'] ?? ''),
                array_slice($toolsMeta, 0, $limit)
            )));
        }

        return array_slice(array_values(array_unique($toolNames)), 0, $limit);
    }

    /**
     * 根据工具名、描述和入参字段对用户问题做简单相关性评分.
     *
     * @param string $message 用户问题
     * @param array<string, mixed> $tool 单个工具元数据
     * @return int 相关性分数，越高越相关
     */
    protected function scoreMcpToolRelevance(string $message, array $tool): int
    {
        $message = mb_strtolower($message, 'UTF-8');
        $toolName = mb_strtolower((string)($tool['name'] ?? ''), 'UTF-8');
        $description = mb_strtolower((string)($tool['description'] ?? ''), 'UTF-8');
        $schema = $tool['inputSchema'] ?? [];
        $properties = is_array($schema) && is_array($schema['properties'] ?? null) ? array_keys($schema['properties']) : [];
        $haystack = $toolName . ' ' . $description . ' ' . implode(' ', $properties);

        $score = 0;
        foreach ($this->getMcpToolRelevanceKeywords($message) as $keyword) {
            if ($keyword !== '' && str_contains($haystack, mb_strtolower($keyword, 'UTF-8'))) {
                $score += 3;
            }
            if ($keyword !== '' && str_contains($message, mb_strtolower($keyword, 'UTF-8'))) {
                $score += 1;
            }
        }

        foreach (preg_split('/[\s,，。；;：:、!?！？\/\\\\|]+/u', $message) ?: [] as $token) {
            $token = trim($token);
            if (mb_strlen($token, 'UTF-8') < 2) {
                continue;
            }
            if (str_contains($haystack, $token)) {
                $score += 2;
            }
        }

        return $score;
    }

    /**
     * 根据用户问题扩展 MCP 工具相关性关键词.
     *
     * @param string $message 用户问题
     * @return string[] 相关性匹配关键词
     */
    protected function getMcpToolRelevanceKeywords(string $message): array
    {
        $keywords = [
            '客户' => ['customer', '客户'],
            '线索' => ['lead', '线索'],
            '订单' => ['order', '订单'],
            '商机' => ['opportunity', '商机'],
            '合同' => ['contract', '合同'],
            '联系人' => ['contact', 'liaison', '联系人'],
            '发票' => ['invoice', '发票'],
            '财务' => ['finance', 'bill', '财务', '流水', '账目', '收支'],
            '流水' => ['finance', 'bill', '流水', '账目'],
            '绩效' => ['assess', '绩效'],
            '汇报' => ['report', '汇报'],
            '考勤' => ['attendance', '考勤'],
            '日程' => ['schedule', '日程'],
            '人员' => ['personnel', '人员', '员工'],
            '员工' => ['personnel', '人员', '员工'],
            '组织' => ['org', 'department', '组织', '部门'],
            '部门' => ['org', 'department', '组织', '部门'],
            '权限' => ['permission', 'scope', 'data_scope', '权限'],
            '下属' => ['subordinates', '下属'],
            '项目' => ['program', 'project', '项目'],
        ];

        $result = [];
        foreach ($keywords as $needle => $items) {
            if (str_contains($message, $needle)) {
                $result = array_merge($result, $items);
            }
        }

        return array_values(array_unique(array_merge($result, $this->getMcpKeywords())));
    }

    /**
     * 聊天接口未经过 McpAuthMiddleware，这里补齐 MCP 工具需要的用户上下文.
     *
     * @return void
     */
    protected function injectMcpUserContext(): void
    {
        $request = request();
        if ($request->input('mcp_user_db_id')) {
            return;
        }

        $user = auth('admin')->user();
        if (!$user) {
            return;
        }

        app(McpUserContextResolver::class)->merge($request, $user);
    }

    /**
     * 规范化流式累积后的 tool_call，作为 assistant 消息回填给模型.
     *
     * @param array<string, mixed> $toolCall 流式聚合得到的工具调用数据
     * @return array<string, mixed> 补齐 id、type、function 字段后的工具调用数据
     */
    protected function normalizeToolCallForMessage(array $toolCall): array
    {
        $toolCall['id'] ??= 'call_' . str_replace('-', '', v4());
        $toolCall['type'] ??= 'function';
        $toolCall['function'] ??= [];
        $toolCall['function']['name'] ??= '';
        $toolCall['function']['arguments'] ??= '{}';

        return $toolCall;
    }

    /**
     * 回填 assistant tool_calls 消息。DeepSeek thinking + tool call 轮次需要保留 reasoning_content.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @param array<int, array<string, mixed>> $toolCalls 本轮模型返回的工具调用列表
     * @param string $content assistant 可见内容
     * @param string $reasoningContent reasoning 思考内容
     * @return void
     */
    protected function appendAssistantToolCallMessage(BaseOption $option, ChatModels $modelsInfo, array $toolCalls, string $content = '', string $reasoningContent = ''): void
    {
        $message = [
            'role' => BaseOption::RULE_ASSISTANT,
            'content' => $content,
            'tool_calls' => $toolCalls,
        ];

        if ($this->isDeepseekOption($option) && trim($reasoningContent) !== '') {
            $message['reasoning_content'] = $reasoningContent;
        }

        $option->messages[] = $message;
    }

    /**
     * 解析模型工具调用参数.
     *
     * @param array<string, mixed> $toolCall 单个工具调用数据
     * @return array<string, mixed> 解析后的参数数组，解析失败返回空数组
     */
    protected function parseToolArguments(array $toolCall): array
    {
        $arguments = $toolCall['function']['arguments'] ?? '{}';
        if (is_array($arguments)) {
            return $arguments;
        }

        $decoded = json_decode((string)$arguments, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * 发送模型请求前压缩消息体，只影响本次模型输入，不影响原始记录保存.
     */
    protected function compressOptionMessages(BaseOption $option, string $scene = ''): void
    {
        $beforeLength = $this->getMessagesLength($option->messages);
        $hasLongItem = $this->hasLongMessageContent($option->messages);

        if ($beforeLength <= self::MESSAGE_BODY_COMPRESS_THRESHOLD && !$hasLongItem) {
            return;
        }

        $option->messages = $this->compressMessages($option->messages);
        $afterLength = $this->getMessagesLength($option->messages);

        $this->sendMessage(sprintf(
            '消息体已压缩%s：%d -> %d',
            $scene ? "({$scene})" : '',
            $beforeLength,
            $afterLength
        ), 'info');
    }

    /**
     * @param array<int, array<string, mixed>> $messages
     */
    protected function getMessagesLength(array $messages): int
    {
        return strlen(json_encode($messages, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param array<int, array<string, mixed>> $messages
     */
    protected function hasLongMessageContent(array $messages): bool
    {
        foreach ($messages as $message) {
            if (strlen((string)($message['content'] ?? '')) > self::MESSAGE_CONTENT_MAX_LENGTH) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, array<string, mixed>> $messages
     * @return array<int, array<string, mixed>>
     */
    protected function compressMessages(array $messages): array
    {
        $messages = array_values(array_map(fn(array $message) => $this->compressSingleMessage($message), $messages));
        if ($this->getMessagesLength($messages) <= self::MESSAGE_BODY_TARGET_LENGTH) {
            return $messages;
        }

        $firstSystem = null;
        if (($messages[0]['role'] ?? '') === BaseOption::RULE_SYSTEM) {
            $firstSystem = array_shift($messages);
        }

        $keepCount = min(5, count($messages));
        $recent = $keepCount > 0 ? array_slice($messages, -$keepCount) : [];
        $older = $keepCount > 0 ? array_slice($messages, 0, -$keepCount) : $messages;

        $compressed = [];
        if ($firstSystem) {
            $compressed[] = $this->compressSingleMessage($firstSystem, self::MESSAGE_CONTENT_MAX_LENGTH);
        }

        if ($older) {
            $compressed[] = [
                'role' => BaseOption::RULE_SYSTEM,
                'content' => $this->buildMessagesSummary($older),
            ];
        }

        foreach ($recent as $message) {
            $compressed[] = $this->compressSingleMessage($message);
        }

        while ($this->getMessagesLength($compressed) > self::MESSAGE_BODY_TARGET_LENGTH && count($compressed) > 3) {
            array_splice($compressed, 1, 1);
        }

        return $compressed;
    }

    /**
     * @param array<string, mixed> $message
     * @return array<string, mixed>
     */
    protected function compressSingleMessage(array $message, int $maxLength = self::MESSAGE_CONTENT_MAX_LENGTH): array
    {
        $content = (string)($message['content'] ?? '');
        if (strlen($content) <= $maxLength) {
            return $message;
        }

        $message['content'] = mb_substr($content, 0, $maxLength, 'UTF-8')
            . "\n\n[以上内容已截断压缩，原始长度：" . strlen($content) . ']';

        return $message;
    }

    /**
     * @param array<int, array<string, mixed>> $messages
     */
    protected function buildMessagesSummary(array $messages): string
    {
        $summary = "以下是较早对话的压缩摘要，仅用于保持上下文：\n";
        foreach ($messages as $message) {
            $role = $message['role'] ?? 'unknown';
            $content = trim((string)($message['content'] ?? ''));
            if ($content === '') {
                continue;
            }

            $summary .= '[' . $role . '] ' . mb_substr($content, 0, 1000, 'UTF-8') . "\n";
            if (strlen($summary) >= self::MESSAGE_SUMMARY_MAX_LENGTH) {
                break;
            }
        }

        if (strlen($summary) > self::MESSAGE_SUMMARY_MAX_LENGTH) {
            $summary = mb_substr($summary, 0, self::MESSAGE_SUMMARY_MAX_LENGTH, 'UTF-8')
                . "\n[摘要已截断]";
        }

        return $summary;
    }

    /**
     * 兼容不同模型的流式思考字段.
     *
     * @param array<string, mixed> $json 单条模型流式响应 JSON
     * @return string 提取到的思考内容，没有则返回空字符串
     */
    protected function getReasoningContent(array $json): string
    {
        $delta = $json['choices'][0]['delta'] ?? [];

        foreach (['reasoning_content', 'reasoning', 'reasoning_text', 'think'] as $field) {
            if (!empty($delta[$field]) && is_string($delta[$field])) {
                return $delta[$field];
            }
        }

        if (!empty($json['choices'][0]['message']['reasoning_content']) && is_string($json['choices'][0]['message']['reasoning_content'])) {
            return $json['choices'][0]['message']['reasoning_content'];
        }

        return '';
    }

    /**
     * 判断当前流式片段是否属于工具调用阶段，避免把中间协议数据返回给用户.
     *
     * @param array<string, mixed> $json 单条模型流式响应 JSON
     * @return bool 是否为工具调用相关片段
     */
    protected function isToolCallChunk(array $json): bool
    {
        $choice = $json['choices'][0] ?? [];
        $finishReason = $choice['finish_reason'] ?? null;

        if (in_array($finishReason, ['tool_calls', 'function_call'], true)) {
            return true;
        }

        $delta = $choice['delta'] ?? [];
        $message = $choice['message'] ?? [];

        return !empty($delta['tool_calls'])
            || !empty($delta['function_call'])
            || !empty($message['tool_calls'])
            || !empty($message['function_call']);
    }

    /**
     * 从异常响应体中提取模型服务返回的错误信息.
     *
     * @param string $response 原始 SSE 响应内容
     * @return string 错误信息，没有则返回空字符串
     */
    protected function getStreamErrorMessage(string $response): string
    {
        foreach (explode("\n", str_replace(["\r\n", "\r"], "\n", $response)) as $line) {
            $line = trim($line);
            if ($line === '' || !str_starts_with($line, 'data:')) {
                continue;
            }

            $payload = trim(substr($line, 5));
            if ($payload === '' || $payload === '[DONE]') {
                continue;
            }

            $json = json_decode($payload, true, 512, JSON_BIGINT_AS_STRING);
            if (json_last_error() === JSON_ERROR_NONE && !empty($json['error']['message'])) {
                return (string)$json['error']['message'];
            }
        }

        return '';
    }

    /**
     * 从 curl 回调数据中提取一个或多个 SSE data payload.
     *
     * @param string $data curl 流式回调收到的原始数据块
     * @return string[] 提取后的 payload 列表
     */
    protected function getStreamPayloads(string $data): array
    {
        $payloads = [];
        foreach (explode("\n", str_replace(["\r\n", "\r"], "\n", $data)) as $line) {
            $line = trim($line);
            if ($line === '' || !str_starts_with($line, 'data:')) {
                continue;
            }

            $payload = trim(substr($line, 5));
            if ($payload !== '') {
                $payloads[] = $payload;
            }
        }

        return $payloads ?: [trim(str_replace(["\n", 'data:'], '', $data))];
    }

    /**
     * 将模型思考内容实时发送给前端，避免等待期间无输出.
     *
     * @param string $content 模型输出的思考内容
     * @return bool 是否发送成功
     */
    protected function sendReasoningMessage(string $content): bool
    {
        return $this->sendThinkingMessage($content, 'reasoning');
    }

    /**
     * 将思考过程、工具执行过程和工具结果按统一 SSE 协议发送给前端.
     *
     * @param string $content 前端展示的思考内容
     * @param string $stage 思考阶段标识
     * @param array<string, mixed> $payload 需要附带给前端的结构化数据
     * @return bool 是否发送成功
     */
    protected function sendThinkingMessage(string $content, string $stage = 'thinking', array $payload = []): bool
    {
        if ($content === '') {
            return true;
        }

        $event = array_merge($payload, [
            'type' => 'thinking',
            'stage' => $stage,
            'choices' => [
                [
                    'index' => 0,
                    'delta' => [
                        'content' => $content,
                        'reasoning_content' => $content,
                    ],
                ],
            ],
        ]);

        $data = 'data: ' . json_encode($event, JSON_UNESCAPED_UNICODE) . "\n\n";

        return $this->send($data);
    }

    /**
     * 将工具执行结果返回给前端，同时避免单个 thinking 事件过大.
     *
     * @param string $toolName 工具名称
     * @param string $content 工具执行结果原文
     * @param string $stage 工具结果阶段，成功或失败
     * @param string $statusText 自定义状态提示
     * @param array<string, mixed> $arguments 本次工具调用参数
     * @return bool 是否发送成功
     */
    protected function sendToolResultThinkingMessage(string $toolName, string $content, string $stage = 'tool_result', string $statusText = '', array $arguments = []): bool
    {
        $result = $this->formatToolResultForFrontend($content);
        $visibleResult = $this->truncateText($result['content'], self::TOOL_RESULT_VISIBLE_MAX_LENGTH, '结果过长，已折叠到结构化字段');
        $statusText = $statusText ?: (($stage === 'tool_error' ? 'MCP工具执行失败：' : 'MCP工具执行完成：') . $toolName);
        $message = $visibleResult === ''
            ? $statusText
            : $statusText . "\n工具结果摘要：" . $visibleResult;

        return $this->sendThinkingMessage($message, $stage, array_merge($this->buildToolArgumentsPayload($arguments), [
            'tool_name' => $toolName,
            'tool_result' => $result['content'],
            'tool_result_truncated' => $result['truncated'],
            'tool_result_length' => $result['length'],
        ]));
    }

    /**
     * 将工具请求参数返回给前端.
     *
     * @param string $toolName 工具名称
     * @param array<string, mixed> $arguments 本次工具调用参数
     * @return bool 是否发送成功
     */
    protected function sendToolStartThinkingMessage(string $toolName, array $arguments): bool
    {
        $argumentsInfo = $this->formatToolArgumentsForFrontend($arguments);
        $message = '正在调用工具：' . $toolName . "\n请求参数：" . $argumentsInfo['content'];

        return $this->sendThinkingMessage($message, 'tool_start', array_merge($this->buildToolArgumentsPayload($arguments), [
            'tool_name' => $toolName,
        ]));
    }

    /**
     * 构建工具参数的前端 payload，参数过长时只返回文本摘要.
     *
     * @param array<string, mixed> $arguments 本次工具调用参数
     * @return array<string, mixed> 工具参数文本、长度、截断状态和可选原始参数
     */
    protected function buildToolArgumentsPayload(array $arguments): array
    {
        $argumentsInfo = $this->formatToolArgumentsForFrontend($arguments);
        $payload = [
            'tool_arguments_text' => $argumentsInfo['content'],
            'tool_arguments_truncated' => $argumentsInfo['truncated'],
            'tool_arguments_length' => $argumentsInfo['length'],
        ];

        if (!$argumentsInfo['truncated']) {
            $payload['tool_arguments'] = $arguments;
        }

        return $payload;
    }

    /**
     * 格式化工具请求参数，控制单个 SSE 事件体积.
     *
     * @param array<string, mixed> $arguments 本次工具调用参数
     * @return array{content: string, truncated: bool, length: int} 参数文本、截断状态和长度
     */
    protected function formatToolArgumentsForFrontend(array $arguments): array
    {
        $content = empty($arguments)
            ? '{}'
            : json_encode($arguments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (!is_string($content) || $content === '') {
            $content = '{}';
        }

        $length = $this->stringLength($content);
        if ($length <= self::TOOL_ARGUMENT_EVENT_MAX_LENGTH) {
            return ['content' => $content, 'truncated' => false, 'length' => $length];
        }

        return [
            'content' => $this->substring($content, 0, self::TOOL_ARGUMENT_EVENT_MAX_LENGTH) . '...（参数过长，已截断）',
            'truncated' => true,
            'length' => $length,
        ];
    }

    /**
     * 格式化工具结果，尽量保持 JSON 可读并限制返回长度.
     *
     * @param string $content 工具执行结果原文
     * @return array{content: string, truncated: bool, length: int} 结果文本、截断状态和长度
     */
    protected function formatToolResultForFrontend(string $content): array
    {
        $content = trim($content);
        if ($content === '') {
            return ['content' => '', 'truncated' => false, 'length' => 0];
        }

        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (is_string($encoded) && $encoded !== '') {
                $content = $encoded;
            }
        }

        $length = $this->stringLength($content);
        if ($length <= self::TOOL_RESULT_EVENT_MAX_LENGTH) {
            return ['content' => $content, 'truncated' => false, 'length' => $length];
        }

        return [
            'content' => $this->substring($content, 0, self::TOOL_RESULT_EVENT_MAX_LENGTH) . '...（结果过长，已截断）',
            'truncated' => true,
            'length' => $length,
        ];
    }

    /**
     * 计算字符串长度，优先使用 UTF-8 多字节长度.
     *
     * @param string $content 待计算内容
     * @return int 字符串长度
     */
    protected function stringLength(string $content): int
    {
        return function_exists('mb_strlen') ? mb_strlen($content, 'UTF-8') : strlen($content);
    }

    /**
     * 截取字符串，优先按 UTF-8 多字节字符截取.
     *
     * @param string $content 待截取内容
     * @param int $start 起始位置
     * @param int $length 截取长度
     * @return string 截取后的字符串
     */
    protected function substring(string $content, int $start, int $length): string
    {
        return function_exists('mb_substr') ? mb_substr($content, $start, $length, 'UTF-8') : substr($content, $start, $length);
    }

    /**
     * 截断过长文本，用于控制返回给前端的可见摘要长度.
     *
     * @param string $content 待截断内容
     * @param int $maxLength 最大可见长度
     * @param string $suffix 截断提示文案
     * @return string 截断后的文本，空内容返回空字符串
     */
    protected function truncateText(string $content, int $maxLength, string $suffix = '已截断'): string
    {
        $content = trim($content);
        if ($content === '') {
            return '';
        }

        if ($this->stringLength($content) <= $maxLength) {
            return $content;
        }

        return $this->substring($content, 0, $maxLength) . "...（{$suffix}）";
    }

    /**
     * 执行流式对话请求，并调度 MCP 工具计划、执行、修复和最终回答.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatApplications $appInfo 当前聊天应用
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @param string $message 用户问题
     * @param array $recordData 当前对话记录数据
     * @param array $saveData 失败时保存记录所需上下文
     * @return array{0: string, 1: array} SSE 响应内容和记录数据
     * @throws BindingResolutionException
     */
    public function streamRequest(BaseOption $option, ChatApplications $appInfo, ChatModels $modelsInfo, string $message, array $recordData = [], array $saveData = [])
    {
        $this->sendMessage('开始请求', 'info');
        $response = '';
        $uuid = (string)($recordData['chat_record_uuid'] ?? '');
        $executedPlanTools = [];
        $executedToolCalls = [];
        $toolObservations = [];
        $failedTools = [];
        $emptyResultTools = [];
        $repairAttempts = [];
        $agentPlan = $this->buildFallbackAgentToolPlan([]);

        $this->sendModelCapabilityThinkingMessage($option, $modelsInfo);
        $this->syncToolMetasFromOption($option);

        if ($option->tools && !$this->supportsToolCalling($option, $modelsInfo)) {
            $this->sendThinkingMessage('当前模型不支持本轮工具调用，已降级为普通回答；如需MCP能力请切换支持工具调用的模型', 'model_capability', [
                'model' => $this->getOptionModelName($option, $modelsInfo),
                'planned_tools' => $this->mcpToolPlan,
            ]);
            $option->setMessage('当前模型不支持工具调用，本轮不能调用MCP工具。请基于已有上下文回答，并明确说明无法实时查询工具数据。', BaseOption::RULE_SYSTEM);
            $option->tools = [];
            $this->mcpToolPlan = [];
        } elseif ($option->tools) {
            $agentPlan = $this->buildAgentToolPlan($option, $modelsInfo, $message);
        }

        for ($round = 1; $round <= self::MAX_TOOL_CALL_ROUNDS; $round++) {
            $this->sendThinkingMessage($round === 1 ? '正在思考中' : '正在根据工具结果继续思考', 'thinking', [
                'agent_state' => $round === 1 ? 'PLAN' : 'VERIFY',
                'max_rounds' => self::MAX_TOOL_CALL_ROUNDS,
            ]);

            $suppressVisibleOutput = $this->hasPendingMcpPlanTools($executedPlanTools);
            $res = $this->stream($modelsInfo->key, $option, $uuid, $suppressVisibleOutput);
            $response .= (string)($res['response'] ?? '');

            $errorMessage = $this->getStreamErrorMessage((string)($res['rawResponse'] ?? $res['response'] ?? ''));
            if ($errorMessage !== '') {
                $this->sendThinkingMessage('模型请求失败：' . $errorMessage, 'tool_error', [
                    'stop_reason' => 'model_error',
                ]);

                if ($toolObservations) {
                    return [
                        $this->finalizeToolResponse(
                            $option,
                            $modelsInfo,
                            $uuid,
                            $response,
                            $toolObservations,
                            'model_error',
                            $this->buildAgentStatePayload($executedPlanTools, $failedTools, $emptyResultTools, $repairAttempts)
                        ),
                        $recordData,
                    ];
                }

                $this->sendMessage($errorMessage, 'error', $saveData);
                return [$response, $recordData];
            }

            $toolCalls = $res['toolCalls'] ?? [];
            if (!$toolCalls && !empty($res['toolCall'])) {
                $toolCalls = [$res['toolCall']];
            }

            $toolCalls = array_values(array_filter(array_map(
                fn(array $toolCall) => $this->normalizeToolCallForMessage($toolCall),
                $toolCalls
            ), fn(array $toolCall) => (string)($toolCall['function']['name'] ?? '') !== ''));

            $this->sendMessage('判断是否需要工具调用：' . json_encode([
                    'tool_count' => count($toolCalls),
                    'has_response' => ($res['response'] ?? '') !== '',
                ], JSON_UNESCAPED_UNICODE), 'info');

            if (!$toolCalls) {
                if ($this->hasPendingMcpPlanTools($executedPlanTools)) {
                    $missingTools = $this->getPendingMcpPlanTools($executedPlanTools);
                    $this->sendThinkingMessage('计划中仍有未调用工具，继续调用：' . implode('、', $missingTools), 'tool_verify', [
                        'missing_tools' => $missingTools,
                        'agent_state' => 'VERIFY',
                    ]);
                    $option->setMessage($this->buildMissingMcpToolsPrompt($missingTools));
                    continue;
                }

                $this->sendThinkingMessage('思考完成', 'tool_complete');
                return [$response, $recordData];
            }

            $this->sendThinkingMessage('准备执行MCP工具数量：' . count($toolCalls), 'tool_start');
            $this->appendAssistantToolCallMessage(
                $option,
                $modelsInfo,
                $toolCalls,
                (string)($res['assistantContent'] ?? ''),
                (string)($res['reasoningContent'] ?? '')
            );

            $executedInThisRound = 0;
            $reusedInThisRound = 0;
            $repairedInThisRound = 0;
            $repairQueue = [];
            foreach ($toolCalls as $toolCall) {
                $toolName = (string)($toolCall['function']['name'] ?? '');
                $arguments = $this->parseToolArguments($toolCall);
                $signature = $this->getToolCallSignature($toolCall);

                if (isset($executedToolCalls[$signature])) {
                    $content = (string)($executedToolCalls[$signature]['content'] ?? '');
                    $reusedInThisRound++;
                    $this->sendThinkingMessage('工具已用相同参数执行过，复用上一轮结果：' . $toolName, 'tool_skip', array_merge($this->buildToolArgumentsPayload($arguments), [
                        'tool_name' => $toolName,
                        'tool_call_signature' => $signature,
                    ]));
                } else {
                    [$recordData, $content] = $this->executeToolCallForStream($toolCall, $appInfo, $message, $recordData, $saveData);
                    $executedToolCalls[$signature] = [
                        'tool_name' => $toolName,
                        'arguments' => $arguments,
                        'content' => (string)$content,
                    ];
                    $observation = $this->buildToolObservation($toolName, $arguments, (string)$content);
                    $toolObservations[] = $observation;
                    $this->recordToolObservationStatus($observation, $failedTools, $emptyResultTools);
                    if ($this->shouldRepairToolArguments($observation, $repairAttempts)) {
                        $repairQueue[] = [
                            'tool_call' => $toolCall,
                            'arguments' => $arguments,
                            'observation' => $observation,
                        ];
                    }
                    $executedInThisRound++;
                }

                if ($toolName !== '') {
                    $executedPlanTools[] = $toolName;
                    $executedPlanTools = array_values(array_unique($executedPlanTools));
                }
                $option->setMessage(content: (string)$content, role: BaseOption::RULE_TOOL, toolCallId: $toolCall['id'], name: $toolName);
            }

            foreach ($repairQueue as $repairItem) {
                [$recordData, $repairCount] = $this->runToolRepairLoop(
                    $option,
                    $modelsInfo,
                    $appInfo,
                    $message,
                    $recordData,
                    $saveData,
                    $repairItem['tool_call'],
                    $repairItem['arguments'],
                    $repairItem['observation'],
                    $executedToolCalls,
                    $toolObservations,
                    $failedTools,
                    $emptyResultTools,
                    $repairAttempts
                );
                $repairedInThisRound += $repairCount;
            }

            $this->compressOptionMessages($option, 'tool_result_round_' . $round);
            $this->sendThinkingMessage('工具结果已写入上下文，继续生成回复', 'tool_result', [
                'executed_count' => $executedInThisRound,
                'reused_count' => $reusedInThisRound,
                'repaired_count' => $repairedInThisRound,
            ]);

            $missingTools = $this->getPendingMcpPlanTools($executedPlanTools);
            $verifier = $this->verifyAgentToolProgress($option, $modelsInfo, $message, $agentPlan, $toolObservations, $missingTools, $failedTools, $emptyResultTools);
            if ($missingTools && $round < self::MAX_TOOL_CALL_ROUNDS) {
                $this->sendThinkingMessage('验证发现仍有计划工具未调用，继续补齐：' . implode('、', $missingTools), 'tool_verify', [
                    'missing_tools' => $missingTools,
                    'verifier_action' => $verifier['action'] ?? 'call_missing_tool',
                ]);
                $option->setMessage($this->buildMissingMcpToolsPrompt($missingTools));
                continue;
            }

            if (!empty($verifier['can_finalize']) && !$missingTools) {
                return [
                    $this->finalizeToolResponse(
                        $option,
                        $modelsInfo,
                        $uuid,
                        $response,
                        $toolObservations,
                        'verified_complete',
                        $this->buildAgentStatePayload($executedPlanTools, $failedTools, $emptyResultTools, $repairAttempts)
                    ),
                    $recordData,
                ];
            }

            if ($executedInThisRound === 0 && $reusedInThisRound > 0 && $repairedInThisRound === 0) {
                return [
                    $this->finalizeToolResponse(
                        $option,
                        $modelsInfo,
                        $uuid,
                        $response,
                        $toolObservations,
                        'duplicate_tool_loop',
                        $this->buildAgentStatePayload($executedPlanTools, $failedTools, $emptyResultTools, $repairAttempts)
                    ),
                    $recordData,
                ];
            }
        }

        return [
            $this->finalizeToolResponse(
                $option,
                $modelsInfo,
                $uuid,
                $response,
                $toolObservations,
                'max_tool_rounds',
                $this->buildAgentStatePayload($executedPlanTools, $failedTools, $emptyResultTools, $repairAttempts)
            ),
            $recordData,
        ];
    }

    /**
     * 生成工具名和参数的签名，用于识别重复工具调用.
     *
     * @param array $toolCall 模型返回的工具调用
     * @return string 工具调用签名
     */
    protected function getToolCallSignature(array $toolCall): string
    {
        $toolName = (string)($toolCall['function']['name'] ?? '');
        $arguments = $this->sortArrayRecursive($this->parseToolArguments($toolCall));

        return hash('sha256', $toolName . ':' . json_encode($arguments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * 递归排序数组，保证参数签名稳定.
     *
     * @param array $data 待排序数组
     * @return array 排序后的数组
     */
    protected function sortArrayRecursive(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sortArrayRecursive($value);
            }
        }

        ksort($data);
        return $data;
    }

    /**
     * 构造工具观察结果，记录工具状态、摘要和失败类型.
     *
     * @param string $toolName 工具名称
     * @param array $arguments 工具请求参数
     * @param string $content 工具返回内容
     * @return array<string, mixed>
     */
    protected function buildToolObservation(string $toolName, array $arguments, string $content): array
    {
        $decoded = json_decode($content, true);
        $isError = is_array($decoded) && (!empty($decoded['error']) || !empty($decoded['isError']));
        $summary = $this->summarizeToolContent($content);
        $failureType = $this->classifyToolObservation($content, $isError);
        $isError = $isError || in_array($failureType, ['tool_error', 'invalid_arguments', 'permission_denied', 'not_found'], true);

        return [
            'tool_name' => $toolName,
            'arguments' => $arguments,
            'summary' => $summary,
            'is_error' => $isError,
            'is_empty_result' => $failureType === 'empty_result',
            'failure_type' => $failureType,
        ];
    }

    /**
     * 根据工具返回内容识别结果类型.
     *
     * @param string $content 工具返回内容
     * @param bool $isError 工具是否显式标记错误
     * @return string 结果类型
     */
    protected function classifyToolObservation(string $content, bool $isError): string
    {
        $trimmed = trim($content);
        if ($trimmed === '') {
            return 'empty_result';
        }

        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded)) {
                $message = strtolower((string)($decoded['message'] ?? $decoded['error'] ?? ''));
                if ($isError) {
                    if (str_contains($message, 'permission') || str_contains($message, '权限')) {
                        return 'permission_denied';
                    }
                    if (str_contains($message, 'not found') || str_contains($message, '不存在')) {
                        return 'not_found';
                    }
                    if (str_contains($message, 'argument') || str_contains($message, '参数') || str_contains($message, 'invalid')) {
                        return 'invalid_arguments';
                    }

                    return 'tool_error';
                }

                foreach (['list', 'data', 'items', 'records', 'rows'] as $field) {
                    if (isset($decoded[$field]) && is_array($decoded[$field]) && count($decoded[$field]) === 0) {
                        return 'empty_result';
                    }
                }

                if ($decoded === []) {
                    return 'empty_result';
                }
            }
        }

        if ($isError) {
            return 'tool_error';
        }

        foreach (['error', 'exception', 'failed', '失败', '异常', '错误', '不存在', 'not found', 'invalid', '参数'] as $needle) {
            if (stripos($trimmed, $needle) !== false) {
                return str_contains($trimmed, '参数') || stripos($trimmed, 'invalid') !== false ? 'invalid_arguments' : 'tool_error';
            }
        }

        foreach (['没有查询到', '未查询到', '暂无数据', '空数据', 'no data', 'empty'] as $needle) {
            if (stripos($trimmed, $needle) !== false) {
                return 'empty_result';
            }
        }

        return 'ok';
    }

    /**
     * 根据工具观察结果维护失败工具和空结果工具列表.
     *
     * @param array $observation 工具观察结果
     * @param string[] $failedTools
     * @param string[] $emptyResultTools
     * @return void
     */
    protected function recordToolObservationStatus(array $observation, array &$failedTools, array &$emptyResultTools): void
    {
        $toolName = (string)($observation['tool_name'] ?? '');
        if ($toolName === '') {
            return;
        }

        if (!empty($observation['is_error'])) {
            $failedTools[] = $toolName;
        } else {
            $failedTools = array_values(array_diff($failedTools, [$toolName]));
        }

        if (!empty($observation['is_empty_result'])) {
            $emptyResultTools[] = $toolName;
        } else {
            $emptyResultTools = array_values(array_diff($emptyResultTools, [$toolName]));
        }

        $failedTools = array_values(array_unique($failedTools));
        $emptyResultTools = array_values(array_unique($emptyResultTools));
    }

    /**
     * 判断工具结果是否需要进入参数修复流程.
     *
     * @param array $observation 工具观察结果
     * @param array<string, int> $repairAttempts
     * @return bool 是否需要修复参数
     */
    protected function shouldRepairToolArguments(array $observation, array $repairAttempts): bool
    {
        $toolName = (string)($observation['tool_name'] ?? '');
        if ($toolName === '' || empty($this->mcpToolsMetaByName[$toolName])) {
            return false;
        }

        $failureType = (string)($observation['failure_type'] ?? 'ok');
        if (!in_array($failureType, ['empty_result', 'invalid_arguments', 'tool_error', 'ambiguous_query'], true)) {
            return false;
        }

        return (int)($repairAttempts[$toolName] ?? 0) < self::MAX_TOOL_REPAIR_ATTEMPTS;
    }

    /**
     * 对可修复的工具调用进行参数修复和重试.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @param ChatApplications $appInfo 当前聊天应用
     * @param string $message 用户问题
     * @param array $recordData 当前对话记录数据
     * @param array $saveData 失败时保存记录所需上下文
     * @param array $toolCall 原始工具调用
     * @param array $oldArguments 原始工具参数
     * @param array $observation 原始工具观察结果
     * @param array<string, array<string, mixed>> $executedToolCalls
     * @param array<int, array<string, mixed>> $toolObservations
     * @param string[] $failedTools
     * @param string[] $emptyResultTools
     * @param array<string, int> $repairAttempts
     * @return array{0: array, 1: int}
     */
    protected function runToolRepairLoop(
        BaseOption $option,
        ChatModels $modelsInfo,
        ChatApplications $appInfo,
        string $message,
        array $recordData,
        array $saveData,
        array $toolCall,
        array $oldArguments,
        array $observation,
        array &$executedToolCalls,
        array &$toolObservations,
        array &$failedTools,
        array &$emptyResultTools,
        array &$repairAttempts
    ): array {
        $toolName = (string)($toolCall['function']['name'] ?? '');
        $repairCount = 0;

        while ($this->shouldRepairToolArguments($observation, $repairAttempts)) {
            $repairAttempts[$toolName] = (int)($repairAttempts[$toolName] ?? 0) + 1;
            $attempt = $repairAttempts[$toolName];
            $newArguments = $this->generateRepairedToolArguments($option, $modelsInfo, $message, $toolName, $oldArguments, $observation, $attempt);

            if (!$newArguments || $this->sortArrayRecursive($newArguments) === $this->sortArrayRecursive($oldArguments)) {
                $this->sendThinkingMessage('工具参数修复未生成有效新参数：' . $toolName, 'tool_repair', array_merge($this->buildToolArgumentsPayload($oldArguments), [
                    'tool_name' => $toolName,
                    'repair_attempt' => $attempt,
                    'failure_type' => $observation['failure_type'] ?? '',
                ]));
                break;
            }

            $this->sendThinkingMessage('正在修复工具参数并重试：' . $toolName, 'tool_repair', array_merge($this->buildToolArgumentsPayload($newArguments), [
                'tool_name' => $toolName,
                'repair_attempt' => $attempt,
                'repair_reason' => $observation['failure_type'] ?? '',
                'old_arguments' => $oldArguments,
                'new_arguments' => $newArguments,
            ]));

            $repairedToolCall = $this->buildToolCallWithArguments($toolName, $newArguments);
            $signature = $this->getToolCallSignature($repairedToolCall);
            if (isset($executedToolCalls[$signature])) {
                $this->sendThinkingMessage('修复后的工具参数已执行过，跳过重复重试：' . $toolName, 'tool_skip', array_merge($this->buildToolArgumentsPayload($newArguments), [
                    'tool_name' => $toolName,
                    'repair_attempt' => $attempt,
                    'tool_call_signature' => $signature,
                ]));
                break;
            }

            $this->appendAssistantToolCallMessage($option, $modelsInfo, [$repairedToolCall]);
            [$recordData, $content] = $this->executeToolCallForStream($repairedToolCall, $appInfo, $message, $recordData, $saveData);
            $option->setMessage(content: (string)$content, role: BaseOption::RULE_TOOL, toolCallId: $repairedToolCall['id'], name: $toolName);

            $executedToolCalls[$signature] = [
                'tool_name' => $toolName,
                'arguments' => $newArguments,
                'content' => (string)$content,
            ];

            $observation = $this->buildToolObservation($toolName, $newArguments, (string)$content);
            $toolObservations[] = $observation;
            $this->recordToolObservationStatus($observation, $failedTools, $emptyResultTools);
            $oldArguments = $newArguments;
            $repairCount++;
        }

        return [$recordData, $repairCount];
    }

    /**
     * 使用指定参数构造一条服务端生成的工具调用.
     *
     * @param string $toolName 工具名称
     * @param array $arguments 工具请求参数
     * @return array 工具调用结构
     */
    protected function buildToolCallWithArguments(string $toolName, array $arguments): array
    {
        return [
            'id' => 'call_repair_' . str_replace('-', '', v4()),
            'type' => 'function',
            'function' => [
                'name' => $toolName,
                'arguments' => json_encode($arguments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
            ],
        ];
    }

    /**
     * 调用模型生成修复后的工具参数.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @param string $message 用户问题
     * @param string $toolName 工具名称
     * @param array $oldArguments 原始工具参数
     * @param array $observation 工具观察结果
     * @param int $attempt 当前修复次数
     * @return array 修复后的工具参数
     */
    protected function generateRepairedToolArguments(
        BaseOption $option,
        ChatModels $modelsInfo,
        string $message,
        string $toolName,
        array $oldArguments,
        array $observation,
        int $attempt
    ): array {
        $toolMeta = $this->mcpToolsMetaByName[$toolName] ?? [];
        $repairOption = clone $option;
        $repairOption->messages = [
            [
                'role' => BaseOption::RULE_SYSTEM,
                'content' => "你是MCP工具参数修复器。只返回JSON对象，不要返回Markdown。"
                    . "根据用户问题、工具schema、旧参数和失败/空结果摘要，生成更可能查到正确数据的新参数。"
                    . "如果字段不确定，优先减少过窄过滤条件；涉及今天/本月等时间时以当前服务端时间为准："
                    . date('Y-m-d H:i:s') . "。返回格式：{\"arguments\":{...},\"reason\":\"修复原因\"}。",
            ],
            [
                'role' => BaseOption::RULE_USER,
                'content' => json_encode([
                    'user_message' => $message,
                    'tool' => $toolMeta,
                    'old_arguments' => $oldArguments,
                    'failure_type' => $observation['failure_type'] ?? '',
                    'result_summary' => $observation['summary'] ?? '',
                    'repair_attempt' => $attempt,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ],
        ];
        $repairOption->tools = [];
        $repairOption->stream = false;

        try {
            $res = (new BaseCurl($modelsInfo->key))->setBody($repairOption)->send(url: $repairOption->url);
            $data = $this->decodeJsonObject((string)($res['choices'][0]['message']['content'] ?? ''));
        } catch (\Throwable $e) {
            $this->sendThinkingMessage('工具参数修复失败：' . $e->getMessage(), 'tool_repair', [
                'tool_name' => $toolName,
                'repair_attempt' => $attempt,
            ]);
            return [];
        }

        $arguments = $data['arguments'] ?? $data;
        return is_array($arguments) ? $arguments : [];
    }

    /**
     * 将工具返回内容压缩为便于模型和前端理解的摘要.
     *
     * @param string $content 工具返回内容
     * @return string 工具结果摘要
     */
    protected function summarizeToolContent(string $content): string
    {
        $content = trim($content);
        if ($content === '') {
            return '工具未返回内容';
        }

        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $summary = $this->summarizeDecodedToolContent($decoded);
            if ($summary !== '') {
                return $this->truncateText($summary, self::TOOL_OBSERVATION_SUMMARY_MAX_LENGTH, '摘要已截断');
            }

            $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (is_string($encoded) && $encoded !== '') {
                $content = $encoded;
            }
        }

        return $this->truncateText($content, self::TOOL_OBSERVATION_SUMMARY_MAX_LENGTH, '摘要已截断');
    }

    /**
     * 将已解析的工具结果数组或标量转换为摘要文本.
     *
     * @param mixed $decoded 已解析的工具返回内容
     * @return string 工具结果摘要
     */
    protected function summarizeDecodedToolContent($decoded): string
    {
        if (is_array($decoded)) {
            if (!empty($decoded['error']) || !empty($decoded['message'])) {
                return trim('错误：' . (string)($decoded['message'] ?? json_encode($decoded, JSON_UNESCAPED_UNICODE)));
            }

            $list = $decoded['list'] ?? $decoded['data'] ?? $decoded['items'] ?? null;
            if (is_array($list)) {
                $count = count($list);
                $sample = array_slice($list, 0, min(3, $count));
                return '共返回 ' . $count . ' 条数据，样例：' . json_encode($sample, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            if (array_keys($decoded) === range(0, count($decoded) - 1)) {
                $count = count($decoded);
                $sample = array_slice($decoded, 0, min(3, $count));
                return '共返回 ' . $count . ' 条数据，样例：' . json_encode($sample, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        if (is_scalar($decoded)) {
            return (string)$decoded;
        }

        return '';
    }

    /**
     * 验证当前工具结果是否足够生成最终回答.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @param string $message 用户问题
     * @param array $agentPlan Agent 工具计划
     * @param array<int, array<string, mixed>> $toolObservations
     * @param string[] $missingTools
     * @param string[] $failedTools
     * @param string[] $emptyResultTools
     * @return array<string, mixed>
     */
    protected function verifyAgentToolProgress(
        BaseOption $option,
        ChatModels $modelsInfo,
        string $message,
        array $agentPlan,
        array $toolObservations,
        array $missingTools,
        array $failedTools,
        array $emptyResultTools
    ): array {
        $fallback = [
            'can_finalize' => empty($missingTools),
            'action' => empty($missingTools) ? 'finalize' : 'call_missing_tool',
            'confidence' => empty($failedTools) && empty($emptyResultTools) ? 'medium' : 'low',
            'reason' => empty($missingTools) ? '计划工具已调用' : '仍有计划工具未调用',
        ];

        $this->sendThinkingMessage('正在验证工具结果是否足够回答', 'tool_verify', [
            'missing_tools' => $missingTools,
            'failed_tools' => array_values(array_unique($failedTools)),
            'empty_result_tools' => array_values(array_unique($emptyResultTools)),
        ]);

        if (!$toolObservations) {
            return $fallback;
        }

        $verifyOption = clone $option;
        $verifyOption->messages = [
            [
                'role' => BaseOption::RULE_SYSTEM,
                'content' => "你是Agent执行验证器。只返回JSON，不要返回Markdown。"
                    . "判断当前工具结果是否足够回答用户；如果还有缺失工具、空结果或失败，应指出下一步。"
                    . "返回格式：{\"can_finalize\":true,\"action\":\"finalize|call_missing_tool|repair_arguments|ask_user\",\"confidence\":\"high|medium|low\",\"reason\":\"原因\"}。",
            ],
            [
                'role' => BaseOption::RULE_USER,
                'content' => json_encode([
                    'user_message' => $message,
                    'agent_plan' => $agentPlan,
                    'tool_observations' => $toolObservations,
                    'missing_tools' => $missingTools,
                    'failed_tools' => array_values(array_unique($failedTools)),
                    'empty_result_tools' => array_values(array_unique($emptyResultTools)),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ],
        ];
        $verifyOption->tools = [];
        $verifyOption->stream = false;

        try {
            $res = (new BaseCurl($modelsInfo->key))->setBody($verifyOption)->send(url: $verifyOption->url);
            $data = $this->decodeJsonObject((string)($res['choices'][0]['message']['content'] ?? ''));
        } catch (\Throwable $e) {
            $this->sendThinkingMessage('工具结果验证失败，使用确定性兜底：' . $e->getMessage(), 'tool_verify');
            return $fallback;
        }

        if (!$data) {
            return $fallback;
        }

        $canFinalize = (bool)($data['can_finalize'] ?? false);
        if ($missingTools) {
            $canFinalize = false;
        }

        $result = [
            'can_finalize' => $canFinalize,
            'action' => (string)($data['action'] ?? $fallback['action']),
            'confidence' => (string)($data['confidence'] ?? $fallback['confidence']),
            'reason' => (string)($data['reason'] ?? $fallback['reason']),
        ];

        $this->sendThinkingMessage('工具结果验证完成：' . $result['reason'], 'tool_verify', [
            'can_finalize' => $result['can_finalize'],
            'verifier_action' => $result['action'],
            'confidence' => $result['confidence'],
            'missing_tools' => $missingTools,
            'failed_tools' => array_values(array_unique($failedTools)),
            'empty_result_tools' => array_values(array_unique($emptyResultTools)),
        ]);

        return $result;
    }

    /**
     * 构造 Agent 调度状态 payload，用于 thinking 事件和最终回答提示.
     *
     * @param string[] $executedPlanTools
     * @param string[] $failedTools
     * @param string[] $emptyResultTools
     * @param array<string, int> $repairAttempts
     * @return array<string, mixed>
     */
    protected function buildAgentStatePayload(array $executedPlanTools, array $failedTools, array $emptyResultTools, array $repairAttempts): array
    {
        $missingTools = $this->getPendingMcpPlanTools($executedPlanTools);

        return [
            'max_rounds' => self::MAX_TOOL_CALL_ROUNDS,
            'executed_tools' => array_values(array_unique($executedPlanTools)),
            'missing_tools' => $missingTools,
            'failed_tools' => array_values(array_unique($failedTools)),
            'empty_result_tools' => array_values(array_unique($emptyResultTools)),
            'repair_attempts' => $repairAttempts,
        ];
    }

    /**
     * 工具循环结束后关闭工具，强制模型基于已有结果生成最终回答；如仍无输出则后端兜底.
     *
     * @param BaseOption $option AI 请求参数对象
     * @param ChatModels $modelsInfo 当前应用绑定的模型配置
     * @param string $uuid 当前对话记录 UUID
     * @param string $response 已累积的 SSE 响应内容
     * @param array<int, array{tool_name: string, arguments: array, summary: string, is_error: bool}> $toolObservations
     * @param string $stopReason 工具调度停止原因
     * @param array<string, mixed> $agentState Agent 调度状态
     * @return string 最终累积后的 SSE 响应内容
     */
    protected function finalizeToolResponse(BaseOption $option, ChatModels $modelsInfo, string $uuid, string $response, array $toolObservations, string $stopReason, array $agentState = []): string
    {
        $stage = $stopReason === 'max_tool_rounds' ? 'tool_limit' : 'tool_complete';
        $message = match ($stopReason) {
            'max_tool_rounds' => '工具调用轮数达到上限，正在基于已有结果整理最终回答',
            'duplicate_tool_loop' => '检测到重复工具调用，正在基于已有结果整理最终回答',
            'verified_complete' => '工具结果已通过验证，正在整理最终回答',
            default => '工具调用调度结束，正在整理最终回答',
        };

        $this->sendThinkingMessage($message, $stage, array_merge($agentState, [
            'stop_reason' => $stopReason,
            'max_rounds' => self::MAX_TOOL_CALL_ROUNDS,
            'tool_observation_count' => count($toolObservations),
        ]));

        $finalOption = clone $option;
        $finalOption->tools = [];
        $finalOption->setMessage($this->buildFinalAnswerPrompt($toolObservations, $stopReason, $agentState));
        $this->compressOptionMessages($finalOption, 'tool_final');

        $res = $this->stream($modelsInfo->key, $finalOption, $uuid);
        $finalResponse = (string)($res['response'] ?? '');
        $response .= $finalResponse;

        $errorMessage = $this->getStreamErrorMessage((string)($res['rawResponse'] ?? $res['response'] ?? ''));
        if ($errorMessage !== '') {
            $this->sendThinkingMessage('最终回答生成失败：' . $errorMessage, 'tool_error', [
                'stop_reason' => $stopReason,
            ]);
        }

        if ($this->extractVisibleResponseText($finalResponse) === '') {
            $fallback = $this->buildFallbackFinalAnswer($toolObservations, $stopReason, $errorMessage, $agentState);
            $response .= $this->sendFinalAnswerFallback($fallback);
        }

        $this->sendThinkingMessage('最终回答整理完成', 'tool_complete', array_merge($agentState, [
            'stop_reason' => $stopReason,
        ]));

        return $response;
    }

    /**
     * 构建最终回答提示词，要求模型只能基于已有工具结果收尾.
     *
     * @param array<int, array{tool_name: string, arguments: array, summary: string, is_error: bool}> $toolObservations
     * @param string $stopReason 工具调度停止原因
     * @param array<string, mixed> $agentState Agent 调度状态
     * @return string 最终回答提示词
     */
    protected function buildFinalAnswerPrompt(array $toolObservations, string $stopReason, array $agentState = []): string
    {
        $summary = $this->buildToolObservationsSummary($toolObservations);
        $stateSummary = json_encode($agentState, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';

        return "工具调用调度已结束，结束原因：{$stopReason}。\n"
            . "你现在不能继续调用任何工具，只能基于已有工具结果回答用户。\n"
            . "如果信息不足，请明确说明已完成哪些查询、哪些工具未调用、哪些工具失败或返回空数据，并给出可确认的结论。\n"
            . "请输出面向用户的最终回答，不要输出工具调用JSON或调试信息。\n"
            . "Agent状态：{$stateSummary}\n"
            . "工具结果摘要：\n{$summary}";
    }

    /**
     * 汇总工具观察结果，生成最终回答可使用的文本摘要.
     *
     * @param array<int, array{tool_name: string, arguments: array, summary: string, is_error: bool}> $toolObservations
     * @return string 工具结果摘要
     */
    protected function buildToolObservationsSummary(array $toolObservations): string
    {
        if (!$toolObservations) {
            return '无可用工具结果。';
        }

        $lines = [];
        foreach ($toolObservations as $index => $observation) {
            $arguments = json_encode($observation['arguments'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $status = !empty($observation['is_error']) ? '失败' : '成功';
            $lines[] = sprintf(
                "%d. 工具：%s；状态：%s；参数：%s；结果摘要：%s",
                $index + 1,
                $observation['tool_name'] ?? '',
                $status,
                $this->truncateText((string)$arguments, 500, '参数摘要已截断'),
                $observation['summary'] ?? ''
            );
        }

        return implode("\n", $lines);
    }

    /**
     * 当最终模型回答失败或为空时，生成后端兜底回答.
     *
     * @param array<int, array{tool_name: string, arguments: array, summary: string, is_error: bool}> $toolObservations
     * @param string $stopReason 工具调度停止原因
     * @param string $errorMessage 最终回答生成错误信息
     * @param array<string, mixed> $agentState Agent 调度状态
     * @return string 兜底最终回答
     */
    protected function buildFallbackFinalAnswer(array $toolObservations, string $stopReason, string $errorMessage = '', array $agentState = []): string
    {
        $reasonText = match ($stopReason) {
            'max_tool_rounds' => '工具调用已达到系统轮数上限',
            'duplicate_tool_loop' => '检测到模型重复请求相同工具',
            'verified_complete' => '工具结果已完成验证',
            default => '工具调用调度已结束',
        };

        $answer = $reasonText . '，我先基于已经获得的工具结果给出阶段性整理。';
        if ($errorMessage !== '') {
            $answer .= "\n最终回答生成时遇到问题：" . $errorMessage;
        }

        $answer .= "\n\n已获得的结果：\n" . $this->buildToolObservationsSummary($toolObservations);
        if (!empty($agentState['missing_tools'])) {
            $answer .= "\n\n未完成工具：" . implode('、', (array)$agentState['missing_tools']);
        }
        if (!empty($agentState['failed_tools'])) {
            $answer .= "\n失败工具：" . implode('、', (array)$agentState['failed_tools']);
        }
        if (!empty($agentState['empty_result_tools'])) {
            $answer .= "\n返回空数据工具：" . implode('、', (array)$agentState['empty_result_tools']);
        }
        $answer .= "\n\n如需更精确的结论，可以缩小查询范围或指定需要优先核对的数据维度。";

        return $answer;
    }

    /**
     * 将后端兜底最终回答按普通答案 SSE 格式发送给前端.
     *
     * @param string $content 兜底最终回答内容
     * @return string 已发送的 SSE 数据
     */
    protected function sendFinalAnswerFallback(string $content): string
    {
        $event = [
            'choices' => [
                [
                    'index' => 0,
                    'delta' => [
                        'content' => $content,
                    ],
                    'finish_reason' => null,
                ],
            ],
        ];
        $data = 'data: ' . json_encode($event, JSON_UNESCAPED_UNICODE) . "\n\n";
        $this->send($data);

        return $data;
    }

    /**
     * 从已累积的 SSE 响应中提取用户可见的最终答案文本.
     *
     * @param string $response 已累积的 SSE 响应内容
     * @return string 提取后的可见答案文本
     */
    protected function extractVisibleResponseText(string $response): string
    {
        $content = '';
        foreach (explode('data:', str_replace(["\r\n", "\r"], "\n", $response)) as $value) {
            $value = trim($value);
            if ($value === '' || $value === '[DONE]') {
                continue;
            }

            $json = json_decode($value, true, 512, JSON_BIGINT_AS_STRING);
            if (json_last_error() === JSON_ERROR_NONE) {
                $content .= (string)($json['choices'][0]['delta']['content'] ?? '');
            }
        }

        return trim($content);
    }

    /**
     * 执行一条工具调用，并返回可回填给模型的 tool role 内容.
     *
     * @param array<string, mixed> $toolCall 工具调用数据
     * @param ChatApplications $appInfo 当前聊天应用
     * @param string $message 用户问题
     * @param array<string, mixed> $recordData 当前对话记录数据
     * @param array<string, mixed> $saveData 失败时保存记录所需上下文
     * @return array{0: array, 1: string} 更新后的记录数据和工具结果内容
     */
    protected function executeToolCallForStream(array $toolCall, ChatApplications $appInfo, string $message, array $recordData, array $saveData = []): array
    {
        $toolName = (string)($toolCall['function']['name'] ?? '');
        $arguments = $this->parseToolArguments($toolCall);

        $this->sendToolStartThinkingMessage($toolName, $arguments);

        if ($toolName === 'run_sql') {
            [$recordData, $runFun, $listData] = $this->runSql($appInfo, $message, $recordData, false, $saveData);
            if (!$runFun) {
                $content = $this->buildToolErrorContent($toolName, 'SQL工具未查询到可用数据');
                $this->sendToolResultThinkingMessage($toolName, $content, 'tool_error', 'SQL工具执行失败：未查询到可用数据', $arguments);
                return [$recordData, $content];
            }

            $content = json_encode($listData ?: ['message' => '查询到的数据为空'], JSON_UNESCAPED_UNICODE);
            $this->sendToolResultThinkingMessage(
                $toolName,
                (string)$content,
                $listData ? 'tool_result' : 'tool_error',
                $listData ? 'SQL工具执行完成' : 'SQL工具返回空数据',
                $arguments
            );

            return [$recordData, (string)$content];
        }

        if (ToolRegistry::getTool($toolName)) {
            $result = app()->get(ToolExecutor::class)->executeWithResult($toolName, $arguments);
            $content = (string)($result['content'][0]['text'] ?? json_encode($result, JSON_UNESCAPED_UNICODE));
            if (!empty($result['isError'])) {
                $this->sendToolResultThinkingMessage($toolName, $content, 'tool_error', 'MCP工具执行失败：' . $toolName, $arguments);
            } else {
                $this->sendToolResultThinkingMessage($toolName, $content, 'tool_result', 'MCP工具执行完成：' . $toolName, $arguments);
            }

            return [$recordData, $content];
        }

        if (isset($this->externalMcpToolMap[$toolName])) {
            $result = $this->callMcpJsonTool($appInfo, $toolName, $arguments);
            $content = (string)($result['content'][0]['text'] ?? json_encode($result, JSON_UNESCAPED_UNICODE));

            if (!empty($result['isError'])) {
                $this->sendToolResultThinkingMessage($toolName, $content, 'tool_error', '外部MCP工具执行失败：' . $toolName, $arguments);
            } else {
                $this->sendToolResultThinkingMessage($toolName, $content, 'tool_result', '外部MCP工具执行完成：' . $toolName, $arguments);
            }

            return [$recordData, $content];
        }

        $message = "Tool not found: {$toolName}";
        $content = $this->buildToolErrorContent($toolName, $message);
        $this->sendToolResultThinkingMessage($toolName, $content, 'tool_error', '工具不存在：' . $toolName, $arguments);

        return [$recordData, $content];
    }

    /**
     * 构建标准工具错误内容，作为 tool role 结果回填给模型.
     *
     * @param string $toolName 工具名称
     * @param string $message 错误说明
     * @return string JSON 格式错误内容
     */
    protected function buildToolErrorContent(string $toolName, string $message): string
    {
        return json_encode([
            'error' => true,
            'tool' => $toolName,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取请求类型.
     * @return BaidubceOption|DeepseekOption
     */
    public function option(int $modelsType)
    {
        if ($modelsType) {
            $option = new BaidubceOption();
        } else {
            $option = new DeepseekOption();
        }
        return $option;
    }

    /**
     * 发送消息.
     * @return mixed|true
     * @throws BindingResolutionException
     */
    public function send(string $message)
    {
        if (extension_loaded('swoole') && php_sapi_name() === 'cli') {
            return app()->get(SwooleResponse::class)->write($message);
        }
        echo $message;
        ob_flush();
        flush();
        return true;
    }

    /**
     * 获取Embedding.
     * @return array|false|string
     * @throws BindingResolutionException
     */
    public function getEmbedding(string $message, string $key = '')
    {
        $key = $key ?: app()->get(ChatModelsService::class)->value(['models_type' => 1], 'key');
        if (!$key) {
            return false;
        }
        $curl = new BaseCurl($key);

        $body = [
            'model' => 'tao-8k',
            'input' => [$message],
        ];
        $response = $curl->setBaeUrl((new BaidubceOption())->baseUrl)->send(url: '/v2/embeddings', body: $body);
        return $response['data'][0]['embedding'] ?? [];
    }

    /**
     * crud对话.
     * @return mixed
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    public function crudDialog(string $message, int $crudId = 0, array $fields = [])
    {
        $tableContent = '';
        if ($crudId) {
            $table = app()->get(SystemCrudService::class)->value(['id' => $crudId], 'table_name_en');
            $tablePrefix = config('database.connections.mysql.prefix');
            $sql = (array)DB::select("SHOW CREATE TABLE `{$tablePrefix}{$table}`");
            $tableContent = "数据表的原始表结构为:\n\n" . $sql[0]->{'Create Table'} . "\n\n已存在表内的字段不用生成，只生成不存在的字段数据 。\n\n";
        }

        $systemContent = <<<EOT
你是一个专业的数据库设计助手，请根据用户描述的业务需求，生成合适的表字段列表。字段规格如下：

字段类型说明：
- switch：布尔值
- input_number：整数
- input_float：精度小数
- input_percentage：百分比
- input_price：金额
- input：文本
- textarea：长文本
- rich_text：富文本
- radio：单选项
- cascader_radio：级联单选 下拉选项
- cascader_address：地址选择
- checkbox：复选项
- tag：标签组
- cascader：级联复选
- date_picker：日期
- date_time_picker：日期时间
- image：图片
- file：文件
- input_select：一对一关联

{$tableContent}

输出要求：
1. 必须生成JSON数组格式
2. 每个字段包含四个属性：
   - field_name：字段中文名
   - field_name_en：字段英文名（使用小写蛇形命名，如user_name）
   - value：字段类型（从上述规格中选择）
   - comment：字段描述

示例格式：
[
  {"field_name": "用户名", "field_name_en": "username", "value": "input", "comment": "用户的名称"},
  {"field_name": "年龄", "field_name_en": "age", "value": "input_number", "comment": "用户的年龄"}
]

请根据用户描述的业务需求，生成合适的字段列表。只需返回JSON数组，不要包含任何解释性文字。
EOT;
        $messages[] = [
            'content' => $systemContent,
            'role' => 'system',
        ];
        if ($fields) {
            $fieldsContent = '目前已经生成的字段为:' . json_encode($fields) . "\n\n如果用户提出修改一生成的字段请在当前字段内修改，返回全量数据";
            $messages[] = [
                'content' => $fieldsContent,
                'role' => 'assistant',
            ];
        }
        $messages[] = [
            'content' => $message,
            'role' => 'user',
        ];

        $res = (new SmsService())->chat($messages);

        $filesContent = $res['data']['data']['choices'][0]['message']['content'] ?? '';
        if (!$filesContent) {
            throw $this->exception($res['data']['msg'] ?? '生成字段列表失败');
        }
        $fields = json_decode($filesContent, true);
        if ($crudId) {
            $fieldAll = app()->get(SystemCrudFieldService::class)->getCrudTableFieldAllCache($crudId);
            $fieldAllName = array_column($fieldAll->toArray(), 'field_name_en');
            $diffFields = [];
            foreach ($fields as $item) {
                if (!in_array($item['field_name_en'], $fieldAllName)) {
                    $diffFields[] = $item;
                }
            }
            $fields = $diffFields;
        }
        return $fields;
    }

    /**
     * 发起底层模型流式请求，聚合可见回复、思考内容和工具调用片段.
     *
     * @param string $key 模型 API Key
     * @param BaseOption $option AI 请求参数对象
     * @param string $uuid 当前对话记录 UUID
     * @param bool $suppressVisibleOutput 是否抑制本轮可见内容直接输出
     * @return array{
     *     response: string,
     *     rawResponse: mixed,
     *     assistantContent: string,
     *     reasoningContent: string,
     *     toolCalls: array<int, array<string, mixed>>,
     *     toolCall: array<string, mixed>,
     *     toolName: string,
     *     runFun: bool
     * }
     */
    protected function stream(string $key, BaseOption $option, string $uuid, bool $suppressVisibleOutput = false)
    {
        $curl = new BaseCurl($key);

        $toolCalls = [];
        $this->sendMessage('提交AI等待输出内容', 'info');
        $visibleResponse = '';
        $assistantContent = '';
        $reasoningResponse = '';
        $rawResponse = $curl->setBody($option)->stream(url: $option->url, stream: function ($data) use (&$toolCalls, &$visibleResponse, &$assistantContent, &$reasoningResponse, $suppressVisibleOutput) {
            foreach ($this->getStreamPayloads($data) as $message) {
                // 跳过 SSE 流结束标记 [DONE]
                if ($message === '' || $message === '[DONE]') {
                    continue;
                }

                $json = json_decode($message, true, 512, JSON_BIGINT_AS_STRING);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }

                $reasoningContent = $this->getReasoningContent($json);
                if ($reasoningContent !== '') {
                    $reasoningResponse .= $reasoningContent;
                }

                $contentDelta = (string)($json['choices'][0]['delta']['content'] ?? $json['choices'][0]['message']['content'] ?? '');
                if ($contentDelta !== '') {
                    $assistantContent .= $contentDelta;
                }

                $isToolChunk = $this->isToolCallChunk($json);
                foreach ($this->extractToolCallChunks($json) as $deltaToolCall) {
                    $index = (int)($deltaToolCall['index'] ?? count($toolCalls));
                    $toolCalls[$index] ??= [
                        'id' => '',
                        'type' => 'function',
                        'function' => [
                            'name' => '',
                            'arguments' => '',
                        ],
                    ];

                    if (isset($deltaToolCall['id'])) {
                        $toolCalls[$index]['id'] = (string)$deltaToolCall['id'];
                    }
                    if (isset($deltaToolCall['type'])) {
                        $toolCalls[$index]['type'] = (string)$deltaToolCall['type'];
                    }
                    if (isset($deltaToolCall['function']['name'])) {
                        $toolCalls[$index]['function']['name'] = (string)$deltaToolCall['function']['name'];
                    }
                    if (isset($deltaToolCall['function']['arguments'])) {
                        $toolCalls[$index]['function']['arguments'] .= (string)$deltaToolCall['function']['arguments'];
                    }
                }

                // 工具调用相关 chunk 和工具执行前的一轮模型响应都不输出给用户
                if ($isToolChunk || !empty($toolCalls) || $suppressVisibleOutput) {
                    continue;
                }

                // 判断是否要二次请求AI，并且是否有错误信息
                if (empty($json['error']['message'])) {
                    // 输出AI返回的内容
                    $visibleData = 'data: ' . $message . "\n\n";
                    $this->sendMessage($visibleData);
                    $visibleResponse .= $visibleData;
                }
            }
        }, uuid: $uuid, tagName: self::CHAT_HISTORY_TABLE);

        ksort($toolCalls);
        $toolCalls = array_values(array_map(
            fn(array $toolCall) => $this->normalizeToolCallForMessage($toolCall),
            $toolCalls
        ));
        $firstToolCall = $toolCalls[0] ?? [];

        return [
            'response' => $visibleResponse,
            'rawResponse' => $rawResponse,
            'assistantContent' => $assistantContent,
            'reasoningContent' => $reasoningResponse,
            'toolCalls' => $toolCalls,
            'toolCall' => $firstToolCall,
            'toolName' => (string)($firstToolCall['function']['name'] ?? ''),
            'runFun' => !empty($toolCalls),
        ];
    }

    /**
     * 提取流式返回中的工具调用片段，支持新版 tool_calls 与旧版 function_call.
     *
     * @param array<string, mixed> $json 单条模型流式响应 JSON
     * @return array<int, array<string, mixed>> 工具调用片段列表
     */
    protected function extractToolCallChunks(array $json): array
    {
        $choice = $json['choices'][0] ?? [];
        $delta = $choice['delta'] ?? [];
        $message = $choice['message'] ?? [];

        if (!empty($delta['tool_calls']) && is_array($delta['tool_calls'])) {
            return $delta['tool_calls'];
        }

        if (!empty($message['tool_calls']) && is_array($message['tool_calls'])) {
            return $message['tool_calls'];
        }

        if (!empty($delta['function_call']) && is_array($delta['function_call'])) {
            return [[
                'index' => 0,
                'type' => 'function',
                'function' => $delta['function_call'],
            ]];
        }

        if (!empty($message['function_call']) && is_array($message['function_call'])) {
            return [[
                'index' => 0,
                'type' => 'function',
                'function' => $message['function_call'],
            ]];
        }

        return [];
    }

    /**
     * 根据类型发送消息.
     * @param mixed $message
     * @return bool
     * @throws BindingResolutionException
     */
    protected function sendMessage($message, string $type = 'success', array $saveData = [])
    {
        if ($type === 'error') {
            $data = 'data: ' . json_encode(['message' => $message, 'type' => $type], JSON_UNESCAPED_UNICODE) . "\n\n";
            $this->send($data);
            $res = $this->send("data: [DONE]\n\n");

            if ($saveData) {
                $this->saveRecord($saveData['userId'], $saveData['historyId'], $saveData['message'], $saveData['chatRecordUuid'], $saveData['recordData'], $saveData['startTime'], (string)$message);
            }
        } elseif ($type === 'list') {
            $data = 'data: ' . json_encode(['choices' => [
                    [
                        'index' => 0,
                        'delta' => [
                            'content' => $message,
                        ],
                        'finish_reason' => 'stop',
                    ],
                ], 'type' => $type], JSON_UNESCAPED_UNICODE) . "\n\n";
            $this->send($data);
            $res = $this->send("data: [DONE]\n\n");
        } elseif ($type == 'info') {
            // 调试信息
            $data = 'data: ' . json_encode(['message' => $message, 'type' => $type], JSON_UNESCAPED_UNICODE) . "\n\n";
            $res = $this->send($data);
        } elseif ($type == 'data') {
            $data = 'data: ' . json_encode(['choices' => [
                    [
                        'index' => 0,
                        'delta' => [
                            'content' => $message,
                        ],
                    ],
                ], 'type' => $type], JSON_UNESCAPED_UNICODE) . "\n\n";
            $res = $this->send($data);
        } else {
            $res = $this->send($message);
        }
        return $res;
    }

    /** 创建sql查询提示词方法返回String字符串
     * @param array $appInfo 应用信息，主要获取数据结构提示词
     * @param array $userInfo 用户信息
     * @return string
     */
    public function CreateSqlPromptGetString($appInfo, $userId)
    {
        // 我的名字
        $userInfo = app()->get(AdminService::class)->value($userId, 'name');
        $myName = $userInfo['name'] ?? '';
        $nowTime = date('Y-m-d H:i:s');
        $adminTable = $this->prefixedTableName('admin');
        $nl2sqlPrompt = <<<EOT
# 请根据以下描述生成SQL语句，只返回SQL语句，不包含任何解释或说明

## 任务类型: 生成SQL查询SELECT语句

## 数据库版本: MySQL 5.7

## 查询规则:

- 表别名: 必须使用，简洁且具有描述性（如`table_name AS t`）

- 字段引用: 必须包含表别名（如`t.field_name`）

- 用户标识查询:
  - 方式: 通过子查询从`{$adminTable}`表的`name`字段获取`id`
  - 示例: 用户名为“小寇”时，子查询为`(SELECT id FROM {$adminTable} WHERE name = '小寇')`

- 个人数据查询: 我的名字是 {$myName} ,必须当提到“我”的时候，必须使用上述子查询结果作为条件 name = '{$myName}'

- 多表连接: 必须指定JOIN条件

- 模糊查询: 使用 LIKE 时需要转义特殊字符，使用 CONCAT('%', 参数, '%')
- 时间范围规范：
   - 优先使用 MySQL 内置日期函数，格式统一为：`字段名 [比较符] 函数()` 或 `字段名 BETWEEN 开始时间 AND 结束时间`；
   - 当前时间: {$nowTime}
   - 时间范围： 当用户查询只说月、周、日的时候，默认查询当前时间之前最近年、月、周、日的数据
   - 常用时间场景及对应 SQL 格式（直接复用）：
     ① 今天：DATE(字段名) = CURDATE() （如 DATE(date) = CURDATE()）
     ② 昨天：DATE(字段名) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
     ③ 近7天：DATE(字段名) BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()
     ④ 近30天：DATE(字段名) BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()
     ⑤ 本月：DATE_FORMAT(字段名, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') 或 DATE(字段名) BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
     ⑥ 上月：DATE(字段名) BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') AND LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
     ⑦ 指定日期：DATE(字段名) = 'YYYY-MM-DD' （如 DATE(date) = '2024-12-01'）
     ⑧ 指定日期范围：DATE(字段名) BETWEEN 'YYYY-MM-DD' AND 'YYYY-MM-DD' （如 DATE(date) BETWEEN '2024-11-01' AND '2024-12-31'）
     ⑨ 近N个月：DATE(字段名) >= DATE_SUB(CURDATE(), INTERVAL N MONTH) （如 N=3 表示近3个月）
   - 说明：时间字段优先使用 DATE(字段名) 转换（兼容 timestamp/datetime 类型），避免时区/时分秒干扰；


{$appInfo->content}

## 返回格式必须严格遵循
1.  仅返回SQL SELECT语句，无任何额外解释、注释、换行多余内容。
2.  字段查询：
    - 若用户指定「字段列表」，则仅查询指定字段，必须包含表别名，且每个字段都需用AS指定中文别名；
    - 若用户未指定「字段列表」，则查询对应表的核心业务字段（参考表结构注释，排除无业务意义的字段如uid、client_id等），每个字段都需用AS指定中文别名。
3.  中文别名规范：
    - 贴合字段的COMMENT注释命名（如`account`字段注释为“用户账号”，别名则为“用户账号”）；
    - 别名简洁无重复，避免生僻词汇，符合中文业务习惯；
    - 关联表字段别名若存在重名（如多个表的`id`），需加上表标识区分（如`a.id AS 员工ID`，`c.id AS 客户ID`）。
4.  禁止使用星号(*)查询所有字段，禁止省略用户要求的核心有价值字段，用户能看明白的字段，禁止添加无关字段，字段最多返回6个。
5.  必须包含表别名、JOIN条件（如需多表关联）。
6.  不要增加额外的换行符，确保SQL语句在一行内。
EOT;
        return $nl2sqlPrompt;
    }

    /*
    /** 创建sql查询提示词方法返回json字符串
     * @return string
     */
    public function CreateSqlPrompt($appInfo, $userInfo)
    {
        // 我的名字
        $myName = $userInfo['name'] ?? '';
        $nowTime = date('Y-m-d H:i:s');
        $adminTable = $this->prefixedTableName('admin');
        $clientBillTable = $this->prefixedTableName('client_bill');
        $nl2sqlPrompt = <<<EOT

角色：专业的MySQL SQL开发工程师，精通单表查询、多表关联、聚合函数、条件过滤、排序等SQL语法，严格遵循下方规则生成精准、可直接执行的MySQL 5.7 SELECT语句。

## 任务类型: 生成SQL查询SELECT语句

## 数据库版本: MySQL 5.7

## 查询规则:
- 严格根据下方提供的**表结构**生成SQL，表名、字段名必须与表结构完全一致，禁止使用不存在的表/字段；
- 仅返回纯SQL语句，无任何前置说明、后置解释、注释、换行、代码块、多余符号，直接返回可执行的SQL；
- 聚合查询（求和/计数/平均值）需添加合理的中文别名，多表关联必须添加简洁且有描述性的表别名（如`{$adminTable} AS ea`，`{$clientBillTable} AS ecb`），避免字段冲突；
- 字段引用强制要求：所有字段必须包含表别名（如`ea.name`，禁止直接写`name`）；

- 用户标识查询规则（强制执行）:
  - 方式: 通过子查询从`{$adminTable}`表的`name`字段获取`id`，表别名固定为`ea`；
  - 示例: 用户名为“小寇”时，子查询为`(SELECT id FROM {$adminTable} AS ea WHERE ea.name = '小寇')`；
  - 个人数据查询：当用户提到“我”“我的”时，必须使用`ea.name = '{$myName}'`作为条件（`ea`为`{$adminTable}`表别名），禁止替换为其他字段/表。

- 多表连接规则（强制执行）:
  - 必须显式指定JOIN类型（优先LEFT JOIN）和完整JOIN条件，禁止省略JOIN条件；
  - 表别名必须在FROM/JOIN子句中定义，且全程统一（如`{$clientBillTable} AS ecb`，后续不可改为`{$clientBillTable} AS b`）。

- 模糊查询规则：使用 LIKE 时必须转义特殊字符，固定格式为 `字段名 LIKE CONCAT('%', 参数, '%')`，禁止直接写 `字段名 LIKE '%参数%'`；

- 时间范围规范（核心补充，强制遵循）：
   - 优先使用 MySQL 内置日期函数，格式统一为：`DATE(字段名) [比较符] 函数()` 或 `DATE(字段名) BETWEEN 开始时间 AND 结束时间`；
   - 当前时间基准值: {$nowTime}（格式：YYYY-MM-DD HH:MM:SS）；
   - 时间范围默认规则：当用户只说“月/周/日”（如“本月”“本周”），默认查询“当前时间所在的月/周/日”；当用户说“去年X月”“前年X月”，必须按以下标准格式生成，禁止脑补计算；
   - 常用时间场景及对应 SQL 格式（直接复用，禁止修改）：
     ① 今天：DATE(字段名) = CURDATE()
     ② 昨天：DATE(字段名) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
     ③ 近7天：DATE(字段名) BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()
     ④ 近30天：DATE(字段名) BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()
     ⑤ 本月：DATE_FORMAT(字段名, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
     ⑥ 上月：DATE_FORMAT(字段名, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')
     ⑦ 去年指定月份（核心新增）：DATE_FORMAT(字段名, '%Y-%m') = CONCAT(YEAR(CURDATE()) - 1, '-', LPAD(月份数字, 2, '0'))
         示例：去年8月 → DATE_FORMAT(字段名, '%Y-%m') = CONCAT(YEAR(CURDATE()) - 1, '-', '08')
     ⑧ 去年全年：DATE_FORMAT(字段名, '%Y') = YEAR(CURDATE()) - 1
     ⑨ 指定日期：DATE(字段名) = 'YYYY-MM-DD'
     ⑩ 指定日期范围：DATE(字段名) BETWEEN 'YYYY-MM-DD' AND 'YYYY-MM-DD'
     ⑪ 近N个月：DATE(字段名) >= DATE_SUB(CURDATE(), INTERVAL N MONTH)
   - 时间字段处理强制要求：优先使用 DATE(字段名) 转换（兼容 timestamp/datetime 类型），避免时区/时分秒干扰；禁止使用硬编码日期（如'2026-02-06'）替代动态函数（如CURDATE()），除非用户明确指定固定日期。

{$appInfo->content}

## 返回格式必须严格遵循（违反则视为无效输出）
1.  仅返回纯SQL SELECT语句，无任何额外解释、注释、换行、代码块、多余标点，直接返回可执行的SQL；
2.  字段查询规则：
    - 若用户指定「字段列表」，则仅查询指定字段，必须包含表别名，且每个字段都需用AS指定中文别名；
    - 若用户未指定「字段列表」，则查询对应表的核心业务字段（参考表结构注释，排除uid、client_id等无业务意义字段），每个字段都需用AS指定中文别名，字段数量最多返回6个；
3.  中文别名规范（强制）：
    - 贴合字段的COMMENT注释命名（如`account`字段注释为“用户账号”，别名则为“用户账号”）；
    - 别名简洁无重复，符合中文业务习惯，关联表重名字段需加表标识（如`ea.id AS 员工ID`，`ec.id AS 客户ID`）；
4.  禁止使用星号(*)查询所有字段，禁止省略用户要求的核心字段，禁止添加无关字段；
5.  多表关联时，必须包含表别名、显式JOIN类型、完整JOIN条件；
6.  聚合查询（SUM/COUNT/AVG等）必须添加中文别名（如`SUM(ecb.num) AS 总销售业绩`），且聚合前必须按业务维度分组（如按员工ID分组）。

EOT;
        return $nl2sqlPrompt;
    }
}
