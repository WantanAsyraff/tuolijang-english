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
        if (!Schema::hasColumn('folder', 'user_id')) {
            Schema::table('folder', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (!Schema::hasColumn('folder', 'del_uid')) {
            Schema::table('folder', function (Blueprint $table) {
                $table->unsignedInteger('del_uid')->default(0)->comment('删除用户id')->after('is_del');
            });
        }
        if (!Schema::hasColumn('folder_auth', 'user_id')) {
            Schema::table('folder_auth', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (!Schema::hasColumn('folder_history', 'user_id')) {
            Schema::table('folder_history', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (!Schema::hasColumn('folder_share', 'user_id')) {
            Schema::table('folder_share', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (!Schema::hasColumn('folder_collaborate', 'user_id')) {
            Schema::table('folder_collaborate', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
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
        Schema::table('folder', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('del_uid');
        });
        Schema::table('folder_auth', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('folder_history', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('folder_share', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('folder_collaborate', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
