<?php
namespace addon\unionwxpay\model;
use app\model\member\Member;
use EasyWeChat\Factory;
use app\model\system\Pay as PayCommon;
use app\model\BaseModel;
use addon\weapp\model\Config as WeappConfig;
use addon\wechat\model\Config as WechatConfig;
use app\model\system\Pay as PayModel;
/**
 * 微信支付配置
 * 版本 1.0.4
 */
class Pay extends BaseModel
{
    private $app;//微信模型
    private $is_weapp = 0;
    private $config = [];
    private $data = [];

    public function __construct($is_weapp = 0, $site_id)
    {
        $this->is_weapp = $is_weapp;
        //微信支付配置
        $config_model = new Config();
        $config_result = $config_model->getPayConfig($site_id);
        $config = $config_result["data"];
        if (!empty($config)) {
            $config_info = $config["value"];
        }
        $app_id = "";
        if($is_weapp == 0){
            $wechat_config_model = new WechatConfig();
            $wechat_config_result = $wechat_config_model->getWechatConfig($site_id);
            $wechat_config = $wechat_config_result["data"];
            if(empty($wechat_config['value']))
            {
                $weapp_config_model = new WeappConfig();
                $weapp_config_result = $weapp_config_model->getWeappConfig($site_id);
                $weapp_config = $weapp_config_result["data"];
                $app_id = $weapp_config["value"]["appid"];
            }else{
                $app_id = $wechat_config["value"]["appid"];
            }
        }else{
            $weapp_config_model = new WeappConfig();
            $weapp_config_result = $weapp_config_model->getWeappConfig($site_id);
            $weapp_config = $weapp_config_result["data"];
            $app_id = $weapp_config["value"]["appid"];
        }
        $this->config = [
            'app_id' => $app_id,        //应用id
            'mch_id' => $config_info["mch_id"] ?? '',       //商户号
            'key' => $config_info["pay_signkey"] ?? '',          // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path' => realpath($config_info["apiclient_cert"]) ?? '', // apiclient_cert.pem XXX: 绝对路径！！！！
            'key_path' => realpath($config_info["apiclient_key"]) ?? '',   // apiclient_key.pem XXX: 绝对路径！！！！
            'notify_url' => '',// 你也可以在下单时单独设置来想覆盖它
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            /**
             * 日志配置
             *
             * level: 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
             * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
             * file：日志文件位置(绝对路径!!!)，要求可写权限
             */
            'log' => [
                'level' => 'debug',
                'permission' => 0777,
                'file' => 'runtime/log/wechat/easywechat.logs',
            ],
            'sandbox' => false, // 设置为 false 或注释则关闭沙箱模式
        ];

        // 引入SDK
        require_once(__DIR__ . '/../library/pay.weixin.jspay/request.php');
        $this->app = new \unionPayRequest($this->config);
//        $this->app = Factory::officialAccount($config);
//        $response = $this->app->server->serve();
        // 将响应输出
//        $response->send();exit; // Laravel 里请使用：return $response;
    }
    /**
     * 生成支付
     * @param $param
     */
    public function pay($param)
    {
        ///绑定商户数据
        $pay_model = new PayModel();
        $pay_model->bindMchPay($param["out_trade_no"], ["app_id" => $this->config["app_id"]]);
        $openid = "";
        //获取用户的open_id
        $member_model = new Member();
        switch ($param["trade_type"]) {
            case 'JSAPI' :
                if ($this->is_weapp == 1) {
                    $member_info_result = $member_model->getMemberInfo([["member_id", "=", $param["member_id"]]], "weapp_openid");
                    $member_info = $member_info_result["data"];
                    $openid = $member_info["weapp_openid"];
                } else {
                    $member_info_result = $member_model->getMemberInfo([["member_id", "=", $param["member_id"]]], "wx_openid");
                    $member_info = $member_info_result["data"];
                    $openid = $member_info["wx_openid"];
                }
                break;
            case 'NATIVE' :
                break;
            case 'MWEB' :
                break;
            case 'APP' :
                break;
        }

        $attach = array();
        $attach[] = $param["member_id"];
        $attach[] = $this->config["app_id"];
        $attach[] = $param["pay_type"];

        $data = array(
            'body' => str_sub($param["pay_body"], 15),
            'out_trade_no' => $param["out_trade_no"],
            'total_fee' => $param["pay_money"] * 100,
            'mch_create_ip' => request()->ip(), // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url' => $param["notify_url"], // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => $param["trade_type"], // 请对应换成你的支付方式对应的值类型
            'attach' => implode('|', $attach),
            'sub_openid' => $openid,
            'sub_appid' => $this->config["app_id"],
            'is_raw' => 1,
            'is_minipg' => 1
        );

        $result = $this->app->index($data);
        //调用支付失败
        if ($result["status"] != 1) {
            return $this->error([], $result["msg"]);
        }

        /**
         * 解析返回内容
         * {"timeStamp":"1662729647","callback_url":"https://qr.95516.com","package":"prepay_id=*****","paySign":"*****","appId":"****","signType":"RSA"，"nonceStr":"b6c1366eaa214e6482b10f3cbc385a74","status":"0"}
         */
        $result['pay_info'] = json_decode($result['pay_info'], true);
        if ($result['pay_info'] == null) {
            return $this->error([], '支付失败!');
        }

        switch ($param["trade_type"]) {
            case 'JSAPI' ://微信支付 或小程序支付
                $return = array(
                    "type" => "jsapi",
                    "data" => $result['pay_info']
                );
                break;
        }

        return $this->success($return);
    }
    /**
     * 支付异步通知
     * @param $param
     * @return mixed
     */
    public function payNotify()
    {
        $message = $this->data;
        // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
        $pay_common = new PayCommon();

        if ($message['return_code'] == 'SUCCESS') {
            //定义支付失败
            $pay_common->onlinePay($message['out_trade_no'], "unionwxpay", $message["transaction_id"], "unionwxpay");
        } else {
            return $this->error($message['return_msg']);
        }

        return $this->success();
    }
    /**
     * 关闭支付
     * @param $param
     */
    public function close($param)
    {
        $data = array(
            'method' => 'submitClose',
            'out_trade_no' => $param["out_trade_no"],
        );

        $result = $this->app->index($data);
        // 调用关闭失败
        if ($result["status"] != 200) {
            return $this->error([], $result["msg"]);
        }

        return $this->success();
    }
    /**
     * 微信原路退款
     * @param $param
     */
    public function refund($param)
    {
        $pay_info = $param["pay_info"];
        $refund_no = $param["refund_no"];
        $total_fee = $pay_info["pay_money"] * 100;
        $refund_fee = $param["refund_fee"] * 100;

        $data = array(
            'method' => 'submitRefund',
            'out_trade_no' => $pay_info["out_trade_no"],
            'out_refund_no' => $refund_no,
            'total_fee' => $total_fee,
            'refund_fee' => $refund_fee,
        );

        $result = $this->app->index($data);
        // 调用退款失败
        if ($result["status"] != 200) {
            return $this->error([], $result["msg"]);
        }

        return $this->success();
    }
    /**
     * 微信转账到零钱
     * @param array $param
     */
    public function transfer(array $param)
    {
        try {
            $config_model = new Config();
            $config_result = $config_model->getPayConfig($param['site_id']);
            if ($config_result['code'] < 0) return $config_result;
            $config = $config_result['data']['value'];
            if (empty($config)) return $this->error([], '平台未配置微信支付');
            if (!$config['transfer_status']) return $this->error([], '平台未启用微信转账');
            $this->app = Factory::payment($this->config);
            $data = [
                'partner_trade_no' => $param['out_trade_no'], // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
                'openid' => $param['account_number'],
                'check_name' => 'FORCE_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
                're_user_name' => $param['real_name'], // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
                'amount' => $param['amount'] * 100, // 转账金额
                'desc' => $param['desc']
            ];
            $res = $this->app->transfer->toBalance($data);
            if ($res['return_code'] == 'SUCCESS') {
                if ($res['result_code'] == 'SUCCESS') {
                    return $this->success([
                        'out_trade_no' => $res['partner_trade_no'], // 商户交易号
                        'payment_no' => $res['payment_no'], // 微信付款单号
                        'payment_time' => $res['payment_time'] // 付款成功时间
                    ]);
                } else {
                    return $this->error([], $res['err_code_des']);
                }
            } else {
                return $this->error([], $res['return_msg']);
            }
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 获取异步通知参数
     *
     * @return void
     */
    public function getAllRequestParameters()
    {
        try {
            $data = [];
            $response = $this->app->index(array(
                'method' => 'callback'
            ));
            $data = $response->getAllParameters();
            $data['return_code'] = 'SUCCESS';
        } catch(\Exception $e) {
            $data = [
                'return_code' => 'FAIL',
                'return_msg' => $e->getMessage()
            ];
        }

        $this->data = $data;
        
        return $data;
    }
}