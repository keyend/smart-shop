<?php
namespace addon\memberregister\shop\controller;
use addon\memberregister\model\Register;
use app\shop\controller\BaseShop;
/**
 * 会员注册
 */
class Config extends BaseShop
{
    public function index()
    {
        $config_model = new Register();
        if (request()->isAjax()) {
            $data   = [
                'point'   => input('point', 0),//注册送积分
                'balance' => input('balance', 0),//注册送余额
                'growth'  => input('growth', ''),//注册赠送成长值
                'coupon'  => input('coupon', ''),//注册送优惠券 (先不用做)
            ];
            $is_use = input("is_use", 0);//是否启用
            $res    = $config_model->setConfig($data, $is_use, $this->site_id);
            $this->addLog("设置会员注册奖励");
            return $res;
        } else {
            //注册后奖励
            $config_result = $config_model->getConfig($this->site_id);
            $this->assign('config', $config_result['data']);
            //获取优惠券（后做）
            return $this->fetch('config/index');
        }
    }
}