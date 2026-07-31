<?php

declare(strict_types=1);


namespace App\Http\Service\Approve;

use App\Constants\ApproveEnum;
use App\Constants\CacheEnum;
use App\Constants\CommonEnum;
use App\Http\Dao\Approve\ApproveFormDao;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 审核流程表单.
 */
class ApproveFormService extends BaseService
{
    public function __construct(ApproveFormDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param mixed $data
     * @param mixed $approve_id
     * @return true
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function saveMore($data, $approve_id)
    {
        foreach ($data as $val) {
            $val['approve_id'] = $approve_id;
            if ($this->dao->exists(['approve_id' => $approve_id, 'uniqued' => $val['uniqued']])) {
                $this->dao->update(['approve_id' => $approve_id, 'uniqued' => $val['uniqued']], $val);
            } else {
                $this->dao->create($val);
            }
        }
        return true;
    }

    /**
     * 提交审批表单.
     * @param mixed $origin
     * @return array
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getApplyForm(int $id, int $uid, array $data = [], string $origin = CommonEnum::ORIGIN_WEB)
    {
        $forms = $this->dao->setDefaultSort(['sort' => 'asc'])->column(['approve_id' => $id], 'content');
        $info  = app(ApproveService::class)->get($id, ['types', 'name', 'icon', 'color', 'info', 'examine'])?->toArray();
        // 处理系统相关审批
        if (in_array($info['types'], ApproveEnum::CUSTOMER_TYPES) || in_array($info['types'], [ApproveEnum::PERSONNEL_HOLIDAY, ApproveEnum::PERSONNEL_SIGN])) {
            foreach ($forms as &$form) {
                if (isset($form['children']) && $form['children']) {
                    foreach ($form['children'] as &$child) {
                        if (isset($child['symbol']) && method_exists(ApproveAssistService::class, $child['symbol'])) {
                            $child = app()->get(ApproveAssistService::class)->{$child['symbol']}(uid: $uid, data: $data, child: $child, origin: $origin) ?: $child;
                        }
                    }
                }
            }
        }
        return compact('info', 'forms');
    }

    /**
     * 获取审批配置所有唯一值
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getUniques(int $approve_id): array
    {
        return collect($this->dao->select(['approve_id' => $approve_id], ['uniqued', 'symbol', 'content', 'title'])?->toArray() ?? [])
            ->flatMap(function ($item) {
                $parentTitle = $item['title'] ?: ($item['content']['title'] ?? ($item['content']['props']['titleIpt'] ?? ''));
                $nodes[]     = [
                    'value'  => $item['uniqued'],
                    'label'  => $parentTitle,
                    'symbol' => $item['symbol'] ?? '',
                    'type'   => $item['content']['type'],
                ];
                if ($item['content']['type'] !== 'approvalBill' && isset($item['content']['children'])) {
                    $children = collect($item['content']['children'])
                        ->map(function ($child) {
                            $childTitle = $child['title'] ?: ($child['props']['titleIpt'] ?? '');
                            $arr        = [
                                'value'  => $child['field'],
                                'label'  => $childTitle,
                                'symbol' => $child['symbol'] ?? '',
                                'type'   => $child['type'],
                            ];
                            if ($child['type'] === 'approvalBill') {
                                $arr['children'] = collect($child['children'] ?? [])->map(function ($val) {
                                    $valTitle = $val['title'] ?: ($val['props']['titleIpt'] ?? '');
                                    return [
                                        'value'  => $val['field'],
                                        'label'  => $valTitle,
                                        'symbol' => $val['symbol'] ?? '',
                                        'type'   => $val['type'],
                                    ];
                                })->all();
                            }
                            return $arr;
                        })->all();
                    $nodes = array_merge($nodes, $children);
                }
                return $nodes;
            })->filter(fn ($item) => $item['label'])->values()->all();
    }

    /**
     * 处理带children的表单子项数据组装.
     * @param array $datum 单条数据
     * @param array $children 子项配置
     * @return array 组装后的子项数组
     */
    public function processFormChildren(array $datum, array $children): array
    {
        return collect($children)
            ->filter(function ($childChild) use ($datum) {
                $childSymbol = Str::snake($childChild['symbol']);
                return isset($datum[$childSymbol]);
            })
            ->mapWithKeys(function ($childChild) use ($datum) {
                $childSymbol = Str::snake($childChild['symbol']);
                return [$childChild['value'] => $datum[$childSymbol]];
            })
            ->all();
    }
}
