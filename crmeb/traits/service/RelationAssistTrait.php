<?php

declare(strict_types=1);


namespace crmeb\traits\service;

use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 关联辅助
 * Trait RelationAssistTrait.
 * @mixin BaseService
 */
trait RelationAssistTrait
{
    /**
     * 创建关联.
     * @param mixed $relationIds
     * @return mixed
     */
    public function createRelation($relationIds, array $passiveIds, array $other = [])
    {
        $data = [];
        if (! is_array($relationIds)) {
            $relationIds = [$relationIds];
        }
        $otherKey   = array_keys($other);
        $otherValue = array_values($other);
        foreach ($relationIds as $relationId) {
            foreach ($passiveIds as $passiveId) {
                $key    = array_merge($this->tableFeildArr(), $otherKey);
                $value  = array_merge([$relationId, $passiveId], $otherValue);
                $data[] = array_combine($key, $value);
            }
        }

        return $this->dao->insert($data);
    }

    /**
     * 删除关联.
     * @return int
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function deleteRelation(int $relationId, ?string $field = null)
    {
        return $this->dao->delete([$field ?: $this->deleteFeildStr() => $relationId]);
    }

    /**
     * 关联删除字段.
     */
    abstract protected function deleteFeildStr(): string;

    /**
     * 字段.
     */
    abstract protected function tableFeildArr(): array;
}
