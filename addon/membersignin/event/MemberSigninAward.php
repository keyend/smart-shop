<?php
namespace addon\membersignin\event;
use addon\membersignin\model\Signin as SigninModel;
/**
 * 会员签到奖励规则
 */
class MemberSigninAward
{
    /**
     * 会员操作
     */
    public function handle($param)
    {
        $signin_model  = new SigninModel();
        $config_result = $signin_model->getConfig($param['site_id']);
        $config_result = $config_result['data'];
        if ($config_result['is_use']) {
            $config_result = $config_result['value'];
            return $config_result;
        }
        return [];
    }
}