<?php

declare(strict_types=1);


namespace App\Http\Middleware;

class ConvertEmptyStringsToNull extends \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull
{
    /**
     * Transform the given value.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        return is_string($value) && $value === '' ? '' : $value;
    }
}
