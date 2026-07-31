<?php

declare(strict_types=1);


namespace App\Http\Requests\Customer;

use App\Http\Requests\ApiValidate;

/**
 * 客户
 * Class ClientRequest.
 */
class InvoiceRequest extends ApiValidate
{
    /**
     * 验证场景.
     * @var string[][]
     */
    protected $scene = [
        'store'        => ['eid', 'cid', 'name', 'bill_date', 'amount', 'title', 'collect_name', 'category_id'],
        'update'       => ['name', 'bill_date', 'amount', 'title', 'collect_name', 'category_id'],
        'open_invalid' => ['remark'],
    ];

    /**
     * 错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required'         => '请填写发票名称',
            'eid.required'          => '请填写客户编号',
            'eid.integer'           => '请填写正确的客户编号',
            'cid.required'          => '请填写订单编号',
            'cid.integer'           => '请填写正确的订单编号',
            'bill_date.required'    => '请选择开票日期',
            'bill_date.date'        => '请选择正确的开票日期',
            'amount.required'       => '请填写发票金额',
            'amount.numeric'        => '发票金额必须为数字',
            'amount.gt'             => '发票金额必须大于0',
            'title.required'        => '请填写发票抬头',
            'collect_name.required' => '请填写邮寄联系人',
            'category_id.required'  => '请选择发票类目',
            'category_id.integer'   => '请选择正确的发票类目',
            'category_id.gt'        => '请选择正确的发票类目',
            'remark.required'       => '请填写作废原因',
            'remark.max'            => '作废原因长度超出限制',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            // 'name'         => 'required',
            'eid'       => 'required|integer',
            'cid'       => 'required|integer',
            'bill_date' => 'required|date',
            'types'     => 'required|integer',
            'amount'    => 'required|numeric|gt:0',
            'title'     => 'required',
            // 'collect_name' => 'required',
            'category_id' => 'required|integer|gt:0',
            'remark'      => 'required|max:120',
        ];
    }
}
