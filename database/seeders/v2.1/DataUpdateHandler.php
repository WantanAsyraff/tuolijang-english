<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

class DataUpdateHandler
{
    public function __construct()
    {
        $this->updateApproveRule();
        $this->updateSystemMenu();
    }

    /**
     * 更新审批规则.
     */
    private function updateApproveRule(): void
    {
        DB::unprepared('UPDATE ' . env('DB_PREFIX', 'eb_') . 'approve_rule parent INNER JOIN ' . env('DB_PREFIX', 'eb_') . 'approve child ON child.id = parent.approve_id SET parent.recall = 1 WHERE child.examine= 0;');
    }

    /**
     * 更新系统菜单.
     */
    private function updateSystemMenu(): void
    {
        // 生成辅助数据
        $sqlData = file_get_contents(database_path('seeders/v2.1/system_menus_copy.sql'));
        $sql     = prefix_correction($sqlData, env('DB_PREFIX', 'eb_'));
        DB::unprepared($sql);
        // 清除内存中的所有策略
        app('enforcer')->clearPolicy();
        DB::table('rules')->truncate();
    }
}
