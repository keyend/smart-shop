<?php
namespace app\api\controller;
use app\model\system\Addon as AddonModel;
/**
 * 插件管理
 * @author Administrator
 *
 */
class Addon extends BaseApi
{
    /**
     * 列表信息
     */
    public function lists()
    {
        $addon = new AddonModel();
        $list  = $addon->getAddonList();
        return $this->response($list);
    }
    public function addonisexit()
    {
        $res                   = [];
        $res['fenxiao']        = addon_is_exit('fenxiao', $this->site_id);                        // 分销
        $res['pintuan']        = addon_is_exit('pintuan', $this->site_id);                        // 拼团
        $res['membersignin']   = addon_is_exit('membersignin', $this->site_id);            // 会员签到
        $res['memberrecharge'] = addon_is_exit('memberrecharge', $this->site_id);        // 会员充值
        $res['memberwithdraw'] = addon_is_exit('memberwithdraw', $this->site_id);        // 会员提现
        $res['pointexchange']  = addon_is_exit('pointexchange', $this->site_id);            // 积分兑换
        $res['manjian']        = addon_is_exit('manjian', $this->site_id);                        //满减
        $res['memberconsume']  = addon_is_exit('memberconsume', $this->site_id);            //会员消费
        $res['memberregister'] = addon_is_exit('memberregister', $this->site_id);        //会员注册
        $res['coupon']         = addon_is_exit('coupon', $this->site_id);                        //优惠券
        $res['bundling']       = addon_is_exit('bundling', $this->site_id);                    //组合套餐
        $res['discount']       = addon_is_exit('discount', $this->site_id);                    //限时折扣
        $res['seckill']        = addon_is_exit('seckill', $this->site_id);                        //秒杀
        $res['topic']          = addon_is_exit('topic', $this->site_id);                            //专题活动
        $res['store']          = addon_is_exit('store', $this->site_id);                            //门店管理
        $res['groupbuy']       = addon_is_exit('groupbuy', $this->site_id);                    //团购
        $res['bargain']        = addon_is_exit('bargain', $this->site_id);                    //砍价
        $res['presale']        = addon_is_exit('presale', $this->site_id);                   // 预售
        $res['notes']        = addon_is_exit('notes', $this->site_id);                   // 运营笔记
        $res['membercancel'] = addon_is_exit('membercancel', $this->site_id);        // 会员注销
        return $this->response($this->success($res));
    }
}