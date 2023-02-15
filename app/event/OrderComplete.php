<?php
namespace app\event;
use app\model\member\Member;
use app\model\member\MemberAccount;
use app\model\member\MemberLevel;
use app\model\order\OrderCommon;
/**
 * 订单完成后
 */
class OrderComplete
{
    // 行为扩展的执行入口必须是run
    public function handle($data)
    {
        //订单返还积分
        $order_model       = new OrderCommon();
        $condition         = array(
            ['order_id', '=', $data['order_id']]
        );
        $order_info_result = $order_model->getOrderInfo($condition, 'order_money,order_status,site_id,member_id');
        $order_info        = $order_info_result['data'];
        //如果缺失已完成
        if ($order_info['order_status'] == 10) {
            //会员等级 计算积分返还比率
            $site_id            = $order_info['site_id'];
            $member_id          = $order_info['member_id'];
            $member_model       = new Member();
            $member_info_result = $member_model->getMemberInfo([['member_id', '=', $member_id], ['site_id', '=', $site_id]], 'member_level');
            $member_info        = $member_info_result['data'];

            if ($member_info['member_level'] > 0) {
                $member_level_model       = new MemberLevel();
                $member_level_info_result = $member_level_model->getMemberLevelInfo([['level_id', '=', $member_info['member_level']], ['site_id', '=', $site_id]], "point_feedback,growth_feedback");
                $member_level_info        = $member_level_info_result['data'];
                $member_account_model     = new MemberAccount();

                if ($member_level_info['point_feedback'] > 0) {
                    //计算返还的积分
                    $point                = $order_info['order_money'] * $member_level_info['point_feedback'];
                    $result               = $member_account_model->addMemberAccount($site_id, $member_id, 'point', $point, 'adjust', '会员消费得积分', '会员消费得到积分' . $point);
                    if ($result['code'] < 0) {
                        return $result;
                    }
                }

                /**
                 * 分销会员升级体系
                 * version 1.0
                 * 单次销费满则赠送成长值
                 */
                if ($member_level_info['growth_feedback'] != '') {
                    $growth_feedback = json_decode($member_level_info['growth_feedback'], true);
                    if ($growth_feedback != null) {
                        if ($growth_feedback['type'] == 1) {
                            $growth_feedback['mix'] = (float)$growth_feedback['mix'];
                            $growth_feedback['value'] = (float)$growth_feedback['value'];
                            $growth_feedback['ratio'] = (float)$growth_feedback['ratio'];
                            $value = 0;

                            if ($growth_feedback['value'] > 0) {
                                if ($order_info['order_money'] > $growth_feedback['value']) {
                                    $value = $growth_feedback['value'];
                                }
                            } elseif($growth_feedback['ratio'] > 0) {
                                $growth_feedback['ratio'] = round($growth_feedback['ratio'] / 100, 4);
                                $value = $growth_feedback['ratio'] * $order_info['order_money'];
                            }

                            if ($value >= $growth_feedback['mix']) {
                                $result = $member_account_model->addMemberAccount($site_id, $member_id, 'growth', $value, 'adjust', '会员消费得成长', '会员消费得成长' . $value);
                                if ($result['code'] < 0) {
                                    return $result;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $order_model->success();
    }
}