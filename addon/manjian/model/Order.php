<?php
namespace addon\manjian\model;
use addon\coupon\model\Coupon;
use app\model\member\MemberAccount;
use app\model\BaseModel;
use app\model\order\Order as BaseOrder;
use think\Exception;
class Order extends BaseModel
{
    /**
     * 订单完成发放满减送所送积分
     * @param $order_id
     */
    public function orderComplete($order_id)
    {
        $order_info = model('order')->getInfo([['order_id', '=', $order_id], ['order_status', '=', BaseOrder::ORDER_COMPLETE]], 'order_id');
        if (!empty($order_info)) {
            $mansong_record = model('promotion_mansong_record')->getList([['order_id', '=', $order_id], ['status', '=', 0]]);
            if (!empty($mansong_record)) {
                model('promotion_mansong_record')->startTrans();
                foreach ($mansong_record as $item) {
                    try {
                        // 发放积分
                        if (!empty($item['point'])) {
                            $member_account = new Memberaccount();
                            $member_account->addMemberAccount($item['site_id'], $item['member_id'], 'point', $item['point'], 'manjian', $item['manjian_id'], "满减送赠送积分");
                        }
                        // 发放优惠券
                        if (!empty($item['coupon'])) {
                            $coupon      = new Coupon();
                            $receive_res = $coupon->receiveCoupon($item['coupon'], $item['site_id'], $item['member_id'], '', 0, 0);
                        }
                        // 变更发放状态
                        model('promotion_mansong_record')->update(['status' => 1], [['id', '=', $item['id']]]);
                        model('promotion_mansong_record')->commit();
                    } catch (\Exception $e) {
                        model('promotion_mansong_record')->rollback();
                    }
                }
            }
        }
    }
}