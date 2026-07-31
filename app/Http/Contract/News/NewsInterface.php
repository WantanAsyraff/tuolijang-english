<?php

declare(strict_types=1);


namespace App\Http\Contract\News;

interface NewsInterface
{
    public function getList(array $where, array $field = ['*'], $sort = null, array $with = []): array;
}
