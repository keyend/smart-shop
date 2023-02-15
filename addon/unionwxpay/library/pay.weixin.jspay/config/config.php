<?php
class Config
{
    private $cfg = array(
        'env' => 'developer',
        'url'=>'https://qra.95516.com/pay/gateway',/*支付请求接口地址，无需更改 */
		'mchId'=>'QRA2904533107PT',/* 测试商户号，商户正式上线时需更改为自己的 */
		'key'=>'c1b43a921b7843f284ddb73ab1597775',  /* 测试密钥，商户需更改为自己的*/
        'version'=>'2.0',
        'sign_type'=>'MD5'
    );

    public function __construct($opts = [])
    {
        $this->loadSettings($opts);
    }

    public function loadSettings($opts = [])
    {
        if (isset($opts['mch_id'])) {
            $this->cfg['mchId'] = $opts['mch_id'];
            $this->cfg['key'] = $opts['key'];
        }
    }

    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
