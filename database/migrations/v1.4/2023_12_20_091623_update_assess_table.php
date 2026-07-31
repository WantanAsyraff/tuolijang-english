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
        Schema::table('assess', function (Blueprint $table) {
            $table->string('self_reply', 512)->default('')->index()->comment('自评')->after('is_show');
            $table->string('reply', 512)->default('')->comment('上级评价')->after('self_reply');
            $table->string('hide_reply', 512)->default('')->comment('上级评价(仅上级可见)')->after('reply');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assess_space', function (Blueprint $table) {
            $table->dropColumn('self_reply');
            $table->dropColumn('reply');
            $table->dropColumn('hide_reply');
        });
    }
};
