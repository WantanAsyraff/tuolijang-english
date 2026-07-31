<?php

declare(strict_types=1);


namespace App\Http\Service\Chat;

use crmeb\basic\BaseService;
use Illuminate\Support\Facades\Redis;

/**
 * Embedding服务类
 * 用于向量数据库的管理，包括存储和查询
 */
class EmbeddingService extends BaseService
{
    /**
     * Redis键前缀
     */
    protected string $redisPrefix = 'embedding:';
    
    /**
     * Redis是否可用
     */
    protected bool $redisAvailable = false;
    
    /**
     * 错误信息
     */
    protected string $errorMessage = '';
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        try {
            // 测试Redis连接
            $this->testRedisConnection();
            $this->redisAvailable = true;
        } catch (\Exception $e) {
            // Redis 不可用时，设置为不可用状态
            $this->redisAvailable = false;
            $this->errorMessage = $e->getMessage();
        }
    }
    
    /**
     * 获取错误信息
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
    
    /**
     * 测试Redis连接
     * @throws \Exception 连接失败时抛出异常
     */
    protected function testRedisConnection()
    {
        try {
            // 使用系统配置的Redis连接
            Redis::ping();
        } catch (\Exception $e) {
            throw new \Exception("Redis 连接失败: " . $e->getMessage());
        }
    }
    
    /**
     * 获取Redis实例
     * @return \Illuminate\Redis\Connections\ConnectionInterface
     */
    protected function getRedis()
    {
        return Redis::connection();
    }
    
    /**
     * 存储向量
     * @param string $content 内容（用户输入的关键词）
     * @param string $sqlText SQL语句
     * @param string $type 类型：'knowledge' 或 'sql'
     * @param array $embedding 向量数据
     * @return bool 是否成功
     */
    public function storeEmbedding(string $content, string $sqlText, string $type, array $embedding, int $expire = 86400 * 30): bool
    {
        // Redis 不可用时，返回 false
        if (!$this->redisAvailable) {
            return false;
        }
        
        try {
            $redis = $this->getRedis();
            
            // 生成唯一ID
            $id = uniqid('emb_', true);
            
            // 存储向量数据到Hash
            $dataKey = "{$this->redisPrefix}data:{$id}";
            $redis->hMSet($dataKey, [
                'id' => $id,
                'content' => $content,
                'sql_text' => $sqlText,
                'type' => $type,
                'embedding' => json_encode($embedding),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // 设置过期时间（可选，根据业务需求调整）
            $redis->expire($dataKey, $expire); // 30天
            
            // 将ID添加到对应类型的Set中
            $typeKey = "{$this->redisPrefix}type:{$type}";
            $redis->sAdd($typeKey, $id);
            
            // 设置Set的过期时间
            $redis->expire($typeKey, $expire+86400); // 31天，比数据多一天
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * 查询相似向量
     * @param array $queryEmbedding 查询向量
     * @param string $type 类型：'knowledge' 或 'sql'，为空则查询所有
     * @param int $limit 返回数量
     * @param float $threshold 相似度阈值
     * @return array 相似结果
     */
    public function searchSimilar(array $queryEmbedding, string $type = 'sql', int $limit = 5, float $threshold = 0.7): array
    {
        // Redis 不可用时，返回空数组
        if (!$this->redisAvailable) {
            return [];
        }
        
        try {
            $redis = $this->getRedis();
            
            // 获取所有向量ID
            $ids = [];
            if ($type) {
                // 获取指定类型的向量ID
                $typeKey = "{$this->redisPrefix}type:{$type}";
                $ids = $redis->sMembers($typeKey);
            } else {
                // 获取所有类型的向量ID
                $types = ['knowledge', 'sql'];
                foreach ($types as $t) {
                    $typeKey = "{$this->redisPrefix}type:{$t}";
                    $typeIds = $redis->sMembers($typeKey);
                    $ids = array_merge($ids, $typeIds);
                }
            }
            
            // 计算相似度并排序
            $similarResults = [];
            foreach ($ids as $id) {
                // 获取向量数据
                $dataKey = "{$this->redisPrefix}data:{$id}";
                $data = $redis->hGetAll($dataKey);
                
                if (!empty($data) && isset($data['embedding'])) {
                    $embedding = json_decode($data['embedding'], true);
                    if (is_array($embedding)) {
                        $similarity = $this->cosineSimilarity($queryEmbedding, $embedding);
                        if ($similarity >= $threshold) {
                            $similarResults[] = [
                                'id' => $data['id'],
                                'content' => $data['content'],
                                'sql_text' => $data['sql_text'],
                                'type' => $data['type'],
                                'similarity' => $similarity
                            ];
                        }
                    }
                }
            }
            
            // 按相似度排序
            usort($similarResults, function ($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });
            
            // 限制返回数量
            return array_slice($similarResults, 0, $limit);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * 计算余弦相似度
     * @param array $vec1 向量1
     * @param array $vec2 向量2
     * @return float 相似度
     */
    public function cosineSimilarity(array $vec1, array $vec2): float
    {
        // 确保两个向量长度相同
        if (count($vec1) !== count($vec2)) {
            return 0;
        }
        
        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;
        
        foreach ($vec1 as $key => $value) {
            $dotProduct += $value * $vec2[$key];
            $norm1 += $value * $value;
            $norm2 += $vec2[$key] * $vec2[$key];
        }
        
        if ($norm1 === 0 || $norm2 === 0) {
            return 0;
        }
        
        return $dotProduct / (sqrt($norm1) * sqrt($norm2));
    }
    
    /**
     * 清空指定类型的向量
     * @param string $type 类型：'knowledge' 或 'sql'
     * @return bool 是否成功
     */
    public function clearEmbeddings(string $type): bool
    {
        // Redis 不可用时，返回 false
        if (!$this->redisAvailable) {
            return false;
        }
        
        try {
            $redis = $this->getRedis();
            
            // 获取指定类型的向量ID
            $typeKey = "{$this->redisPrefix}type:{$type}";
            $ids = $redis->sMembers($typeKey);
            
            // 删除每个向量的数据
            foreach ($ids as $id) {
                $dataKey = "{$this->redisPrefix}data:{$id}";
                $redis->del($dataKey);
            }
            
            // 删除类型集合
            $redis->del($typeKey);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * 获取指定类型的向量数量
     * @param string $type 类型：'knowledge' 或 'sql'
     * @return int 数量
     */
    public function countEmbeddings(string $type): int
    {
        // Redis 不可用时，返回 0
        if (!$this->redisAvailable) {
            return 0;
        }
        
        try {
            $redis = $this->getRedis();
            $typeKey = "{$this->redisPrefix}type:{$type}";
            return $redis->sCard($typeKey);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * 获取 Redis 是否可用
     * @return bool
     */
    public function isRedisAvailable(): bool
    {
        // 首先检查缓存的状态
        if ($this->redisAvailable) {
            return true;
        }
        
        // 如果缓存的状态为不可用，进行实时检查
        try {
            // 使用系统配置的Redis连接进行测试
            $this->testRedisConnection();
            
            // 如果所有测试都通过，更新缓存的状态
            $this->redisAvailable = true;
            $this->errorMessage = '';
            return true;
        } catch (\Exception $e) {
            $this->errorMessage = 'Redis 检查失败: ' . $e->getMessage();
            return false;
        }
    }
    
    /**
     * 获取 SQLite 驱动是否可用（保持兼容性）
     * @return bool
     */
    public function isSqliteAvailable(): bool
    {
        // 保持向后兼容，返回 Redis 的可用性
        return $this->isRedisAvailable();
    }
    
    /**
     * 获取Embedding（本地实现）
     * @param string $message 消息内容
     * @param string $type（未使用，保持兼容性）
     * @return array 向量数据
     */
    public function getEmbedding(string $message, string $type = 'sql'): array
    {
        // 本地实现：基于文本特征的简单向量生成
        $vector = [];
        $dimension = 128; // 向量维度
        
        // 初始化向量为0
        for ($i = 0; $i < $dimension; $i++) {
            $vector[$i] = 0.0;
        }
        
        // 基于字符级特征生成向量
        $length = strlen($message);
        for ($i = 0; $i < $length; $i++) {
            $char = $message[$i];
            $charCode = ord($char);
            
            // 计算哈希值，映射到向量维度
            $hash = ($charCode * 31 + $i) % $dimension;
            // 使用字符频率作为向量值
            $vector[$hash] += 1.0 / $length;
        }
        
        // 归一化向量
        $norm = 0.0;
        foreach ($vector as $value) {
            $norm += $value * $value;
        }
        $norm = sqrt($norm);
        
        if ($norm > 0) {
            foreach ($vector as &$value) {
                $value /= $norm;
            }
        }
        
        return $vector;
    }

    /**
     * 从向量数据库中搜索相似的 SQL 查询
     * @param string $message 用户输入的消息
     * @param int $limit 返回数量
     * @param float $threshold 相似度阈值
     * @return string|null 最相似的 SQL 查询语句
     */
    public function searchSqlInVectorDB(string $message, int $limit = 3, float $threshold = 0.9): ?string
    {
        try {
            // 检查 Redis 是否可用
            if (!$this->redisAvailable) {
                return null;
            }
            
            // 获取用户消息的向量表示
            $embedding = $this->getEmbedding($message, 'sql');
            
            // 检查向量是否为有效数组
            if (!is_array($embedding) || empty($embedding)) {
                return null;
            }
            
            // 搜索相似的向量
            $similarResults = $this->searchSimilar(
                $embedding,
                'sql',
                $limit,
                $threshold
            );
            
            // 如果有相似结果，返回最相似的 SQL 语句
            if (!empty($similarResults)) {
                // 按相似度排序（虽然 searchSimilar 已经排序，但再次确认）
                usort($similarResults, function ($a, $b) {
                    return $b['similarity'] <=> $a['similarity'];
                });
                
                $bestMatch = $similarResults[0];
                // 检查相似度是否足够高
                if ($bestMatch['similarity'] >= $threshold) {
                    return $bestMatch['sql_text'];
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
    /**
     * 删除向量数据库中的相似向量 相似度0.9
     * @param string $message 用户输入的消息
     * @param float $threshold 相似度阈值
     * @return bool 是否删除成功
     */
    public function deleteEmbedding(string $message, float $threshold = 0.9, string $type = 'sql'): bool
    {
        // Redis 不可用时，返回 false
        if (!$this->redisAvailable) {
            return false;
        }
        
        try {
            // 获取用户消息的向量表示
            $embedding = $this->getEmbedding($message, $type);
            
            // 检查向量是否为有效数组
            if (!is_array($embedding) || empty($embedding)) {
                return false;
            }
            
            // 搜索相似的向量
            $similarResults = $this->searchSimilar(
                $embedding,
                $type, // 空字符串表示搜索所有类型
                3,  // 只需要相似度最高的一个
                $threshold
            );
            
            // 如果有相似结果，删除相似度最高的那个
            if (!empty($similarResults)) {
                $bestMatch = $similarResults[0];
                $id = $bestMatch['id'];
                $type = $bestMatch['type'];
                
                $redis = $this->getRedis();
                
                // 删除向量数据
                $dataKey = "{$this->redisPrefix}data:{$id}";
                $redis->del($dataKey);
                
                // 从类型集合中移除ID
                $typeKey = "{$this->redisPrefix}type:{$type}";
                $redis->sRem($typeKey, $id);
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * 检查向量数据库中是否存在相似的向量
     * @param string $message 用户输入的消息
     * @param string $type 向量类型（默认：'sql'）
     * @return bool 是否存在相似向量
     */
    public function checkEmbeddingExists(string $message, string $type = 'sql'): bool
    {
        
        try {
            // 获取用户消息的向量表示
            $embedding = $this->getEmbedding($message, $type);
            
            // 检查向量是否为有效数组
            if (!is_array($embedding) || empty($embedding)) {
                return false;
            }
            
            // 搜索相似的向量
            $similarResults = $this->searchSimilar(
                $embedding,
                $type, // 空字符串表示搜索所有类型
                1,  // 只需要相似度最高的一个
                0.9 // 相似度阈值
            );
            
            // 如果有相似结果，返回 true
            return !empty($similarResults);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * 保存 SQL 到向量数据库
     * @param string $userMessage 用户输入的消息
     * @param string $listSql 返回的sql语句
     * @param string $type 类型：'knowledge' 或 'sql'
     * @param int $expire 过期时间，默认3600秒
     * @return bool 是否成功
     */
    public function saveSqlToVectorDB(string $userMessage, string $listSql, string $type = 'sql', int $expire = 3600): bool
    {
        $embedding = $this->getEmbedding($userMessage, 'sql');
        \Log::info('向量获取成功，维度: ' . (is_array($embedding) ? count($embedding) : '0'));
        
        // 存储向量
        try {
            // 先判断是否存在该条记录
            $exists = $this->checkEmbeddingExists($userMessage, $type);
            if ($exists) {
                \Log::info('该条记录已存在，无需重复存储');
                return true;
            }
            
            $result = $this->storeEmbedding(
                $userMessage,
                $listSql,
                $type,
                $embedding,
                $expire
            );
            \Log::info('向量存储结果: ' . ($result ? '成功' : '失败'));
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('向量存储时出错: ' . $e->getMessage());
            return false;
        }
    }

}