<?php
namespace app\api\controller;
use app\model\system\Servicer;
use app\model\web\Config as ConfigModel;
class Config extends BaseApi
{
    /**
     * 详情信息
     */
    public function defaultimg()
    {
        $upload_config_model = new ConfigModel();
        $res = $upload_config_model->getDefaultImg($this->site_id, 'shop');
        if (!empty($res[ 'data' ][ 'value' ])) {
            return $this->response($this->success($res[ 'data' ][ 'value' ]));
        } else {
            return $this->response($this->error());
        }
    }
    /**
     * 版权信息
     */
    public function copyright()
    {
        $config_model = new ConfigModel();
        $res = $config_model->getCopyright($this->site_id, 'shop');
        return $this->response($this->success($res[ 'data' ][ 'value' ]));
    }
    /**
     * 获取当前时间戳
     * @return false|string
     */
    public function time()
    {
        $time = time();
        return $this->response($this->success($time));
    }
    /**
     * 获取验证码配置
     */
    public function getCaptchaConfig(){
        
        $config_model = new ConfigModel();
        $info = $config_model->getCaptchaConfig();
        return $this->response($this->success($info));
    }
    /**
     * 客服配置
     */
    public function servicer(){
        $servicer_model = new Servicer();
        $result = $servicer_model->getServicerConfig()['data'] ?? [];
        return $this->response($this->success($result['value'] ?? []));
    }
}