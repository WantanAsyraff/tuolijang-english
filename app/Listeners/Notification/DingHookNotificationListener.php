<?php

declare(strict_types=1);


namespace App\Listeners\Notification;

use App\Constants\NoticeEnum;
use App\Events\SystemMessageEvent;
use App\Jobs\WebhookJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * 钉钉Webhook通知监听器
 * 处理企业微信、钉钉等Webhook通知消息.
 */
class DingHookNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public int $delay;

    /**
     * 处理事件.
     */
    public function handle(SystemMessageEvent $event): void
    {
        if (! isset($event->templates['ding']['status']) || ! $event->templates['ding']['status']) {
            return;
        }
        $this->delay = $event->templates['system']['push_rule'] ? (int) $event->templates['system']['minute'] : $event->setDelay;
        try {
            $template = $event->templates['ding'];
            preg_match_all('/\{#[\x7f-\xffa-z0-9_]+}/', $template['content_template'], $arr);
            $templateNewVar = $arr[0] ?? [];
            $newParams      = [];
            $newKey         = [];
            foreach ($templateNewVar as $v) {
                $key      = trim(str_replace(['{#', '}'], '', $v));
                $newKey[] = $v;
                if ($key) {
                    $newParams[] = $params[$key] ?? '';
                }
            }
            if ($newKey) {
                $content = str_replace($newKey, $newParams, $template['content_template']);
            } else {
                $content = $template['content_template'];
            }

            if (isset($template['webhook_url']) && $template['webhook_url']) {
                WebhookJob::dispatch($template['webhook_url'], $event->messageInfo['title'], $content, $template['url'], NoticeEnum::TYPE_DING_HOOK);
            }
            Log::info('Webhook notification sent successfully', [
                'template_type' => $event->type,
                'webhook_type'  => 'dingHook',
                'entId'         => $event->entId,
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook notification listener error: ' . $e->getMessage(), $event->getData());
        }
    }
}
