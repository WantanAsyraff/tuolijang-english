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
    public function up(): void
    {
        Schema::create('salesman_custom_field', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->string('custom_type', 18)->default('')->comment('类型');
            $table->text('field_list')->default('')->comment('自定义数据');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('salesman_custom_field');
    }
};
