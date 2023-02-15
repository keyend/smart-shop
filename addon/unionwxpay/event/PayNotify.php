<?php
namespace addon\unionwxpay\event;
use addon\unionwxpay\model\Pay as PayModel;
use app\model\system\Pay as PayCommon;
/**
 * 支付回调
 */
class PayNotify
{
    /**
     * 获取默认站点ID
     *
     * @return void
     */
    private function getSiteId()
    {
        $site =  model('site')->getInfo([['site_type', '=', 'shop']], 'site_id');
        return $site['site_id'];
    }

    private function logger($title, $data) {
    	$handler = fopen(__DIR__ . '/result.txt','a+');
    	$content = "================".$title."===================\n";
    	if(is_string($data) === true){
    		$content .= $data."\n";
    	}
    
    	if(is_array($data) === true){
    			foreach($data as $k=>$v){
    					$content .= "{$k}: {$v}\n";
    			}
    	}
    
    	$flag = fwrite($handler,$content);
    	fclose($handler);
    
    	return $flag;
    }

    /**
     * 支付方式及配置
     */
    public function handle()
    {
		$input = file_get_contents('php://input');
		$this->logger('NOTIFY', $input);
		
        $site_id = $this->getSiteId();
        $pay_model = new PayModel(1,$site_id);
        $params = $pay_model->getAllRequestParameters();
        if (is_array($params) && isset($params['out_trade_no'])) {
            $pay = new PayCommon();
            $pay_info = $pay->getPayInfo($params['out_trade_no']);
            $pay_info = $pay_info['data'];
            if (!empty($pay_info)) {
                $config = model('config')->getInfo([
                    ['value', 'like', "%{$params['sub_appid']}%"],
                    ['config_key', 'in', ['WECHAT_CONFIG', 'WEAPP_CONFIG']],
                    ['app_module', '=', 'shop'],
                    ['site_id', '=', $pay_info['site_id']]
                ], 'config_key');
                if (!empty($config)) {
                    $is_weapp  = $config['config_key'] == 'WEAPP_CONFIG' ? 1 : 0;
                    $result = $pay_model->payNotify();
                    if ($result['code'] != -1) {
                        if (is_string($result['code']) && $result['code'] === 'FAIL') {
                        } else {
                            die('success');
                        }
                    }
                }
            }
            die('failure');
        }
    }
}