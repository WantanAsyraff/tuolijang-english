<?php

declare(strict_types=1);


namespace App\Http\Contract\Client;

interface ClientContractSubscribeInterface
{
    /**
     * 关注.
     */
    public function subscribe(int $uid, int $cid, int $status): bool;
}
