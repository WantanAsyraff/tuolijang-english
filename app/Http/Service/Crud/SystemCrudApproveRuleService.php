<?php

declare(strict_types=1);


namespace App\Http\Service\Crud;

use App\Http\Dao\Crud\SystemCrudApproveRuleDao;
use crmeb\basic\BaseService;

class SystemCrudApproveRuleService extends BaseService
{
    public function __construct(SystemCrudApproveRuleDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 处理规则配置.
     */
    public function checkRuleConfig($data, $userId, $type = 'ruleConfig'): array
    {
        return [
            'abnormal' => $data[$type]['abnormal'] ?: 0,
            'auto'     => $data[$type]['auto'] !== '' ? $data[$type]['auto'] : 2,
            'edit'     => $data[$type]['edit'] ?? '',
            'recall'   => $data[$type]['recall'],
            'refuse'   => $data[$type]['refuse'] ?? 0,
            'user_id'  => $userId,
            'is_transfer'  => $data[$type]['is_transfer'] ?? 1,
            'is_sign'  => $data[$type]['is_sign'] ?? 0,
        ];
    }
}
