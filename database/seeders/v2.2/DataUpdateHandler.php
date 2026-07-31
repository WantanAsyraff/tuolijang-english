<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DataUpdateHandler
{
    public function __construct()
    {
        $this->fixToJson();
    }

    /**
     * 更新审批规则.
     */
    private function fixToJson(): void
    {
        Artisan::call('fix:to-json', ['--table' => 'customer', '--field' => 'customer_label']);
        Artisan::call('fix:to-json', ['--table' => 'customer', '--field' => 'area_cascade']);
        Artisan::call('fix:to-json', ['--table' => 'customer', '--field' => 'member']);
        Artisan::call('fix:to-json', ['--table' => 'contract', '--field' => 'contract_category']);
        Artisan::call('fix:to-json', ['--table' => 'customer_clue', '--field' => 'customer_label']);
        Artisan::call('fix:to-json', ['--table' => 'customer_clue', '--field' => 'area_cascade']);
        // 清除内存中的所有策略
        app('enforcer')->clearPolicy();
        DB::table('rules')->truncate();
    }
}
