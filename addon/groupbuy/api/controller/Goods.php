<?php
namespace addon\groupbuy\api\controller;
use addon\groupbuy\model\Groupbuy as GroupbuyModel;
use app\api\controller\BaseApi;
use addon\groupbuy\model\Poster;
use app\model\goods\GoodsService;
/**
 * 团购商品
 */
class Goods extends BaseApi
{
    /**
     * 基础信息
     */
    public function info()
    {
        $groupbuy_id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;
        $sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
        if (empty($groupbuy_id)) {
            return $this->response($this->error('', 'REQUEST_GROUPBUY_ID'));
        }
        if (empty($sku_id)) {
            return $this->response($this->error('', 'REQUEST_SKU_ID'));
        }
        $groupbuy_model = new GroupbuyModel();
        $condition = [
            [ 'sku.sku_id', '=', $sku_id ],
            [ 'pg.groupbuy_id', '=', $groupbuy_id ],
        ];
        $info = $groupbuy_model->getGroupbuyGoodsDetail($condition);
        // 查询当前商品参与的营销活动信息
//		$goods_promotion = event('GoodsPromotion', ['goods_id' => $info[ 'data' ][ 'goods_id' ], 'sku_id' => $info[ 'data' ][ 'sku_id' ]]);
//		$info[ 'data' ][ 'goods_promotion' ] = $goods_promotion;
        return $this->response($info);
    }
    /**
     * 团购商品详情信息
     */
    public function detail()
    {
        $groupbuy_id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;
        if (empty($groupbuy_id)) {
            return $this->response($this->error('', 'REQUEST_GROUPBUY_ID'));
        }
        $groupbuy_model = new GroupbuyModel();
        $condition = [
            [ 'pg.groupbuy_id', '=', $groupbuy_id ],
            [ 'pg.site_id', '=', $this->site_id ],
        ];
        $goods_sku_detail = $groupbuy_model->getGroupbuyGoodsDetail($condition);
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
    public function page()
    {
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
        $goods_id_arr = isset($this->params[ 'goods_id_arr' ]) ? $this->params[ 'goods_id_arr' ] : '';//goods_id数组
        $condition = [
            [ 'pg.status', '=', 2 ],// 状态（1未开始  2进行中  3已结束）
            [ 'sku.stock', '>', 0 ],
            [ 'sku.goods_state', '=', 1 ],
            [ 'sku.is_delete', '=', 0 ],
            [ 'sku.site_id', '=', $this->site_id ]
        ];
        if (!empty($goods_id_arr)) {
            $condition[] = [ 'sku.goods_id', 'in', $goods_id_arr ];
        }
        $groupbuy_model = new GroupbuyModel();
        $list = $groupbuy_model->getGroupbuyGoodsPageList($condition, $page, $page_size);
        return $this->response($list);
    }
    /**
     * 获取商品海报
     */
    public function poster()
    {
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));
        $promotion_type = 'groupbuy';
        $qrcode_param = json_decode($this->params[ 'qrcode_param' ], true);
        $qrcode_param[ 'source_member' ] = $qrcode_param[ 'source_member' ] ?? 0;
        $poster = new Poster();
        $res = $poster->goods($this->params[ 'app_type' ], $this->params[ 'page' ], $qrcode_param, $promotion_type, $this->site_id);
        return $this->response($res);
    }
}