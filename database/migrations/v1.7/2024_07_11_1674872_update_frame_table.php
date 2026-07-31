<?php

declare(strict_types=1);



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('frame', 'user_id')) {
            Schema::table('frame', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('部门主管ID')->after('id');
            });
        }
        if (!Schema::hasColumn('frame_assist', 'updated_at')) {
            Schema::table('frame_assist', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            });
        }
        if (!Schema::hasColumn('frame_assist', 'deleted_at')) {
            Schema::table('frame_assist', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable()->after('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('frame', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('frame_assist', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
        Schema::table('frame_assist', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
