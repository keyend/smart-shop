<?php
/**
 * 支付接口调测例子
 * ================================================================
 * index 进入口，方法中转
 * submitOrderInfo 提交订单信息
 * queryOrder 查询订单
 * 
 * ================================================================
 */
require('Utils.class.php');
require('config/config.php');
require('class/RequestHandler.class.php');
require('class/ClientResponseHandler.class.php');
require('class/PayHttpClient.class.php');

Class unionPayRequest
{
    //$url = 'http://192.168.1.185:9000/pay/gateway';

    private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;
    private $cfg = null;
    
    public function __construct($options = []){
        $this->Request($options);
    }

    public function Request($options = []){
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();
        $this->cfg = new Config($options);

        $this->reqHandler->setGateUrl($this->cfg->C('url'));

        $sign_type = $this->cfg->C('sign_type');

        if ($sign_type == 'MD5') {
            $this->reqHandler->setKey($this->cfg->C('key'));
            $this->resHandler->setKey($this->cfg->C('key'));
            $this->reqHandler->setSignType($sign_type);
        } else if ($sign_type == 'RSA_1_1' || $sign_type == 'RSA_1_256') {
            $this->reqHandler->setRSAKey($this->cfg->C('private_rsa_key'));
            $this->resHandler->setRSAKey($this->cfg->C('public_rsa_key'));
            $this->reqHandler->setSignType($sign_type);
        }
    }
    
    public function index($data)
    {
        $data['method'] = isset($data['method'])?$data['method']:'submitOrderInfo';

        switch($data['method']){
            case 'submitOrderInfo'://提交订单
                return $this->submitOrderInfo($data);
                break;

            case 'queryOrder'://查询订单
                return $this->queryOrder($data);
                break;

            case 'submitRefund'://提交退款
                return $this->submitRefund($data);
                break;

            case 'queryRefund'://查询退款
                return $this->queryRefund($data);
                break;

            case 'submitClose'://关闭订单
                return $this->submitClose($data);
                break;

            case 'callback':
                return $this->callback($data);
                break;
        }
    }

    /**
     * 提交订单信息
     */
    public function submitOrderInfo($post)
    {
        $this->reqHandler->setReqParams($post, array('method'));
        $this->reqHandler->setParameter('service','pay.weixin.jspay');//接口类型：pay.weixin.jspay
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('sign_type',$this->cfg->C('sign_type'));
        $this->reqHandler->setParameter('nonce_str',mt_rand());
        // 测试商品
        if ($this->cfg->C('env') === 'developer') {
            // $this->reqHandler->setParameter('sub_appid','xxx');
            // $this->reqHandler->setParameter('sub_openid','');
        }

        $this->reqHandler->createSign();

        $data = Utils::toXml($this->reqHandler->getAllParameters());
        //var_dump($data);
        Utils::dataRecodes(date("Y-m-d H:i:s",time()).'公众号支付请求XML',$data);//请求xml记录到result.txt
        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);

        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            $res = $this->resHandler->getAllParameters();
            Utils::dataRecodes(date("Y-m-d H:i:s",time()).'支付返回XML',$res);
            if($this->resHandler->isTenpaySign()){
                
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    //当返回状态与业务结果都为0时继续判断
                    //echo json_encode(array('status'=>200,'data'=>$res));
                    return array(
                        'status'   => 1,
                        'service'  => 'pay.weixin.jspay',
                        'out_trade_no' => $post['out_trade_no'],
                        'pay_info' => $this->resHandler->getParameter('pay_info')
                    );
                }else{
                    return array('status'=>500,'msg'=>$this->resHandler->getParameter('message').$this->resHandler->getParameter('err_code').$this->resHandler->getParameter('err_msg'));
                }
            }
            
            
            return array('status'=>500,'msg'=>$this->resHandler->getParameter('message'));
        }else{
            return array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo());
        }
    }

    /**
     * 查询订单
     */
    public function queryOrder($post){
        $this->reqHandler->setReqParams($post, array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])){
            return array('status'=>500, 'msg'=>'请输入商户订单号,平台订单号!');
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.query');//接口类型：unified.trade.query
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('sign_type',$this->cfg->C('sign_type'));
        $this->reqHandler->setParameter('nonce_str',mt_rand());//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                $res = $this->resHandler->getAllParameters();
                Utils::dataRecodes('查询订单',$res);
                //支付成功会输出更多参数，详情请查看文档中的7.1.4返回结果
                return array('status'=>200,'msg'=>'查询订单成功，请查看result.txt文件！','data'=>$res);
            }
            return array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message'));
        }else{
            return array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo());
        }
    }
    
	
	 /**
     * 提交退款
     */
    public function submitRefund($post){
        $this->reqHandler->setReqParams($post,array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])){
            return array('status'=>500, 'msg'=>'请输入商户订单号或平台订单号!');
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refund');//接口类型：unified.trade.refund
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str',mt_rand());//随机字符串，必填项，不长于 32 位
        $this->reqHandler->setParameter('sign_type',$this->cfg->C('sign_type'));
        $this->reqHandler->setParameter('op_user_id',$this->cfg->C('mchId'));//必填项，操作员帐号,默认为商户号

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                 'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                 'out_refund_no'=>$this->resHandler->getParameter('out_refund_no'),
                                 'refund_id'=>$this->resHandler->getParameter('refund_id'),
                                 'refund_channel'=>$this->resHandler->getParameter('refund_channel'),
                                 'refund_fee'=>$this->resHandler->getParameter('refund_fee'),
                                 'coupon_refund_fee'=>$this->resHandler->getParameter('coupon_refund_fee'));*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('提交退款',$res);
                    return array('status'=>200,'msg'=>'退款成功,请查看result.txt文件！','data'=>$res);
                }else{
                    return array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg'));
                }
            }
            return array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message'));
        }else{
            return array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo());
        }
    }

    /**
     * 查询退款
     */
    public function queryRefund($post){
        $this->reqHandler->setReqParams($post,array('method'));
        if(count($this->reqHandler->getAllParameters()) === 0){
            return array('status'=>500, 'msg'=>'请输入商户订单号,平台订单号,商户退款单号,平台退款单号!');
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refundquery');//接口类型：unified.trade.refundquery
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('sign_type',$this->cfg->C('sign_type'));
        $this->reqHandler->setParameter('nonce_str',mt_rand());//随机字符串，必填项，不长于 32 位
        
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);//设置请求地址与请求参数
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                  'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                  'refund_count'=>$this->resHandler->getParameter('refund_count'));
                    for($i=0; $i<$res['refund_count']; $i++){
                        $res['out_refund_no_'.$i] = $this->resHandler->getParameter('out_refund_no_'.$i);
                        $res['refund_id_'.$i] = $this->resHandler->getParameter('refund_id_'.$i);
                        $res['refund_channel_'.$i] = $this->resHandler->getParameter('refund_channel_'.$i);
                        $res['refund_fee_'.$i] = $this->resHandler->getParameter('refund_fee_'.$i);
                        $res['coupon_refund_fee_'.$i] = $this->resHandler->getParameter('coupon_refund_fee_'.$i);
                        $res['refund_status_'.$i] = $this->resHandler->getParameter('refund_status_'.$i);
                    }*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('查询退款',$res);
                    return array('status'=>200,'msg'=>'查询成功,请查看result.txt文件！','data'=>$res);
                }else{
                    return array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code'));
                }
            }
            return array('status'=>500,'msg'=>$this->resHandler->getContent());
        }else{
            return array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo());
        }
    }

    /**
     * 关闭订单
     */
    public function submitClose($post) 
    {
        $this->reqHandler->setReqParams($post,array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['out_trade_no'])){
            return array('status'=>500, 'msg'=>'请输入商户订单号!');
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refund');//接口类型：unified.trade.refund
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str',mt_rand());//随机字符串，必填项，不长于 32 位
        $this->reqHandler->setParameter('sign_type',$this->cfg->C('sign_type'));
        $this->reqHandler->setParameter('op_user_id',$this->cfg->C('mchId'));//必填项，操作员帐号,默认为商户号

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('关闭订单',$res);
                    return array('status'=>200,'msg'=>'关闭成功,请查看result.txt文件！','data'=>$res);
                }else{
                    return array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg'));
                }
            }
            return array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message'));
        }else{
            return array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo());
        }
    }

    /**
     * 提供给平台的回调方法
     */
    public function callback($post){
        $xml = file_get_contents('php://input');
        Utils::dataRecodes(date("Y-m-d H:i:s",time()).'回调通知XML', $xml);
        $this->resHandler->setContent($xml);
        // 默认请求为付款操作
        $method = 'pay';
        // 分解附加参数
        if ($this->resHandler->getParameter('attach') !== '') {
            $attach = explode('|', $this->resHandler->getParameter('attach'));

            global $_W;

            $_W['openid'] = $attach[0];
            $_W['uniacid'] = $attach[1];

            $method = $attach[2];
            // 重新加载配置
            $this->cfg->loadSettings();
        }

        $this->resHandler->setKey($this->cfg->C('key'));
        if($this->resHandler->isTenpaySign()){
            if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                Utils::dataRecodes('接口回调收到通知参数', $this->resHandler->getAllParameters());
                $this->resHandler->setParameter('method', $method);
                return $this->resHandler;
            }else{
                throw new \Exception($this->resHandler->getParameter('err_msg'));
            }
        }else{
            throw new \Exception('验签失败');
        }
    }
}
