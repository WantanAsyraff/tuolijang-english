<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\User;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Requests\enterprise\user\EnterpriseUserEducationRequest;
use App\Http\Service\Company\CompanyUserEducationService;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 教育经历
 * Class EducationController.
 */
class EducationController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(CompanyUserEducationService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 获取保存和修改字段.
     * @return array|\string[][]
     */
    protected function getRequestFields(): array
    {
        return [
            ['user_id', 0],
            ['start_time', ''],
            ['end_time', ''],
            ['school_name', ''],
            ['major', ''],
            ['education', ''],
            ['academic', ''],
            ['remark', ''],
        ];
    }

    /**
     * 字段验证
     */
    protected function getRequestClassName(): string
    {
        return EnterpriseUserEducationRequest::class;
    }

    /**
     * 搜索字段.
     */
    protected function getSearchField(): array
    {
        return [
            ['user_id', ''],
        ];
    }
}
