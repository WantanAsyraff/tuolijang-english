<?php

declare(strict_types=1);


namespace App\Http\Service\Customer;

use App\Constants\AttachEnum;
use App\Constants\CustomEnum\CustomEnum;
use App\Constants\CustomEnum\ProductEnum;
use App\Constants\System\ViewSearchEnum;
use App\Http\Dao\Customer\ProductDao;
use App\Http\Service\Attach\AttachService;
use App\Http\Service\Config\DictDataService;
use App\Http\Service\Config\FormService;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Webpatser\Uuid\Uuid;

/**
 * 产品service.
 */
class ProductService extends BaseService
{
    use CustomerTrait;

    public function __construct(ProductDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 保存产品
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function saveProduct(array $data, int $uid, int $spec_type, array $attr, array $attrValue, int $id = 0)
    {
        if (! $spec_type) {
            if (count($attrValue) > 1) {
                throw $this->exception('单规格商品属性错误');
            }
        }
        $formService = app(FormService::class);
        $attaches    = [];
        $list        = $formService->getFormDataList(CustomEnum::PRODUCT);
        $formService->fieldValueCheck($data, CustomEnum::PRODUCT, 0, $list);
        foreach ($list as $item) {
            $type      = strtolower($item['type']);
            $inputType = strtolower($item['input_type']);
            foreach ($data as $key => &$datum) {
                if ($item['key'] == $key) {
                    if (in_array($inputType, ['file', 'images'])) {
                        $attaches = array_merge($attaches, (array) $datum);
                    }
                    $datum = $formService->getFormValue($type, $inputType, $datum);
                }
            }
        }
        if (! $id) {
            $data['uid'] = $uid;
        }
        $attaches = array_filter($attaches);
        return $this->transaction(function () use ($spec_type, $attaches, $attr, $attrValue, $data, $id) {
            $data['spec_type'] = $spec_type;
            if ($data['path']) {
                $path        = is_array($data['path']) ? $data['path'] : json_decode($data['path'], true);
                $data['pid'] = end($path);
            }
            if ($id) {
                $this->dao->update($id, $data);
            } else {
                $result = $this->dao->create($data);
                $id     = $result->id;
            }
            if ($attaches) {
                app(AttachService::class)->update(['id' => $attaches], ['relation_id' => $id, 'relation_type' => AttachEnum::RELATION_TYPE[ViewSearchEnum::VIEW_PRODUCT]]);
            }
            $settleParams         = $this->setAttrValue($attrValue, $id);
            $settleParams['attr'] = $this->setAttr($attr, $id);
            $this->save($id, $settleParams);

            return $id;
        });
    }

    /**
     * 编辑表单.
     * @param mixed $id
     * @return array
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getInfo($id)
    {
        $info = $this->dao->get($id, with: ['attr', 'attrValue', 'category'])?->toArray();
        if (! $info) {
            throw $this->exception('数据获取异常');
        }
        foreach ($info['attr'] as $k => $v) {
            $info['attr'][$k] = [
                'value'  => $v['attr_name'],
                'detail' => $this->getAttrValue($v['attr_values']),
            ];
        }
        $attrValue = $info['attr_value'];
        unset($info['attr_value']);
        $arr = [];
        foreach ($attrValue as $item) {
            $sku = explode(',', $item['sku']);
            foreach ($sku as $k => $v) {
                $item['value' . $k] = $v;
            }
            $arr[] = $item;
        }
        $attachService   = app(AttachService::class);
        $dictDataService = app(DictDataService::class);

        $list = app(FormService::class)->getFormDataWithType(CustomEnum::PRODUCT);
        foreach ($list as &$item) {
            foreach ($item['data'] as &$datum) {
                if (array_key_exists($datum['key'], $info)) {
                    $type           = strtolower($datum['type']);
                    $inputType      = strtolower($datum['input_type']);
                    $datum['value'] = $this->handleFieldValue($inputType, $type, $info[$datum['key']], CustomEnum::SCENE_INFO);
                    if ($datum['dict_ident']) {
                        if (is_dimensional_data($datum['value'])) {
                            $datum['value'] = $this->handleDictValue($datum['dict_ident'], $datum['value'], $type);
                        } else {
                            $datum['value'] = $dictDataService->getNamesByValue($datum['dict_ident'], $datum['value']);
                        }
                    }
                    if (in_array($inputType, ['file', 'images'])) {
                        $datum['files'] = empty($datum['value']) ? [] : $attachService->select(['id' => $datum['value']], ['id', 'att_dir', 'att_size', 'real_name'])?->toArray();
                    }
                    if ($datum['key'] == 'path') {
                        $datum['value'] = $datum['value'] ? app(ProductCategoryService::class)->column(['id' => end($datum['value'])], 'name') : [];
                    }
                }
            }
        }

        return [
            'attr'      => $info['attr'],
            'name'      => $info['name'],
            'spec_type' => $info['spec_type'],
            'attrValue' => $arr,
            'list'      => $list,
        ];
    }

    /**
     * 客户编辑表单数据.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getEditInfo(int $id): mixed
    {
        $info = $this->dao->get($id, with: ['attr', 'attrValue', 'category'])?->toArray();
        if (! $info) {
            throw $this->exception('数据获取异常');
        }
        foreach ($info['attr'] as $k => $v) {
            $info['attr'][$k] = [
                'value'  => $v['attr_name'],
                'detail' => $this->getAttrValue($v['attr_values']),
            ];
        }
        $attrValue = $info['attr_value'];
        unset($info['attr_value']);
        $arr = [];
        foreach ($attrValue as $item) {
            $sku = explode(',', $item['sku']);
            foreach ($sku as $k => $v) {
                $item['value' . $k] = $v;
                $item['attr_arr'][] = $v;
            }
            $arr[] = $item;
        }
        $attachField   = $this->getAttachField();
        $attachService = app(AttachService::class);
        $list          = app(FormService::class)->getFormDataWithType(CustomEnum::PRODUCT, platform: $this->getPlatform());
        foreach ($list as &$item) {
            foreach ($item['data'] as &$datum) {
                if (array_key_exists($datum['key'], $info)) {
                    $type           = strtolower($datum['type']);
                    $inputType      = strtolower($datum['input_type']);
                    $datum['value'] = $this->handleFieldValue($inputType, $type, $info[$datum['key']], CustomEnum::SCENE_EDIT);
                    if ($inputType == 'member') {
                        $datum['options'] = $this->handleFieldValue($inputType, $type, $info[$datum['key']]);
                    }
                    if (in_array($inputType, ['file', 'images'])) {
                        $datum['files'] = empty($datum['value']) ? []
                            : $attachService->getListByRelationType(AttachService::RELATION_TYPE_PRODUCT, $datum['value'], $attachField);
                    }
                }
            }
        }
        return [
            'attr'      => $info['attr'],
            'spec_type' => $info['spec_type'],
            'attrValue' => $arr,
            'list'      => $list,
        ];
    }

    /**
     * 产品详情.
     * @return array
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function detail(int $id)
    {
        $data = $this->dao->get($id, with: ['attr', 'attrValue', 'category'])?->toArray();
        foreach ($data['attr'] as $k => $v) {
            $data['attr'][$k] = [
                'value'  => $v['attr_name'],
                'detail' => $v['attr_values'],
            ];
        }
        $attrValue = $data['attr_value'];
        unset($data['attr_value']);
        $arr = [];
        foreach ($attrValue as $item) {
            $sku = explode(',', $item['sku']);
            foreach ($sku as $k => $v) {
                $item['value' . $k] = $v;
            }
            $arr[] = $item;
        }
        $data['attrValue'] = $arr;
        return $data;
    }

    /**
     * 字段过滤.
     * @return string[]
     */
    public function dictFilterField(): array
    {
        return [];
    }

    /**
     * 获取用户设置的搜索列表.
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws BindingResolutionException
     */
    public function searchField()
    {
        $field[]  = ['types', ''];
        $fieldSet = app(FormService::class)->getCustomDataByTypes(CustomEnum::PRODUCT, ['key as field', 'input_type']);
        $fieldSet = array_merge($fieldSet, ProductEnum::PRODUCT_SEARCH_FIELD);
        $field    = [];
        foreach ($fieldSet as $value) {
            $field[] = [$value['field'], ''];
        }
        return $field;
    }

    /**
     * 无需同步字段.
     * @return string[]
     */
    public function getOutOfSyncField(): array
    {
        return [];
    }

    /**
     * 获取产品选择属性树.
     * @return array
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getAttrTree(array $where)
    {
        $attr_like = '';
        if (isset($where['attr_like'])) {
            $attr_like   = $where['attr_like'];
            $where['id'] = array_unique(app(ProductAttrValueService::class)->column(['attr_like' => $attr_like], 'product_id'));
            unset($where['attr_like']);
        }
        [$page, $limit] = $this->getPageValue();
        $data           = $this->dao->setDefaultSort(['sort', 'id'])->select($where, ['id', 'name', 'unit_name'], ['attrValue'], $page, $limit)?->toArray();
        foreach ($data as &$item) {
            $item['attr_value'] = collect($item['attr_value'])->filter(function ($v) use ($attr_like) {
                return str_contains($v['sku'], $attr_like);
            })->values();
        }
        $count = $this->dao->count($where);
        return $this->listData($data, $count);
    }

    private function getAttrValue($attrValue)
    {
        $values = [];
        foreach ($attrValue as $v) {
            $values[] = ['value' => $v];
        }
        return $values;
    }

    /**
     * TODO 单产品sku.
     * @param mixed $data
     * @return array
     * @day 2020-08-05
     */
    private function detailAttrValue($data)
    {
        $sku = [];
        foreach ($data as $value) {
            $_value = [
                'sku'      => $value['sku'],
                'price'    => $value['price'],
                'stock'    => $value['stock'],
                'image'    => $value['image'],
                'weight'   => $value['weight'],
                'volume'   => $value['volume'],
                'sales'    => $value['sales'],
                'unique'   => $value['unique'],
                'bar_code' => $value['bar_code'],
            ];
            $sku[$value['sku']] = $_value;
        }
        return $sku;
    }

    /**
     * 格式产品SKU.
     * @return array
     */
    private function setAttrValue(array $attrValue, int $productId)
    {
        $price = $stock = $ot_price = $cost = $svip_price = 0;
        try {
            foreach ($attrValue as $value) {
                $sku = '';
                if (! empty($value['detail']) && is_array($value['detail'])) {
                    $sku = implode(',', $value['detail']);
                }

                $sprite = ($value['price'] < 0) ? 0 : $value['price'];

                $cost  = ! $cost ? $value['cost'] : (($cost > $value['cost']) ? $cost : $value['cost']);
                $price = ! $price ? $sprite : (($price > $sprite) ? $sprite : $price);

                $unique    = str_replace('-', '', (string) Uuid::generate(4));
                $new_price = $value['price'] ? (($value['price'] < 0) ? 0 : $value['price']) : 0;
                $array     = [
                    'detail'     => json_encode($value['detail'] ?? '', JSON_UNESCAPED_UNICODE),
                    'bar_code'   => $value['bar_code'] ?? '',
                    'image'      => $value['image'] ?? '',
                    'cost'       => $value['cost'] ? (($value['cost'] < 0) ? 0 : $value['cost']) : 0,
                    'price'      => $new_price,
                    'product_id' => $productId,
                    'sku'        => $sku,
                    'unique'     => $unique,
                ];
                $result['attrValue'][] = $array;
            }
            $result['data'] = [
                'price'      => $price,
                'stock'      => $stock,
                'ot_price'   => $ot_price,
                'cost'       => $cost,
                'svip_price' => $svip_price,
            ];
            return $result;
        } catch (\Exception $exception) {
            throw $this->exception('规格错误 ：' . $exception->getMessage() . $exception->getFile() . $exception->getLine());
        }
    }

    /**
     * 保存产品规格.
     * @param mixed $productId
     * @param mixed $settleParams
     * @return true
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function save($productId, $settleParams)
    {
        $productAttrRepository = app(ProductAttrService::class);
        $productAttrRepository->delete(['product_id' => $productId]);
        if (isset($settleParams['attr'])) {
            $productAttrRepository->insert($settleParams['attr']);
        }
        $productAttrValueRepository = app(ProductAttrValueService::class);
        $productAttrValueRepository->delete(['product_id' => $productId]);
        if (isset($settleParams['attrValue'])) {
            $arr = array_chunk($settleParams['attrValue'], 30);
            foreach ($arr as $item) {
                $productAttrValueRepository->insert($item);
            }
        }
        return true;
    }

    /**
     * 格式产品规格.
     * @return array
     */
    private function setAttr(array $data, int $productId)
    {
        $result = [];
        foreach ($data as $value) {
            $result[] = [
                'product_id'  => $productId,
                'attr_name'   => $value['value'] ?? $value['attr_name'],
                'attr_values' => implode('-!-', array_column($value['detail'], 'value')),
            ];
        }
        return $result;
    }

    /**
     * 获取列表搜索条件.
     */
    private function viewSearchWhere(array $where, int $uid = 0): array
    {
        if (! isset($where['view_search'])) {
            unset($where['scope_frame']);
            return $where;
        }
        switch ((int) $where['view_search']) {
            case 1:// 上架
                $where['is_show'] = 1;
                break;
            case 2:// 下架
                $where['is_show'] = 0;
                break;
        }
        unset($where['view_search'], $where['scope_frame']);
        return $where;
    }
}
