<?php

declare(strict_types=1);


namespace App\Jobs;

use crmeb\services\HttpService;
use crmeb\utils\MessageType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $botUrl, private string $title, private string $content, private string $url, private int $type = 0, private string $userName = '', private string $crudName = '') {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [];
        switch ($this->type) {
            case MessageType::TYPE_WORK:
                $data = [
                    'msgtype'       => 'template_card',
                    'template_card' => [
                        'card_type'  => 'text_notice',
                        'main_title' => [
                            'title' => $this->title,
                        ],
                        'card_action' => [
                            'type' => 1,
                            'url'  => $this->url,
                        ],
                        'horizontal_content_list' => [
                            [
                                'keyname' => '实体名称',
                                'value'   => $this->crudName,
                            ],
                            [
                                'keyname' => '操作人',
                                'value'   => $this->userName,
                            ],
                            [
                                'keyname' => '消息内容',
                                'value'   => $this->content,
                            ],
                        ],
                        'jump_list' => [
                            [
                                'type'  => 1,
                                'title' => '查看详情',
                                'url'   => $this->url,
                            ],
                        ],
                    ],
                ];
                break;
            case MessageType::TYPE_DING:
            case MessageType::TYPE_OTHER:
                $data = [
                    'msgtype'  => 'markdown',
                    'markdown' => [
                        'title' => $this->title,
                        'text'  => '
                            #### ' . $this->title . '\n
                            ##### 实体名称：' . $this->crudName . '\n
                            ##### 操作人：' . $this->userName . '\n
                            ##### 消息内容：' . $this->content . '\n
                            ##### [查看详情](' . $this->url . ') \n
                        ',
                    ],
                ];
                break;
        }
        if ($data) {
            app(HttpService::class)->setHeader(['Content-Type:application/json'])->requests(url: $this->botUrl, method: 'POST', data: json_encode($data), header: true, code: true);
        }
    }
}
