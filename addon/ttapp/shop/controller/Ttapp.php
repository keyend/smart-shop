<?php
namespace addon\ttapp\shop\controller;
use addon\ttapp\model\Config as ConfigModel;
use app\model\system\Upgrade;
use app\shop\controller\BaseShop;
use addon\ttapp\model\Ttapp as ttappModel;
/**
 * 微信小程序功能设置
 */
class Ttapp extends BaseShop
{
    protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'TTAPP_CSS' => __ROOT__ . '/addon/ttapp/shop/view/public/css',
            'TTAPP_JS'  => __ROOT__ . '/addon/ttapp/shop/view/public/js',
            'TTAPP_IMG' => __ROOT__ . '/addon/ttapp/shop/view/public/img',
            'TTAPP_SVG' => __ROOT__ . '/addon/ttapp/shop/view/public/svg',
        ];
    }
    /**
     * 功能设置
     */
    public function setting()
    {
        $config_model  = new ConfigModel();
        $config_result = $config_model->getConfig($this->site_id);
        $config        = $config_result['data']["value"];
        $is_new_version = 0;
        // 获取站点小程序版本信息
        $version_info          = $config_model->getVersion($this->site_id);
        $version_info          = $version_info['data']['value'];
        $currrent_version_info = config('info');
        if (!isset($version_info['version']) || (isset($version_info['version']) && $version_info['version'] != $currrent_version_info['version_no'])) {
            $is_new_version = 1;
        }
        $this->assign('is_new_version', $is_new_version);
        $ttapp_menu = event('ttappMenu', ['site_id' => $this->site_id]);
        $this->assign('ttapp_menu', $ttapp_menu);
        return $this->fetch('ttapp/setting', [], $this->replace);
    }
    /**
     * 公众号配置
     */
    public function config()
    {
        $ttapp_model = new ConfigModel();
        if (request()->isAjax()) {
            $ttapp_name     = input('ttapp_name', '');
            $appid          = input('appid', '');
            $platform_key   = input('platform_key', '');
            $private_key    = input('private_key', '');
            $public_key     = input('public_key', '');
            $appsecret      = input('appsecret', '');
            $is_use         = input('is_use', 0);
            $qrcode         = input('qrcode', '');
            $data           = array(
                "appid"          => $appid,
                "appsecret"      => $appsecret,
                'platform_key'   => $platform_key,
                'public_key'     => $public_key,
                'private_key'    => $private_key,
                "ttapp_name"     => $ttapp_name,
                'qrcode'         => $qrcode,
                'is_use'         => $is_use
            );
            $res            = $ttapp_model->setConfig($data, $is_use, $this->site_id);
            return $res;
        } else {
            $ttapp_config_result = $ttapp_model->getConfig($this->site_id);
            $config_info         = $ttapp_config_result['data']["value"];
            $this->assign("config_info", $config_info);
            // 获取当前域名
            $url = __ROOT__;
            // 去除链接的http://头部
            $url_top = str_replace("https://", "", $url);
            $url_top = str_replace("http://", "", $url_top);
            // 去除链接的尾部/?s=
            $url_top       = str_replace('/?s=', '', $url_top);
            $call_back_url = addon_url("ttapp://api/auth/pair");
            $this->assign("url", $url_top);
            $this->assign("call_back_url", $call_back_url);
            return $this->fetch('ttapp/config', [], $this->replace);
        }
    }
}