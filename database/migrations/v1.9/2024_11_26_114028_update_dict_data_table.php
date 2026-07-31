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
        if (!Schema::hasColumn('dict_data', 'color')) {
            Schema::table('dict_data', function (Blueprint $table) {
                $table->string('color',32)->default('')->comment('颜色')->after('sort');
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
        if (Schema::hasColumn('dict_data', 'color')) {
            Schema::table('dict_data', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }
    }
};
