<?php
namespace app\event;
use app\model\member\Withdraw;
/**
 *  会员提现申请发送消息
 */
class MessageUserWithdrawalApply
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "USER_WITHDRAWAL_APPLY") {
            //发送订单消息
            $model = new Withdraw();
            return $model->messageUserWithdrawalApply($param);
        }
    }
}