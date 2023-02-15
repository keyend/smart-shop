<?php
namespace app\shop\controller;
use app\model\system\AddonQuick;
use app\model\system\Promotion as PrmotionModel;
use app\model\system\Site as SiteModel;
/**
 * 营销
 * Class Promotion
 * @package app\shop\controller
 */
class Promotion extends BaseShop
{
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }
    /**
     * 营销中心
     * @return mixed
     */
    public function index()
    {
        $promotion_model = new PrmotionModel();
        $promotions      = $promotion_model->getSitePromotions($this->site_id);
        $this->assign("promotion", $promotions);
        $addon_quick_model = new AddonQuick();
        //店铺促销
        $shop_addon = $addon_quick_model->getAddonQuickByAddonType($promotions,'shop');
        $this->assign('shop_addon',$shop_addon);
        //会员互动
        $member_addon = $addon_quick_model->getAddonQuickByAddonType($promotions,'member');
        $this->assign('member_addon',$member_addon);
        return $this->fetch("promotion/index");
    }
    /**
     * 会员营销
     * @return mixed
     */
    public function member()
    {
        $promotion_model = new PrmotionModel();
        $promotions      = $promotion_model->getSitePromotions($this->site_id);
        $this->assign("promotion", $promotions);
        return $this->fetch("promotion/member");
    }
    /**
     * 营销工具
     * @return mixed
     */
    public function tool()
    {
        $promotion_model = new PrmotionModel();
        $promotions      = $promotion_model->getPromotions();
		//临时排序
		$tempArr = array(); //临时数组
		foreach($promotions['shop'] as $k=>$v){
				$tempArr[$k] = $v['sort'];
		}
		array_multisort($tempArr,SORT_ASC,$promotions['shop']); //第二个参数传排序规则
        $this->assign("promotion", $promotions['shop']);
        $addon_quick_model = new AddonQuick();
        $addon = $addon_quick_model->getAddonQuickByAddonType($promotions['shop'],'tool');
        $this->assign('tool_addon',$addon);
        return $this->fetch("promotion/tool");
    }
}