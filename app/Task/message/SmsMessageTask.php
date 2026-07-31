<?php

declare(strict_types=1);


namespace App\Task\message;

use crmeb\services\SmsService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

/**
 * 新版短信消息发送
 * Class SmsMessageSendJob.
 */
class SmsMessageTask extends Task
{
    public function __construct(protected $phone, protected $entId, protected $templateCode, protected $message, protected $var) {}

    public function handle()
    {
        try {
            $smsMake = app()->get(SmsService::class);
            $result  = $smsMake->send($this->phone, $this->templateCode, $this->var);
            if ($result && isset($result['status']) && $result['status'] != 200) {
                Log::error('短信发送' . $result['msg'], $result);
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
