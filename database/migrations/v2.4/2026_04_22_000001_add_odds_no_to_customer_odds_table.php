<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOddsNoToCustomerOddsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('customer_odds', 'odds_no')) {
            Schema::table('customer_odds', function (Blueprint $table) {
                $table->string('odds_no', 30)->nullable()->comment('商机编号')->after('name');
            });
        }

        if (! $this->indexExists('odds_no')) {
            Schema::table('customer_odds', function (Blueprint $table) {
                $table->unique('odds_no', 'odds_no');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('customer_odds', 'odds_no')) {
            Schema::table('customer_odds', function (Blueprint $table) {
                if ($this->indexExists('odds_no')) {
                    $table->dropUnique('odds_no');
                }
                $table->dropColumn('odds_no');
            });
        }
    }

    private function indexExists(string $index): bool
    {
        $table = DB::getTablePrefix() . 'customer_odds';
        $index = DB::getPdo()->quote($index);
        return ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = {$index}"));
    }
}
