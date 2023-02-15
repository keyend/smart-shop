<?php
namespace app\event;
use addon\membercancel\model\MemberCancel;
/**
 *  会员注销申请通知
 */
class MessageCancelApply
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        if ($param["keywords"] == "USER_CANCEL_APPLY") {
            $model = new MemberCancel();
            return $model->memberCancelApply($param);
        }
    }
}