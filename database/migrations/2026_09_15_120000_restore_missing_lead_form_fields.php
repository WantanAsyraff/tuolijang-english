<?php

use App\Constants\CacheEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Restores the stock Lead form only when its category survived but every
 * active field was removed. Without these rows the lead-create API correctly
 * returns an empty schema, which leaves the web drawer blank.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('form_cate') || ! Schema::hasTable('form_data')) {
            return;
        }

        $categoryIds = DB::table('form_cate')
            ->where('types', 4)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->orderByDesc('sort')
            ->orderBy('id')
            ->pluck('id');

        // A configured Lead form must never be overwritten. This repair is
        // deliberately limited to the broken "groups but no visible fields"
        // state found in local installations created from incomplete seed data.
        if ($categoryIds->isEmpty() || DB::table('form_data')
            ->whereIn('cate_id', $categoryIds)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->exists()) {
            return;
        }

        $categoryId = (int) $categoryIds->first();
        $now        = now();
        $fields     = [
            ['key' => 'name', 'key_name' => '线索名称', 'type' => 'text', 'input_type' => 'input', 'required' => 1, 'placeholder' => '请输入线索名称', 'max' => 255, 'min' => 1, 'sort' => 10],
            ['key' => 'source', 'key_name' => '线索来源', 'type' => 'single', 'input_type' => 'select', 'required' => 1, 'placeholder' => '请选择线索来源', 'max' => 255, 'min' => 1, 'dict_ident' => 'clue_way', 'sort' => 9],
            ['key' => 'phone', 'key_name' => '联系电话', 'type' => 'text', 'input_type' => 'input', 'required' => 0, 'placeholder' => '请输入联系电话', 'max' => 255, 'min' => 1, 'sort' => 8],
            ['key' => 'customer_label', 'key_name' => '客户标签', 'type' => 'checked', 'input_type' => 'checked', 'required' => 0, 'placeholder' => '请选择客户标签', 'max' => 50, 'min' => 1, 'sort' => 7],
            ['key' => 'area_cascade', 'key_name' => '省市区', 'type' => 'single', 'input_type' => 'select', 'required' => 0, 'placeholder' => '请选择省市区', 'max' => 50, 'min' => 1, 'dict_ident' => 'area_cascade', 'sort' => 6],
            ['key' => 'address', 'key_name' => '详细地址', 'type' => 'text', 'input_type' => 'input', 'required' => 0, 'placeholder' => '请输入详细地址', 'max' => 255, 'min' => 1, 'sort' => 5],
            ['key' => 'status', 'key_name' => '线索状态', 'type' => 'radio', 'input_type' => 'radio', 'required' => 0, 'placeholder' => '请选择线索状态', 'max' => 255, 'min' => 1, 'dict_ident' => 'clue_status', 'value' => '1', 'sort' => 4],
            ['key' => 'createtime', 'key_name' => '线索时间', 'type' => 'datetime', 'input_type' => 'datetime', 'required' => 0, 'placeholder' => '请选择线索时间', 'max' => 0, 'min' => 0, 'sort' => 3],
            ['key' => 'mark', 'key_name' => '备注', 'type' => 'textarea', 'input_type' => 'input', 'required' => 0, 'placeholder' => '请填写备注', 'max' => 255, 'min' => 1, 'sort' => 2],
        ];

        DB::table('form_data')->insert(array_map(function (array $field) use ($categoryId, $now): array {
            return array_merge([
                'cate_id'       => $categoryId,
                'param'         => '',
                'decimal_place' => 0,
                'upload_type'   => 0,
                'dict_ident'    => '',
                'value'         => '',
                'uniqued'       => 0,
                'desc'          => '',
                'link_type'     => 0,
                'link_field'    => '',
                'status'        => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ], $field);
        }, $fields));

        Cache::tags([CacheEnum::TAG_CUSTOMER])->flush();
    }

    public function down(): void
    {
        // The inserted rows become user-editable form configuration; rolling
        // them back could delete fields that an administrator has since tuned.
    }
};
