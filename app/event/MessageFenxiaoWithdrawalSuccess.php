<?php
namespace app\event;
use addon\fenxiao\model\FenxiaoWithdraw;
/**
 *  分销提现成功发送消息
 */
class MessageFenxiaoWithdrawalSuccess
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "FENXIAO_WITHDRAWAL_SUCCESS") {
            //发送订单消息
            $model = new FenxiaoWithdraw();
            return $model->messageFenxiaoWithdrawalSuccess($param);
        }
    }
}