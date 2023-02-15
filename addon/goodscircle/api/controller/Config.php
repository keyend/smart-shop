<?php
namespace addon\goodscircle\api\controller;
use app\api\controller\BaseApi;
use addon\goodscircle\model\Config as ConfigModel;
/**
 * 好物圈
 */
class Config extends BaseApi
{
    /**
     * 获取好物圈配置
     */
    public function info()
    {
        $config = new ConfigModel();
        $res    = $config->getGoodscircleConfig($this->site_id);
        return $this->response($this->success($res['data']));
    }
}