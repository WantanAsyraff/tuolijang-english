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
        Schema::table('form_cate', function (Blueprint $table) {
            $table->string('ident', 32)->default('')->comment('标识');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('form_cate', function (Blueprint $table) {
            $table->dropColumn(['ident']);
        });
    }
};
