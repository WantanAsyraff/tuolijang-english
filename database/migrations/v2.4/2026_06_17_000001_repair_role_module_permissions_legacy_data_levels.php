<?php

declare(strict_types=1);

use App\Constants\DataPermissionLevelEnum;
use App\Constants\ModuleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const BATCH_SIZE = 200;

    public function up(): void
    {
        if (! Schema::hasColumn('enterprise_role', 'module_permissions')) {
            return;
        }

        DB::table('enterprise_role')
            ->select(['id', 'data_level', 'directly', 'frame_id', 'module_permissions'])
            ->orderBy('id')
            ->chunkById(self::BATCH_SIZE, function ($roles): void {
                foreach ($roles as $role) {
                    $modulePermissions = $this->decodeModulePermissions($role->module_permissions);
                    if (! $this->needsRepair((int) $role->data_level, (int) $role->directly, $role->frame_id, $modulePermissions)) {
                        continue;
                    }

                    DB::table('enterprise_role')
                        ->where('id', $role->id)
                        ->update([
                            'module_permissions' => json_encode($this->buildModulePermissions($role), JSON_UNESCAPED_UNICODE),
                        ]);
                }
            });
    }

    public function down(): void
    {
        //
    }

    private function needsRepair(int $legacyLevel, int $directly, $frameId, ?array $modulePermissions): bool
    {
        if (empty($modulePermissions)) {
            return true;
        }

        if (! in_array($legacyLevel, [2, 3, 4, 5], true)) {
            return false;
        }

        $legacyFrameIds = $this->decodeFrameIds($frameId);
        foreach (ModuleEnum::all() as $module => $name) {
            if (! isset($modulePermissions[$module])) {
                return true;
            }

            $permission = $modulePermissions[$module];
            if ((int) ($permission['data_level'] ?? -1) !== $legacyLevel) {
                return false;
            }
            if ((int) ($permission['directly'] ?? 0) !== $directly) {
                return false;
            }
            if ($this->normalizeIds($permission['frame_id'] ?? []) !== $this->normalizeIds($legacyFrameIds)) {
                return false;
            }
        }

        return true;
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

    private function decodeModulePermissions($value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (empty($value)) {
            return null;
        }

        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function decodeFrameIds($frameId): array
    {
        if (empty($frameId)) {
            return [];
        }

        $frameIds = json_decode((string) $frameId, true);
        return is_array($frameIds) ? $frameIds : [];
    }

    private function normalizeIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        sort($ids);
        return $ids;
    }
};
