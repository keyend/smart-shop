<?php
namespace addon\topic\shop\controller;
use app\shop\controller\BaseShop;
use addon\topic\model\Topic as TopicModel;
use addon\topic\model\TopicGoods as TopicGoodsModel;
/**
 * 专题活动
 * @author Administrator
 *
 */
class Topic extends BaseShop
{
    /**
     * 专题活动列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page       = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $topic_name = input('topic_name', '');
            $condition[] = ['site_id', '=', $this->site_id];
            if ($topic_name) {
                $condition[] = ['topic_name', 'like', '%' . $topic_name . '%'];
            }
            $status = input('status', '');
            if ($status !== '') {
                $condition[] = ['status', '=', $status];
            }
            $order = 'modify_time desc,create_time desc';
            $field = '*';
            $topic_model = new TopicModel();
            $res         = $topic_model->getTopicPageList($condition, $page, $page_size, $order, $field);
            return $res;
        } else {
            $this->forthMenu();
            return $this->fetch("topic/lists");
        }
    }
    /**
     * 添加专题活动
     */
    public function add()
    {
        if (request()->isAjax()) {
            $topic_name  = input("topic_name", '');
            $start_time  = input("start_time", 0);
            $end_time    = input("end_time", 0);
            $remark      = input("remark", '');
            $topic_adv   = input("topic_adv", '');
            $bg_color    = input("bg_color", '#ffffff');
            $topic_model = new TopicModel();
            $data        = array(
                'site_id'    => $this->site_id,
                "topic_name" => $topic_name,
                "start_time" => $start_time,
                "end_time"   => $end_time,
                "remark"     => $remark,
                "topic_adv"  => $topic_adv,
                'bg_color'   => $bg_color
            );
            $res         = $topic_model->addTopic($data);
            return $res;
        } else {
            return $this->fetch("topic/add");
        }
    }
    /**
     * 编辑专题活动
     */
    public function edit()
    {
        $topic_id    = input("topic_id", '');
        $topic_model = new TopicModel();
        if (request()->isAjax()) {
            $topic_name = input("topic_name", '');
            $start_time = input("start_time", 0);
            $end_time   = input("end_time", 0);
            $remark     = input("remark", '');
            $topic_adv  = input("topic_adv", '');
            $bg_color   = input("bg_color", '#ffffff');
            $data = array(
                "topic_name" => $topic_name,
                "start_time" => $start_time,
                "end_time"   => $end_time,
                "remark"     => $remark,
                "topic_adv"  => $topic_adv,
                "topic_id"   => $topic_id,
                'bg_color'   => $bg_color
            );
            $res  = $topic_model->editTopic($data, $this->site_id);
            return $res;
        } else {
            $condition         = array(
                ["topic_id", "=", $topic_id]
            );
            $topic_info_result = $topic_model->getTopicInfo($condition);
            $this->assign("info", $topic_info_result["data"]);
            return $this->fetch("topic/edit");
        }
    }
    /**
     * 删除专题活动
     */
    public function delete()
    {
        $topic_id    = input("topic_id", '');
        $topic_model = new TopicModel();
        $res         = $topic_model->deleteTopic($topic_id, $this->site_id);
        return $res;
    }
    /**
     * 专题活动商品列表
     */
    public function goods()
    {
        $topic_id = input("topic_id", 0);
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $sku_name  = input('sku_name', '');
            $condition         = [];
            $condition[]       = ['nptg.topic_id', '=', $topic_id];
            $condition[]       = ['nptg.site_id', '=', $this->site_id];
            $condition[]       = ['ngs.sku_name', 'like', '%' . $sku_name . '%'];
            $order             = '';
            $field             = '*';
            $topic_goods_model = new TopicGoodsModel();
            $res = $topic_goods_model->getTopicGoodsPageList($condition, $page, $page_size, $order, $field);
            return $res;
        } else {
            $topic_model = new TopicModel();
            $topic_info = $topic_model->getTopicInfo(['topic_id' => $topic_id], '*');
            $this->assign("topic_id", $topic_id);
            $this->assign("topic_info", $topic_info['data']);
            return $this->fetch("topic/goods");
        }
    }
    /**
     * 添加专题活动商品
     */
    public function addTopicGoods()
    {
        if (request()->isAjax()) {
            $topic_id          = input("topic_id", 0);
            $sku_ids           = input("sku_ids", '');
            $topic_goods_model = new TopicGoodsModel();
            $res               = $topic_goods_model->addTopicGoods($topic_id, $this->site_id, $sku_ids);
            return $res;
        }
    }
    /**
     * 编辑专题活动商品
     */
    public function editTopicGoods()
    {
        if (request()->isAjax()) {
            $topic_id          = input("topic_id", 0);
            $sku_id            = input("sku_id", 0);
            $price             = input("price", 0);
            $topic_goods_model = new TopicGoodsModel();
            $res               = $topic_goods_model->editTopicGoods($topic_id, $this->site_id, $sku_id, $price);
            return $res;
        }
    }
    /**
     * 删除专题活动商品
     */
    public function deleteTopicGoods()
    {
        if (request()->isAjax()) {
            $topic_id          = input("topic_id", 0);
            $sku_id            = input("sku_id", 0);
            $topic_goods_model = new TopicGoodsModel();
            $res               = $topic_goods_model->deleteTopicGoods($topic_id, $this->site_id, $sku_id);
            return $res;
        }
    }
    /**
     * 专题活动商品列表
     */
    public function goodslist()
    {
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $sku_name  = input('sku_name', '');
            $condition         = [];
            $condition[]       = ['nptg.site_id', '=', $this->site_id];
            if($sku_name){
                $condition[]       = ['ngs.sku_name', 'like', '%' . $sku_name . '%'];
            }
            $order             = '';
            $field             = '*';
            $topic_goods_model = new TopicGoodsModel();
            $res = $topic_goods_model->getTopicGoodsPageList($condition, $page, $page_size, $order, $field);
            return $res;
        } else {
            $this->forthMenu();
            return $this->fetch("topic/goodslist");
        }
    }
}