<?php
namespace addon\weapp\event;
use addon\weapp\model\Weapp;
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
        if ($param['app_type'] == 'weapp') {
            $weapp = new Weapp($param['site_id']);
            return $weapp->decryptData($param);
        }
    }
}