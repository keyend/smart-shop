<?php
namespace addon\seckill\api\controller;
use app\api\controller\BaseApi;
use addon\seckill\model\Seckill as SeckillModel;
use addon\seckill\model\Poster;
use app\model\goods\GoodsService;
/**
 * 秒杀商品
 */
class Seckillgoods extends BaseApi
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
        $condition = [
            [ 'sg.id', '=', $id ]
        ];
        $seckill_model = new SeckillModel();
        $goods_sku_detail = $seckill_model->getSeckillGoodsDetail($condition);
        $goods_sku_detail = $goods_sku_detail[ 'data' ];
        $res[ 'goods_sku_detail' ] = $goods_sku_detail;
        if (empty($goods_sku_detail)) return $this->response($this->error($res));
        // 查询当前商品参与的营销活动信息
//		$goods_promotion = event('GoodsPromotion', [ 'goods_id' => $goods_sku_detail['goods_id'], 'sku_id' => $goods_sku_detail['sku_id'] ]);
//		$res['goods_sku_detail']['goods_promotion'] = $goods_promotion;
        // 商品服务
        $goods_service = new GoodsService();
        $goods_service_list = $goods_service->getServiceList([ [ 'site_id', '=', $this->site_id ], [ 'id', 'in', $res[ 'goods_sku_detail' ][ 'goods_service_ids' ] ] ], 'service_name,desc');
        $res[ 'goods_sku_detail' ][ 'goods_service' ] = $goods_service_list[ 'data' ];
        return $this->response($this->success($res));
    }
    public function page()
    {
        $seckill_id = isset($this->params[ 'seckill_id' ]) ? $this->params[ 'seckill_id' ] : 0;
        $site_id = $this->site_id;
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
        if (empty($seckill_id)) {
            return $this->response($this->error('', 'REQUEST_SECKILL_ID'));
        }
        $condition = [
            [ 'npsg.seckill_id', '=', $seckill_id ],
            [ 'ngs.stock', '>', 0 ],
            [ 'ngs.goods_state', '=', 1 ],
            [ 'ngs.is_delete', '=', 0 ],
            [ 'ngs.site_id', '=', $site_id ]
        ];
        $seckill_model = new SeckillModel();
        $res = $seckill_model->getSeckillGoodsPageList($condition, $page, $page_size);
        $list = $res[ 'data' ][ 'list' ];
        foreach ($list as $key => $val) {
            if ($val[ 'price' ] != 0) {
                $discount_rate = floor($val[ 'seckill_price' ] / $val[ 'price' ] * 100);
            } else {
                $discount_rate = 100;
            }
            $list[ $key ][ 'discount_rate' ] = $discount_rate;
        }
        $res = [
            'list' => $list
        ];
        return $this->response($this->success($res));
    }
    /**
     * 获取商品海报
     */
    public function poster()
    {
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));
        $promotion_type = 'seckill';
        $qrcode_param = json_decode($this->params[ 'qrcode_param' ], true);
        $qrcode_param[ 'source_member' ] = $qrcode_param[ 'source_member' ] ?? 0;
        $poster = new Poster();
        $res = $poster->goods($this->params[ 'app_type' ], $this->params[ 'page' ], $qrcode_param, $promotion_type, $this->site_id);
        return $this->response($res);
    }
}