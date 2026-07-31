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
        if (!Schema::hasColumn('message', 'crud_id')) {
            Schema::table('message', function (Blueprint $table) {
                $table->integer('crud_id')->index()->default(0)->comment('实体id')->after('remind_time');
            });
        }
        if (!Schema::hasColumn('message', 'event_id')) {
            Schema::table('message', function (Blueprint $table) {
                $table->integer('event_id')->index()->default(0)->comment('实体的触发器id')->after('crud_id');
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
        if (!Schema::hasColumn('message', 'crud_id')) {
            Schema::table('message', function (Blueprint $table) {
                $table->dropColumn('crud_id');
            });
        }
        if (!Schema::hasColumn('message', 'event_id')) {
            Schema::table('message', function (Blueprint $table) {
                $table->dropColumn('event_id');
            });
        }
    }
};
