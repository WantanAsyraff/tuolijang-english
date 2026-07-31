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
        if (!Schema::hasColumn('promotion_data', 'rank')) {
            Schema::table('promotion_data', function (Blueprint $table) {
                $table->string('rank')->default('')->comment('职级')->after('promotion_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('promotion_data', 'rank')) {
            Schema::table('promotion_data', function (Blueprint $table) {
                $table->dropColumn('rank');
            });
        }
    }
};
