<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::table('salesman_custom_field', function (Blueprint $table) {
            $table->string('custom_type', 32)->default('')->comment('类型')->change();
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('salesman_custom_field', function (Blueprint $table) {
            $table->string('custom_type')->length(18)->default('')->comment('类型')->change();
        });
    }
};
