<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_config', function (Blueprint $table) {
            $table->id();
            $table->date('day')->nullable()->comment('日期');
            $table->tinyInteger('is_rest')->default(1)->comment('是否休息 0、上班；1、休息；');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_config');
    }
};
