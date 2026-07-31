<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::create('work_member_other', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id')->default('0')->comment('企业成员id');
            $table->text('extattr')->nullable()->comment('扩展属性');
            $table->text('external_profile')->nullable()->comment('成员对外属性');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信成员其他信息');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_member_other');
    }
};
