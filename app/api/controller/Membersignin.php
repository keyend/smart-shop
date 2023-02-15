<?php
namespace app\api\controller;
use addon\membersignin\model\Signin;
use app\model\member\MemberSignin as MemberSigninModel;
class Membersignin extends BaseApi
{
    /**
     * 是否已签到
     */
    public function issign()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $member_signin = new MemberSigninModel();
        $res           = $member_signin->isSign($token['data']['member_id']);
        return $this->response($res);
    }
    /**
     * 签到
     */
    public function signin()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $member_signin = new MemberSigninModel();
        $res           = $member_signin->signin($token['data']['member_id'], $this->site_id);
        return $this->response($res);
    }
    /**
     * 签到奖励规则
     * @return string
     */
    public function award()
    {
        $member_signin = new MemberSigninModel();
        $info          = $member_signin->getAward($this->site_id);
        return $this->response($info);
    }
    /**
     * 获取签到记录
     */
    public function getSignRecords()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $member_signin = new MemberSigninModel();
        $date = strtotime(date('Y-m-01 00:00:00')) - 86400*6;
        $condition = [
            ['member_id','=',$this->member_id],
            ['create_time','between',[$date,time()]],
            ['action','=','membersignin']
        ];
        $list = $member_signin->getMemberSigninList($condition,'create_time','id asc');
        return $this->response($list);
    }
    /**
     * 获取签到是否开启
     */
    public function getSignStatus()
    { 
        $config_model = new Signin();
        $config_result = $config_model->getConfig($this->site_id);
        return $this->response($config_result);
    }
}