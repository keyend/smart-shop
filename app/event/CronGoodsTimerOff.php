<?php
namespace app\event;
use app\model\goods\Goods;
/**
 * 定时下架商品
 * @author Administrator
 *
 */
class CronGoodsTimerOff
{
    public function handle($param)
    {
        $goods_model = new Goods();
        $condition = [
            [ 'goods_id', '=', $param['relate_id'] ]
        ];
        $res = $goods_model->cronModifyGoodsState($condition,0);
        return $res;
    }
}