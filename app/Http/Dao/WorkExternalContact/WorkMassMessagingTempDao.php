<?php

declare(strict_types=1);


namespace App\Http\Dao\WorkExternalContact;

use App\Http\Model\WorkExternalContact\WorkMassMessagingTemp;
use crmeb\basic\BaseDao;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 企微群发素材.
 */
class WorkMassMessagingTempDao extends BaseDao
{
    use ListSearchTrait;

    protected $table = 'work_mass_messaging_temp';

    public function getModel(bool $need = true)
    {
        if (! Schema::hasTable($this->table)) {
            method_exists($this, 'createTable') && $this->createTable();
        }
        return parent::getModel($need);
    }

    protected function setModel()
    {
        return WorkMassMessagingTemp::class;
    }

    /**
     * 创建表.
     */
    protected function createTable()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('创建用户ID');
            $table->unsignedInteger('group_id')->default(0)->index()->comment('分组ID');
            $table->unsignedTinyInteger('types')->default(0)->comment('类型:0、素材,1、关联数据;');
            $table->text('content')->default('')->comment('内容');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('企微群发素材');
            $table->engine    = 'InnoDB';
            $table->charset   = 'utf8';
            $table->collation = 'utf8_unicode_ci';
        });
    }
    /**
     * 获取已删除的素材内容
     *
     * @param integer $id
     * @return string
     */
    public function getTempTrashedContent(int $id): string
    {
        $info = $this->getModel()->withTrashed()->find($id);

        return $info['content'] ?? '';
    } 
}
