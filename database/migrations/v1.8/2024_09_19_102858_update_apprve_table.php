<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('approve', 'sort')) {
            Schema::table('approve', function (Blueprint $table) {
                $table->unsignedInteger('sort')->default(0)->comment('排序')->after('status');
            });
        }
        if (!Schema::hasColumn('approve_apply', 'apply_id')) {
            Schema::table('approve_apply', function (Blueprint $table) {
                $table->unsignedInteger('apply_id')->default(0)->comment('关联审批ID')->after('link_id');
            });
        }
        if (!Schema::hasColumn('approve_apply', 'is_recall')) {
            Schema::table('approve_apply', function (Blueprint $table) {
                $table->unsignedInteger('is_recall')->default(0)->comment('是否为撤销审批')->after('apply_id');
            });
        }
        if (!Schema::hasColumn('approve_rule', 'is_transfer')) {
            Schema::table('approve_rule', function (Blueprint $table) {
                $table->unsignedInteger('is_transfer')->default(0)->comment('是否可转审')->after('recall');
            });
        }
        if (!Schema::hasColumn('approve_rule', 'is_sign')) {
            Schema::table('approve_rule', function (Blueprint $table) {
                $table->unsignedInteger('is_sign')->default(0)->comment('是否可加签')->after('is_transfer');
            });
        }
        if (Schema::hasColumn('approve_user', 'status')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->integer('status')->default(0)->comment('审批状态：-1、无需审批；0、待审批；1、已通过；2、已拒绝；')->change();
            });
        }
        if (!Schema::hasColumn('approve_user', 'is_sign')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->unsignedInteger('is_sign')->default(0)->comment('是否为加签')->after('status');
            });
        }
        if (!Schema::hasColumn('approve_user', 'is_transfer')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->unsignedInteger('is_transfer')->default(0)->comment('是否为转审：0、正常节点；1、已转审；2、被转审；')->after('is_sign');
            });
        }
        if (!Schema::hasColumn('approve_user', 'parent')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->unsignedInteger('parent')->default(0)->comment('转审人ID')->after('is_transfer');
            });
        }
        if (!Schema::hasColumn('approve_user', 'content')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->string('content', 512)->default('')->comment('人员说明')->after('process_info');
            });
        }
        if (!Schema::hasColumn('roster_cycle_shift', 'deleted_at')) {
            Schema::table('roster_cycle_shift', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable()->after('updated_at');
            });
        }
        if (!Schema::hasColumn('schedule_type', 'user_id')) {
            Schema::table('schedule_type', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->after('id');
            });
        }
        if (!Schema::hasColumn('system_crud_approve', 'types')) {
            Schema::table('system_crud_approve', function (Blueprint $table) {
                $table->unsignedInteger('types')->default(0)->comment('审批类型')->after('status');
            });
        }
        if (!Schema::hasColumn('system_crud_approve', 'sort')) {
            Schema::table('system_crud_approve', function (Blueprint $table) {
                $table->unsignedInteger('sort')->default(0)->comment('排序')->after('types');
            });
        }
        if (!Schema::hasColumn('system_crud_approve_rule', 'is_sign')) {
            Schema::table('system_crud_approve_rule', function (Blueprint $table) {
                $table->unsignedInteger('is_sign')->default(0)->comment('是否可加签')->after('recall');
            });
        }
        if (!Schema::hasColumn('system_crud_approve_rule', 'is_transfer')) {
            Schema::table('system_crud_approve_rule', function (Blueprint $table) {
                $table->unsignedInteger('is_transfer')->default(0)->comment('是否可转审')->after('is_sign');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('approve', 'sort')) {
            Schema::table('approve', function (Blueprint $table) {
                $table->dropColumn('sort');
            });
        }
        if (Schema::hasColumn('approve_apply', 'apply_id')) {
            Schema::table('approve_apply', function (Blueprint $table) {
                $table->dropColumn('apply_id');
            });
        }
        if (Schema::hasColumn('approve_apply', 'is_recall')) {
            Schema::table('approve_apply', function (Blueprint $table) {
                $table->dropColumn('is_recall');
            });
        }
        if (Schema::hasColumn('approve_rule', 'is_transfer')) {
            Schema::table('approve_rule', function (Blueprint $table) {
                $table->dropColumn('is_transfer');
            });
        }
        if (Schema::hasColumn('approve_rule', 'is_sign')) {
            Schema::table('approve_rule', function (Blueprint $table) {
                $table->dropColumn('is_sign');
            });
        }
        if (Schema::hasColumn('approve_user', 'is_sign')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->dropColumn('is_sign');
            });
        }
        if (Schema::hasColumn('approve_user', 'is_transfer')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->dropColumn('is_transfer');
            });
        }
        if (Schema::hasColumn('approve_user', 'parent')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->dropColumn('parent');
            });
        }
        if (Schema::hasColumn('approve_user', 'content')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->dropColumn('content');
            });
        }
        if (Schema::hasColumn('roster_cycle_shift', 'deleted_at')) {
            Schema::table('roster_cycle_shift', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        if (Schema::hasColumn('schedule_type', 'user_id')) {
            Schema::table('schedule_type', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
        if (Schema::hasColumn('system_crud_approve', 'types')) {
            Schema::table('system_crud_approve', function (Blueprint $table) {
                $table->dropColumn('types');
            });
        }
        if (Schema::hasColumn('system_crud_approve', 'sort')) {
            Schema::table('system_crud_approve', function (Blueprint $table) {
                $table->dropColumn('sort');
            });
        }
        if (Schema::hasColumn('system_crud_approve_rule', 'is_sign')) {
            Schema::table('system_crud_approve_rule', function (Blueprint $table) {
                $table->dropColumn('is_sign');
            });
        }
        if (Schema::hasColumn('system_crud_approve_rule', 'is_transfer')) {
            Schema::table('system_crud_approve_rule', function (Blueprint $table) {
                $table->dropColumn('is_transfer');
            });
        }
    }
};
