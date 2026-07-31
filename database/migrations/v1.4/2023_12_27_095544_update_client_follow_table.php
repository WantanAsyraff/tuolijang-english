<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_follow', function (Blueprint $table) {
            $table->integer('follow_version')->default(0)->comment('跟进版本');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_follow', function (Blueprint $table) {
            $table->dropColumn('follow_version');
        });
    }
};
