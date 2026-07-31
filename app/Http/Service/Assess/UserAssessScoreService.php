<?php

declare(strict_types=1);


namespace App\Http\Service\Assess;

use App\Http\Dao\Company\CompanyUserAssessScoreDao;
use crmeb\basic\BaseService;

/**
 * 评分记录
 * Class UserAssessScoreService.
 */
class UserAssessScoreService extends BaseService
{
    /**
     * UserAssessScoreService constructor.
     */
    public function __construct(CompanyUserAssessScoreDao $dao)
    {
        $this->dao = $dao;
    }

    public function createOrSave($data)
    {
        if (! $this->dao->get($data, ['id'], [], 'id')) {
            $this->dao->create($data);
        }
    }

    /**
     * 评分记录列表.
     * @return array|mixed
     */
    public function getScoreRecord($id)
    {
        $where = [
            'assessid' => $id,
            'types'    => 0,
        ];
        return parent::getList($where, ['id', 'assessid', 'userid', 'score', 'grade', 'total', 'mark', 'created_at'], 'id', ['card']);
    }

    /**
     * 删除记录列表.
     * @param array $where
     * @return array|mixed
     */
    public function getDeleteList($where)
    {
        return parent::getList(
            $where,
            ['id', 'assessid', 'userid', 'check_uid', 'test_uid', 'score', 'grade', 'total', 'mark', 'info', 'created_at'],
            'id',
            ['card', 'check', 'test']
        );
    }
}
