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
        Schema::table('system_crud', function (Blueprint $table) {
            $table->integer('list_type')->default('0')->comment('0=默认；1=树形');
            $table->string('comment_title', 50)->default('')->comment('评论标题')->change();
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('system_crud', function (Blueprint $table) {
            $table->dropColumn(['list_type']);
            $table->string('comment_title')->length(50)->comment('评论标题')->change();
        });
    }
};
