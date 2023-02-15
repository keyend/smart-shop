<?php
namespace app\event;
use addon\membercancel\model\MemberCancel;
/**
 *  会员注销失败通知
 */
class MessageCancelFail
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        if ($param["keywords"] == "USER_CANCEL_FAIL") {
            $model = new MemberCancel();
            return $model->memberCancelFail($param);
        }
    }
}