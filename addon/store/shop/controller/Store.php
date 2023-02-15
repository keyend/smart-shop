<?php
namespace addon\store\shop\controller;

use app\shop\controller\BaseShop;
use app\model\store\Store as StoreModel;
use app\model\system\Addon;
use app\model\system\Group as GroupModel;
use app\model\system\Menu;
use app\model\system\User as UserModel;
use app\model\web\Config as ConfigModel;
use app\model\web\WebSite;
use app\model\system\Site;
/**
 * 商户控制器
 * @package addon.store.controller
 */
class Store extends BaseShop
{
    /**
     * 替换规则
     *
     * @var array
     */
    protected $replace = [];

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->replace = [
            'STORE_CSS' => __ROOT__ . '/addon/store/store/view/public/css',
            'STORE_JS' => __ROOT__ . '/addon/store/store/view/public/js',
            'STORE_IMG' => __ROOT__ . '/addon/store/store/view/public/img',
        ];
        parent::__construct();
    }
}