<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi;

use App\Constants\CacheEnum;
use App\Http\Service\Attach\AttachService;
use App\Http\Service\Company\CompanyService;
use App\Http\Service\Config\SystemAgreementService;
use App\Http\Service\Crud\CrudModuleService;
use App\Http\Service\Crud\SystemCrudQuestionnaireService;
use App\Http\Service\Crud\SystemCrudService;
use App\Http\Service\Notice\NoticeRecordService;
use App\Http\Service\System\SystemCityService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

/**
 * 公共控制器
 * Class CommonController.
 */
#[Prefix('uni/common')]
class CommonController extends AuthController
{
    /**
     * 网站配置.
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Get('config', '获取网站配置', ['auth.admin', 'ent.auth', 'ent.log'])]
    public function getConfig()
    {
        $logo = $siteTitle = null;
        if ($this->isEnt) {
            $logo      = empty($this->entInfo['logo']) ? '' : $this->entInfo['logo'];
            $siteTitle = empty($this->entInfo['title']) ? '' : $this->entInfo['title'];
        }
        if (! $logo) {
            $logo = sys_config('ent_website_logo');
        }
        if (! $siteTitle) {
            $siteTitle = sys_config('site_name');
        }
        return $this->success([
            'site_logo'       => $logo,
            'site_title'      => $siteTitle,
            'avatar'          => $this->userInfo['avatar'],
            'account'         => $this->userInfo['account'],
            'enterprise_name' => $this->entInfo['enterprise_name'] ?? '',
        ]);
    }

    /**
     * 上传文件.
     * @return mixed
     */
    #[Post('upload', '上传文件', ['auth.admin', 'ent.auth', 'ent.log'])]
    public function upload(AttachService $services)
    {
        [$pid, $file, $relationType, $relationId, $eid, $md5, $chunkIndex, $chunkTotal] = $this->request->postMore([
            ['cid', 0],
            ['file', 'file'],
            ['relation_type', ''],
            ['relation_id', 0],
            ['eid', ''],
            ['md5', ''],        // 文件md5
            ['chunk_index', 0], // 分片索引
            ['chunk_total', 0], // 总分片数
        ], true);

        $key = 'upload.key.' . $this->request->ip();

        if (Cache::tags([CacheEnum::TAG_OTHER])->get($key) > 500) {
            return $this->fail('您今日上传文件的次数已达上限');
        }

        if (Cache::tags([CacheEnum::TAG_OTHER])->has($key)) {
            Cache::increment($key, 1);
        } else {
            Cache::add($key, 1, Carbon::today()->endOfDay()->getTimestamp() - Carbon::today()->getTimestamp());
        }

        if (Cache::tags([CacheEnum::TAG_OTHER])->has($key)) {
            Cache::increment($key, 1);
        } else {
            Cache::add($key, 1, Carbon::today()->endOfDay()->getTimestamp() - Carbon::today()->getTimestamp());
        }

        try {
            $fileInfo = $services->setOption(['chunk_index' => (int) $chunkIndex, 'chunk_total' => (int) $chunkTotal, 'md5' => $md5])
                ->setRelationType($relationType)->setRelationId((int) $relationId, (int) $eid)
                ->upload((int) $pid, $file, 1, 0, 2, $this->entId, $this->uuid);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }

        if ($fileInfo === true) {
            return $this->success('ok');
        }
        $fileInfo['url'] = link_file($fileInfo['url']);
        return $this->success('上传成功', $fileInfo);
    }

    /**
     * 消息列表.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('message', '获取消息列表', ['auth.admin', 'ent.auth', 'ent.log'])]
    public function message(NoticeRecordService $services)
    {
        return $this->success($services->getMessageList($this->uuid, $this->entId, (int) $this->request->get('cate_id', 0), $this->request->get('title', ''), 0, false));
    }

    /**
     * 修改消息状态
     * @return mixed
     */
    #[Put('message/{id}/{is_read}', '修改消息状态', ['auth.admin', 'ent.auth', 'ent.log'])]
    public function updateMessage(NoticeRecordService $services, $id, $isRead)
    {
        if (! $id) {
            return $this->fail('common.empty.attrs');
        }
        $messageInfo = $services->get($id);
        if (! $messageInfo) {
            return $this->fail('消息不存在');
        }
        $messageInfo->is_read = $isRead;
        if ($messageInfo->save()) {
            return $this->success('common.update.succ');
        }
        return $this->fail('common.update.fail');
    }

    /**
     * 用户协议.
     */
    #[Get('agreement/{ident}', '用户协议')]
    public function getAgreement(SystemAgreementService $services, $ident): mixed
    {
        return $this->success($services->getAgreement($ident));
    }

    /**
     * 获取服务配置.
     * @throws \ReflectionException
     */
    #[Get('server_config', '服务配置')]
    public function getServerConfig(CompanyService $service): mixed
    {
        return $this->success($service->getServerConfig());
    }

    /**
     * 站点配置.
     */
    #[Get('config/site', '站点配置')]
    public function siteConfig(): mixed
    {
        return $this->success([
            // 备案号
            'site_record_number' => sys_config('site_record_number'),
            // seo标题
            'site_seo_title' => sys_config('ent_site_seo_title'),
            // 网站地址
            'site_address' => sys_config('site_url'),
            // 电话号码
            'site_tel' => sys_config('site_tel'),
        ]);
    }

    /**
     * 城市数据.
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Get('city', '城市数据')]
    public function city()
    {
        return $this->success(app()->get(SystemCityService::class)->cityTree());
    }

    /**
     * 获取图形验证码
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Get('aj_captcha', '获取图形验证码')]
    public function ajcaptcha()
    {
        $captchaType = $this->request->get('captchaType', 'clickWord');
        if (! in_array($captchaType, ['clickWord', 'blockPuzzle'])) {
            $captchaType = 'clickWord';
        }
        return $this->success(aj_captcha_create($captchaType));
    }

    /**
     * 一次验证
     * @return mixed
     */
    #[Post('ajcheck', '一次验证')]
    public function ajcheck()
    {
        [$token, $pointJson, $captchaType] = $this->request->postMore([
            ['token', ''],
            ['pointJson', ''],
            ['captchaType', ''],
        ], true);
        try {
            aj_captcha_check_one($captchaType, $token, $pointJson);
            return $this->success();
        } catch (\Throwable) {
            return $this->fail('验证失败重新尝试');
        }
    }

    /**
     * 获取问卷表单信息.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Get('questionnaire_info/{unique}', '获取问卷表单信息')]
    public function getQuestionnaireInfo(SystemCrudQuestionnaireService $service, CrudModuleService $moduleService, $unique)
    {
        if (! $unique) {
            return $this->fail('缺少参数');
        }

        $questionnaire = $service->getQuestionnaire($unique);

        try {
            auth('admin')->userOrFail();
        } catch (\Exception) {
            if (! $questionnaire->role_type) {
                return app('json')->make(410003, '请先登录');
            }
        }

        return $this->success($moduleService->getCreateForm(crud: $questionnaire['crud'], uni: true));
    }

    /**
     * 问卷表单保存.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('questionnaire_save/{name}/{unique}', '问卷表单保存', ['ent.crud'])]
    public function saveQuestionnaire(Request $request, CrudModuleService $service, SystemCrudQuestionnaireService $questionnaireService)
    {
        $unique = $this->request->route()->parameter('unique');

        if (! $unique) {
            return $this->fail('缺少参数');
        }

        $questionnaire = $questionnaireService->getQuestionnaire($unique);

        $admin = null;

        try {
            $admin = auth('admin')->userOrFail();
        } catch (\Exception) {
            if (! $questionnaire->role_type) {
                return app('json')->make(410003, '请先登录');
            }
        }

        if (! $admin) {
            $captchaVerification = $request->post('captchaVerification');
            $captchaType         = $request->post('captchaType');
            if (! $captchaVerification || ! $captchaType) {
                return $this->fail('请滑动验证码');
            }

            try {
                aj_captcha_check_two($captchaType, $captchaVerification);
            } catch (\Throwable) {
                return $this->fail('二次验证失败，请重新滑动验证码');
            }

            $key = 'throttle_' . $unique . $request->ip();

            if (Cache::has($key)) {
                return $this->fail('一分钟内只能提交一次问卷');
            }
        }

        $crudInfo = $this->request->crudInfo;

        if (in_array($crudInfo->table_name_en, app()->get(SystemCrudService::class)->notAllowOperateTable())) {
            return $this->fail('系统默认数据不允许创建');
        }

        $data = $service->checkData($crudInfo, $request);

        $service->saveModule($crudInfo, $data, ['uid' => $admin['id'] ?? $questionnaire['user_id'], 'ip' => ! $admin ? $request->ip() : '']);

        if (isset($key)) {
            Cache::put($key, true, 60); // 缓存1分钟
        }

        return $this->success('添加成功');
    }
}
