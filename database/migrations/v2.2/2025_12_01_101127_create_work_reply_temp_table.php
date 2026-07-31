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
        Schema::create('work_reply_temp', function (Blueprint $table) {
            $table->comment('快捷回复表');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('创建用户ID');
            $table->unsignedInteger('group_id')->default(0)->index()->comment('分租ID');
            $table->string('types')->default('')->comment('内容类型');
            $table->string('title')->default('')->comment('标题');
            $table->string('info', 512)->default('')->comment('摘要');
            $table->string('link', 512)->default('')->comment('链接');
            $table->string('app_id')->default('')->comment('小程序AppID');
            $table->text('content')->comment('文本内容');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('work_reply_temp');
    }
};
