<?php
namespace app\api\controller;
use app\model\system\Site as SiteModel;
/**
 * 店铺
 * @author Administrator
 *
 */
class Site extends BaseApi
{
    /**
     * 基础信息
     */
    public function info()
    {
        $field                       = 'site_id,site_domain,site_name,logo';
        $website_model               = new SiteModel();
        $info                        = $website_model->getSiteInfo([['site_id', '=', $this->site_id]], $field);
        $info['data']['shop_status'] = 1;

        return $this->response($info);
    }
    /**
     * 是否显示店铺相关功能，用于审核小程序
     */
    public function isShow()
    {
        $res = 1;// 0 隐藏，1 显示
        return $this->response($this->success($res));
    }
}