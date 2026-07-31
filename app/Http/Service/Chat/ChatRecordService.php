<?php

declare(strict_types=1);


namespace App\Http\Service\Chat;

use App\Http\Dao\Chat\ChatRecordDao;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ChatRecordService extends BaseService
{
    public function __construct(ChatRecordDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 获取历史记录.
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDialogRecord(int $chatIhistoryId, int $num = 3)
    {
        return $this->dao->getModel()->where('chat_history_id', $chatIhistoryId)->select(['problem_text', 'answer_text'])->orderBy('id', 'desc')->limit($num)->get()->toArray();
    }

    /**
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDialogNum(int $uid, int $appId, int $num)
    {
        return $this->dao->getModel()->where('chat_applications_id', $appId)->whereDate('created_at', Carbon::today()->toDateString())->where('uid', $uid)->count() >= $num;
    }

    /**
     * 获取对话记录.
     * @return array
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getRecord(string $uuid, int $uid)
    {
        $record = $this->dao->get(['chat_record_uuid' => $uuid]);
        if (! $record) {
            throw $this->exception('没有查询到该对话记录');
        }
        $sql = json_decode($record->sql_text, true);
        if (! $sql) {
            throw $this->exception('没有查询到该对话记录的SQL语句');
        }

        if (empty($sql['list_sql']) || empty($sql['page_sql']) || empty($sql['table_fields'])) {
            throw $this->exception('该对话记录的SQL语句已失效，请重新对话');
        }

        [$page, $limit] = $this->getPageValue();
        
        // 如果包含LIMIT 20 LIMIT 或 limit 0,20 limit 不区分大小写，则删除LIMIT 20 或删除 limit 0,20
        $pattern = '/LIMIT\s+\d+(?:\s*,\s*\d+)?(?:\s+OFFSET\s+\d+)?(?!\$\{)/i';
        $sql['list_sql'] = preg_replace($pattern, '', $sql['list_sql'], 1); // 第二个参数是空字符串 ''
        $sql['list_sql'] = str_replace(['${page}', '${limit}'], [($page - 1) * $limit, $limit], $sql['list_sql']);
        $sql['list_sql'] = str_replace(['${user_id}'], (string) $uid, $sql['list_sql']);

        try {
            // 替换占位符为实际值
            $list  = DB::select($sql['list_sql']);
            if ($sql['page_sql'] == 'new') {
                $count = $sql['totalNum'] ?? 0;
            }else{
                $count = ((array) DB::select($sql['page_sql'])[0])['count_nums'] ?? 0;
            }
            

            $tableFields = [];
            foreach ($sql['table_fields'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $tableFields[$k] = $k;
                    }
                } else {
                    $tableFields[$key] = $value;
                }
            }
            return $this->listData($list, $count, ['table_fields' => $tableFields]);
        } catch (\Exception $e) {
            throw $this->exception('查询失败，请重新对话:' . $e->getMessage());
        }
    }
}
