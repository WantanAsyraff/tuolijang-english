<?php

declare(strict_types=1);

use App\Constants\CacheEnum;
use App\Support\MalaysiaAddressCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(static function (): void {
            MalaysiaAddressCatalog::sync();
        });

        Cache::tags([CacheEnum::TAG_CONFIG, CacheEnum::TAG_DICT, CacheEnum::TAG_CUSTOMER])->flush();
    }

    public function down(): void
    {
        // System-owned geography is additive. Never remove it during rollback
        // because address records may already reference these stable IDs.
    }
};