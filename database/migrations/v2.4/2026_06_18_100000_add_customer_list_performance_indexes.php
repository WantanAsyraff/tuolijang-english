<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $indexes = [
        'customer' => [
            'idx_customer_uid_id' => ['uid', 'id'],
            'idx_customer_uid_status_id' => ['uid', 'customer_status', 'id'],
            'idx_customer_creator_uid_id' => ['creator_uid', 'id'],
            'idx_customer_before_uid_id' => ['before_uid', 'id'],
            'idx_customer_external_userid' => ['external_userid'],
        ],
        'customer_clue' => [
            'idx_customer_clue_uid_id' => ['uid', 'id'],
            'idx_customer_clue_uid_status_id' => ['uid', 'status', 'id'],
            'idx_customer_clue_creator_uid_id' => ['creator_uid', 'id'],
            'idx_customer_clue_before_uid_id' => ['before_uid', 'id'],
            'idx_customer_clue_work_user' => ['userid', 'external_userid'],
        ],
        'customer_odds' => [
            'idx_customer_odds_uid_id' => ['uid', 'id'],
            'idx_customer_odds_uid_status_id' => ['uid', 'status', 'id'],
            'idx_customer_odds_creator_uid_id' => ['creator_uid', 'id'],
            'idx_customer_odds_before_uid_id' => ['before_uid', 'id'],
            'idx_customer_odds_eid_id' => ['eid', 'id'],
            'idx_customer_odds_work_user' => ['userid', 'external_userid'],
        ],
        'contract' => [
            'idx_contract_uid_id' => ['uid', 'id'],
            'idx_contract_uid_signing_status_id' => ['uid', 'signing_status', 'id'],
            'idx_contract_creator_uid_id' => ['creator_uid', 'id'],
            'idx_contract_eid_id' => ['eid', 'id'],
            'idx_contract_oid_id' => ['oid', 'id'],
            'idx_contract_end_status_abnormal' => ['end_date', 'signing_status', 'is_abnormal'],
        ],
        'client_remind' => [
            'idx_client_remind_cid_ent_status_time' => ['cid', 'entid', 'status', 'time', 'deleted_at'],
        ],
        'client_follow' => [
            'idx_client_follow_eid_types_time_uniqued' => ['eid', 'types', 'time', 'uniqued', 'deleted_at'],
            'idx_client_follow_link_eid_created' => ['link_type', 'eid', 'created_at'],
        ],
        'schedule_remind' => [
            'idx_schedule_remind_uniqued_sid' => ['uniqued', 'sid', 'deleted_at'],
        ],
        'schedule_task' => [
            'idx_schedule_task_pid_status' => ['pid', 'status'],
        ],
        'client_subscribe' => [
            'idx_client_subscribe_types_uid_status_eid' => ['types', 'uid', 'subscribe_status', 'eid'],
        ],
        'customer_liaison' => [
            'idx_customer_liaison_uid_id' => ['uid', 'id'],
            'idx_customer_liaison_eid_tel' => ['eid', 'liaison_tel'],
            'idx_customer_liaison_eid_name' => ['eid', 'liaison_name'],
        ],
        'client_bill' => [
            'idx_client_bill_cid_status_types_date' => ['cid', 'status', 'types', 'date'],
            'idx_client_bill_eid_status_types' => ['eid', 'status', 'types'],
            'idx_client_bill_uid_status_types_date' => ['uid', 'status', 'types', 'date'],
        ],
        'customer_product_assist' => [
            'idx_customer_product_assist_link_product' => ['link_type', 'link_id', 'product_name'],
            'idx_customer_product_assist_pid_link' => ['pid', 'link_type', 'link_id'],
        ],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $indexes) {
                foreach ($indexes as $name => $columns) {
                    if ($this->hasColumns($table, $columns) && ! $this->indexExists($table, $name)) {
                        $blueprint->index($columns, $name);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $indexes) {
                foreach (array_keys($indexes) as $name) {
                    if ($this->indexExists($table, $name)) {
                        $blueprint->dropIndex($name);
                    }
                }
            });
        }
    }

    private function hasColumns(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function indexExists(string $table, string $index): bool
    {
        $table = DB::getTablePrefix() . $table;
        $index = DB::getPdo()->quote($index);

        return ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = {$index}"));
    }
};
