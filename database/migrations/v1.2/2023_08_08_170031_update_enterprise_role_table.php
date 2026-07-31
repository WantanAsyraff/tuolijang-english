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
        Schema::table('enterprise_role', function (Blueprint $table) {
            $table->unsignedInteger('data_level')->default(1)->comment('数据范围：见枚举；')->after('entid');
            $table->unsignedTinyInteger('directly')->default(0)->comment('是否包含直属下级；')->after('data_level');
            $table->string('frame_id')->default('')->comment('指定部门ID；')->after('directly');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enterprise_role', function (Blueprint $table) {
            $table->dropColumn('data_level');
            $table->dropColumn('directly');
            $table->dropColumn('frame_id');
        });
    }
};
