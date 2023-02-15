<?php
namespace addon\membersignin\shop\controller;
use addon\membersignin\model\Signin;
use app\shop\controller\BaseShop;
/**
 * 会员签到
 */
class Config extends BaseShop
{
    public function index()
    {
        $config_model = new Signin();
        if (request()->isAjax()) {
            $data   = input("json", "{}");
            $is_use = input("is_use", 0);//是否启用
            $data   = json_decode($data,true);
            $res    = $config_model->setConfig($data, $is_use, $this->site_id);
            $this->addLog("设置会员签到奖励");
            return $res;
        } else {
            $config_result = $config_model->getConfig($this->site_id);
            $this->assign('config', $config_result['data']);
            return $this->fetch('config/index');
        }
    }
}