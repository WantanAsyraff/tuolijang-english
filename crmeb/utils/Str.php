<?php

declare(strict_types=1);


namespace crmeb\utils;

class Str
{
    /**
     * 过滤字段.
     * @param mixed $value
     * @return mixed
     */
    public static function filterValue($value, array $filter = [])
    {
        $filter = $filter ?: ['strip_tags', 'addslashes', 'trim', 'htmlspecialchars'];
        foreach ($filter as $closure) {
            if (function_exists($closure)) {
                $value = $closure($value);
            }
        }
        return $value;
    }

    /**
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }
        return $value;
    }

    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }
}
