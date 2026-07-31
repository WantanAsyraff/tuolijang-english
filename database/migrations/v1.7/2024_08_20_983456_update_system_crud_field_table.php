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
        if (!Schema::hasColumn('system_crud_field', 'is_uniqid')) {
            Schema::table('system_crud_field', function (Blueprint $table) {
                $table->tinyInteger('is_uniqid')->default(0)->comment('0=不唯一，1=唯一')->after('is_default');
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
        Schema::table('system_crud_field', function (Blueprint $table) {
            $table->dropColumn('is_uniqid');
        });
    }
};
