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
        Schema::create('customer_product_attr_value', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->integer('product_id')->default('0')->comment('商品ID');
            $table->text('detail')->comment('商品属性详情');
            $table->string('sku', 36)->default('')->comment('商品属性索引值');
            $table->decimal('ot_price', 11, 2)->default('0.00')->comment('原价');
            $table->decimal('price', 11, 2)->default('0.00')->comment('售价');
            $table->decimal('cost', 11, 2)->default('0.00')->comment('成本价');
            $table->string('image', 512)->default('')->comment('图片');
            $table->string('bar_code', 32)->default('')->comment('产品条码');
            $table->string('unique', 36)->default('')->comment('唯一值');
            $table->engine = 'InnoDB';
            $table->comment('产品属性值表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('customer_product_attr_value');
    }
};
