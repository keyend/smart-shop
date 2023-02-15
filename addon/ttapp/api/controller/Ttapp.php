<?php
namespace addon\ttapp\api\controller;
use addon\ttapp\model\Config;
use app\api\controller\BaseApi;
use addon\ttapp\model\Ttapp as TtappModel;
class Ttapp extends BaseApi
{
    /**
     * 获取openid
     */
    public function authCodeToOpenid()
    {
        $app_model   = new TtappModel($this->site_id);
        $res         = $app_model->authCodeToOpenid($this->params);
        return $this->response($res);
    }
    /*
     * 获取小程序码
     */
    public function qrcode(){
        $config_model = new Config();
        $config = $config_model->getWeappConfig($this->site_id);
        $qrcode = $config['data']['value']['qrcode'] ?? '';
        return $this->response($this->success($qrcode));
    }
    /**
     * 获取手机号
     * @return void
     */
    public function getmobile()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $code        = $this->params['code'];
        $model       = new TtappModel($this->site_id);
        $res         = $model->getMobile($code);

        if ($res["code"] >= 0) {
            $member_model = app()->make(\app\model\member\Member::class);
            $member_model->editMember([
                'mobile' => $res['data']['purePhoneNumber']
            ], [['member_id', '=', $token['data']['member_id'], ['site_id', '=', $this->site_id]]]);
            $res['data']['mobile'] = $res['data']['purePhoneNumber'];
        }

        return $this->response($res);
    }
}