<?php

declare(strict_types=1);


namespace App\Http\Contract\Client;

interface ClientSubscribeInterface
{
    /**
     * 关注.
     */
    public function subscribe(int $uid, int $eid, int $status, int $type = 1): bool;
}
