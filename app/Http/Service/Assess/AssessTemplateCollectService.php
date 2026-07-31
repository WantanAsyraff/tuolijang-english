<?php

declare(strict_types=1);


namespace App\Http\Service\Assess;

use App\Http\Dao\Access\TemplateCollectDao;
use crmeb\basic\BaseModel;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

/**
 * 考核模板收藏service.
 */
class AssessTemplateCollectService extends BaseService
{
    /**
     * AssessTemplateCollectService constructor.
     */
    public function __construct(TemplateCollectDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 模板收藏.
     * @return BaseModel|int|Model
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function collectTemp($id, $uid, $entid)
    {
        if ($this->dao->exists(['temp_id' => $id, 'user_id' => $uid, 'entid' => $entid])) {
            return $this->dao->delete(['temp_id' => $id, 'user_id' => $uid, 'entid' => $entid]);
        }
        return $this->dao->create(['temp_id' => $id, 'user_id' => $uid, 'entid' => $entid]);
    }
}
