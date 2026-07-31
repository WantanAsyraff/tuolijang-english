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
        if (!Schema::hasColumn('user_card_perfect', 'creator')) {
            Schema::table('user_card_perfect', function (Blueprint $table) {
                $table->unsignedInteger('creator')->default(0)->comment('邀请人ID')->after('id');
            });
        }
        if (!Schema::hasColumn('user_card_perfect', 'user_id')) {
            Schema::table('user_card_perfect', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('被邀请人ID')->after('creator');
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
        Schema::table('user_card_perfect', function (Blueprint $table) {
            $table->dropColumn('creator');
            $table->dropColumn('user_id');
        });
    }
};
