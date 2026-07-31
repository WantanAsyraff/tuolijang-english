<?php

declare(strict_types=1);

use App\Constants\ModuleEnum;
use App\Constants\DataPermissionLevelEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const BATCH_SIZE = 200;

    /**
     * Run the migrations.
     *
     * 将现有角色的 data_level 迁移到 module_permissions 字段
     * 将旧版枚举映射为新版模块权限枚举：
     * 旧 0-不允许 => 新 1-仅本人
     * 旧 1-仅本人 => 新 1-仅本人
     * 旧 5-直属下级 => 新 2-直属下级
     * 旧 2-本部门 => 新 3-本部门
     * 旧 3-自定义部门 => 新 4-自定义部门
     * 旧 4-全部数据 => 新 5-全部数据
     */
    public function up(): void
    {
        // 检查 module_permissions 列是否存在
        if (! DB::getSchemaBuilder()->hasColumn('enterprise_role', 'module_permissions')) {
            Schema::table('enterprise_role', function (Blueprint $table) {
                $table->json('module_permissions')->nullable()->comment('内置模块数据权限配置')->after('frame_id');
            });
        }

        $this->migrateModulePermissions();
        $this->repairLegacyMappedModulePermissions();
    }

    private function migrateModulePermissions(): void
    {
        DB::table('enterprise_role')
            ->select(['id', 'data_level', 'directly', 'frame_id', 'module_permissions'])
            ->orderBy('id')
            ->chunkById(self::BATCH_SIZE, function ($roles): void {
                foreach ($roles as $role) {
                    if (! empty($role->module_permissions)) {
                        continue;
                    }

                    $modulePermissions = $this->buildModulePermissions($role);

                    // 更新角色
                    DB::table('enterprise_role')
                        ->where('id', $role->id)
                        ->update(['module_permissions' => json_encode($modulePermissions, JSON_UNESCAPED_UNICODE)]);
                }
            });
    }

    private function buildModulePermissions(object $role): array
    {
        $modulePermissions = [];
        foreach (ModuleEnum::all() as $module => $name) {
            $modulePermissions[$module] = [
                'data_level' => $this->mapLegacyDataLevel((int) $role->data_level),
                'directly'   => (int) $role->directly,
                'frame_id'   => $this->decodeFrameIds($role->frame_id),
            ];
        }

        return $modulePermissions;
    }

    private function mapLegacyDataLevel(int $level): int
    {
        return match ($level) {
            0, 1    => DataPermissionLevelEnum::SELF,
            5       => DataPermissionLevelEnum::DIRECT_SUBORDINATE,
            2       => DataPermissionLevelEnum::DEPARTMENT,
            3       => DataPermissionLevelEnum::CUSTOM_DEPARTMENT,
            4       => DataPermissionLevelEnum::ALL,
            default => DataPermissionLevelEnum::SELF,
        };
    }

    private function decodeFrameIds($frameId): array
    {
        if (empty($frameId)) {
            return [];
        }

        $frameIds = json_decode((string) $frameId, true);
        return is_array($frameIds) ? $frameIds : [];
    }

    private function repairLegacyMappedModulePermissions(): void
    {
        DB::table('enterprise_role')
            ->select(['id', 'data_level', 'directly', 'frame_id', 'module_permissions'])
            ->whereNotNull('module_permissions')
            ->orderBy('id')
            ->chunkById(self::BATCH_SIZE, function ($roles): void {
                foreach ($roles as $role) {
                    $modulePermissions = json_decode((string) $role->module_permissions, true);
                    if (! $this->needsLegacyMappingRepair((int) $role->data_level, $modulePermissions)) {
                        continue;
                    }

                    DB::table('enterprise_role')
                        ->where('id', $role->id)
                        ->update(['module_permissions' => json_encode($this->buildModulePermissions($role), JSON_UNESCAPED_UNICODE)]);
                }
            });
    }

    private function needsLegacyMappingRepair(int $legacyLevel, $modulePermissions): bool
    {
        if (! is_array($modulePermissions)) {
            return true;
        }

        $expectedWrongLevel = $legacyLevel;
        if (! in_array($expectedWrongLevel, [2, 3, 4, 5], true)) {
            return false;
        }

        foreach (ModuleEnum::all() as $module => $name) {
            if (! isset($modulePermissions[$module])) {
                return true;
            }

            if ((int) ($modulePermissions[$module]['data_level'] ?? -1) !== $expectedWrongLevel) {
                return false;
            }
        }

        return true;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enterprise_role', function (Blueprint $table) {
            $table->dropColumn('module_permissions');
        });
    }
};
