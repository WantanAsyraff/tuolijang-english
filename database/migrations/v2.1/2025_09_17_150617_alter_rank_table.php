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
        Schema::table('rank', function (Blueprint $table) {
            $table->integer('sort')->default('0')->comment('排序');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('rank', function (Blueprint $table) {
            $table->dropColumn(['sort']);
        });
    }
};
