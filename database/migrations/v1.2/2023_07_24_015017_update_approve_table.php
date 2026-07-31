<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approve', function (Blueprint $table) {
            $table->unsignedInteger('types')->default(0)->comment('审批类型：见枚举；')->after('info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approve', function (Blueprint $table) {
            $table->dropColumn('types');
        });
    }
};
