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
        Schema::table('contract', function (Blueprint $table) {
            $table->string('oid', 255)->default('')->comment('商机编号');
            $table->string('contract_no', 255)->default('')->comment('订单编号')->change();
            $table->decimal('contract_price', 10)->default('0.00')->comment('合同金额(元)')->change();
            $table->string('contract_followed', 255)->default('1')->comment('是否关注')->change();
            $table->string('signing_status', 255)->default('')->comment('签约状态')->change();
            $table->string('contract_category', 255)->default('')->comment('合同分类')->change();
            $table->string('contract_cate', 32)->default('')->comment('合同分类copy')->change();
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->dropColumn(['oid']);
            $table->string('contract_no', 255)->default('')->comment('合同编号')->change();
            $table->decimal('contract_price', 255)->default('1.00')->comment('合同金额')->change();
            $table->string('contract_followed', 255)->default('')->comment('是否关注')->change();
            $table->string('signing_status', 255)->default('1')->comment('签约状态')->change();
            $table->string('contract_category', 255)->default('"[\"25\"]"')->comment('合同分类')->change();
            $table->string('contract_cate', 255)->default('')->comment('合同分类copy')->change();
        });
    }
};
