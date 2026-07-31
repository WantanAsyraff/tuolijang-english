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
        if (! Schema::hasColumn('bill_list', 'file_id')) {
            Schema::table('bill_list', function (Blueprint $table) {
                $table->string('file_id', 32)->default('')->comment('附件ID')->after('mark');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('bill_list', 'file_id')) {
            Schema::table('bill_list', function (Blueprint $table) {
                $table->dropColumn('file_id');
            });
        }
    }
};
