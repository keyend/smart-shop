<?php
namespace addon\agencyteam;
use app\shop\controller\BaseShop;

class Controller extends BaseShop
{
    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
    protected $replace = [];

    public function __construct()
    {
        parent::__construct();

        $this->replace = [
            'ADDON_JS' => __ROOT__ . '/addon/agencyteam/static/public/js',
            'ADDON_CSS' => __ROOT__ . '/addon/agencyteam/static/public/css'
        ];

        error_reporting(E_ERROR);
    }
}