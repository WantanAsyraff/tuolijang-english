<?php

declare(strict_types=1);


namespace App\Http\Service\Config;

use App\Http\Dao\Category\CategoryDao;
use crmeb\basic\BaseService;
use crmeb\interfaces\ResourceServicesInterface;
use crmeb\traits\service\CategoryTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * 系统配置分类.
 */
class SystemConfigCateService extends BaseService implements ResourceServicesInterface
{
    use CategoryTrait;

    public function __construct(CategoryDao $dao)
    {
        $this->dao = $dao;
        $this->setMaxLevel(2);
    }

    /**
     * 获取修改表单数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function resourceEdit(int $id, array $other = []): array
    {
        if (! ($data = $this->dao->get($id))) {
            throw $this->exception('无效参数！');
        }
        return $this->createElementForm('修改配置分类', $this->createFormRule($data->toArray()), '/admin/system/configCate/' . $id, 'PUT');
    }

    /**
     * 获取添加表单数据.
     */
    public function resourceCreate(array $other = []): array
    {
        return $this->createElementForm('创建配置分类', $this->createFormRule(), '/admin/system/configCate');
    }

    /**
     * 设置分类类型.
     */
    protected function getType(): string
    {
        return 'systemConfig';
    }

    /**
     * 分类层级.
     */
    protected function getLevel(): int
    {
        return 1;
    }

    /**
     * 分类层级.
     */
    protected function getEntId(): int
    {
        return 1;
    }
}
