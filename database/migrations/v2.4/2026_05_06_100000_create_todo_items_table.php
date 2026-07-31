<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('todo_items')) {
            Schema::create('todo_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->default(0)->comment('待办归属用户ID');
                $table->string('type', 32)->default('')->comment('待办类型(对应TodoEnum)');
                $table->unsignedInteger('source_id')->default(0)->comment('来源表主键ID');
                $table->string('title', 255)->default('')->comment('待办标题');
                $table->json('extra')->nullable()->comment('类型特有扩展数据');
                $table->dateTime('source_created_at', 3)->nullable()->comment('来源记录的created_at(排序依据)');
                $table->unsignedTinyInteger('status')->default(1)->comment('1=待办中 2=已完成/已失效');
                $table->timestamps();
                $table->comment('统一待办表');

                $table->index(['user_id', 'status', 'source_created_at'], 'idx_user_status_created');
                $table->unique(['user_id', 'type', 'source_id'], 'uk_user_type_source');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_items');
    }
};
