<?php
namespace addon\weapp\api\controller;
use addon\weapp\model\Config;
use addon\weapp\model\Message;
use app\api\controller\BaseApi;
use addon\weapp\model\Weapp as WeappModel;
class Weapp extends BaseApi
{
    /**
     * 获取openid
     */
    public function authCodeToOpenid()
    {
        $weapp_model = new WeappModel($this->site_id);
        $res         = $weapp_model->authCodeToOpenid($this->params);
        return $this->response($res);
    }
    /**
     * 获取消息模板id(最多三条)
     */
    public function messageTmplIds(){
        $keywords = $this->params['keywords'] ?? '';
        $message = new Message();
        $res = $message->getMessageTmplIds($this->site_id, $keywords);
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
     * 分享
     * @return false|string
     */
    public function share(){
        $config_model = new Config();
        $config = $config_model->getShareConfig($this->site_id, 'shop');
        return $this->response($this->success($config['data']['value']));
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
        $weapp_model = new WechatModel($this->site_id);
        $res         = $weapp_model->getMobile($code);

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