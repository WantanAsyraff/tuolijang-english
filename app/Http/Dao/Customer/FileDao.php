<?php

declare(strict_types=1);


namespace App\Http\Dao\Customer;

use App\Http\Model\Customer\File;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

class FileDao extends BaseDao
{
    use ListSearchTrait;

    public function setModel(): string
    {
        return File::class;
    }

    /**
     * 修改图片分类.
     * @return bool
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function move(array $data)
    {
        $where['id'] = $data['images'];
        return $this->search($where)->update(['cid' => $data['cid']]);
    }

    /**
     * 修改附件关联.
     * @param mixed $where
     * @param mixed $value
     * @param mixed $key
     * @return bool
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function updateRelation($where, $value, $key)
    {
        return $this->search($where)->update([$key => $value]);
    }

    public function sumSize($entId)
    {
        return $this->getModel(false)->where('entid', $entId)->sum('att_size');
    }
}
