<?php
namespace addon\bargain\api\controller;
use addon\bargain\model\Poster;
use app\api\controller\BaseApi;
use addon\bargain\model\Bargain as BargainModel;
use app\model\goods\GoodsService;
/**
 * 砍价商品
 */
class Goods extends BaseApi
{
    /**
     * 商品详情
     */
    public function detail()
    {
        $id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;
        if (empty($id)) {
            return $this->response($this->error('', 'REQUEST_ID'));
        }
        $bargain = new BargainModel();
        $condition = [
            [ 'pbg.id', '=', $id ],
            [ 'pbg.site_id', '=', $this->site_id ],
            [ 'pbg.status', '=', 1 ]
        ];
        $goods_sku_detail = $bargain->getBargainGoodsDetail($condition);
        $goods_sku_detail = $goods_sku_detail[ 'data' ];
        $res[ 'goods_sku_detail' ] = $goods_sku_detail;
        if (empty($goods_sku_detail)) return $this->response($this->error($res));
        $goods_service = new GoodsService();
        $goods_service_list = $goods_service->getServiceList([ [ 'site_id', '=', $this->site_id ], [ 'id', 'in', $res[ 'goods_sku_detail' ][ 'goods_service_ids' ] ] ], 'service_name,desc');
        $res[ 'goods_sku_detail' ][ 'goods_service' ] = $goods_service_list[ 'data' ];
        $token = $this->checkToken();
        if ($token[ 'code' ] == 0) {
            $launch_info = $bargain->getBargainLaunchDetail([
                [ 'bargain_id', '=', $goods_sku_detail[ 'bargain_id' ] ],
                [ 'sku_id', '=', $goods_sku_detail[ 'sku_id' ] ],
                [ 'member_id', '=', $this->member_id ],
                [ 'status', '=', 0 ]
            ], 'launch_id');
            if (!empty($launch_info[ 'data' ])) $res[ 'goods_sku_detail' ][ 'launch_info' ] = $launch_info[ 'data' ];
        }
        return $this->response($this->success($res));
    }
    /**
     * 商品海报
     * @return false|string
     */
    public function poster()
    {
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));
        $promotion_type = 'bargain';
        $qrcode_param = json_decode($this->params[ 'qrcode_param' ], true);
        $qrcode_param[ 'source_member' ] = $qrcode_param[ 'source_member' ] ?? 0;
        $poster = new Poster();
        $res = $poster->goods($this->params[ 'app_type' ], $this->params[ 'page' ], $qrcode_param, $promotion_type, $this->site_id);
        return $this->response($res);
    }
}