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
        Schema::create('customer_liaison', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->default(0)->comment('业务员ID');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->integer('creator_uid')->default(0)->comment('创建人ID');
            $table->string('liaison_name', 50)->default('')->comment('联系人姓名');
            $table->string('liaison_tel', 12)->default('')->comment('联系电话');
            $table->string('liaison_job', 50)->default('')->comment('联系人职位');
            $table->string('e06d7153', 50)->default('')->comment('性别');
            $table->string('e06d7152', 50)->default('')->comment('联系人邮箱');
            $table->string('e06d7159', 50)->default('')->comment('联系人微信');
            $table->string('l753bf282', 50)->default('')->comment('备注');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_liaison');
    }
};
