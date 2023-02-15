<?php
namespace app\event;
use app\model\member\Withdraw;
/**
 * 会员提现成功发送消息
 */
class messageUserWithdrawalSuccess
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "USER_WITHDRAWAL_SUCCESS") {
            //发送订单消息
            $model = new Withdraw();
            return $model->messageUserWithdrawalSuccess($param);
        }
    }
}