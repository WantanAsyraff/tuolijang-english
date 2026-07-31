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
        Schema::table('customer_odds', function (Blueprint $table) {
            $table->unsignedInteger('cid')->default(0)->comment('关联订单ID')->after('eid');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('customer_odds', function (Blueprint $table) {
            $table->dropColumn('cid');
        });
    }
};
