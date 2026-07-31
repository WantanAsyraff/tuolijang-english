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
        if (!Schema::hasColumn('system_config', 'category')) {
            Schema::table('system_config', function (Blueprint $table) {
                $table->string('category', 32)->default(0)->comment('配置分类')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_config', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
