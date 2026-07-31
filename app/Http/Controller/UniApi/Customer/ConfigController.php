<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi\Customer;

use App\Http\Controller\UniApi\AuthController;
use App\Http\Service\System\SystemGroupDataService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 客户相关业务设置
 * Class ConfigController.
 */
#[Prefix('uni/client/config/group')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ConfigController extends AuthController
{
    /**
     * 模块前缀
     */
    protected string $prefix = 'client_';

    /**
     * @var string[]
     */
    protected array $configTypes = [
        'way'     => '客户来源',
        'renew'   => '续费类型',
        'invoice' => '发票类型',
        'cate'    => '客户分类',
    ];

    public function __construct(SystemGroupDataService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 客户配置列表组.
     * @throws BindingResolutionException
     */
    #[Get('/', '获取客户配置列表组')]
    public function getConfigList(): mixed
    {
        [$keys, $page, $limit] = $this->request->getMore([
            ['keys', []],
            ['page', 0],
            ['limit', 0],
        ], true);
        if (! $keys) {
            return $this->fail(__('common.empty.attrs'));
        }

        $data = [];
        if (! is_array($keys)) {
            $keys = [$keys];
        }
        foreach ($keys as $key) {
            if (! array_key_exists($key, $this->configTypes)) {
                return $this->fail(__('common.empty.attrs'));
            }
            $group = ent_data($this->entId, $this->prefix . $key, (int) $limit, (int) $page);
            foreach ($group as &$item) {
                $item['title'] = $item['value']['title'] ?? '';
                unset($item['value']);
            }
            $data[$key] = $group;
        }
        return $this->success($data);
    }
}
