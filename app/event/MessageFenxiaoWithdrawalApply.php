<?php
namespace app\event;
use addon\fenxiao\model\FenxiaoWithdraw;
/**
 *  分销提现申请发送消息
 */
class MessageFenxiaoWithdrawalApply
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "FENXIAO_WITHDRAWAL_APPLY") {
            //发送订单消息
            $model = new FenxiaoWithdraw();
            return $model->messageFenxiaoWithdrawalApply($param);
        }
    }
}