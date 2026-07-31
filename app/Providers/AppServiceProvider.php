<?php

declare(strict_types=1);


namespace App\Providers;

use App\Http\Service\Admin\AdminService;
use App\Http\Service\Config\CompanyConfigService;
use App\Http\Service\Config\SystemConfigService;
use App\Http\Service\System\SystemGroupService;
use App\Http\Service\Work\WorkConfigService;
use App\Listeners\wechat\WorkListener;
use crmeb\services\ApiResponseService;
use crmeb\services\ConfigService;
use crmeb\services\EntUserService;
use crmeb\services\GroupDataService;
use crmeb\services\wechat\config\HttpCommonConfig;
use crmeb\services\wechat\config\WorkConfig;
use crmeb\services\wechat\Work;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // 绑定response
        $this->app->singleton('json', ApiResponseService::class);

        $stores = $this->app->config->get('cache.default', 'file');
        $timeout = $this->app->config->get('cache.stores.' . $stores . '.timeout', 3600);
        // 绑定组合数据
        $this->app->singleton('group_config', function ($app) use ($timeout) {
            return new GroupDataService(SystemGroupService::class, $app->cache, $timeout);
        });
        // 绑定系统config
        $this->app->singleton('config_crmeb', function ($app) use ($timeout) {
            return new ConfigService(SystemConfigService::class, $timeout);
        });
        // 绑定系统config
        $this->app->singleton('ent_config_crmeb', function ($app) use ($timeout) {
            return new ConfigService(CompanyConfigService::class, $app->cache, $timeout);
        });
        // 绑定企业用户
        $this->app->singleton('enterprise_user', function ($app) use ($timeout) {
            return new EntUserService(AdminService::class, $app->cache, $timeout);
        });

        $this->app->singleton('admin_user', function ($app) use ($timeout) {
            return new EntUserService(AdminService::class, $app->cache, $timeout);
        });

        $this->app->bind(HttpCommonConfig::class, function () {
            return (new HttpCommonConfig())->setServe(WorkConfigService::class);
        });
        // 企业微信
        $this->app->singleton(Work::class, function () {
            return (new Work())->setPushMessageHandler(WorkListener::class)
                ->setConfigHandler(WorkConfigService::class);
        });

        $this->app->singleton(WorkConfig::class, function () {
            return (new WorkConfig($this->app->make(HttpCommonConfig::class)))->setHandler(WorkConfigService::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }
}
