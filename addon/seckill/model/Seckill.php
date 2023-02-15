<?php
namespace addon\seckill\model;
use app\model\BaseModel;
use app\model\goods\Goods;
use think\facade\Cache;
/**
 * 限时秒杀(时段)
 */
class Seckill extends BaseModel
{
    /**
     * 添加秒杀时段
     * @param unknown $data
     */
    public function addSeckill($data)
    {
        //时间段检测
        $seckill_count = model('promotion_seckill')->getCount([
            [ 'seckill_start_time|seckill_end_time', 'between', [ $data[ 'seckill_start_time' ], $data[ 'seckill_end_time' ] ] ],
            [ 'site_id', '=', $data[ 'site_id' ] ]
        ]);
        if ($seckill_count > 0) {
            return $this->error('', '秒杀时间段设置冲突');
        }
        $seckill_count = model('promotion_seckill')->getCount([
            [ 'seckill_start_time', '<=', $data[ 'seckill_start_time' ] ],
            [ 'seckill_end_time', '>=', $data[ 'seckill_end_time' ] ],
            [ 'site_id', '=', $data[ 'site_id' ] ]
        ]);
        if ($seckill_count > 0) {
            return $this->error('', '秒杀时间段设置冲突');
        }
        //添加数据
        $data[ 'create_time' ] = time();
        $seckill_id = model('promotion_seckill')->add($data);
        Cache::tag("promotion_seckill")->clear();
        return $this->success($seckill_id);
    }
    /**
     * 修改秒杀时段
     * @param unknown $data
     * @return multitype:string
     */
    public function editSeckill($data, $site_id)
    {
        //时间段检测
        $seckill_count = model('promotion_seckill')->getCount([
            [ 'seckill_start_time|seckill_end_time', 'between', [ $data[ 'seckill_start_time' ], $data[ 'seckill_end_time' ] ] ],
            [ 'site_id', '=', $site_id ],
            [ 'seckill_id', '<>', $data[ 'seckill_id' ] ]
        ]);
        if ($seckill_count > 0) {
            return $this->error('', '秒杀时间段设置冲突');
        }
        $seckill_count = model('promotion_seckill')->getCount([
            [ 'seckill_start_time', '<=', $data[ 'seckill_start_time' ] ],
            [ 'seckill_end_time', '>=', $data[ 'seckill_end_time' ] ],
            [ 'site_id', '=', $site_id ],
            [ 'seckill_id', '<>', $data[ 'seckill_id' ] ]
        ]);
        if ($seckill_count > 0) {
            return $this->error('', '秒杀时间段设置冲突');
        }
        //更新数据
        $data[ 'modify_time' ] = time();
        $res = model('promotion_seckill')->update($data, [ [ 'seckill_id', '=', $data[ 'seckill_id' ] ] ]);
        Cache::tag("promotion_seckill")->clear();
        return $this->success($res);
    }
    /**
     * 删除秒杀时段
     * @param unknown $seckill_id
     */
    public function deleteSeckill($seckill_id)
    {
        $res = model('promotion_seckill')->delete([ [ 'seckill_id', '=', $seckill_id ] ]);
        if ($res) {
            $alias = 'psg';
            $join = [
                [ 'goods_sku g', 'g.sku_id = psg.sku_id', 'inner' ],
            ];
            $seckill_goods_list = model('promotion_seckill_goods')->getList([ [ 'seckill_id', '=', $seckill_id ] ], 'g.goods_id', '', $alias, $join);
            $goods = new Goods();
            foreach ($seckill_goods_list as $k => $v) {
                $goods->modifyPromotionAddon($v[ 'goods_id' ], [ 'seckill' => $seckill_id ], true);
            }
            model('promotion_seckill_goods')->delete([ [ 'seckill_id', '=', $seckill_id ] ]);
        }
        Cache::tag("promotion_seckill")->clear();
        return $this->success($res);
    }
    /**
     * 获取秒杀时段信息
     * @param unknown $condition
     * @param string $field
     */
    public function getSeckillInfo($condition, $field = '*')
    {
        $data = json_encode([ $condition, $field ]);
        $cache = Cache::get("promotion_seckill_getSeckillInfo_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $res = model('promotion_seckill')->getInfo($condition, $field);
        Cache::tag("promotion_seckill")->set("promotion_seckill_getSeckillInfo_" . $data, $res);
        return $this->success($res);
    }
    /**
     * 获取秒杀时段列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getSeckillList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $data = json_encode([ $condition, $field, $order, $limit ]);
        $cache = Cache::get("promotion_seckill_getSeckillList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('promotion_seckill')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("promotion_seckill")->set("promotion_seckill_getSeckillList_" . $data, $list);
        return $this->success($list);
    }
    /**
     * 转换秒杀时间
     * @param $info
     * @return mixed
     */
    public function transformSeckillTime($info)
    {
        $info[ 'start_hour' ] = floor($info[ 'seckill_start_time' ] / 3600);
        $info[ 'start_minute' ] = floor(( $info[ 'seckill_start_time' ] % 3600 ) / 60);
        $info[ 'start_second' ] = $info[ 'seckill_start_time' ] % 60;
        $info[ 'end_hour' ] = floor($info[ 'seckill_end_time' ] / 3600);
        $info[ 'end_minute' ] = floor(( $info[ 'seckill_end_time' ] % 3600 ) / 60);
        $info[ 'end_second' ] = $info[ 'seckill_end_time' ] % 60;
        if ($info[ 'start_hour' ] < 10) $info[ 'start_hour' ] = '0' . $info[ 'start_hour' ];
        if ($info[ 'start_minute' ] < 10) $info[ 'start_minute' ] = '0' . $info[ 'start_minute' ];
        if ($info[ 'start_second' ] < 10) $info[ 'start_second' ] = '0' . $info[ 'start_second' ];
        if ($info[ 'end_hour' ] < 10) $info[ 'end_hour' ] = '0' . $info[ 'end_hour' ];
        if ($info[ 'end_minute' ] < 10) $info[ 'end_minute' ] = '0' . $info[ 'end_minute' ];
        if ($info[ 'end_second' ] < 10) $info[ 'end_second' ] = '0' . $info[ 'end_second' ];
        return $info;
    }
    /******************************************************秒杀商品*********************************************************************/
    /**
     * 添加秒杀商品
     * @param unknown $seckill_id
     * @param unknown $site_id
     * @param unknown $sku_ids
     * @return multitype:string
     */
    public function addSeckillGoods($seckill_id, $site_id, $sku_ids)
    {
        $sku_list = model("goods_sku")->getList([
            [ 'sku_id', 'in', $sku_ids ],
            [ 'site_id', '=', $site_id ],
        ], 'sku_id, price,goods_id');
        $data = [];
        $goods = new Goods();
        foreach ($sku_list as $val) {
            $goods_count = model("promotion_seckill_goods")->getCount([ 'seckill_id' => $seckill_id, 'sku_id' => $val[ 'sku_id' ] ]);
            if (empty($goods_count)) {
                $data[] = [
                    'seckill_id' => $seckill_id,
                    'site_id' => $site_id,
                    'sku_id' => $val[ 'sku_id' ],
                    'seckill_price' => $val[ 'price' ]
                ];
            }
            $goods->modifyPromotionAddon($val[ 'goods_id' ], [ 'seckill' => $seckill_id ]);
        }
        model("promotion_seckill_goods")->addList($data);
        return $this->success();
    }
    /**
     * 修改秒杀商品
     * @param unknown $seckill_id
     * @param unknown $site_id
     * @param unknown $sku_id
     * @param unknown $price
     * @return multitype:string
     */
    public function editSeckillGoods($seckill_id, $site_id, $sku_id, $price)
    {
        $data = [
            'seckill_id' => $seckill_id,
            'site_id' => $site_id,
            'sku_id' => $sku_id,
            'seckill_price' => $price
        ];
        model("promotion_seckill_goods")->update($data, [ [ 'seckill_id', '=', $seckill_id ], [ 'sku_id', '=', $sku_id ], [ 'site_id', '=', $site_id ] ]);
        return $this->success();
    }
    /**
     * 修改秒杀商品限购数量
     * @param unknown $seckill_id
     * @param unknown $site_id
     * @param unknown $sku_id
     * @param unknown $price
     * @return multitype:string
     */
    public function editSeckillGoodsNum($seckill_id, $site_id, $sku_id, $num)
    {
        $data = [
            'seckill_id' => $seckill_id,
            'site_id' => $site_id,
            'sku_id' => $sku_id,
            'num' => $num
        ];
        model("promotion_seckill_goods")->update($data, [ [ 'seckill_id', '=', $seckill_id ], [ 'sku_id', '=', $sku_id ], [ 'site_id', '=', $site_id ] ]);
        return $this->success();
    }
    /**
     * 删除秒杀商品
     * @param unknown $seckill_id
     * @param int $site_id
     * @param unknown $sku_id
     * @return multitype:string
     */
    public function deleteSeckillGoods($seckill_id, $site_id, $sku_id)
    {
        model("promotion_seckill_goods")->delete([ [ 'seckill_id', '=', $seckill_id ], [ 'sku_id', '=', $sku_id ], [ 'site_id', '=', $site_id ] ]);
        $goods_sku_info = model("goods_sku")->getInfo([ [ 'sku_id', '=', $sku_id ] ], 'goods_id');
        $goods = new Goods();
        $goods->modifyPromotionAddon($goods_sku_info[ 'goods_id' ], [ 'seckill' => $seckill_id ], true);
        return $this->success();
    }
    /**
     * 获取秒杀商品详情
     * @param array $condition
     * @param string $field
     * @return mixed
     */
    public function getSeckillGoodsDetail($condition = [], $field = 'sku.goods_id,sku.sku_id,sku.sku_name,sku.sku_spec_format,sku.price,sku.promotion_type,sku.stock,sku.click_num,(sku.sale_num + sku.virtual_sale) as sale_num,sku.collect_num,sku.sku_image,sku.sku_images,sku.site_id,sku.goods_content,sku.goods_state,sku.is_virtual,sku.is_free_shipping,sku.goods_spec_format,sku.goods_attr_format,sku.introduction,sku.unit,sku.video_url,sku.evaluate,sku.goods_service_ids,sg.seckill_id,sg.seckill_price,sg.num,ps.seckill_start_time,ps.seckill_end_time,sg.id,g.goods_image,g.goods_stock,g.goods_name')
    {
        $alias = 'sg';
        $join = [
            [ 'goods_sku sku', 'sg.sku_id = sku.sku_id', 'inner' ],
            [ 'goods g', 'g.goods_id = sku.goods_id', 'inner' ],
            [ 'promotion_seckill ps', 'ps.seckill_id = sg.seckill_id', 'inner' ],
        ];
        $list = model('promotion_seckill_goods')->getInfo($condition, $field, $alias, $join);
        return $this->success($list);
    }
    /**
     * 获取秒杀商品列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return mixed
     */
    public function getSeckillGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'npsg.id desc', $field = '')
    {
        if (empty($field)) {
            $field = ' ngs.sku_name, ngs.sku_id, ngs.sku_no, ngs.sku_spec_format, ngs.price, ngs.market_price,
	        ngs.cost_price, ngs.stock, ngs.weight, ngs.volume,
	        ngs.click_num, ngs.sale_num, ngs.collect_num, ngs.sku_image, ngs.sku_images,
	        ngs.goods_class, ngs.goods_id, ngs.goods_attr_class, ngs.goods_attr_name,
	        ngs.goods_name,ngs.site_id,npsg.id,npsg.seckill_id, npsg.seckill_price, npsg.seckill_id,npsg.num,
	        nps.seckill_start_time, nps.seckill_end_time, nps.name';
        }
        $alias = 'npsg';
        $join = [
            [ 'goods_sku ngs', 'npsg.sku_id = ngs.sku_id', 'inner' ],
            [ 'promotion_seckill nps', 'npsg.seckill_id = nps.seckill_id', 'inner' ],
        ];
        $list = model('promotion_seckill_goods')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }
}