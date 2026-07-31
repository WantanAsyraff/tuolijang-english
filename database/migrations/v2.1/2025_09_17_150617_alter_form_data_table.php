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
        Schema::table('form_data', function (Blueprint $table) {
            $table->unsignedInteger('types')->default('0')->comment('类型：1、客户；2、合同；3、发票；4、线索；5、商机；6、产品；');
            $table->unsignedInteger('link_type')->default('0')->comment('关联类型：1、客户；2、合同；3、发票；4、线索；5、商机；6、产品；');
            $table->string('link_field', 64)->default('')->comment('关联字段');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('form_data', function (Blueprint $table) {
            $table->dropColumn(['types', 'link_type', 'link_field']);
        });
    }
};
