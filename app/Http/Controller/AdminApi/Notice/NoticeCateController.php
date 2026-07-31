<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Notice;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Service\Message\MessageService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 消息控制器
 * Class Message.
 */
class NoticeCateController extends AuthController
{
    /**
     * Message constructor.
     */
    public function __construct(MessageService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 分类.
     * @return mixed
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function cate()
    {
        return $this->success($this->service->getMessageCateList($this->entId));
    }

    public function syncMessage()
    {
        $this->service->syncMessage($this->entId);
        return $this->success('ok');
    }
}
