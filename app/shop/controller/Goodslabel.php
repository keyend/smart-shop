<?php
namespace app\shop\controller;
use app\model\goods\GoodsLabel as GoodsLabelModel;
class GoodsLabel extends BaseShop
{
    /**
     * 商品分组列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page_index  = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $search_keys = input('search_keys', "");
            $condition   = [];
            $condition[] = ['site_id', '=', $this->site_id];
            if (!empty($search_keys)) {
                $condition[] = ['label_name', 'like', '%' . $search_keys . '%'];
            }
            $goods_attr_model = new GoodsLabelModel();
            $list             = $goods_attr_model->getLabelPageList($condition, $page_index, $page_size);
            return $list;
        } else {
            return $this->fetch('goodslabel/lists');
        }
    }
    /**
     * 商品分组添加
     */
    public function add()
    {
        if (request()->isAjax()) {
            $label_name = input('label_name', '');
            $data       = [
                'site_id'    => $this->site_id,
                'label_name' => $label_name,
                'desc'       => input('desc', 0)
            ];
            $model      = new GoodsLabelModel();
            $res        = $model->addLabel($data);
            return $res;
        }
    }
    /**
     * 商品分组编辑
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $data  = [
                'id'         => input('id', ''),
                'site_id'    => $this->site_id,
                'label_name' => input('label_name', ''),
                'desc'       => input('desc', 0)
            ];
            $model = new GoodsLabelModel();
            $res   = $model->editLabel($data);
            return $res;
        }
    }
    /**
     * 商品分组删除
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $id     = input("id", 0);
            $model  = new GoodsLabelModel();
            $result = $model->deleteLabel([['id', '=', $id], ['site_id', '=', $this->site_id]]);
            return $result;
        }
    }
    /**
     * 修改排序
     */
    public function modifySort()
    {
        if (request()->isAjax()) {
            $sort   = input("sort", 0);
            $id     = input("id", 0);
            $model  = new GoodsLabelModel();
            $result = $model->modifySort($sort, $id, $this->site_id);
            return $result;
        }
    }
}