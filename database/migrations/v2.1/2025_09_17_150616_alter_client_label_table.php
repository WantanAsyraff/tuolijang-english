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
        Schema::table('client_label', function (Blueprint $table) {
            $table->integer('is_work')->default('0')->comment('0=本地标签，1=企业微信标签');
            $table->string('work_group_id', 100)->default('')->comment('企业微信标签分组ID');
            $table->string('work_tag_id', 100)->default('')->comment('企业微信标签ID');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('client_label', function (Blueprint $table) {
            $table->dropColumn(['is_work', 'work_group_id', 'work_tag_id']);
        });
    }
};
