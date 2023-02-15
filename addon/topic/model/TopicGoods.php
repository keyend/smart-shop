<?php
namespace addon\topic\model;
use app\model\BaseModel;
use app\model\goods\Goods;
/**
 * 专题活动
 */
class TopicGoods extends BaseModel
{
    /**
     * 添加专题商品
     * @param unknown $topic_id
     * @param unknown $site_id
     * @param unknown $sku_ids
     * @return multitype:string
     */
    public function addTopicGoods($topic_id, $site_id, $sku_ids)
    {
        $sku_list = model("goods_sku")->getList([
            [ 'sku_id', 'in', $sku_ids ],
            [ 'site_id', '=', $site_id ],
        ], 'goods_id, sku_id, price');
        $topic_info = model("promotion_topic")->getInfo([ [ 'topic_id', '=', $topic_id ] ], 'start_time, end_time');
        $data = [];
        $goods = new Goods();
        foreach ($sku_list as $val) {
            $goods_count = model("promotion_topic_goods")->getCount([ 'topic_id' => $topic_id, 'sku_id' => $val[ 'sku_id' ] ]);
            if (empty($goods_count)) {
                $data[] = [
                    'topic_id' => $topic_id,
                    'site_id' => $site_id,
                    'sku_id' => $val[ 'sku_id' ],
                    'topic_price' => $val[ 'price' ],
                    'start_time' => $topic_info[ 'start_time' ],
                    'end_time' => $topic_info[ 'end_time' ]
                ];
                $goods->modifyPromotionAddon($val[ 'goods_id' ], [ 'topic' => $topic_id ]);
            }
        }
        model("promotion_topic_goods")->addList($data);
        return $this->success();
    }
    /**
     * 修改专题商品
     * @param unknown $topic_id
     * @param unknown $site_id
     * @param unknown $sku_id
     * @param unknown $price
     * @return multitype:string
     */
    public function editTopicGoods($topic_id, $site_id, $sku_id, $price)
    {
        $data = [
            'topic_id' => $topic_id,
            'site_id' => $site_id,
            'sku_id' => $sku_id,
            'topic_price' => $price
        ];
        model("promotion_topic_goods")->update($data, [ [ 'topic_id', '=', $topic_id ], [ 'sku_id', '=', $sku_id ], [ 'site_id', '=', $site_id ] ]);
        return $this->success();
    }
    /**
     * 删除专题商品
     * @param unknown $topic_id
     * @param unknown $site_id
     * @param unknown $sku_id
     * @return multitype:string
     */
    public function deleteTopicGoods($topic_id, $site_id, $sku_id)
    {
        $goods_sku_info = model("goods_sku")->getInfo([ [ 'sku_id', '=', $sku_id ] ], 'goods_id');
        $goods = new Goods();
        $goods->modifyPromotionAddon($goods_sku_info[ 'goods_id' ], [ 'topic' => $topic_id ], true);
        model("promotion_topic_goods")->delete([ [ 'topic_id', '=', $topic_id ], [ 'sku_id', '=', $sku_id ], [ 'site_id', '=', $site_id ] ]);
        return $this->success();
    }
    /**
     * 获取专题商品详情
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getTopicGoodsDetail($condition, $field = 'sku.goods_id,sku.sku_id,sku.sku_name,sku.sku_spec_format,sku.price,sku.promotion_type,sku.stock,sku.click_num,(sku.sale_num + sku.virtual_sale) as sale_num,sku.collect_num,sku.sku_image,sku.sku_images,sku.site_id,sku.goods_content,sku.goods_state,sku.is_virtual,sku.is_free_shipping,sku.goods_spec_format,sku.goods_attr_format,sku.introduction,sku.unit,sku.video_url,sku.evaluate,sku.goods_service_ids,ptg.id,ptg.topic_id,ptg.start_time,ptg.end_time,ptg.topic_price,pt.topic_name,g.goods_image,g.goods_stock,g.goods_name')
    {
        $alias = 'ptg';
        $join = [
            [ 'goods_sku sku', 'ptg.sku_id = sku.sku_id', 'inner' ],
            [ 'goods g', 'g.goods_id = sku.goods_id', 'inner' ],
            [ 'promotion_topic pt', 'pt.topic_id = ptg.topic_id', 'inner' ],
        ];
        $list = model('promotion_topic_goods')->getInfo($condition, $field, $alias, $join);
        return $this->success($list);
    }
    /**
     * 获取专题商品列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return mixed
     */
    public function getTopicGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '')
    {
        if (empty($field)) {
            $field = 'ngs.sku_id, ngs.sku_name, ngs.sku_no, ngs.sku_spec_format, ngs.price, ngs.market_price,
	        ngs.cost_price, ngs.discount_price, ngs.promotion_type, ngs.stock,
	        ngs.weight, ngs.volume, ngs.click_num, ngs.sale_num, ngs.collect_num, ngs.sku_image,
	        ngs.sku_images, ngs.goods_id, ngs.goods_class, ngs.goods_class_name, ngs.goods_attr_class,
	        ngs.goods_attr_name, ngs.goods_name, ngs.site_id, ngs.site_name,
	        ngs.goods_content, ngs.goods_state,ngs.goods_stock_alarm,
	        ngs.is_virtual, ngs.virtual_indate, ngs.is_free_shipping, ngs.shipping_template, ngs.goods_spec_format,
	        ngs.goods_attr_format, ngs.is_delete, ngs.introduction, ngs.keywords, ngs.unit, ngs.sort,npt.topic_name,
	        npt.topic_adv, npt.status, nptg.start_time, nptg.end_time, nptg.topic_price, npt.topic_id';
        }
        $alias = 'nptg';
        $join = [
            [ 'goods_sku ngs', 'nptg.sku_id = ngs.sku_id', 'inner' ],
            [ 'promotion_topic npt', 'nptg.topic_id = npt.topic_id', 'inner' ],
        ];
        $list = model('promotion_topic_goods')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }
}