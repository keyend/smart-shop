<?php
namespace addon\memberregister\api\controller;
use app\api\controller\BaseApi;
use addon\memberregister\model\Register;
/**
 * 会员注册奖励
 */
class Config extends BaseApi
{
    /**
     * 计算信息
     */
    public function Config()
    {
        //注册后奖励
        $register_model = new Register();
        $info = $register_model->getConfig($this->site_id);
        return $this->response($info);
    }
}