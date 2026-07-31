<?php

declare(strict_types=1);


namespace crmeb\basic;

/**
 * Class BaseEntService.
 */
abstract class BaseEntService extends BaseService
{
    /**
     * 条件字段名.
     */
    public const ENT_FIELD = 'entid';

    /**
     * BaseEntService constructor.
     */
    public function __construct()
    {
        $this->dao = app()->get($this->setDao());
        $this->initialize();
    }

    /**
     * 设置Dao.
     */
    abstract protected function setDao(): string;

    /**
     * 设置默认条件.
     * @return array
     */
    protected function getDefaultWhere()
    {
        return [self::ENT_FIELD => 1];
    }

    /**
     * 初始化.
     */
    protected function initialize()
    {
        $this->dao->setDefaultWhere($this->getDefaultWhere());
    }
}
