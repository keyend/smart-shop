<?php
namespace app\event;
use app\model\goods\VirtualGoods;
/**
 * 虚拟商品核销
 */
class VirtualGoodsVerify
{
    public function handle($data)
    {
        if ($data['verify_type'] == 'virtualgoods') {
            $virtual_goods_model = new VirtualGoods();
            $res                 = $virtual_goods_model->verify($data["verify_code"]);
            return $res;
        }
        return '';
    }
}