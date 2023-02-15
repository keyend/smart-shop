<?php
namespace addon\membersignin\model;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 会员签到
 */
class Signin extends BaseModel
{
    /**
     * 会员签到奖励设置
     * @param $data
     * @param $is_use
     * @return array
     */
    public function setConfig($data, $is_use, $site_id)
    {
        if (!empty($data)) {
            $day = 0;
            foreach ($data as $k => $v) {
                if ($v['day'] == 0) {
                    return $this->error('', '连续签到天数不能设置为0');
                }
                
                if ($k != 0) {
                    if ($v['day'] <= $day) {
                        return $this->error('', '连续签到天数不能相同或者小于上一个签到天数');
                    }
                    if (($v['day'] - $day) > 1) {
                        return $this->error('', '连续签到天数不能隔天设置');
                    }
                }
                $day = $v['day'];
            }
        }
        $config = new ConfigModel();
        $res = $config->setConfig($data, '会员签到奖励设置', $is_use, [['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'MEMBER_SIGNIN_REWARD_CONFIG']]);
        return $res;
    }
    /**
     * 会员签到奖励设置
     */
    public function getConfig($site_id)
    {
        $config = new ConfigModel();
        $res = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'MEMBER_SIGNIN_REWARD_CONFIG']]);
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                [
                    "point" => 0,
                    "growth" => 0,
                    'coupon' => 0,
                    "day" => 1
                ]
            ];
        }
        return $res;
    }
}