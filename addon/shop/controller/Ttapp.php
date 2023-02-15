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
            $ttapp_original = input('ttapp_original', '');
            $appid          = input('appid', '');
            $appsecret      = input('appsecret', '');
            $token          = input('token', 'TOKEN');
            $encodingaeskey = input('encodingaeskey', '');
            $is_use         = input('is_use', 0);
            $qrcode         = input('qrcode', '');
            $data           = array(
                "appid"          => $appid,
                "appsecret"      => $appsecret,
                "token"          => $token,
                "ttapp_name"     => $ttapp_name,
                "ttapp_original" => $ttapp_original,
                "encodingaeskey" => $encodingaeskey,
                'qrcode'         => $qrcode
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
    /**
     * 小程序包管理
     *
     */
    public function package()
    {
        $config       = new ConfigModel();
        $ttapp_config = $config->getttappConfig($this->site_id);
        $ttapp_config = $ttapp_config['data']['value'];
        if (empty($ttapp_config) || empty($ttapp_config['appid'])) $this->error('小程序尚未配置，请先配置您的小程序！', addon_url('ttapp://shop/ttapp/config'));
        $this->assign('config', $ttapp_config);
        $is_new_version = 0;
        // 获取站点小程序版本信息
        $version_info          = $config->getttappVersion($this->site_id);
        $version_info          = $version_info['data']['value'];
        $currrent_version_info = config('info');
        if (!isset($version_info['version']) || (isset($version_info['version']) && $version_info['version'] != $currrent_version_info['version_no'])) {
            $is_new_version = 1;
        }
        $this->assign('is_new_version', $is_new_version);
        // 检测授权
        $upgrade_model = new Upgrade();
        $auth_info = $upgrade_model->authInfo();
        $this->assign('is_auth', ($auth_info['code'] == 0));
        return $this->fetch('ttapp/package', [], $this->replace);
    }
    /**
     * 小程序包下载
     */
    public function download()
    {
        if (strstr(ROOT_URL, 'niuteam.cn') === false) {
            $ttapp = new ttappModel();
            $ttapp->download($this->site_id);
            $config       = new ConfigModel();
            $version_info = config('info');
            $config->setttappVersion(['version' => $version_info['version_no']], 1, $this->site_id);
        }
    }
    /**
     * 下载uniapp源码
     */
    public function downloadUniapp(){
        if (strstr(ROOT_URL, 'niuteam.cn') === false) {
            $app_info = config('info');
            $upgrade_model = new Upgrade();
            $res = $upgrade_model->downloadUniapp($app_info['version_no']);
            if ($res['code'] == 0) {
                $filename = "upload/{$app_info['version_no']}_uniapp.zip";
                $res = file_put_contents($filename, base64_decode($res['data']));
                header("Content-Type: application/zip");
                header("Content-BirthdayGift-Encoding: Binary");
                header("Content-Length: " . filesize($filename));
                header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
                readfile($filename);
                @unlink($filename);
            } else {
                return $this->error($res['message']);
            }
        }
    }
    /**
     * 分享
     */
    public function share(){
        $config_model = new ConfigModel();
        if (request()->isAjax()) {
            $key = input('key', 'index');
            $value = [
                'title' => input('title', ''),
                'path' => input('path', '')
            ];
            $res = $config_model->setShareConfig($this->site_id, $this->app_module, $key, $value);
            return $res;
        }
        $scene = [
            [
                'key' => 'index',
                'title' => '首页'
            ]
        ];
        $this->assign('scene', $scene);
        $config = $config_model->getShareConfig($this->site_id, $this->app_module);
        $this->assign('config', $config['data']['value']);
        $this->assign('shop_info', $this->shop_info);
		
        return $this->fetch('ttapp/share', [], $this->replace);
    }
}