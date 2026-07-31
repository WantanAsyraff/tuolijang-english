<?php

declare(strict_types=1);


namespace crmeb\utils;

class Disk
{
    /**
     * 容量转换.
     * @return string
     */
    public function getSizeName($size)
    {
        if ($size >= 1073741824) {
            // 转成GB
            $size = round($size / 1073741824 * 100) / 100 . 'G';
        } elseif ($size >= 1048576) {
            // 转成MB
            $size = round($size / 1048576 * 100) / 100 . 'M';
        } elseif ($size >= 1024) {
            // 转成KB
            $size = round($size / 1024 * 100) / 100 . 'K';
        } else {
            // 不转换直接输出
            $size = $size . 'B';
        }

        return $size;
    }
}
