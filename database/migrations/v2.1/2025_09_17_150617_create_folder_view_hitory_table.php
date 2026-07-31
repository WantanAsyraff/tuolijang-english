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
        Schema::create('folder_view_hitory', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 32)->comment('修改用户');
            $table->unsignedInteger('folder_id')->comment('文件 id');
            $table->string('file_name', 255)->comment('文件真实名称');
            $table->string('file_url', 255)->comment('文件 url');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('folder_view_hitory');
    }
};
