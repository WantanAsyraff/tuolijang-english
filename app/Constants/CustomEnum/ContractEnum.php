<?php

declare(strict_types=1);


namespace App\Constants\CustomEnum;

/**
 * 订单业务
 */
final class ContractEnum extends CustomEnum
{
    /**
     * 移交订单.
     */
    public const OPERATE_SHIFT = 5;

    /**
     * 新增订单.
     */
    public const OPERATE_CREATE = 6;

    /**
     * 我查看订单.
     */
    public const CONTRACT_VIEWER = 5;

    /**
     * 我负责订单.
     */
    public const CONTRACT_CHARGE = 6;

    /**
     * 查看客户订单.
     */
    public const CUSTOMER_VIEWER_CONTRACT = 115;

    /**
     * 负责客户订单.
     */
    public const CUSTOMER_CHARGE_CONTRACT = 125;

    /**
     * 公海客户订单.
     */
    public const CUSTOMER_HEIGHT_SEAS_CONTRACT = 135;

    public const CONTRACT_LIST_FIELD = [
        [
            'field'      => 'contract_no',
            'name'       => '订单编号',
            'input_type' => 'input',
        ],
        [
            'field' => 'bill_no',
            'name'  => '付款单号',
        ],
        [
            'field' => 'salesman',
            'name'  => '负责人',
            'type'  => 'salesman',
        ],
        [
            'field' => 'creator',
            'name'  => '创建人',
        ],
        [
            'field' => 'payment_status',
            'name'  => '付款状态',
        ],
        [
            'field' => 'created_at',
            'name'  => '创建时间',
        ],
        [
            'field' => 'payment_time',
            'name'  => '付款时间',
        ],
        [
            'field' => 'fail_days',
            'name'  => '到期时长',
        ],
        [
            'field' => 'contract_status',
            'name'  => '订单状态',
        ],
        [
            'field' => 'signing_status',
            'name'  => '签约状态',
        ],
        // [
        //     'field' => 'contract_customer',
        //     'name'  => '客户名称',
        //     'type'  => 'customer',
        // ],
    ];

    public const CONTRACT_SEARCH_FIELD = [
        [
            'field'      => 'contract_no',
            'name'       => '订单编号',
            'input_type' => 'input',
        ],
        [
            'field'      => 'creator',
            'name'       => '创建人',
            'input_type' => 'personnel',
        ],
        [
            'field'      => 'salesman',
            'name'       => '负责人',
            'input_type' => 'personnel',
        ],
        [
            'field'      => 'bill_no',
            'name'       => '付款单号',
            'input_type' => 'input',
        ],
        [
            'field'      => 'product_name',
            'name'       => '产品名称',
            'input_type' => 'input',
        ],
        [
            'field'      => 'created_at',
            'name'       => '创建日期',
            'input_type' => 'date',
        ],
        [
            'field'      => 'payment_time',
            'name'       => '付款日期',
            'input_type' => 'date',
        ],
        [
            'field'      => 'contract_status',
            'name'       => '订单状态',
            'input_type' => 'radio',
            'type'       => 'radio',
            'dict_ident' => 'contract_status',
        ],
        [
            'field'      => 'signing_status',
            'name'       => '签约状态',
            'input_type' => 'radio',
            'type'       => 'radio',
            'dict_ident' => 'signing_status',
        ],
        //        [
        //            'field' => 'start_date',
        //            'name'  => '开始日期',
        //            'input_type'  => 'date',
        //        ],
        //        [
        //            'field' => 'end_date',
        //            'name'  => '结束日期',
        //            'input_type'  => 'date',
        //        ],
    ];

    public const CONTRACT_VIEWER_LIST_FIELD = [];

    public const CONTRACT_VIEWER_LIST_DEFAULT_FIELD = [
        'contract_name', 'contract_no', 'contract_price', 'contract_customer', 'start_date', 'end_date', 'payment_status', 'fail_days',
        'contract_status', 'contract_followed', 'salesman', 'creator', 'created_at',
    ];

    public const CONTRACT_CHARGE_LIST_DEFAULT_FIELD = [
        'contract_name', 'contract_no', 'contract_price', 'contract_customer', 'start_date', 'end_date', 'payment_status',
        'contract_status', 'contract_followed', 'creator', 'created_at',
    ];

    public const CONTRACT_VIEWER_SEARCH_FIELD = [
        [
            'field'      => 'scope_frame',
            'name'       => '管理范围',
            'input_type' => 'scope_frame',
        ],
    ];

    public const CONTRACT_VIEWER_SEARCH_DEFAULT_FIELD = [
        'contract_name', 'contract_no', 'contract_category', 'start_date',
    ];

    public const CONTRACT_CHARGE_SEARCH_DEFAULT_FIELD = [
        'contract_name', 'contract_no', 'contract_category', 'contract_customer', 'bill_no', 'created_at',
        'payment_time', 'start_date', 'end_date',
    ];

    public const CONTRACT_CHARGE_LIST_FIELD = [];

    public const CONTRACT_CHARGE_SEARCH_FIELD = [];

    public const CONTRACT_NOT_ALLOW_DELETE_FIELD = [
        'contract_status', 'contract_price', 'contract_followed',
        'contract_customer', 'signing_status', 'oid', 'contract_no',
    ];

    public const CUSTOMER_CONTRACT_VIEWER_LIST_DEFAULT_FIELD = [
        'contract_name', 'contract_no', 'contract_price', 'payment_status', 'contract_status', 'salesman', 'creator',
    ];

    public const CONTRACT_TYPE = [
        self::CONTRACT_VIEWER,
        self::CONTRACT_CHARGE,
        self::CUSTOMER_VIEWER_CONTRACT,
        self::CUSTOMER_CHARGE_CONTRACT,
        self::CUSTOMER_HEIGHT_SEAS_CONTRACT,
        self::CONTRACT_VIEWER,
    ];
}
