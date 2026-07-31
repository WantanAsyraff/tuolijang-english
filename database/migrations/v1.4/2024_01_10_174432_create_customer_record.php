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
        Schema::create('customer_record', function (Blueprint $table) {
            $table->id();
            $table->integer('eid')->comment('客户ID');
            $table->tinyInteger('type')->comment('记录类型 1、退回公海；2、领取；3、流失；4、取消流失；5、移交同事；');
            $table->integer('uid')->comment('业务员ID');
            $table->integer('creator_uid')->comment('创建人ID');
            $table->integer('record_version')->default(0)->comment('记录版本');
            $table->string('reason', 255)->comment('原因');
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
        Schema::dropIfExists('customer_record');
    }
};
