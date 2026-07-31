<?php

declare(strict_types=1);


namespace App\Http\Model\Work;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WorkMessageIndex extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'work_message_index_' . date('Ym');

        if (! Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Blueprint $table) {
                $table->id();
                $table->string('corp_id', 20)->default('')->comment('企业ID');
                $table->unsignedInteger('index_id')->default(0)->comment('对应type的 ID');
                $table->tinyInteger('index_type')->default(0)->comment('0=员工，1=客户，2=群聊');
                $table->timestamps();

                $table->index(['corp_id', 'index_id', 'index_type'], 'corp_id_index_id_index_type');
            });
        }
    }

    public function client()
    {
        return $this->hasOne(WorkClient::class, 'id', 'index_id');
    }
}
