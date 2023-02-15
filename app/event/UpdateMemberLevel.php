<?php
namespace app\event;
use addon\coupon\model\Coupon;
use app\model\member\MemberAccount;
use app\model\member\MemberLevel;
use app\job\EntityMQService;
/**
 * 会员等级变化（执行会员成长值变化）
 */
class UpdateMemberLevel
{
    // 行为扩展的执行入口必须是run
    public function handle($data)
    {
        $member_account_model = new MemberAccount();
        model('member_account')->startTrans();
        try {
            if ($data['account_type'] == 'growth') {
                //成长值变化等级检测变化
                $growth_info = model("member")->getInfo([['member_id', '=', $data['member_id']]], 'growth, member_level');
                //查询会员等级
                $member_level = new MemberLevel();
                $level_list   = $member_level->getMemberLevelList([['growth', '<=', $growth_info['growth']]], 'level_id, level_name, sort, growth, send_point, send_balance, send_coupon', 'growth desc');
                $level_detail = [];
                if (!empty($level_list['data'])) {
                    //检测升级
                    if ($growth_info['member_level'] == 0) {
                        //将用户设置为最大等级
                        $data_level = [
                            'member_level'      => $level_list['data'][0]['level_id'],
                            'member_level_name' => $level_list['data'][0]['level_name']
                        ];
                        model("member")->update($data_level, [['member_id', '=', $data['member_id']]]);
                        $level_detail = $level_list['data'][0];
                    } else {
                        $level_info = $member_level->getMemberLevelInfo([['level_id', '=', $growth_info['member_level']]]);
                        if (empty($level_info['data'])) {
                            //将用户设置为最大等级
                            $data_level = [
                                'member_level'      => $level_list['data'][0]['level_id'],
                                'member_level_name' => $level_list['data'][0]['level_name']
                            ];
                            model("member")->update($data_level, [['member_id', '=', $data['member_id']]]);
                            $level_detail = $level_list['data'][0];
                        } else {
                            if ($level_info['data']['growth'] < $level_list['data'][0]['growth']) {
                                //将用户设置为最大等级
                                $data_level = [
                                    'member_level'      => $level_list['data'][0]['level_id'],
                                    'member_level_name' => $level_list['data'][0]['level_name']
                                ];
                                model("member")->update($data_level, [['member_id', '=', $data['member_id']]]);
                                $level_detail = $level_list['data'][0];
                            }
                        }
                    }
                }
                //  如果存在已升级等级   发放升级奖励
                if (!empty($level_detail)) {
                    //赠送红包
                    if ($level_detail[ 'send_balance' ] > 0) {
                        $balance = $level_detail['send_balance'];
                        $member_account_model->addMemberAccount($data['site_id'], $data['member_id'], 'balance', $balance, 'upgrade', '会员升级得红包' . $balance, '会员升级得红包' . $balance);
                    }
                    //赠送积分
                    if ($level_detail['send_point'] > 0) {
                        $send_point = $level_detail['send_point'];
                        $member_account_model->addMemberAccount($data['site_id'], $data['member_id'], 'point', $send_point, 'upgrade', '会员升级得积分' . $send_point, '会员升级得积分' . $send_point);
                    }
                    //给用户发放优惠券
                    $coupon_model = new Coupon();
                    $coupon_array = empty($level_detail['send_coupon']) ? [] : explode(',', $level_detail['send_coupon']);
                    if (!empty($coupon_array)) {
                        foreach ($coupon_array as $k => $v) {
                            $coupon_model->receiveCoupon($v, $data['site_id'], $data['member_id'], 3);
                        }
                    }
                }
            }
            model('member_account')->commit();
            // 升级后事件
            if (!empty($level_detail)) {
                EntityMQService::push(__CLASS__, ['syncUpgrade', [
                    'member_id' => $data['member_id'],
                    'level' => $level_detail
                ]]);
            }
            return $member_account_model->success();
        } catch (\Exception $e) {
            model('member_account')->rollback();
            return $member_account_model->error('', $e->getMessage());
        }
    }

    /**
     * 升级后期事件
     *
     * @param array $data
     * @return void
     */
    public function syncUpgrade($data)
    {
        if (!empty($data)) {
            event('UpdateMemberLevel', $data);
        }
    }
}