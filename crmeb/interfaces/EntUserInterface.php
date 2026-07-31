<?php

declare(strict_types=1);


namespace crmeb\interfaces;

/**
 * 配置基础类
 * Interface EntUserInterface.
 */
interface EntUserInterface
{
    public function uuidToUid(string $uuid, int $entId): int;

    public function uuidToCardId(string $uuid, int $entId): int;
}
