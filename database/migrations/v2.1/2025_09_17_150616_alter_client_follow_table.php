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
        Schema::table('client_follow', function (Blueprint $table) {
            $table->string('link_type', 32)->default('customer')->comment('关联业务:customer:客户,contract:合同,clue:线索,odds:商机');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('client_follow', function (Blueprint $table) {
            $table->dropColumn(['link_type']);
        });
    }
};
