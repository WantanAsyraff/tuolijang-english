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
        Schema::table('assess_target', function (Blueprint $table) {
            $table->unsignedInteger('sort')->default(0)->comment('排序')->after('ratio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('assess_target', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
