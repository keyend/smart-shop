<?php
namespace app\event;
use addon\membercancel\model\MemberCancel;
/**
 *  会员注销成功通知
 */
class MessageCancelSuccess
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "USER_CANCEL_SUCCESS") {
            //发送订单消息
            $model = new MemberCancel();
            return $model->memberCancelSuccess($param);
        }
    }
}