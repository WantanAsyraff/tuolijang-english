<?php

declare(strict_types=1);

use App\Constants\CustomEnum\CustomEnum;
use App\Http\Service\Config\FormService;
use App\Http\Service\Open\OpenapiRuleService;
use Illuminate\Support\Facades\DB;

class DataUpdateHandler
{
    protected $cardModels = [
    ];

    public function __construct()
    {
        $this->saveApiRule();
    }

    /**
     * 保存自定义业务文档.
     * @throws Throwable
     */
    public function saveApiRule(): mixed
    {
        return DB::transaction(function () {
            $rules = [
                [
                    'name'    => '对外接口授权',
                    'crud_id' => 0,
                    'method'  => '',
                    'url'     => '',
                    'type'    => 0,
                    'child'   => [
                        [
                            'name'       => '授权登录',
                            'crud_id'    => 0,
                            'method'     => 'POST',
                            'url'        => 'open/auth/login',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'access_key',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '授权账号',
                                ],
                                [
                                    'name'      => 'secret_key',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '授权秘钥',
                                ],
                            ],
                            'response_data' => [
                                [
                                    'name'      => 'token',
                                    'form_type' => 'string',
                                    'message'   => '授权凭证',
                                ],
                                [
                                    'name'      => 'token_type',
                                    'form_type' => 'string',
                                    'message'   => '凭证类型',
                                ],
                                [
                                    'name'      => 'expires_in',
                                    'form_type' => 'int',
                                    'message'   => '过期时间',
                                ],
                            ],
                        ],
                    ],
                ],
                CustomEnum::CUSTOMER => [
                    'name'    => '客户基本信息',
                    'crud_id' => 0,
                    'method'  => '',
                    'url'     => '',
                    'type'    => 0,
                    'child'   => [
                        [
                            'name'    => '新增客户',
                            'crud_id' => 0,
                            'method'  => 'POST',
                            'url'     => 'open/customer',
                            'type'    => 1,
                        ],
                        [
                            'name'    => '修改客户',
                            'crud_id' => 0,
                            'method'  => 'PUT',
                            'url'     => 'open/customer/{id}',
                            'type'    => 1,
                        ],
                        [
                            'name'    => '删除客户',
                            'crud_id' => 0,
                            'method'  => 'DELETE',
                            'url'     => 'open/customer/{id}',
                            'type'    => 1,
                        ],
                    ],
                ],
                CustomEnum::CONTRACT => [
                    'name'    => '合同',
                    'crud_id' => 0,
                    'method'  => '',
                    'url'     => '',
                    'type'    => 0,
                    'child'   => [
                        [
                            'name'    => '新增合同',
                            'crud_id' => 0,
                            'method'  => 'POST',
                            'url'     => 'open/contract',
                            'type'    => 1,
                        ],
                        [
                            'name'    => '修改合同',
                            'crud_id' => 0,
                            'method'  => 'PUT',
                            'url'     => 'open/contract/{id}',
                            'type'    => 1,
                        ],
                        [
                            'name'    => '删除合同',
                            'crud_id' => 0,
                            'method'  => 'DELETE',
                            'url'     => 'open/contract/{id}',
                            'type'    => 1,
                        ],
                    ],
                ],
                CustomEnum::LIAISON => [
                    'name'    => '客户联系人',
                    'crud_id' => 0,
                    'method'  => '',
                    'url'     => '',
                    'type'    => 0,
                    'child'   => [
                        [
                            'name'    => '新增联系人',
                            'crud_id' => 0,
                            'method'  => 'POST',
                            'url'     => 'open/liaison',
                            'type'    => 1,
                        ],
                        [
                            'name'    => '修改联系人',
                            'crud_id' => 0,
                            'method'  => 'PUT',
                            'url'     => 'open/liaison/{id}',
                            'type'    => 1,
                        ],
                        [
                            'name'    => '删除联系人',
                            'crud_id' => 0,
                            'method'  => 'DELETE',
                            'url'     => 'open/liaison/{id}',
                            'type'    => 1,
                        ],
                    ],
                ],
                [
                    'name'    => '发票记录',
                    'crud_id' => 0,
                    'method'  => '',
                    'url'     => '',
                    'type'    => 0,
                    'child'   => [
                        [
                            'name'       => '新增发票',
                            'crud_id'    => 0,
                            'method'     => 'POST',
                            'url'        => 'open/invoice',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'uid',
                                    'form_type' => 'int',
                                    'is_must'   => false,
                                    'message'   => '关联业务员id',
                                ],
                                [
                                    'name'      => 'bill_id',
                                    'form_type' => 'array',
                                    'is_must'   => false,
                                    'message'   => '关联付款单id',
                                    'symbol'    => 'billId',
                                ],
                                [
                                    'name'      => 'eid',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '关联客戶id',
                                    'symbol'    => 'customerList',
                                ],
                                [
                                    'name'      => 'cid',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '关联合同id',
                                    'symbol'    => 'contractList',
                                ],
                                [
                                    'name'      => 'bill_date',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '期望开票日期',
                                    'symbol'    => 'desireDate',
                                ],
                                [
                                    'name'      => 'collect_type',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '开票要求',
                                    'symbol'    => 'invoicingMethod',
                                ],
                                [
                                    'name'      => 'collect_name',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '联系人',
                                    'symbol'    => 'liaisonMan',
                                ],
                                [
                                    'name'      => 'collect_tel',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '联系电话',
                                    'symbol'    => 'telephone',
                                ],
                                [
                                    'name'      => 'collect_email',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '邮箱地址',
                                    'symbol'    => 'invoicingEmail',
                                ],
                                [
                                    'name'      => 'mail_address',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '邮寄地址',
                                    'symbol'    => 'mailingAddress',
                                ],
                                [
                                    'name'      => 'types',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '发票类型',
                                    'symbol'    => 'invoiceType',
                                ],
                                [
                                    'name'      => 'amount',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '开票金额（元）',
                                    'symbol'    => 'invoiceAmount',
                                ],
                                [
                                    'name'      => 'price',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '付款单金额（元）',
                                    'symbol'    => 'billAmount',
                                ],
                                [
                                    'name'      => 'title',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '发票抬头',
                                    'symbol'    => 'invoiceHeader',
                                ],
                                [
                                    'name'      => 'ident',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '纳税人识别号',
                                    'symbol'    => 'dutyParagraph',
                                ],
                                [
                                    'name'      => 'mark',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '备注',
                                    'symbol'    => 'remark',
                                ],
                            ],
                        ],
                        [
                            'name'       => '发票作废',
                            'crud_id'    => 0,
                            'method'     => 'PUT',
                            'url'        => 'open/invoice/{id}/invalid',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'uid',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '业务员id',
                                ],
                                [
                                    'name'      => 'remark',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '作废原因',
                                ],
                            ],
                        ],
                    ],
                ], // 发票记录
                [
                    'name'    => '客户账目记录',
                    'crud_id' => 0,
                    'method'  => '',
                    'url'     => '',
                    'type'    => 0,
                    'child'   => [
                        [
                            'name'       => '新增账目回款',
                            'crud_id'    => 0,
                            'method'     => 'POST',
                            'url'        => 'open/bill/payment',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'uid',
                                    'form_type' => 'int',
                                    'is_must'   => false,
                                    'message'   => '关联业务员id',
                                ],
                                [
                                    'name'      => 'cid',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '关联合同id',
                                    'symbol'    => 'contractList',
                                ],
                                [
                                    'name'      => 'bill_cate_id',
                                    'form_type' => 'array',
                                    'is_must'   => true,
                                    'message'   => '财务收入科目id',
                                    'symbol'    => 'incomeCategories',
                                ],
                                [
                                    'name'      => 'type_id',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '支付方式id',
                                    'symbol'    => 'payType',
                                ],
                                [
                                    'name'      => 'num',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '回款金额（元）',
                                    'symbol'    => 'collectionAmount',
                                ],
                                [
                                    'name'      => 'date',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '付款时间',
                                    'symbol'    => 'payTime',
                                ],

                                [
                                    'name'      => 'attach',
                                    'form_type' => 'array',
                                    'is_must'   => false,
                                    'message'   => '付款凭证id',
                                    'symbol'    => 'paymentVoucher',
                                ],
                                [
                                    'name'      => 'mark',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '备注',
                                    'symbol'    => 'remark',
                                ],
                            ],
                        ],
                        [
                            'name'       => '新增账目续费',
                            'crud_id'    => 0,
                            'method'     => 'POST',
                            'url'        => 'open/bill/renewal',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'uid',
                                    'form_type' => 'int',
                                    'is_must'   => false,
                                    'message'   => '关联业务员id',
                                ],
                                [
                                    'name'      => 'cid',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '关联合同id',
                                    'symbol'    => 'contractList',
                                ],
                                [
                                    'name'      => 'bill_cate_id',
                                    'form_type' => 'array',
                                    'is_must'   => true,
                                    'message'   => '财务收入科目id',
                                    'symbol'    => 'incomeCategories',
                                ],
                                [
                                    'name'      => 'cate_id',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '续费类型id',
                                    'symbol'    => 'renewalType',
                                ],
                                [
                                    'name'      => 'type_id',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '支付方式id',
                                    'symbol'    => 'payType',
                                ],
                                [
                                    'name'      => 'num',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '续费金额（元）',
                                    'symbol'    => 'renewalAmount',
                                ],
                                [
                                    'name'      => 'date',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '付款时间',
                                    'symbol'    => 'payTime',
                                ],
                                [
                                    'name'      => 'end_date',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '续费结束日期',
                                    'symbol'    => 'renewalEndTime',
                                ],

                                [
                                    'name'      => 'attach',
                                    'form_type' => 'array',
                                    'is_must'   => false,
                                    'message'   => '付款凭证id',
                                    'symbol'    => 'paymentVoucher',
                                ],
                                [
                                    'name'      => 'mark',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '备注',
                                    'symbol'    => 'remark',
                                ],
                            ],
                        ],
                        [
                            'name'       => '新增账目支出',
                            'crud_id'    => 0,
                            'method'     => 'POST',
                            'url'        => 'open/bill/expend',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'uid',
                                    'form_type' => 'int',
                                    'is_must'   => false,
                                    'message'   => '关联业务员id',
                                ],
                                [
                                    'name'      => 'cid',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '关联合同id',
                                    'symbol'    => 'contractList',
                                ],
                                [
                                    'name'      => 'bill_cate_id',
                                    'form_type' => 'array',
                                    'is_must'   => true,
                                    'message'   => '财务支出科目id',
                                    'symbol'    => 'expenditureCategories',
                                ],
                                [
                                    'name'      => 'type_id',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '支付方式id',
                                    'symbol'    => 'payType',
                                ],
                                [
                                    'name'      => 'num',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '支出金额（元）',
                                    'symbol'    => 'expenditureAmount',
                                ],
                                [
                                    'name'      => 'date',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '付款时间',
                                    'symbol'    => 'payTime',
                                ],
                                [
                                    'name'      => 'attach',
                                    'form_type' => 'array',
                                    'is_must'   => false,
                                    'message'   => '付款凭证id',
                                    'symbol'    => 'paymentVoucher',
                                ],
                                [
                                    'name'      => 'mark',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '备注',
                                    'symbol'    => 'remark',
                                ],
                            ],
                        ],
                        [
                            'name'    => '删除账目',
                            'crud_id' => 0,
                            'method'  => 'DELETE',
                            'url'     => 'open/bill/{id}',
                            'type'    => 1,
                        ],
                        [
                            'name'       => '新增付款提醒',
                            'crud_id'    => 0,
                            'method'     => 'POST',
                            'url'        => 'open/bill/remind',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'types',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '提醒类型',
                                ],
                                [
                                    'name'      => 'eid',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '关联客户id',
                                ],
                                [
                                    'name'      => 'cid',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '关联合同id',
                                ],
                                [
                                    'name'      => 'num',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '金额(元)',
                                ],
                                [
                                    'name'      => 'time',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '提醒日期',
                                ],
                                [
                                    'name'      => 'mark',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '提醒内容',
                                ],
                            ],
                        ],
                    ],
                ], // 账目记录
                [
                    'name'    => '日程待办',
                    'crud_id' => 0,
                    'method'  => '',
                    'url'     => '',
                    'type'    => 0,
                    'child'   => [
                        [
                            'name'       => '新增日程',
                            'crud_id'    => 0,
                            'method'     => 'POST',
                            'url'        => 'open/schedule',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'uid',
                                    'form_type' => 'int',
                                    'is_must'   => false,
                                    'message'   => '关联业务员id',
                                ],
                                [
                                    'name'      => 'title',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '待办标题',
                                ],
                                [
                                    'name'      => 'member',
                                    'form_type' => 'array',
                                    'is_must'   => true,
                                    'message'   => '参与人id',
                                ],
                                [
                                    'name'      => 'start_time',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '开始时间',
                                ],
                                [
                                    'name'      => 'end_time',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '结束时间',
                                ],
                                [
                                    'name'      => 'all_day',
                                    'form_type' => 'int',
                                    'is_must'   => false,
                                    'message'   => '是否全天',
                                ],
                                [
                                    'name'      => 'period',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '重复周期',
                                ],
                                [
                                    'name'      => 'days',
                                    'form_type' => 'array',
                                    'is_must'   => true,
                                    'message'   => '重复周期为周/月时的重复频率',
                                ],
                                [
                                    'name'      => 'rate',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '重复频率',
                                ],
                                [
                                    'name'      => 'remind',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '是否提醒',
                                ],
                                [
                                    'name'      => 'type',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '操作方式',
                                ],
                                [
                                    'name'      => 'fail_time',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '重复截至日期',
                                ],
                            ],
                        ],
                        [
                            'name'       => '修改日程',
                            'crud_id'    => 0,
                            'method'     => 'PUT',
                            'url'        => 'open/schedule/{id}',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'title',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '待办标题',
                                ],
                                [
                                    'name'      => 'member',
                                    'form_type' => 'array',
                                    'is_must'   => true,
                                    'message'   => '参与人id',
                                ],
                                [
                                    'name'      => 'start_time',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '开始时间',
                                ],
                                [
                                    'name'      => 'end_time',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '结束时间',
                                ],
                                [
                                    'name'      => 'start',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '开始时间',
                                ],
                                [
                                    'name'      => 'end',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '结束时间',
                                ],
                                [
                                    'name'      => 'all_day',
                                    'form_type' => 'int',
                                    'is_must'   => false,
                                    'message'   => '是否全天',
                                ],
                                [
                                    'name'      => 'period',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '重复周期',
                                ],
                                [
                                    'name'      => 'days',
                                    'form_type' => 'array',
                                    'is_must'   => true,
                                    'message'   => '重复周期为周/月时的重复频率',
                                ],
                                [
                                    'name'      => 'rate',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '重复频率',
                                ],
                                [
                                    'name'      => 'remind',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '是否提醒',
                                ],
                                [
                                    'name'      => 'type',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '操作方式',
                                ],
                                [
                                    'name'      => 'fail_time',
                                    'form_type' => 'string',
                                    'is_must'   => false,
                                    'message'   => '重复截至日期',
                                ],
                            ],
                        ],
                        [
                            'name'       => '删除日程',
                            'crud_id'    => 0,
                            'method'     => 'DELETE',
                            'url'        => 'open/schedule/{id}',
                            'type'       => 1,
                            'post_prams' => [
                                [
                                    'name'      => 'start',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '开始时间',
                                ],
                                [
                                    'name'      => 'end',
                                    'form_type' => 'string',
                                    'is_must'   => true,
                                    'message'   => '结束时间',
                                ],
                                [
                                    'name'      => 'type',
                                    'form_type' => 'int',
                                    'is_must'   => true,
                                    'message'   => '操作方式',
                                ],
                            ],
                        ],
                    ],
                ], // 日程待办
            ];
            $option      = [];
            $formService = app()->get(FormService::class);
            $ruleService = app()->get(OpenapiRuleService::class);
            $field       = ['id', 'key', 'key_name', 'type', 'input_type', 'required'];
            foreach ($rules as $key => $rule) {
                if (in_array($key, [CustomEnum::CUSTOMER, CustomEnum::CONTRACT, CustomEnum::LIAISON])) {
                    $option = $formService->getCustomDataByTypes($key, $field);
                }
                $pid = $ruleService->value(['name' => $rule['name'], 'crud_id' => 0], 'id');
                if (! $pid) {
                    $parent = $ruleService->create(['name' => $rule['name'], 'crud_id' => 0, 'type' => 0]);
                    $pid    = $parent?->id;
                }

                if (! $pid) {
                    break;
                }

                foreach ($rule['child'] as $child) {
                    $id = $ruleService->value(['name' => $child['name'], 'pid' => $pid], 'id');
                    if (! $id) {
                        $ruleService->create($ruleService->assembleParams(array_merge($child, ['pid' => $pid]), $option));
                    } else {
                        $ruleService->update(['id' => $id], $ruleService->assembleParams($child, $option));
                    }
                }
            }
            return true;
        });
    }
}
