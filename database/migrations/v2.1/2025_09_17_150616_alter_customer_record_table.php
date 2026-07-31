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
        Schema::table('customer_record', function (Blueprint $table) {
            $table->string('link_type', 32)->default('customer')->comment('关联业务:customer:客户,contract:合同,invoice:发票,clue:线索,odds:商机,');
            $table->integer('eid')->default(0)->comment('关联业务ID')->change();
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('customer_record', function (Blueprint $table) {
            $table->dropColumn(['link_type']);
            $table->integer('eid')->comment('客户ID')->change();
        });
    }
};
