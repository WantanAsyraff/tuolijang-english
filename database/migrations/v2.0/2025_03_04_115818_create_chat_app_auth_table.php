<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (! Schema::hasTable('chat_app_auth')) {
            Schema::create('chat_app_auth', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('app_id')->default(0)->comment('创建用户ID');
                $table->unsignedInteger('user_id')->default(0)->comment('用户ID');
                $table->index(['user_id', 'app_id']);
                $table->comment('应用成员关联表');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('chat_app_auth');
    }
};
