<?php

declare(strict_types=1);


namespace crmeb\interfaces;

/**
 * Resource Controller 接口
 * Interface ResourceController.
 */
interface ResourceControllerInterface
{
    /**
     * 展示数据.
     * @return mixed
     */
    public function index();

    /**
     * 创建数据.
     * @return mixed
     */
    public function create();

    /**
     * 添加.
     * @return mixed
     */
    public function store();

    /**
     * 隐藏展示.
     * @param mixed $id
     * @return mixed
     */
    public function show($id);

    /**
     * 获取修改数据.
     * @param mixed $id
     * @return mixed
     */
    public function edit($id);

    /**
     * 修改数据.
     * @param mixed $id
     * @return mixed
     */
    public function update($id);

    /**
     * 删除数据.
     * @param mixed $id
     * @return mixed
     */
    public function destroy($id);
}
