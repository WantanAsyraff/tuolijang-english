<?php

declare(strict_types=1);


namespace crmeb\swoole\server;

use crmeb\swoole\queue\Manager as QueueManager;
use SwooleTW\Http\Server\Manager;

/**
 * Trait InteractsWithQueue.
 */
trait InteractsWithQueue
{
    public function prepareQueue(Manager $manager)
    {
        /** @var QueueManager $queueManager */
        $queueManager = $this->laravel->make(QueueManager::class);

        $queueManager->attachToServer($manager, $this->output);
    }
}
