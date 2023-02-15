<?php
namespace addon\fenxiao\event;
use addon\fenxiao\model\Fenxiao;

/**
 * 会员注销
 */
class MemberCancel
{

    /**
     * @param unknown $order
     * @return multitype:
     */
    public function handle($param)
    {
        $fenxiao_model = new Fenxiao();
        $res = $fenxiao_model->CronMemberCancel($param['member_id'],$param['site_id']);
        return $res;
    }
}