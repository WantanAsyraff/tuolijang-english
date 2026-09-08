<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `eb_enterprise` MODIFY `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号'");
        DB::statement("ALTER TABLE `eb_user` MODIFY `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号'");
        DB::statement("ALTER TABLE `eb_user` MODIFY `standby_contacts_phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备用联系人手机号'");
        DB::statement("ALTER TABLE `eb_user_enterprise` MODIFY `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号'");
    }

    public function down(): void
    {
        // Do not shrink populated international numbers during rollback.
    }
};