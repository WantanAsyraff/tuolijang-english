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
        Schema::table('client_labels', function (Blueprint $table) {
            $table->integer('link_type')->default('1')->comment('业务类型：1、客户；4、线索；');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('client_labels', function (Blueprint $table) {
            $table->dropColumn(['link_type']);
        });
    }
};
