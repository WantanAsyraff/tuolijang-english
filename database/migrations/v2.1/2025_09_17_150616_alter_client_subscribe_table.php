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
        Schema::table('client_subscribe', function (Blueprint $table) {
            $table->integer('types')->default('0')->comment('类型：0、客户；1、线索；2、商机；');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('client_subscribe', function (Blueprint $table) {
            $table->dropColumn(['types']);
        });
    }
};
