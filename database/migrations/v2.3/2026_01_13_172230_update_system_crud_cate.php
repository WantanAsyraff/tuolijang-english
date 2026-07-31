<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $table = 'system_crud_cate';

    /**
     * Run the migrations.
     */
    public function up()
    {
        if (! Schema::hasColumn($this->table, 'info')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->string('info', 255)->default('')->comment('应用简介');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (! Schema::hasColumn($this->table, 'info')) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('info');
            });
        }
    }
};
