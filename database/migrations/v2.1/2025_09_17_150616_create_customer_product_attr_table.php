<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::create('customer_product_attr', function (Blueprint $table) {
            $table->id()->comment('主键ID');
            $table->integer('product_id')->default('0')->comment('产品ID');
            $table->string('attr_name', 36)->default('')->comment('属性名');
            $table->text('attr_values')->comment('属性值');
            $table->engine = 'InnoDB';
            $table->comment('产品规格表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('customer_product_attr');
    }
};
