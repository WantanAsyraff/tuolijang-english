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
        Schema::create('work_mass_messaging_temp_attach', function (Blueprint $table) {
            $table->comment('素材附件表');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('创建用户ID');
            $table->unsignedInteger('temp_id')->default(0)->index()->comment('素材ID');
            $table->string('types')->default('')->comment('内容类型');
            $table->string('title')->default('')->comment('标题');
            $table->string('info', 512)->default('')->comment('摘要');
            $table->string('link', 512)->default('')->comment('链接');
            $table->string('app_id')->default('')->comment('小程序AppID');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('work_mass_messaging_temp_attach');
    }
};
