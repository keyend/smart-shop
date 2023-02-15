<?php
namespace addon\fenxiao\model;
use app\model\BaseModel;
/**
 * 分销
 */
class FenxiaoGoodsSku extends BaseModel
{
    /**
     * 添加分销商品
     * @param $data
     * @return array
     */
    public function addSku($data)
    {
        $res = model('fenxiao_goods_sku')->add($data);
        return $this->success($res);
    }
    /**
     * 编辑分销商品
     * @param $data
     * @param array $condition
     * @return array
     */
    public function editSku($data, $condition = [])
    {
        $data['update_time'] = time();
        $res                 = model('fenxiao_goods_sku')->update($data, $condition);
        return $this->success($res);
    }
    /**
     * 删除分销商品
     * @param array $condition
     * @return array
     */
    public function deleteSku($condition = [])
    {
        $res = model('fenxiao_goods_sku')->delete($condition);
        return $this->success($res);
    }
    /**
     * 获取分销商品详情
     * @param array $condition
     * @param string $field
     */
    public function getFenxiaoGoodsSkuDetail($condition = [], $field = 'fgs.goods_sku_id,fgs.goods_id,fgs.sku_id,fgs.level_id,fgs.one_rate,fgs.one_money,fgs.two_rate,fgs.two_money,fgs.three_rate,fgs.three_money,gs.discount_price,gs.fenxiao_price')
    {
        $alias = 'fgs';
        $join  = [
            ['goods_sku gs', 'fgs.sku_id = gs.sku_id', 'inner'],
            ['goods g','g.goods_id = gs.goods_id','inner']
        ];
        $list  = model('fenxiao_goods_sku')->getInfo($condition, $field, $alias, $join);
        return $this->success($list);
    }
    /**
     * 获取分销sku列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getSkuList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('fenxiao_goods_sku')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }
    /**
     * 获取分销商品分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getFenxiaoGoodsSkuPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'g.create_time desc', $field = 'g.goods_id,g.goods_name,g.goods_image,g.sku_id,gs.sku_name,gs.discount_price,gs.price,gs.promotion_type,gs.is_free_shipping,gs.stock,gs.sale_num,gs.sku_image,gs.site_id,gs.fenxiao_price,g.fenxiao_type,(g.sale_num + g.virtual_sale) as sale_sort')
    {
        $alias = 'g';
        $join  = [
            ['goods_sku gs', 'g.sku_id = gs.sku_id', 'inner'],
        ];
        $list = model('goods')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }
    /**
     * 批量添加分销商品
     * @param $data
     */
    public function addSkuList($data)
    {
        $re = model('fenxiao_goods_sku')->addList($data);
        return $this->success($re);
    }
    /**
     * 获取分销商品信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getFenxiaoGoodsSkuInfo($condition = [], $field = "*"){
        $res = model('fenxiao_goods_sku')->getInfo($condition, $field);
        return $this->success($res);
    }
    /**
     * 获取商品一级分销佣金
     * @param $sku_id
     * @param $member_id
     */
    public function getSkuFenxiaoCommission($sku_id, $level_id){
        $commission = 0.00;
        $alias = 'fgs';
        $condition = [ [ 'gs.sku_id', '=', $sku_id ], [ 'g.is_fenxiao', '=', 1 ] ];
        $join  = [
            ['goods g','g.goods_id = gs.goods_id','inner']
        ];
        $sku_info = model('goods_sku')->getInfo($condition, 'g.fenxiao_type,gs.fenxiao_price,gs.discount_price', 'gs', $join);
        if (!empty($sku_info)) {
            $discount_price = $sku_info[ 'fenxiao_price' ] > 0 ? $sku_info[ 'fenxiao_price' ] : $sku_info[ 'discount_price' ];
            // 默认规则
            if ($sku_info['fenxiao_type'] == 1) {
                $fenxiao_level = new FenxiaoLevel();
                $level_info = $fenxiao_level->getLevelInfo([ ['level_id', '=', $level_id ] ], 'one_rate');
                $level_info = $level_info['data'];
                if (!empty($level_info)) {
                    $commission = number_format($discount_price * $level_info[ 'one_rate' ] / 100, 2, '.', '');
                }
            }  else {
                $fenxiao_sku_info = $this->getFenxiaoGoodsSkuInfo([ ['level_id', '=', $level_id ], ['sku_id', '=', $sku_id] ]);
                $fenxiao_sku_info = $fenxiao_sku_info['data'];
                if (!empty($fenxiao_sku_info)) {
                    $commission = $fenxiao_sku_info['one_money'];
                    if ($fenxiao_sku_info['one_rate'] > 0) {
                        $commission = number_format($discount_price * $fenxiao_sku_info[ 'one_rate' ] / 100, 2, '.', '');
                    }
                }
            }
        }
        return $this->success($commission);
    }
}