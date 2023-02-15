<?php
namespace addon\membersignin\event;
use app\model\member\Member as MemberModel;
use app\model\member\MemberAccount as MemberAccountModel;
use app\model\member\MemberSignin as MemberSigninModel;
/**
 * 会员签到奖励
 */
class MemberSignin
{
    /**
     * @param $param
     * @return string|\multitype
     */
    public function handle($param)
    {
        $member_model         = new MemberModel();
        $member_signin_model  = new MemberSigninModel();
        $member_account_model = new MemberAccountModel();
        // 查询当前用户连签天数
        $member_info = $member_model->getMemberInfo([['member_id', '=', $param['member_id']]], 'sign_days_series,site_id');
        $member_info = $member_info['data'];
        $award = $member_signin_model->getAward($member_info['site_id']);
        $award = $award['data'];
        $res = [];
        if (!empty($award)) {
            foreach ($award as $k => $v) {
                if ($member_info['sign_days_series'] == $v['day']) {
                    $res    = $v;
                    break;
                }
            }
            if(empty($res)){
                $res = array_pop($award);
            }
            foreach ($res as $curr_k => $curr_v) {
                if ($curr_k != 'day') {
                    $adjust_num   = $curr_v;
                    $account_type = $curr_k;
                    $remark       = '签到送' . $adjust_num . $this->accountType($curr_k);
                    $member_account_model->addMemberAccount($param['site_id'], $param['member_id'], $account_type, $adjust_num, 'signin', 0, $remark);
                }
            }
        }
        return $res;
    }
    private function accountType($key)
    {
        $type = [
            'point'  => '积分',
            'growth' => '成长值',
            'coupon' => '优惠券'
        ];
        return $type[$key];
    }
}