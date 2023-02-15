<?php
namespace addon\pintuan\api\controller;
use addon\pintuan\model\Pintuan as PintuanModel;
use app\api\controller\BaseApi;
use addon\pintuan\model\Poster;
use app\model\goods\GoodsService;
/**
 * 拼团商品
 */
class Goods extends BaseApi
{
    /**
     * 基础信息
     */
    public function info()
    {
        $sku_id     = isset($this->params['sku_id']) ? $this->params['sku_id'] : 0;
        $pintuan_id = isset($this->params['pintuan_id']) ? $this->params['pintuan_id'] : 0;
        if (empty($sku_id)) {
            return $this->response($this->error('', 'REQUEST_SKU_ID'));
        }
        if (empty($pintuan_id)) {
            return $this->response($this->error('', 'REQUEST_PINTUAN_ID'));
        }
        $goods     = new PintuanModel();
        $condition = [
            ['sku.sku_id', '=', $sku_id],
            ['ppg.pintuan_id', '=', $pintuan_id],
            ['pp.status', '=', 1]
        ];
        $info      = $goods->getPintuanGoodsDetail($condition);
        return $this->response($info);
    }
    /**
     * 拼团商品详情信息
     */
    public function detail()
    {
        $id = isset($this->params['id']) ? $this->params['id'] : 0;
        if (empty($id)) {
            return $this->response($this->error('', 'REQUEST_ID'));
        }
        $pintuan_model           = new PintuanModel();
        $condition               = [
            ['ppg.id', '=', $id],
            ['sku.site_id', '=', $this->site_id],
            ['pp.status', '=', 1]
        ];
        $goods_sku_detail        = $pintuan_model->getPintuanGoodsDetail($condition);
        $goods_sku_detail        = $goods_sku_detail['data'];
        $res['goods_sku_detail'] = $goods_sku_detail;
        if (empty($goods_sku_detail)) return $this->response($this->error($res));
        // 查询当前商品参与的营销活动信息
//		$goods_promotion = event('GoodsPromotion', ['goods_id' => $goods_sku_detail[ 'goods_id' ], 'sku_id' => $goods_sku_detail[ 'sku_id' ]]);
//		$res[ 'goods_sku_detail' ][ 'goods_promotion' ] = $goods_promotion;
        // 商品服务
        $goods_service                            = new GoodsService();
        $goods_service_list                       = $goods_service->getServiceList([['site_id', '=', $this->site_id], ['id', 'in', $res['goods_sku_detail']['goods_service_ids']]], 'service_name,desc');
        $res['goods_sku_detail']['goods_service'] = $goods_service_list['data'];
        return $this->response($this->success($res));
    }
    public function page()
    {
        $site_id      = $this->site_id;
        $page         = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size    = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $goods_id_arr = isset($this->params['goods_id_arr']) ? $this->params['goods_id_arr'] : '';//goods_id数组
        $condition = [
            ['pp.status', '=', 1],// 状态（0正常 1活动进行中  2活动已结束  3失效  4删除）
            ['sku.stock', '>', 0],
            ['sku.goods_state', '=', 1],
            ['sku.is_delete', '=', 0],
            ['sku.site_id', '=', $site_id]
        ];
        if (!empty($goods_id_arr)) {
            $condition[] = ['sku.goods_id', 'in', $goods_id_arr];
        }
        $pintuan_model = new PintuanModel();
        $list          = $pintuan_model->getPintuanGoodsPageList($condition, $page, $page_size);
        return $this->response($list);
    }
    /**
     * 获取商品海报
     */
    public function poster()
    {
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));
        $promotion_type                = 'pintuan';
        $qrcode_param                  = json_decode($this->params['qrcode_param'], true);
        $qrcode_param['source_member'] = $qrcode_param['source_member'] ?? 0;
        $poster                        = new Poster();
        $res                           = $poster->goods($this->params['app_type'], $this->params['page'], $qrcode_param, $promotion_type, $this->site_id);
        return $this->response($res);
    }
}