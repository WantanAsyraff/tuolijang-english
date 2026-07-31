<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Config;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Service\Config\CompanyConfigService;
use Illuminate\Contracts\Container\BindingResolutionException;

class ConfigController extends AuthController
{
    /**
     * ConfigController constructor.
     */
    public function __construct(CompanyConfigService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 获取工作台配置表单.
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getWorkBenchFrom()
    {
        return $this->success($this->service->getConfigFrom($this->entId, '工作台设置', 'work_bench', '/ent/config/work_bench'));
    }

    /**
     * 保存工作台配置表单.
     * @return mixed
     * @throws BindingResolutionException
     */
    public function saveWorkBenchFrom()
    {
        $config = $this->service->select(['category' => 'work_bench', 'entid' => $this->entId], ['key', 'key_name', 'value', 'type', 'input_type']);
        foreach ($config as $value) {
            $keys[] = [
                $value['key'], $value['value'],
            ];
        }
        $data = $this->request->postMore($keys);
        $this->service->saveConfig($this->entId, 'work_bench', $data);
        return $this->success('保存配置成功');
    }
}
