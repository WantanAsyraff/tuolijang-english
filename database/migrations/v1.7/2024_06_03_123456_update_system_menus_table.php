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
        if (!Schema::hasColumn('system_menus', 'crud_id')) {
            Schema::table('system_menus', function (Blueprint $table) {
                $table->integer('crud_id')->default(0)->comment('实体id')->after('menu_type');
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
        Schema::table('system_menus', function (Blueprint $table) {
            $table->dropColumn('crud_id');
        });
    }
};
