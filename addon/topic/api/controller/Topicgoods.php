<?php
namespace addon\topic\api\controller;
use addon\topic\model\Poster;
use addon\topic\model\Topic as TopicModel;
use addon\topic\model\TopicGoods as TopicGoodsModel;
use app\api\controller\BaseApi;
use app\model\goods\GoodsService;
/**
 * 专题活动商品
 */
class Topicgoods extends BaseApi
{
    /**
     * 详情信息
     */
    public function detail()
    {
        $id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;
        if (empty($id)) {
            return $this->response($this->error('', 'REQUEST_ID'));
        }
        $topic_goods_model = new TopicGoodsModel();
        $condition = [
            [ 'ptg.id', '=', $id ],
            [ 'pt.status', '=', 2 ]
        ];
        $goods_sku_detail = $topic_goods_model->getTopicGoodsDetail($condition);
        $goods_sku_detail = $goods_sku_detail[ 'data' ];
        $res[ 'goods_sku_detail' ] = $goods_sku_detail;
        if (empty($goods_sku_detail)) return $this->response($this->error($res));
        // 查询当前商品参与的营销活动信息
//		$goods_promotion = event('GoodsPromotion', ['goods_id' => $goods_sku_detail[ 'goods_id' ], 'sku_id' => $goods_sku_detail[ 'sku_id' ]]);
//		$res[ 'goods_sku_detail' ][ 'goods_promotion' ] = $goods_promotion;
        // 商品服务
        $goods_service = new GoodsService();
        $goods_service_list = $goods_service->getServiceList([ [ 'site_id', '=', $this->site_id ], [ 'id', 'in', $res[ 'goods_sku_detail' ][ 'goods_service_ids' ] ] ], 'service_name,desc');
        $res[ 'goods_sku_detail' ][ 'goods_service' ] = $goods_service_list[ 'data' ];
        return $this->response($this->success($res));
    }
    /**
     * 列表信息
     */
    public function page()
    {
        $topic_id = isset($this->params[ 'topic_id' ]) ? $this->params[ 'topic_id' ] : 0;
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
        if (empty($topic_id)) {
            return $this->response($this->error('', 'REQUEST_TOPIC_ID'));
        }
        $condition = [
            [ 'nptg.topic_id', '=', $topic_id ],
            [ 'ngs.goods_state', '=', 1 ],
            [ 'ngs.is_delete', '=', 0 ]
        ];
        $order = 'nptg.id desc';
        $field = 'nptg.id,nptg.topic_id,nptg.start_time,nptg.end_time,nptg.site_id,ngs.sku_id,ngs.sku_name,ngs.price,ngs.discount_price,ngs.sku_image,ngs.goods_name,nptg.topic_price,ngs.is_free_shipping,ngs.introduction,ngs.is_virtual,ngs.sale_num';
        $topic_goods_model = new TopicGoodsModel();
        $topic_model = new TopicModel();
        $info = $topic_model->getTopicInfo([ [ "topic_id", "=", $topic_id ] ], 'bg_color,topic_adv,topic_name');
        $info = $info[ 'data' ];
        $res = $topic_goods_model->getTopicGoodsPageList($condition, $page, $page_size, $order, $field);
        $res[ 'data' ][ 'bg_color' ] = $info[ 'bg_color' ];
        $res[ 'data' ][ 'topic_adv' ] = $info[ 'topic_adv' ];
        $res[ 'data' ][ 'topic_name' ] = $info[ 'topic_name' ];
        return $this->response($res);
    }
    /**
     * 获取商品海报
     */
    public function poster()
    {
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));
        $promotion_type = 'topic';
        $qrcode_param = json_decode($this->params[ 'qrcode_param' ], true);
        $qrcode_param[ 'source_member' ] = $qrcode_param[ 'source_member' ] ?? 0;
        $poster = new Poster();
        $res = $poster->goods($this->params[ 'app_type' ], $this->params[ 'page' ], $qrcode_param, $promotion_type, $this->site_id);
        return $this->response($res);
    }
}