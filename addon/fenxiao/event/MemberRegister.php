<?php
namespace addon\fenxiao\event;
use addon\fenxiao\model\Fenxiao;

/**
 * 活动展示
 */
class MemberRegister
{
    /**
     * 会员注册
     * @param unknown $order
     * @return multitype:
     */
    public function handle($param)
    {
        if (isset($param['member_id']) && !empty($param['member_id'])) {
            $fenxiao = new Fenxiao();
            $fenxiao->memberRegister($param['member_id'], $param['site_id']);
        }
    }
}