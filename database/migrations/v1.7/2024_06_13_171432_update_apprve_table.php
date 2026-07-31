<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (! Schema::hasColumn('approve', 'user_id')) {
            Schema::table('approve', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('approve_apply', 'user_id')) {
            Schema::table('approve_apply', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('approve_content', 'user_id')) {
            Schema::table('approve_content', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('approve_form', 'user_id')) {
            Schema::table('approve_form', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('approve_process', 'user_id')) {
            Schema::table('approve_process', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('approve_user', 'user_id')) {
            Schema::table('approve_user', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('approve_reply', 'user_id')) {
            Schema::table('approve_reply', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('approve_rule', 'user_id')) {
            Schema::table('approve_rule', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('enterprise_notice', 'user_id')) {
            Schema::table('enterprise_notice', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('storage_record', 'user_id')) {
            Schema::table('storage_record', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('storage_record', 'operator')) {
            Schema::table('storage_record', function (Blueprint $table) {
                $table->unsignedInteger('operator')->default(0)->comment('操作用户id')->after('user_id');
            });
        }
        if (! Schema::hasColumn('rank_job', 'user_id')) {
            Schema::table('rank_job', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('enterprise_user_work', 'user_id')) {
            Schema::table('enterprise_user_work', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('enterprise_user_education', 'user_id')) {
            Schema::table('enterprise_user_education', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('enterprise_user_change', 'uid')) {
            Schema::table('enterprise_user_change', function (Blueprint $table) {
                $table->unsignedInteger('uid')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('schedule_type', 'user_id')) {
            Schema::table('schedule_type', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户id')->after('id');
            });
        }
        if (! Schema::hasColumn('system_crud_approve', 'types')) {
            Schema::table('system_crud_approve', function (Blueprint $table) {
                $table->unsignedInteger('types')->default(0)->comment('审批类型')->after('id');
            });
        }
        if (! Schema::hasColumn('bill_list', 'user_id')) {
            Schema::table('bill_list', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->default(0)->comment('用户ID')->after('id');
            });
        }
        if (! Schema::hasColumn('bill_list', 'order_id')) {
            Schema::table('bill_list', function (Blueprint $table) {
                $table->unsignedInteger('order_id')->default(0)->comment('订单ID')->after('link_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('approve', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('approve_apply', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('approve_content', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('approve_form', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('approve_process', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('approve_user', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('approve_reply', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('approve_rule', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('enterprise_notice', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('storage_record', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('operator');
        });
        Schema::table('rank_job', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('enterprise_user_work', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('enterprise_user_education', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('enterprise_user_change', function (Blueprint $table) {
            $table->dropColumn('uid');
        });
        Schema::table('schedule_type', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('system_crud_approve', function (Blueprint $table) {
            $table->dropColumn('types');
        });
        Schema::table('bill_list', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('bill_list', function (Blueprint $table) {
            $table->dropColumn('order_id');
        });
    }
};
