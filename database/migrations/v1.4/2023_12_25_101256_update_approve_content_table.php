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
        Schema::table('approve_content', function (Blueprint $table) {
            $table->string('symbol', 32)->default('')->index()->comment('字段标识')->after('types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approve_content', function (Blueprint $table) {
            $table->dropColumn('symbol');
        });
    }
};
