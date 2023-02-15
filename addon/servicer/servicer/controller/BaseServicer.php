<?php
namespace addon\servicer\servicer\controller;
use app\Controller;
use app\model\shop\Shop;
use app\model\store\Store as StoreModel;
use app\model\system\Addon;
use app\model\system\Group as GroupModel;
use app\model\system\Menu;
use app\model\system\Site;
use app\model\system\User as UserModel;
use app\model\web\Config as ConfigModel;
use app\model\web\WebSite;
/**
 * 客服
 */
class BaseServicer extends Controller
{
    protected $crumbs = [];
    protected $uid;
    protected $user_info;
    protected $url;
    protected $group_info;
    protected $site_id;
    protected $shop_info;
    protected $app_module = "servicer";
    protected $addon = '';
    protected $replace;
    public function __construct()
    {
        $this->replace = [
            'SERVICER_CSS' => __ROOT__ . '/addon/servicer/servicer/view/public/css',
            'SERVICER_JS'  => __ROOT__ . '/addon/servicer/servicer/view/public/js',
            'SERVICER_IMG' => __ROOT__ . '/addon/servicer/servicer/view/public/img',
        ];
        //执行父类构造函数
        parent::__construct();
        //检测基础登录
        $user_model      = new UserModel();
        //检测基础登录
        $this->site_id = request()->siteid();
        $this->uid       = $user_model->uid($this->app_module, $this->site_id);
        $this->url       = request()->parseUrl();
        $this->addon     = request()->addon() ? request()->addon() : '';
        $this->user_info = $user_model->userInfo($this->app_module, $this->site_id);
        $this->assign("user_info", $this->user_info);
        $this->site_id = $this->user_info["site_id"];
        if (empty($this->uid)) {
            $this->redirect(addon_url("servicer://servicer/login/login"));
            exit();
        }
        if (!request()->isAjax()) {
            $this->initBaseInfo();
        }
    }
    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        return ['code' => $code, 'data' => $data, 'msg' => $msg];
    }
    /**
     * 加载基础信息
     */
    private function initBaseInfo()
    {
        $site_model = new Site();
        $this->shop_info = $site_model->getSiteInfo([['site_id', '=', $this->site_id]], '*')['data'] ?? [];
        $this->assign("shop_info", $this->shop_info);
        //加载版权信息
        $config_model = new ConfigModel();
        $copyright    = $config_model->getCopyright();
        $this->assign('copyright', $copyright['data']['value']);
    }
}