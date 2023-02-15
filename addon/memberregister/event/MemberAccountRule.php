<?php
namespace addon\memberregister\event;
use addon\memberregister\model\Register;
/**
 * 会员账户变化规则
 */
class MemberAccountRule
{
    public function handle($data)
    {
        $config = new Register();
        $info   = $config->getConfig($data['site_id']);
        $return = '';
        if ($data['account'] == 'point') {
            if ($info['data']['is_use'] == 1) {
                $return .= "会员注册，赠送" . $info['data']['value']['point'] . "积分";
            }
        }
        if ($data['account'] == 'growth') {
            if ($info['data']['is_use'] == 1) {
                $return .= "会员注册，赠送" . $info['data']['value']['growth'] . "成长值";
            }
        }
        if ($data['account'] == 'balance') {
            if ($info['data']['is_use'] == 1) {
                $return .= "会员注册，赠送" . $info['data']['value']['balance'] . "元余额红包";
            }
        }
        return isset($return) ? $return : '';
    }
}