<?php

declare(strict_types=1);


namespace crmeb\traits\service;

use crmeb\basic\BaseService;

/**
 * path service
 * Trait PathServiceTrait.
 * @mixin BaseService
 */
trait PathServiceTrait
{
    /**
     * 更新path字段.
     * @param array|string[] $field
     * @param mixed $path
     */
    public function updatePathStr(int $id, $path, array $newPath, array $field = ['path'])
    {
        $strPath    = $this->getPathValue($path);
        $strNewPath = $this->getPathValue($newPath);
        if ($strPath != $strNewPath) {
            $this->dao->setFields($field)->updatePath($id, $strPath, $strNewPath);
        }
    }

    /**
     * 获取path值
     * @param mixed $path
     * @return array|string
     */
    public function getPathValue($path, bool $str = true)
    {
        if ($str) {
            return is_string($path) ? $path : (is_array($path) ? '/' . implode('/', $path) : '');
        }
        return is_string($path) ? array_merge(array_filter(explode('/', $path))) : (is_array($path) ? $path : []);
    }
}
