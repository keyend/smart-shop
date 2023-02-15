<?php
namespace extend;

use GuzzleHttp\Client;
use think\facade\Log;
/**
 * 字节小程序接入API
 * @version 1.0.0
 */
class Tiktalk
{
    const ENV = 'prod';
    const OPEN_API_URL = 'https://developer.toutiao.com/api/apps';
    const OPEN_API_SANDBOX_URL = 'https://open-sandbox.douyin.com/api/apps';

    /**
     * 返回对象
     *
     * @var stdClass
     */
    private $res;

    /**
     * 配置信息
     *
     * @var array
     */
    private $config;

    /**
     * 返回错误
     *
     * @var array
     */
    private $errors = [
        '0' => '请求成功',
        '-1' => '系统错误',
        '40014' => '未传必要参数，请检查',
        '40015' => 'appid 错误',
        '40017' => 'secret 错误',
        '40018' => 'code 错误',
        '40019' => 'acode 错误',
    ];

    /**
     * 构造函数初始化配置
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (empty($config) || !isset($config['appid']) || !isset($config['appsecret'])) {
            throw new \Exception('init failed');
        }

        $this->config = $config;
        $this->res = new \app\model\BaseModel();
    }

    /**
     * 返回连接地址
     *
     * @param string $protocol
     * @return string
     */
    private function getUrl($protocol)
    {
        return (self::ENV == 'dev' ? self::OPEN_API_SANDBOX_URL : self::OPEN_API_URL) . $protocol;
    }

    /**
     * 返回错误信息
     *
     * @param integer $code
     * @return string
     */
    private function getError($code)
    {
        if (isset($this->errors[$code])) {
            return $this->errors[$code];
        }
    }

    /**
     * 返回一个实例
     *
     * @param array $config
     * @return std
     */
    public static function instance($opts) 
    {
        return new static($opts);
    }

    /**
     * 提交请求
     *
     * @param string $url
     * @param array $params
     * @param string $type
     * @return array
     */
    public function request($url, $params = [], $type = 'POST')
    {
        $client = new Client();
        $url = $this->getUrl($url);

        try {
            Log::info("REQUEST {$url}?" . http_build_query($params));
            $params = [
                'headers' => [
                    'Content-Type'  => 'application/json;charset=utf-8'
                ],
                'body' => json_encode($params),
                'verify' => false
            ];
            $response = $client->request($type, $url, $params);
            if ($response->getStatusCode() != 200) {
                throw new \Exception('连接失败');
            }

            $body = $response->getBody();

            Log::info("RESPONSE {$body}");

            $res = json_decode($body, true);
            if (is_null($res)) {
                throw new \Exception('解析字节API返回内容失败');
            }

            if (isset($res['err_no']) && $res['err_no'] != 0) {
                $this->res->error($this->getError($res['err_no'])??$res['err_tips']);
            }
            
            $data = $res['data'];
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return $this->res->error($e->getMessage());
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
            return $this->res->error($e->getMessage());
        } catch (\Guzzle\Http\Exception\BadResponseException $e) {
            return $this->res->error($e->getMessage());
        } catch (\Exception $e) {
            return $this->res->error($e->getMessage());
        }

        return $this->res->success($data);
    }

    /**
     * 通过login接口获取到登录凭证后，开发者可以通过服务器发送请求的方式获取 session_key 和 openId。
     *
     * @param string $session_id
     * @return void
     */
    public function getOpenId($session_id)
    {
        return $this->request('/v2/jscode2session', [
            'appid' => $this->config['appid'],
            'secret' => $this->config['appsecret'],
            'code' => $session_id
        ]);
    }

    /**
     * 消息AES解密
     *
     * @param string $session_key
     * @param string $iv
     * @param string $encryptedData
     * @return void
     */
    public function decryptData($session_key = '', $iv = '', $encryptedData = '')
    {
        try {
            $decryptData = openssl_decrypt(base64_decode($encryptedData), 'AES-128-CBC', $session_key, OPENSSL_RAW_DATA, $iv);
            Log::info("解码消息 " . $encryptedData . " 返回: " . $decryptData);

            $result = json_decode($decryptData, true);
            if ($result == null) {
                throw new \Exception("消息解密失败");
            }
        } catch(\Exception $e) {
            return $this->res->error($e->getMessage());
        }

        return $this->res->success($result);
    }
}