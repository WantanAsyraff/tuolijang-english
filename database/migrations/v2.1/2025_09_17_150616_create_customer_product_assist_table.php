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
        Schema::create('customer_product_assist', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->integer('product_id')->default('0')->comment('产品ID');
            $table->string('product_name', 256)->default('')->comment('产品名');
            $table->string('sku', 256)->default('')->comment('产品规格');
            $table->string('image', 512)->default('')->comment('产品图片');
            $table->decimal('price', 11, 2)->default('0.00')->comment('售价');
            $table->decimal('ot_price', 11, 2)->default('0.00')->comment('原价');
            $table->decimal('total_price', 11, 2)->default('0.00')->comment('总价');
            $table->integer('count')->default('0')->comment('数量');
            $table->integer('discount')->default('0')->comment('折扣百分比');
            $table->text('remark')->comment('备注');
            $table->string('unique', 32)->default('')->comment('商品属性唯一值');
            $table->integer('link_id')->default('0')->comment('关联ID');
            $table->integer('link_type')->default('1')->comment('关联业务类型:1、 客户；2、订单；3、联系人；4、线索；5、商机；6、产品；7、合同签约；');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('产品业务关联表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('customer_product_assist');
    }
};
