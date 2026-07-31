<?php

declare(strict_types=1);


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (! Schema::hasColumn('admin', 'mcp_key')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->string('mcp_key', 64)->default('')->comment('MCP工具调用唯一值')->after('work_member_id');
            });
        }

        DB::table('admin')
            ->where(function ($query) {
                $query->whereNull('mcp_key')->orWhere('mcp_key', '');
            })
            ->orderBy('id')
            ->chunkById(200, function ($users): void {
                foreach ($users as $user) {
                    DB::table('admin')
                        ->where('id', $user->id)
                        ->update(['mcp_key' => $this->generateMcpKey()]);
                }
            }, 'id');

        $this->ensureEmptyMcpKeysFilled();

        if (! $this->indexExists('admin_mcp_key_unique')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->unique('mcp_key', 'admin_mcp_key_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('admin', 'mcp_key')) {
            Schema::table('admin', function (Blueprint $table) {
                if ($this->indexExists('admin_mcp_key_unique')) {
                    $table->dropUnique('admin_mcp_key_unique');
                }
                $table->dropColumn('mcp_key');
            });
        }
    }

    private function generateMcpKey(): string
    {
        do {
            $key = bin2hex(random_bytes(24));
        } while (DB::table('admin')->where('mcp_key', $key)->exists());

        return $key;
    }

    private function ensureEmptyMcpKeysFilled(): void
    {
        while (DB::table('admin')->whereNull('mcp_key')->orWhere('mcp_key', '')->exists()) {
            $user = DB::table('admin')
                ->where(function ($query) {
                    $query->whereNull('mcp_key')->orWhere('mcp_key', '');
                })
                ->orderBy('id')
                ->first(['id']);

            if (! $user) {
                return;
            }

            DB::table('admin')->where('id', $user->id)->update(['mcp_key' => $this->generateMcpKey()]);
        }
    }

    private function indexExists(string $index): bool
    {
        $table = DB::getTablePrefix() . 'admin';
        $index = DB::getPdo()->quote($index);
        return ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = {$index}"));
    }
};
