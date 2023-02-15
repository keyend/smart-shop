<?php
namespace addon\weapp\event;
use addon\weapp\model\Ttapp;
/**
 * 开放数据解密
 */
class DecryptData
{
    /**
     * 执行安装
     */
    public function handle($param = [])
    {
        if ($param['app_type'] == 'ttapp') {
            $app = new Ttapp($param['site_id']);
            return $app->decryptData($param);
        }
    }
}