<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi;

use App\Http\Service\Package\UpgradeService;
use App\Http\Service\System\SystemCityService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicController extends AuthController
{
    /**
     * 获取版本号.
     * @return mixed
     */
    public function getVersion(UpgradeService $service)
    {
        [$appName,$appId,$version,$versionCode] = $this->request->postMore([
            ['appName', ''],
            ['appId', ''],
            ['version', ''],
            ['versionCode', ''],
        ], true);
        if ($appName != config('uni.appName') && $appId != config('uni.appId')) {
            return $this->fail('无效的版本信息');
        }
        if ($versionCode < config('uni.versionCode')) {
            $info = $service->getUpgradeVersion($versionCode, $appId);
            return $this->success('您的系统版本过低...', $info);
        }
        if ($versionCode == config('uni.versionCode')) {
            return $this->success('您的系统版本无需更新', [
                'upgrade' => 'none',
            ]);
        }
        return $this->fail('无效的版本信息');
    }

    /**
     * @param mixed $key
     * @return BinaryFileResponse|StreamedResponse
     */
    public function package(UpgradeService $service, $key = '')
    {
        [$url,$name] = $service->getUpgradeInfo($key);
        // 远程资源下载
        if (str_starts_with($url, 'http') || str_starts_with($url, 'https')) {
            return Response::streamDownload(function () use ($url) {
                echo Http::withoutVerifying()->get($url);
            }, $name);
        }
        $name = str_replace(['/', '\\'], '&', $name);
        // 本地资源下载
        return Response::download(public_path($url), $name);
    }

    /**
     * 城市数据.
     * @return mixed
     * @throws BindingResolutionException
     */
    public function city()
    {
        return $this->success(app()->get(SystemCityService::class)->cityTree());
    }
}
