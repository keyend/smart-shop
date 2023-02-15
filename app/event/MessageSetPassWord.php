<?php
declare (strict_types = 1);
namespace app\event;
use app\model\member\Member;
/**
 * 设置密码
 */
class MessageSetPassWord
{
	public function handle($param)
	{
	    //发送订单消息
        if($param["keywords"] == "SET_PASSWORD"){
            $member_model = new Member();
            $result = $member_model->bindCode($param);
            return $result;
        }
	}
	
}