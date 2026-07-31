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
        Schema::create('work_mass_messaging_temp', function (Blueprint $table) {
            $table->comment('企微群发素材');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('创建用户ID');
            $table->unsignedInteger('group_id')->default(0)->index()->comment('分组ID');
            $table->text('content')->comment('内容');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('types')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('work_mass_messaging_temp');
    }
};
