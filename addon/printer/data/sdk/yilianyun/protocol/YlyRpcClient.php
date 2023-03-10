<?php
namespace addon\printer\data\sdk\yilianyun\protocol;
use addon\printer\data\sdk\yilianyun\config\YlyConfig;
use Exception;
class YlyRpcClient{
    private $clientId;
    private $clientSecret;
    private $requestUrl;
    private $token;
    private $log;
    public function __construct($token, YlyConfig $config)
    {
        $this->clientId = $config->getClientId();
        $this->clientSecret = $config->getClientSecret();
        $this->requestUrl = $config->getRequestUrl();
        $this->log = $config->getLog();
        $this->token = $token;
    }
    public function call($action, array $params)
    {
        $time = time();
        $params = array_merge(array(
            'client_id' => $this->clientId,
            'timestamp' => $time,
            'sign' => $this->getSign($time),
            'id' => $this->uuid4(),
            'access_token' => $this->token,
        ), $params);
        $result = $this->send($params, $this->requestUrl . '/' .$action);
        $response = json_decode($result, false, 512, JSON_BIGINT_AS_STRING);
        if (is_null($response)) {
            throw new Exception("invalid response.");
        }
        if (
            isset($response->error) &&
            $response->error != 0
        ){
            $errorDescription = isset($response->body) ? $response->error_description . $response->body : $response->error_description;
            throw new Exception('Call method ' . $action . ' error code is ' . $response->error . ' error message is ' . $errorDescription);
        }
        return $response;
    }
    public function getSign($timestamp)
    {
        return md5(
            $this->clientId.
            $timestamp.
            $this->clientSecret
        );
    }
    public function uuid4(){
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = '-';
        $uuidV4 =
            substr($charid, 0, 8) . $hyphen .
            substr($charid, 8, 4) . $hyphen .
            substr($charid, 12, 4) . $hyphen .
            substr($charid, 16, 4) . $hyphen .
            substr($charid, 20, 12);
        return $uuidV4;
    }
    public function send($data,$url)
    {
        $requestInfo = http_build_query($data);
        $log = $this->log;
        if ($log != null) {
            $log->info("request data: " . $requestInfo);
        }
        $curl = curl_init(); // ????????????CURL??????
        curl_setopt($curl, CURLOPT_URL, $url); // ??????????????????
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // ??????????????????????????????
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Expect:'
        )); // ??????????????????????????????
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // ??????????????????
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // ????????????Referer
        curl_setopt($curl, CURLOPT_POST, 1); // ?????????????????????Post??????
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestInfo); // Post??????????????????
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // ??????????????????????????????
        curl_setopt($curl, CURLOPT_HEADER, 0); // ???????????????Header????????????
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // ??????????????????????????????????????????
        $response = curl_exec($curl); // ????????????
        if (curl_errno($curl)) {
            if ($log != null) {
                $log->error("error: " . curl_error($curl));
            }
            throw new Exception(curl_error($curl));
        }
        if ($log != null) {
            $log->info("response: " . $response);
        }
        curl_close($curl); // ??????CURL??????
        return $response; // ????????????
    }
}