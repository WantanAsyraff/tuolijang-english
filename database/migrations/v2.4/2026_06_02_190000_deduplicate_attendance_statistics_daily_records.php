<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private const UNIQUE_INDEX = 'uniq_attendance_statistics_uid_date_active';
    private const LOOKUP_INDEX = 'idx_attendance_statistics_uid_date_active_id';
    private const GROUP_BATCH_SIZE = 500;

    public function up(): void
    {
        $table = $this->fullTableName();
        if (! $this->columnExists('statistics_date')) {
            DB::statement("ALTER TABLE `{$table}` ADD COLUMN `statistics_date` date GENERATED ALWAYS AS (date(`created_at`)) STORED AFTER `uid`");
        }
        if (! $this->columnExists('active_flag')) {
            DB::statement("ALTER TABLE `{$table}` ADD COLUMN `active_flag` tinyint GENERATED ALWAYS AS (if(`deleted_at` is null,1,null)) STORED AFTER `statistics_date`");
        }
        if (! $this->indexExists(self::UNIQUE_INDEX) && ! $this->indexExists(self::LOOKUP_INDEX)) {
            DB::statement("ALTER TABLE `{$table}` ADD INDEX `" . self::LOOKUP_INDEX . "` (`uid`, `statistics_date`, `active_flag`, `id`)");
        }

        $this->deduplicateActiveRecords();

        if (! $this->indexExists(self::UNIQUE_INDEX)) {
            DB::statement("ALTER TABLE `{$table}` ADD UNIQUE KEY `" . self::UNIQUE_INDEX . "` (`uid`, `statistics_date`, `active_flag`)");
        }
        if ($this->indexExists(self::LOOKUP_INDEX)) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `" . self::LOOKUP_INDEX . '`');
        }
    }

    public function down(): void
    {
        $table = $this->fullTableName();
        if ($this->indexExists(self::UNIQUE_INDEX)) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `" . self::UNIQUE_INDEX . '`');
        }
        if ($this->indexExists(self::LOOKUP_INDEX)) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `" . self::LOOKUP_INDEX . '`');
        }
        if ($this->columnExists('active_flag')) {
            DB::statement("ALTER TABLE `{$table}` DROP COLUMN `active_flag`");
        }
        if ($this->columnExists('statistics_date')) {
            DB::statement("ALTER TABLE `{$table}` DROP COLUMN `statistics_date`");
        }
    }

    private function deduplicateActiveRecords(): void
    {
        $lastUid  = 0;
        $lastDate = null;

        do {
            $query = DB::table('attendance_statistics')
                ->selectRaw('uid, statistics_date, COUNT(*) as total')
                ->where('active_flag', 1)
                ->whereNotNull('statistics_date');

            if ($lastDate !== null) {
                $query->where(function ($query) use ($lastUid, $lastDate): void {
                    $query->where('uid', '>', $lastUid)
                        ->orWhere(function ($query) use ($lastUid, $lastDate): void {
                            $query->where('uid', $lastUid)
                                ->where('statistics_date', '>', $lastDate);
                        });
                });
            }

            $groups = $query
                ->groupBy('uid', 'statistics_date')
                ->havingRaw('COUNT(*) > 1')
                ->orderBy('uid')
                ->orderBy('statistics_date')
                ->limit(self::GROUP_BATCH_SIZE)
                ->get();

            foreach ($groups as $group) {
                $this->deduplicateGroup((int) $group->uid, (string) $group->statistics_date);
            }

            if ($groups->isNotEmpty()) {
                $lastGroup = $groups->last();
                $lastUid   = (int) $lastGroup->uid;
                $lastDate  = (string) $lastGroup->statistics_date;
            }
        } while ($groups->count() === self::GROUP_BATCH_SIZE);
    }

    private function deduplicateGroup(int $uid, string $statisticsDate): void
    {
        $rows = DB::table('attendance_statistics')
            ->where('uid', $uid)
            ->where('statistics_date', $statisticsDate)
            ->where('active_flag', 1)
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        if (count($rows) < 2) {
            return;
        }

        usort($rows, fn (array $a, array $b) => $this->recordScore($b) <=> $this->recordScore($a) ?: $a['id'] <=> $b['id']);

        $keep      = array_shift($rows);
        $merged    = $this->mergeDuplicateRows($keep, $rows);
        $deleteIds = array_column($rows, 'id');

        DB::transaction(function () use ($keep, $merged, $deleteIds): void {
            DB::table('attendance_statistics')->where('id', $keep['id'])->update($merged);
            foreach (array_chunk($deleteIds, 1000) as $ids) {
                DB::table('attendance_statistics')->whereIn('id', $ids)->delete();
            }
        });
    }

    private function recordScore(array $row): int
    {
        $score = 0;
        foreach (['one', 'two', 'three', 'four'] as $slot) {
            $score += empty($row[$slot . '_shift_time']) ? 0 : 1000;
            $score += ((int) ($row[$slot . '_shift_record_id'] ?? 0)) > 0 ? 500 : 0;
            $score += ((int) ($row[$slot . '_shift_status'] ?? 0)) > 0 ? 100 : 0;
            $score += ((int) ($row[$slot . '_shift_location_status'] ?? 0)) > 0 ? 10 : 0;
        }

        return $score + (((float) ($row['actual_work_hours'] ?? 0)) > 0 ? 50 : 0);
    }

    private function mergeDuplicateRows(array $keep, array $duplicates): array
    {
        $merged = $keep;
        foreach ($duplicates as $row) {
            foreach (['one', 'two', 'three', 'four'] as $slot) {
                $this->mergeSlot($merged, $row, $slot);
            }

            if (((float) ($merged['actual_work_hours'] ?? 0)) <= 0 && ((float) ($row['actual_work_hours'] ?? 0)) > 0) {
                $merged['actual_work_hours'] = $row['actual_work_hours'];
            }
            if (((float) ($merged['required_work_hours'] ?? 0)) <= 0 && ((float) ($row['required_work_hours'] ?? 0)) > 0) {
                $merged['required_work_hours'] = $row['required_work_hours'];
            }
            if (empty($merged['updated_at']) || (! empty($row['updated_at']) && $row['updated_at'] > $merged['updated_at'])) {
                $merged['updated_at'] = $row['updated_at'];
            }
        }

        return [
            'one_shift_time'              => $merged['one_shift_time'],
            'one_shift_is_after'          => $merged['one_shift_is_after'],
            'one_shift_status'            => $merged['one_shift_status'],
            'one_shift_location_status'   => $merged['one_shift_location_status'],
            'one_shift_record_id'         => $merged['one_shift_record_id'],
            'two_shift_time'              => $merged['two_shift_time'],
            'two_shift_is_after'          => $merged['two_shift_is_after'],
            'two_shift_status'            => $merged['two_shift_status'],
            'two_shift_location_status'   => $merged['two_shift_location_status'],
            'two_shift_record_id'         => $merged['two_shift_record_id'],
            'three_shift_time'            => $merged['three_shift_time'],
            'three_shift_is_after'        => $merged['three_shift_is_after'],
            'three_shift_status'          => $merged['three_shift_status'],
            'three_shift_location_status' => $merged['three_shift_location_status'],
            'three_shift_record_id'       => $merged['three_shift_record_id'],
            'four_shift_time'             => $merged['four_shift_time'],
            'four_shift_is_after'         => $merged['four_shift_is_after'],
            'four_shift_status'           => $merged['four_shift_status'],
            'four_shift_location_status'  => $merged['four_shift_location_status'],
            'four_shift_record_id'        => $merged['four_shift_record_id'],
            'required_work_hours'         => $merged['required_work_hours'],
            'actual_work_hours'           => $merged['actual_work_hours'],
            'updated_at'                  => $merged['updated_at'],
        ];
    }

    private function mergeSlot(array &$merged, array $row, string $slot): void
    {
        $timeField = $slot . '_shift_time';
        if (empty($merged[$timeField]) && ! empty($row[$timeField])) {
            foreach (['time', 'is_after', 'status', 'location_status', 'record_id'] as $suffix) {
                $field           = $slot . '_shift_' . $suffix;
                $merged[$field] = $row[$field];
            }
            return;
        }

        foreach (['status', 'location_status', 'record_id'] as $suffix) {
            $field = $slot . '_shift_' . $suffix;
            if (empty($merged[$field]) && ! empty($row[$field])) {
                $merged[$field] = $row[$field];
            }
        }
    }

    private function columnExists(string $column): bool
    {
        $table = $this->fullTableName();
        $column = DB::getPdo()->quote($column);
        return ! empty(DB::select("SHOW COLUMNS FROM `{$table}` LIKE {$column}"));
    }

    private function indexExists(string $index): bool
    {
        $table = $this->fullTableName();
        $index = DB::getPdo()->quote($index);
        return ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = {$index}"));
    }

    private function fullTableName(): string
    {
        return DB::getTablePrefix() . 'attendance_statistics';
    }
};
