<?php

declare(strict_types=1);


namespace App\Http\Service\Config;

use App\Http\Dao\Config\AgreementDao;
use crmeb\basic\BaseService;
use crmeb\services\FormService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Cache;

/**
 * 协议
 * Class SystemAgreementService.
 */
class SystemAgreementService extends BaseService
{
    /**
     * SystemAgreementService constructor.
     */
    public function __construct(AgreementDao $dao, FormService $builder)
    {
        $this->dao     = $dao;
        $this->builder = $builder;
    }

    /**
     * 协议数据.
     */
    public function getAgreement(string $ident): array
    {
        return Cache::remember('agreement:' . $ident, 60, function () use ($ident) {
            return toArray($this->dao->get(['ident' => $ident], ['ident', 'title', 'content']));
        });
    }

    /**
     * 保存修改.
     * @throws BindingResolutionException
     */
    public function resourceUpdate(int $id, array $data): int
    {
        return $this->dao->update($id, $data);
    }

    /**
     * 获取详情.
     */
    public function getInfo(int $id): array
    {
        return toArray($this->dao->get($id));
    }
}
