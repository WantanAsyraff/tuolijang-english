<?php

declare(strict_types=1);


namespace App\Http\Service\Train;

use App\Http\Contract\Company\EmployeeTrainInterface;
use App\Http\Dao\Train\EmployeeTrainDao;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 员工培训
 * Class EmployeeTrainService.
 */
class EmployeeTrainService extends BaseService implements EmployeeTrainInterface
{
    protected string $trainType = '';

    /**
     * EmployeeTrainService constructor.
     */
    public function __construct(EmployeeTrainDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 类型检查.
     */
    public function setTrainType(string $type): EmployeeTrainInterface
    {
        if (! in_array($type, [self::COMPANY_PROFILE, self::ORGANIZATION_CHART, self::STRATEGIC_PLAN])) {
            throw $this->exception('培训类型错误');
        }

        $this->trainType = $type;
        return $this;
    }

    /**
     * 详情.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getInfo(): mixed
    {
        return toArray($this->dao->get(['type' => $this->trainType]));
    }

    /**
     * 保存.
     */
    public function updateTrain(string $content): mixed
    {
        return $this->updateOrCreate(['type' => $this->trainType], ['type' => $this->trainType, 'content' => htmlspecialchars($content)]);
    }
}
