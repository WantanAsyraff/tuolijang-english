<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (! Schema::hasColumn('admin', 'e_sign')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->unsignedTinyInteger('e_sign')->default(0)->comment('是否认证电子签');
            });
        }
        if (! Schema::hasColumn('admin', 'e_userid')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->string('e_userid', 64)->default('')->comment('电子签用户ID');
            });
        }
        if (! Schema::hasColumn('admin', 'e_openid')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->string('e_openid', 64)->default('')->comment('电子签用户标识');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('admin', 'e_sign')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->dropColumn(['e_userid', 'e_sign', 'e_openid']);
            });
        }
    }
};
