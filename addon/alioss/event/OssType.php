<?php
namespace addon\alioss\event;
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
            "sms_type"      => "alioss",
            "sms_type_name" => "阿里云OSS",
            "edit_url"      => "alioss://shop/config/config",
            "shop_url"      => "alioss://shop/config/config",
            "desc"          => "阿里云OSS存储"
        );
        return $info;
    }
}