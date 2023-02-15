<?php
namespace addon\ttapp\model;
use app\model\BaseModel;
use app\model\system\Api;
use extend\Tiktalk;
use think\facade\Cache;
use addon\ttapp\model\Config as ConfigModel;
use app\model\web\Config as WebConfig;
/**
 * 字节小程序配置
 */
class Ttapp extends BaseModel
{
    private $app;//字节模型
    //小程序类型
    public $service_type = array(
        0 => "小程序",
    );
    //小程序认证类型
    public $verify_type = array(
        -1 => "未认证",
        0  => "字节认证",
    );
    //business_info 说明
    public $business_type = array(
        'open_store' => "是否开通字节门店功能",
        'open_scan'  => "是否开通字节扫商品功能",
        'open_pay'   => "是否开通字节支付功能",
        'open_card'  => "是否开通字节卡券功能",
        'open_shake' => "是否开通字节摇一摇功能",
    );
    // 站点ID
    private $site_id;
    public function __construct($site_id = 0)
    {
        $this->site_id = $site_id;
        //字节小程序配置
        $ttapp_config_model = new ConfigModel();
        $ttapp_config       = $ttapp_config_model->getConfig($site_id);
        $ttapp_config       = $ttapp_config["data"]["value"];
        $config             = [
            'appid'         => $ttapp_config['appid'] ?? '',
            'appsecret'     => $ttapp_config["appsecret"] ?? ''
        ];

        $this->app          = Tiktalk::instance($config);
    }
    /**
     * TODO
     * 根据 jsCode 获取用户 session 信息
     * @param $param
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function authCodeToOpenid($param)
    {
        try {
            $result = $this->app->getOpenId($param['code']);
            if (0 <= $result['code']) {
                if (isset($result['data']['err_no'])) {
                    throw new \Exception($result['data']['err_tips']);
                }

                $res = [];
                $res['app_type'] = 'ttapp';
                $res['toutiao_openid'] = $result['data']['openid'];
                $member = model('member')->getInfo([['toutiao_openid', '=', $result['data']['openid']]], 'member_id,toutiao_openid,nickname,headimg');
                if (!empty($member)) {
                    $res = array_merge($res, $member);
                }

                Cache::set('ttapp_' . $res['toutiao_openid'], $result['data']);
                $result['data'] = $res;
            }

            return $result;
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 生成二维码
     * @param unknown $param
     */
    public function createQrcode($param)
    {
        try {
            $checkpath_result = $this->checkPath($param['qrcode_path']);
            if ($checkpath_result["code"] != 0) return $checkpath_result;
            // scene:场景值最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~
            $scene = '';
            if (!empty($param['data'])) {
                foreach ($param['data'] as $key => $value) {
                    if ($scene == '') $scene .= $key . '-' . $value;
                    else $scene .= '&' . $key . '-' . $value;
                }
            }
            $response = $this->app->app_code->getUnlimit($scene, [
                'page'  => substr($param['page'], 1),
                'width' => isset($param['width']) ? $param['width'] : 120
            ]);
            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $filename = $param['qrcode_path'] . '/';
                $filename .= $response->saveAs($param['qrcode_path'], $param['qrcode_name'] . '_' . $param['app_type'] . '.png');
                return $this->success(['path' => $filename]);
            } else {
                return $this->error($response, $response['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 校验目录是否可写
     * @param unknown $path
     * @return multitype:number unknown |multitype:unknown
     */
    private function checkPath($path)
    {
        if (is_dir($path) || mkdir($path, intval('0755', 8), true)) {
            return $this->success();
        }
        return $this->error('', "directory {$path} creation failed");
    }
    /**
     * 消息解密
     * @param array $param
     */
    public function decryptData($param = [])
    {
        try {
            $cache       = Cache::get('ttapp_' . $param['ttapp_openid']);
            $session_key = $cache['session_key'] ?? '';
            $result      = $this->app->decryptData($session_key, $param['iv'], $param['encryptedData']);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
}