<?php

declare(strict_types=1);


namespace crmeb\interfaces;

/**
 * Resource Services 接口
 * Interface ResourceServicesInterface.
 */
interface ResourceServicesInterface
{
    /**
     * 获取列表数据.
     * @param array|string[] $field
     * @param null|array|string $sort
     */
    public function getList(array $where, array $field = ['*'], $sort = null, array $with = []): array;

    /**
     * 创建数据之前获取的数据.
     */
    public function resourceCreate(array $other = []): array;

    /**
     * 创建数据.
     * @return mixed
     */
    public function resourceSave(array $data);

    /**
     * 获取修改数据.
     */
    public function resourceEdit(int $id, array $other = []): array;

    /**
     * 修改数据.
     * @param int $id
     * @return mixed
     */
    public function resourceUpdate($id, array $data);

    /**
     * 删除数据.
     * @param mixed $id
     * @return mixed
     */
    public function resourceDelete($id, ?string $key = null);

    /**
     * 显示隐藏.
     * @param mixed $id
     * @return mixed
     */
    public function resourceShowUpdate($id, array $data);
}
