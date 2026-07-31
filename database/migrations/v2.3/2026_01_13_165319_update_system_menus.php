<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    protected $table = 'system_menus';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->table, 'crud_app_id')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->integer('crud_app_id')->default(0)->comment('低代码的应用id');
            });
        }
        if (!Schema::hasColumn($this->table, 'crud_dashboard_id')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->integer('crud_dashboard_id')->default(0)->comment('低代码图表的id');
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
        if (Schema::hasColumn($this->table, 'crud_app_id')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn(['crud_app_id']);
            });
        }
        if (Schema::hasColumn($this->table, 'crud_dashboard_id')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn(['crud_dashboard_id']);
            });
        }
    }
};
