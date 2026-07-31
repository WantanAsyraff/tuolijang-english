<?php

declare(strict_types=1);


namespace App\Http\Model\Client;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;
use Illuminate\Database\Query\Builder;

/**
 * 客户订单发票类目
 * Class ClientInvoiceCategory.
 */
class ClientInvoiceCategory extends BaseModel
{
    use TimeDataTrait;

    protected $table = 'client_invoice_category';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'entid'      => 'integer',
        'sort'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * id作用域
     * @param Builder $query
     * @param mixed $value
     */
    public function scopeNotid($query, $value)
    {
        if ($value) {
            $query->where('id', '<>', $value);
        }
    }

    /**
     * name作用域
     * @param Builder $query
     * @param mixed $value
     * @return mixed
     */
    public function scopeNameLike($query, $value)
    {
        if ($value) {
            return $query->where('name', 'like', '%' . $value . '%');
        }
    }
}
