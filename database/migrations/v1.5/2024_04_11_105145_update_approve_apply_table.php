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
        Schema::table('approve_apply', function (Blueprint $table) {
            $table->unsignedInteger('crud_id')->default(0)->comment('关联实体ID')->after('number');
            $table->unsignedInteger('link_id')->default(0)->comment('实体数据ID')->after('crud_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('frame', function (Blueprint $table) {
            $table->dropColumn('crud_id');
            $table->dropColumn('link_id');
        });
    }
};
