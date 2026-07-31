<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('client_invoice', function (Blueprint $table) {
            $table->string('unique', 64)->default('')->index()->comment('唯一值')->after('entid');
            $table->string('serial_number', 64)->default('')->comment('发票流水号')->after('unique');
            $table->unsignedInteger('link_id')->default(0)->comment('关联申请审批ID')->after('creator');
            $table->unsignedInteger('revoke_id')->default(0)->comment('撤销申请ID')->after('link_id');
            $table->string('link_bill', 128)->default('')->comment('关联付款单ID')->after('revoke_id');
        });
        Schema::table('client_bill', function (Blueprint $table) {
            $table->unsignedInteger('apply_id')->default(0)->comment('关联申请审批ID')->after('bill_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('client_invoice', function (Blueprint $table) {
            $table->dropColumn('unique');
            $table->dropColumn('serial_number');
            $table->dropColumn('link_id');
            $table->dropColumn('revoke_id');
            $table->dropColumn('link_bill');
        });
        Schema::table('client_bill', function (Blueprint $table) {
            $table->dropColumn('apply_id');
        });
    }
};
