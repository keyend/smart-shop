<?php
namespace addon\alioss\shop\controller;
use addon\alioss\model\Config as ConfigModel;
use app\shop\controller\BaseShop;
/**
 * 七牛云上传管理
 */
class Config extends BaseShop
{
	
	/**
	 * 云上传配置
	 * @return mixed
	 */
	public function config()
	{
		$config_model = new ConfigModel();
		if (request()->isAjax()) {
			$bucket = input("bucket", "");
			$access_key_id = input("access_key_id", "");
			$access_key_secret = input("access_key_secret", "");
			$endpoint = input("endpoint", "");
			$status = input("status", 0);
            $domain = input("domain", "");
            $is_domain = input("is_domain", 0);
			$data = array(
				"bucket" => $bucket,
				"access_key_id" => $access_key_id,
				"access_key_secret" => $access_key_secret,
				"endpoint" => $endpoint,
                "domain" => $domain,
                "is_domain" => $is_domain
			);
			
			$result = $config_model->setAliossConfig($data, $status, $this->site_id, $this->app_module);
			return $result;
		} else {
			$info_result = $config_model->getAliossConfig($this->site_id, $this->app_module);
			$info = $info_result["data"];
			$this->assign("info", $info);
			return $this->fetch("config/config");
		}
	}
}