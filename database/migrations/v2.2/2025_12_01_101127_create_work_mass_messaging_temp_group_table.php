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
        Schema::create('work_mass_messaging_temp_group', function (Blueprint $table) {
            $table->comment('企微群发素材分组');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('pid')->default(0)->index()->comment('父级分组ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('创建用户ID');
            $table->string('name')->default('')->comment('分组名称');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('work_mass_messaging_temp_group');
    }
};
