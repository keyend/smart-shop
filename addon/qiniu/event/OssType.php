<?php
namespace addon\qiniu\event;
/**
 * 云上传方式
 */
class OssType
{
    /**
     * 短信发送方式方式及配置
     */
    public function handle()
    {
        $info = array(
            "sms_type"      => "qiniu",
            "sms_type_name" => "七牛云存储",
            "edit_url"      => "qiniu://shop/config/config",
            "shop_url"      => "qiniu://shop/config/config",
            "desc"          => "七牛云存储,提供10GB免费存储空间"
        );
        return $info;
    }
}